<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
use DB;
use Gate;

class CalculatorCommissionController extends Controller
{
    public function getIndex() {
        Gate::allows('modules', 'finance_calculator_commissions_access');

        $data = [
            'stastics_total' => [
                'total_transfer_up_line' => [
                    'icon' => 'fa fa-users',
                    'type' => 'amount',
                    'name' => 'Tổng tiền tuyến trên chuyển',
                    'total' => 0
                ],
                'total_transfer_down_line' => [
                    'icon' => 'fa fa-balance-scale',
                    'type' => 'amount',
                    'name' => 'Tổng tiền chuyển tuyến trên',
                    'total' => 0
                ],
                'total_deposit' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng nạp của nhánh',
                    'total' => 0
                ],
                'total_withdraw' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng rút của nhánh',
                    'total' => 0
                ],
                'total_profit' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng doanh số',
                    'total' => 0
                ],
            ],
            'histories_bonus' => [],
            'user_bonus' => null
        ];

        return view('admin.calculator.index', $data);
    }

    public function postSearch(Request $request) {
        $this->validate($request, [
            'username' => 'required'
        ]);

        $username = $request->username;
        $user_bonus = DB::table('users')->where('username', $username)->orWhere('email', $username)->where('status', 1)->first();
        if(is_null($user_bonus)) {
            return redirect()->route('admin.commissions.calculator')->with('alert_error', 'User '.$username.' này không tồn tại.');
        }

        $histories_bonus = DB::table('commission_calculator')->join('users', 'commission_calculator.userid', '=', 'users.id')
                        ->where('commission_calculator.userid', Auth::id())->where('commission_calculator.user_leader', $user_bonus->id)
                        ->select('commission_calculator.*', 'users.username as admin_bonus')->orderBy('commission_calculator.created_at', 'desc')->paginate(10);

        $users_up_line = [];
        if($user_bonus->sponsor_id > 0) {
            $users_up_line = $this->getUpline($user_bonus->id);
        }
        $users_down_line = $this->getDownline($user_bonus->id);

        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $total_transfer_up_line = DB::table('transfers')->whereIn('userid', $users_up_line)->where('recipient_id', $user_bonus->id);
        $total_transfer_down_line = DB::table('transfers')->where('userid', $user_bonus->id)->whereIn('recipient_id', $users_up_line);
        $total_deposit = DB::table('deposit')->whereIn('userid', $users_down_line)->where('status', 1);
        $total_withdraw = DB::table('withdraw')->whereIn('userid', $users_down_line)->where('status', 1);

        if(isset($request->date_from) && isset($request->date_to)) {
            $total_transfer_up_line = $total_transfer_up_line->whereBetween('created_at', [$date_from, $date_to]);
            $total_transfer_down_line = $total_transfer_down_line->whereBetween('created_at', [$date_from, $date_to]);
            $total_deposit = $total_deposit->whereBetween('created_at', [$date_from, $date_to]);
            $total_withdraw = $total_withdraw->whereBetween('created_at', [$date_from, $date_to]);
        }

        $total_transfer_up_line = $total_transfer_up_line->sum('total');
        $total_transfer_down_line = $total_transfer_down_line->sum('total');
        $total_deposit = $total_deposit->sum('total');
        $total_withdraw = $total_withdraw->sum('total');

        $data = [
            'stastics_total' => [
                'total_transfer_up_line' => [
                    'icon' => 'fa fa-users',
                    'type' => 'amount',
                    'name' => 'Tổng tiền tuyến trên chuyển',
                    'total' => $total_transfer_up_line,
                ],
                'total_transfer_down_line' => [
                    'icon' => 'fa fa-balance-scale',
                    'type' => 'amount',
                    'name' => 'Tổng tiền chuyển tuyến trên',
                    'total' => $total_transfer_down_line,
                ],
                'total_deposit' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng nạp của nhánh',
                    'total' => $total_deposit,
                ],
                'total_withdraw' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng rút của nhánh',
                    'total' => $total_withdraw,
                ],
                'total_profit' => [
                    'icon' => 'fa fa-usd',
                    'type' => 'money',
                    'name' => 'Tổng doanh số',
                    'total' => 0
                ],
            ],
            'filter' => [
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'username' => $request->username,
            ],
            'histories_bonus' => $histories_bonus,
            'user_bonus' => $user_bonus
        ];
        $data['stastics_total']['total_profit']['total'] = ($data['stastics_total']['total_transfer_up_line']['total'] - $data['stastics_total']['total_transfer_down_line']['total']) + ($data['stastics_total']['total_deposit']['total'] - $data['stastics_total']['total_withdraw']['total']);

        return view('admin.calculator.index', $data);
    }

    public function getUpline($userid, $users = []) {
        $user = DB::table('users')->where('id', $userid)->first();
        if(is_null($user)) {
            return false;
        }

        $user_sponsor = DB::table('users')->where('id', $user->sponsor_id)->first();
        array_push($users, $user_sponsor->id);

        if($user_sponsor->sponsor_id > 0) {
            return $this->getUpline($user_sponsor->id, $users);
        }

        return $users;
    }

    public function getDownline($userid) {
        $checkInNetwork = DB::select(DB::raw("select * from (
                                    SELECT 
                                        id,
                                        username,
                                        volume,
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
                                                    LENGTH(@tem) - INSTR(@tem, sponsor_id) + 1),',',1) + 1))) as u"));

        if(count($checkInNetwork)) {
            foreach($checkInNetwork as $key => $value) {
                $users[$key] = $value->id;
            }
            return $users;
        }

        return [];
    }

    public function postBonusUser(Request $request) {
        Gate::allows('modules', 'finance_calculator_commissions_add');

        $this->validate($request, [
            'username' => 'required',
            'ratio' => 'required|numeric',
            'total_profit' => 'required|numeric',
        ]);

        $user_bonus = DB::table('users')->where('username', $request->username)->orWhere('email', $request->username)->where('status', 1)->first();
        if(is_null($user_bonus)) {
            return response()->json([
                'status' => 422,
                'message' => 'User not found'
            ]);
        }

        $startWeek = Carbon::now()->startOfWeek()->startOfDay();
		$endOfWeek = Carbon::now()->endOfWeek()->endOfDay();

        $total = $request->total_profit * $request->ratio / 100;
        DB::table('commission_calculator')->insert([
            'userid' => Auth::id(),
            'user_leader' => $user_bonus->id,
            'ratio' => $request->ratio,
            'total' => $total,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Bonus leader '.number_format($total, 2).' successfully.'
        ]);
    }
}
