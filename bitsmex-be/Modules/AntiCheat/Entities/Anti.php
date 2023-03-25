<?php

namespace Modules\AntiCheat\Entities;

use DB;
use Carbon\Carbon;
use Modules\TelegramBot\Entities\Telegram;
use App\Jobs\SendEmail;
use Session;

class Anti {
    public static function firewall($userid, $type, $data) {
        $user = DB::table('users')->lockForUpdate()->where('id', $userid)->first();
        // Thông báo biến động tài chính
        if($type != 'trade') {
            Telegram::handle($userid, $type, $data);
        }

        // Cảnh báo mức độ 1
        if($data['amount'] > 5000 && ($type == 'transfer' || $type == 'wallet_transfer')) {
            Telegram::handle($userid, 'danger_level_1', $data);
            // DB::table('users')->where('id', $userid)->update(['status' => 2]);
            // SendEmail::dispatch($user->email, 'Detect doubts and temporarily lock account', 'danger', ['user' => $user]);
        }

        // Cảnh báo mức độ 3
        if($user->wallet_balance < 0 || $user->live_balance < 0 || $user->robot_profit_balance < 0 || $user->robot_bonus_balance < 0) {
            Telegram::handle($userid, 'danger_level_3', $data);
            DB::table('users')->lockForUpdate()->where('id', $userid)->update(['status' => 2]);
            SendEmail::dispatch($user->email, 'Detect doubts and temporarily lock account', 'danger', ['user' => $user]);
            if(isset($data['recipient_id'])) {
                $recipient = DB::table('users')->where('id', $data['recipient_id'])->first();
                if($recipient->wallet_balance < 0 || $recipient->live_balance < 0 || $recipient->bonus_balance < 0 || $recipient->robot_profit_balance < 0 || $recipient->robot_bonus_balance < 0) {
                    Telegram::handle($data['recipient_id'], 'danger_level_3', $data);
                    DB::table('users')->lockForUpdate()->where('id', $data['recipient_id'])->update(['status' => 2]);
                    SendEmail::dispatch($recipient->email, 'Detect doubts and temporarily lock account', 'danger', ['user' => $recipient]);
                }
            }
        }
        // if($type != 'trade') {
        //     // Cảnh báo mức độ 2
        //     switch($type) {
        //         case 'wallet_transfer':
        //             $last_request = DB::table('wallet_transfers')->lockForUpdate()->where('userid', $userid)->orderBy('id', 'desc')->value('created_at');
        //         break;
        //         case 'transfer':
        //             $last_request = DB::table('transfers')->lockForUpdate()->where('userid', $userid)->orderBy('id', 'desc')->value('created_at');
        //         break;
        //         case 'withdraw':
        //             $last_request = DB::table('withdraw')->lockForUpdate()->where('userid', $userid)->orderBy('id', 'desc')->value('created_at');
        //         break;
        //         case 'robot_order':
        //             $last_request = DB::table('robot_order')->lockForUpdate()->where('userid', $userid)->orderBy('id', 'desc')->value('created_at');
        //         break;
        //     }
            
        //     $last_request = strtotime($last_request);
        //     $now = strtotime(now());
        //     if($now - $last_request <= 3) {
        //         $data['second'] = $now - $last_request;
        //         Telegram::handle($userid, 'danger_level_2', $data);
        //         DB::table('users')->where('id', $userid)->update(['status' => 2]);
        //         SendEmail::dispatch($user->email, 'Detect doubts and temporarily lock account', 'danger', ['user' => $user]);
        //     }
        //     Session::put('last_request', $data['created_at']);
        // }
    }
}