<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Level Calculator
 */
trait LevelCal
{
    public function calLevel($userid,$levels,$start = null, $end = null, $maxLevel = 9){
        
        if(is_null($start)){
            $start = Carbon::now()->startOfWeek();
        }

        if(is_null($end)){
            $end = Carbon::now()->endOfWeek();
        }
        $level = 0;
        $conditionActive = 200;
        $f1_active =    DB::select(DB::raw("SELECT 
                                                count(u.id) as f1_active
                                            FROM
                                                (SELECT * FROM users WHERE users.sponsor_id = ".$userid." ) AS u
                                                    LEFT JOIN
                                                (SELECT 
                                                    IFNULL(SUM(orders.amount),0) AS total, orders.userid
                                                FROM
                                                    orders
                                                WHERE
                                                    orders.type = 'live'
                                                    AND orders.expert_id = 0
                                                    AND orders.created_at BETWEEN '".$start."' AND '".$end."' 
                                                GROUP BY orders.userid) AS o ON o.userid = u.id
                                            WHERE total >= ".$conditionActive));
        $personal_volume_have = DB::table('orders')
                                    ->where('userid', $userid)
                                    ->whereBetween('created_at', [$start, $end])
                                    ->where('type', 'live')
                                    ->where('expert_id', 0)
                                    ->sum('amount');
        $user = DB::table('users')->find($userid);
        foreach($levels as $value){
            if($personal_volume_have >= $value->volume_personal && $f1_active[0]->f1_active >= $value->f1_active){
                $level = $value->level_number;
            } else{
                break;
            }
        }

        if($user->level > $level && $user->level <= $maxLevel){
            $level = $user->level;
        }

        $total_volume_week = $this->getBranchVolume($userid,$maxLevel,$start,$end);
        $total_branch_volume = 0;
        foreach($total_volume_week as $value){
            $total_branch_volume += $value->branch_volume;
        }
        $data = [
            "level" => $level,
            "f1_active" => $f1_active[0]->f1_active,
            "vol_trade_week" =>  $personal_volume_have,
            "total_volume_week" => $total_volume_week,
            "total_branch_volume" => $total_branch_volume
        ];

        return $data;
    }

    public function getBranchVolume($userid,$level,$start,$end){
        $branch_volume = DB::select(DB::raw("SELECT level ,sum(total) as branch_volume, count(id) as f_active
                                    FROM (SELECT 
                                                id,
                                                username,
                                                sponsor_id,
                                                REVERSE(SUBSTRING_INDEX(REVERSE(@visit), ':', 1)) AS level
                                            FROM
                                                (SELECT * FROM users) AS u,
                                                (SELECT @pv:='".$userid."', @n:=0, @visit:='".$userid.":0') as initialisation
                                            WHERE
                                                FIND_IN_SET(sponsor_id, @pv)
                                                    AND LENGTH(@pv:=CONCAT(@pv, ',', id))
                                                    AND LENGTH(@tem:=@visit)
                                                    AND LENGTH(@visit:=CONCAT(@tem,',',id,':',
                                                    SUBSTRING_INDEX(SUBSTRING(@tem,INSTR(@tem, sponsor_id) + LENGTH(sponsor_id)+ 1,LENGTH(@tem) - INSTR(@tem, sponsor_id) + 1),',',1) + 1))) as u
                                    LEFT JOIN (SELECT sum(amount) as total, orders.userid FROM orders WHERE orders.type = 'live' AND orders.created_at BETWEEN '".$start."' AND '".$end."' GROUP BY orders.userid) as o ON o.userid = u.id
                                    WHERE level <= ".$level."
                                    GROUP BY level
                                    ORDER BY level"));
      for($i = 0 ; $i < count($branch_volume) ; $i++){
        if($branch_volume[$i]->branch_volume != null && $branch_volume[$i]->f_active != null){
            $branch_volume[$i]->commission = $branch_volume[$i]->branch_volume*$branch_volume[$i]->f_active/100;
        } else {
            $branch_volume[$i]->commission = 0;
        }
      }
      return $branch_volume;
    }
}
