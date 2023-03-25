<?php

namespace Modules\TelegramBot\Entities;
use App\User;
use DB;

class Telegram {

    public static function handle($userid, $type, $data) {
        $user = DB::table('users')->where('id', $userid)->first();
        $message = '';
        switch($type) {
            case 'wallet_transfer':
                $message = '🎯 <strong>'.$user->username.'</strong> vừa chuyển <b>$'.$data['amount'].'</b> từ <b>'.$data['from_balance_type'].'</b> đến <b>'.$data['to_balance_type'].'</b>.'.chr(10).chr(10);
            break;
            case 'transfer':
                $recipient = DB::table('users')->where('id', $data['recipient_id'])->lockForUpdate()->first();
                $message = '<strong>'.$user->username.'</strong> vừa chuyển <b>$'.$data['amount'].'</b> đến <b>'.$recipient->username.'</b>'.chr(10).chr(10);
            break;
            case 'withdraw':
                $message = '<strong>'.$user->username.'</strong> vừa tạo lệnh rút <b>$'.$data['amount'].'</b> đến địa chỉ ví <b>'.$data['address'].'</b>'.chr(10).chr(10);
            break;
            case 'deposit':
                $message = '<strong>'.$user->username.'</strong> vừa nạp thành công <b>$'.$data['amount'].'</b>'.chr(10).chr(10);
            break;
            case 'danger_level_1':
                if(isset($data['recipient_id'])) {
                    $recipient = DB::table('users')->where('id', $data['recipient_id'])->lockForUpdate()->first();
                    $message = '<code>Cảnh báo cấp độ 1: <strong>'.$user->username.'</strong> chuyển <b>$'.$data['amount'].'</b> đến <b>'.$recipient->username.'</b></code>'.chr(10).chr(10);
                } else {
                    $message = '<code>Cảnh báo cấp độ 1: <strong>'.$user->username.'</strong> vừa chuyển <b>$'.$data['amount'].'</b> từ <b>'.$data['from_balance_type'].'</b> đến <b>'.$data['to_balance_type'].'</b></code>'.chr(10).chr(10);
                }
            break;
            case 'danger_level_2':
                $message = '<code>Cảnh báo cấp độ 2: <strong>'.$user->username.'</strong> đang thao tác với tốc độ ánh sáng. Hệ thống đã tạm khóa tài khoản và gửi Email thông báo.</code>'.chr(10).chr(10);
            break;
            case 'danger_level_3':
                $message = '<code>Cảnh báo cấp độ 3: <strong>'.$user->username.'</strong> đang có nhiều nghi vấn với balance. Hệ thống đã tạm khóa tài khoản và gửi Email thông báo.</code>'.chr(10).chr(10);
            break;
            case 'trade':
                return false;
            break;
            case 'robot_order':
                return false;
            break;
        }
        $message .= '👉 Số dư trước:'.chr(10);
        $message .= 'Live Balance: $'.round($data['user']->primary_balance, 2).chr(10);
        $message .= '👉 Số dư sau:'.chr(10);
        $message .= 'Live Balance: $'.round($user->primary_balance, 2).chr(10);
        $telegram_token = '2012264344:AAHDiYBLcEkRnNUdO-mvDoM39XjdwuYgK4k';
        $group_id = '-1001195427976';
        file_get_contents('https://api.telegram.org/bot'.$telegram_token.'/sendMessage?chat_id='.$group_id.'&text='.urlencode($message).'&parse_mode=html');
    }
}