<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class UsersExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $users = DB::table('users')->get();
        $user_status = ['Inactive', 'Actived', 'Banned'];
        foreach($users as $key => $value) {
            $data[] = array(
                'id' => $key+1,
                'email' => $value->email,
                'username' => $value->username,
                'phone' => $value->phone_number,
                'last_seen' => $this->last_seen($value->id),
                'total_f'=> count($this->total_referral($value->id)),
                'total_deposit' => number_format($this->total_deposit($value->id), 2),
                'total_withdraw' => number_format($this->total_withdraw($value->id), 2),
                'total_profit' => $this->total_profit($value->id),
                'total_agencies' => number_format($this->total_commission($value->id)['total_agency'], 2),
                'total_trade' => number_format($this->total_commission($value->id)['total_trade'], 2),
            );
        }
        return collect($data);
    }

    public function headings(): array
    {
        return [
            'STT',
            'Email',
            'Tên',
            'Số điện thoại',
            'Thời gian đăng nhập',
            'Số F trong toàn bộ hệ thống',
            'Tổng rút',
            'Tổng nạp',
            'Tổng lợi nhuận',
            'Tổng hoa hồng đại lý',
            'Tổng hoa hồng giao dịch',
        ];
    }

    public function last_seen($userid) {
        return DB::table('recent_logins')->where('userid', $userid)->pluck('created_at')->first();
    }

    public function total_referral($userid) {
        $total_referral = DB::select(DB::raw("SELECT 
            id,
            username,
            volume,
            is_agency,
            sponsor_id,
            REVERSE(SUBSTRING_INDEX(REVERSE(@visit),':',1)) as level
        FROM
            (SELECT 
                *
            FROM
                users) AS u,
            (SELECT @pv:=".$userid.", @n:=0, @visit:='".$userid.":0') initialisation
        WHERE
        FIND_IN_SET(sponsor_id, @pv)
            AND LENGTH(@pv:=CONCAT(@pv, ',', id))
            AND LENGTH(@tem:=@visit)
            AND LENGTH(@visit:=CONCAT(@tem,',',id,':',SUBSTRING_INDEX(SUBSTRING(@tem,
                    INSTR(@tem, sponsor_id) + LENGTH(sponsor_id) + 1,
                    LENGTH(@tem) - INSTR(@tem, sponsor_id) + 1),',',1) + 1))"));
        // $total_agency = 0;
        // foreach($total_referral as $key => $value) {
        //     if($value->is_agency) {
        //         $total_agency++;
        //     }
        // }

        return $total_referral;
    }

    public function total_deposit($userid) {
        return DB::table('deposit')->where('userid', $userid)->where('status', 1)->sum('total');
    }

    public function total_withdraw($userid) {
        return DB::table('withdraw')->where('userid', $userid)->where('status', 1)->sum('total');
    }

    public function total_profit($userid) {
        return DB::table('orders')->where('type', 'live')->where('userid', $userid)->select(DB::raw('IFNULL(SUM(CASE WHEN status = 1 THEN amount*profit_percent/100 ELSE 0 END) - SUM(CASE WHEN status = 2 THEN amount ELSE 0 END), 0) as profit'))->value('profit');
    }

    public function total_commission($userid) {
        $commissions = DB::table('commissions')->where('userid', $userid)->get();
        $commissions_trade = 0;
        $commissions_agency = 0;
        foreach($commissions as $key => $value) {
            if($value->commission_type == 'trade') {
                $commissions_trade += $value->amount;
            }else if($value->commission_type == 'agency'){
                $commissions_agency += $value->amount;
            }
        }
        $data = [
            'total_agency' => $commissions_agency,
            'total_trade' => $commissions_trade,
        ];
        return $data;
    }
}
