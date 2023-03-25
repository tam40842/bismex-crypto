<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Level
 */
trait Level
{

    /**
     * Save data
     */
    public function insertCommission($user, $monthDay, $calLevel = 0)
    {
        $result = [];
        $level = $user->is_franchise;
        $result = array_merge($result, $this->upLineCommission($user, $monthDay, $level));
        return $result;
    }

    public function upLineCommission($user, $monthDay, $level)
    {
        $result = [];
        $div = 2;
        $limitUnActive = 0;
        $commission = round($user->vol * 0.012, 2);
        if ($commission <= 0) {
            return $result;
        }

        $uplineUser = DB::select(DB::raw("SELECT u.id, u.username, u.is_franchise, IF(u.last_week_level > u.level,u.last_week_level,u.level) AS level, u.f_no
                                        FROM
                                            (WITH RECURSIVE tree
                                            AS (
                                                SELECT id, username, is_franchise, volume, sponsor_id, level, last_week_level, 1 as f_no
                                                FROM users
                                                WHERE id = " . $user->sponsor_id . "
                                                UNION ALL
                                                SELECT i.id, i.username, i.is_franchise, i.volume, i.sponsor_id, i.level, i.last_week_level, t.f_no+1 as f_no
                                                FROM users i INNER JOIN tree t ON  i.id = t.sponsor_id
                                                )
                                            SELECT t.id, t.username, t.is_franchise, t.level, t.last_week_level, t.f_no
                                            FROM tree as t) as u 
                                            WHERE u.f_no;"));

        foreach ($uplineUser as $item) {
            if($limitUnActive == 7){
                return $result;
            }

            if ($item->is_franchise == 0) {
                $limitUnActive++;
                continue;
            } else{
                $limitUnActive = 0;
            }

            $trade_bonus = $commission;
            $paid = DB::table('commissions')
                ->where('com_date', $monthDay)
                ->where('userid', $item->id)
                ->where('commission_type', 'trade')
                ->where('f_userid', $user->id)
                ->where('f_no', $item->f_no)
                ->sum('amount');

            $hasPay = round($trade_bonus - $paid, 2);

            if ($hasPay <= 0) {
                $commission =  round($commission / 2, 2);
                $div = $div * 2;
                continue;
            }

            DB::beginTransaction();
            try {
                $newData = [
                    'name' => 'Trade bonus',
                    'userid' => $item->id,
                    'amount' => $hasPay,
                    'message' => 'Trade bonus F' . $item->f_no,
                    'f_userid' => $user->id,
                    'f_no' => $item->f_no,
                    'commission_type' => 'trade',
                    'status' => 0,
                    'com_date' => $monthDay
                ];
                
                $newData['volume'] = $user->vol;

                DB::table('commissions')->insert($newData);
                DB::commit();
                $result[] = [$item->username, $user->username, $user->vol, 'F.' . $item->f_no, null, '1/' . $div, $hasPay];
                $commission =  round($commission / 2, 2);
                $div = $div * 2;
            } catch (QueryException $ex) {
                DB::rollBack();
                continue;
            }
        }

        return $result;
    }
}
