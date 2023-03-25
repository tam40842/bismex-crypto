<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Franchise
 */
trait Franchise
{
    public $limit_level = 7;
    public $franchise_fee = 50;

    public function uplineFranchise($sponsor_id, $userid, $floor = 1) {
        if($floor <= $this->limit_level) {
            if($sponsor_id > 0) {
                $user = DB::table('users')->where('id', $sponsor_id)->first();
                if(!is_null($user)) {
                    $bonus_accept = false;
                    if($user->is_franchise) {
                        $bonus_accept = true;
                    }
                    if($bonus_accept) {
                        $bonus_amount = $this->franchise_fee * (1/pow(2,$floor));
                        TransactionHistory::historyLiveBalance($sponsor_id, 'ACTIVE FRANCHISE BONUS', $bonus_amount, 'primary_balance');
                        DB::table('users')->where('id', $sponsor_id)->increment('primary_balance', $bonus_amount);
                        DB::table('commissions')->insert([
                            'name' => 'IB Franchise',
                            'userid' => $sponsor_id,
                            'amount' => $bonus_amount,
                            'ref_id' => $userid,
                            'f_userid' => $userid,
                            'level' => $floor,
                            'volume' => $this->franchise_fee,
                            'message' => 'IB Franchise',
                            'commission_type' => 'ib_franchise',
                            'status' => 1,
                            'yearweek' => Carbon::now()->format("YW"),
                            'com_date' => Carbon::now()->format("Ymd")
                        ]);
                        $floor++;
                    }
                    $this->uplineFranchise($user->sponsor_id, $userid, $floor);
                }
            }
        }
    }
}