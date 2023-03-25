<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;

class CopyTrading implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $expert;
    protected $orderid;

    public function __construct($expert, $orderid)
    {
        $this->expert = $expert;
        $this->orderid = $orderid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * kiểm tra đánh tiền live
         * truy vấn user đang đánh có trong supper trader hay không
         * tìm tất cả các user follow theo user supper này trong tình trạng status == 1
         * nếu tiền kiểm tra trong bản order_trader xem user follow này đặt min là bao nhiêu, max là bao nhiêu
         * + nếu lệnh đánh nằm trong khoảng min-max thì duyệt, ngược lại
         * + kiểm tra balance trade_order của user follow balance còn tiền hay không và đủ tiền để đánh lệnh theo supper trader ?
         * nếu thắng thì cộng tiền cho user follow nếu thua thì trừ tiền balance user follow đó
         * trả fee cho supper trader
         * cho lưu lại hết tất cả lệnh đánh của user supper trader và user follow đã active
         */
        
        $order = DB::table('orders')->where('orderid', $this->orderid)->where('type', 'live')->where('status', '!=', 0)->first();
        $user_expert = DB::table('copy_trader')->where('copy_trader.id', $this->expert)
                        ->LeftJoin('copy_order', 'copy_trader.userid', '=', 'copy_order.user_expert')
                        ->where('copy_trader.status', 1)->where('copy_order.status', 1)
                        ->select('copy_order.*', 'copy_trader.amount_min', 'copy_trader.fee', 'copy_trader.userid as user_expert')->get();

        if(!is_null($user_expert)) {
            foreach($user_expert as $value_copy) {
                $user_follow = DB::table('users')->where('id', $value_copy->user_follow)->where('status', 1)->first();
                if(!is_null($user_follow) && !is_null($order)) {
                    if($order->amount >= $value_copy->min_copy && $order->amount <= $value_copy->max_copy && $value_copy->balance > $order->amount) {
                        if($order->status == 1) {
                            $profit = $order->amount * $order->profit_percent / 100;
                            $fee_expert = $profit * $value_copy->fee / 100;
                            $total_copy = $profit - $fee_expert;
                            DB::table('copy_order')->where('user_expert', $value_copy->user_expert)->where('user_follow', $value_copy->user_follow)->increment('balance', $total_copy);
                            DB::table('users')->where('id', $order->userid)->increment('live_balance', $fee_expert);
                        }else if($order->status == 2) {
                            $fee_expert = 0;
                            $total_copy = 0;
                            DB::table('copy_order')->where('user_expert', $value_copy->user_expert)->where('user_follow', $value_copy->user_follow)->decrement('balance', $order->amount);
                        }
                        
                        DB::table('copy_profit_histories')->insert([
                            'user_expert' => $value_copy->user_expert,
                            'user_follow' => $value_copy->user_follow,
                            'orderid' => $order->orderid,
                            'status' => $order->status,
                            'fee_expert' => $fee_expert,
                            'total' => $total_copy,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
