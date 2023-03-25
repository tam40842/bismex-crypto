<?php
namespace App;
use Illuminate\Support\Facades\DB;

class TransactionHistory{

    public static function historyLiveBalance($userid, $type, $change, $walletType = 'live_balance', $message = '') {
        // Add transaction
        $user = DB::table('users')->where('id', $userid)->first();
        $currentVal = $user->{$walletType};
        $newVal = $currentVal + $change;
        DB::table('transactions')->insert([
            'userid' => $userid,
            'type' => $type,
            'wallet_type' => $walletType,
            'original' => $currentVal,
            'change' => $change,
            'balance' => $newVal,
            'message' => $message,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}