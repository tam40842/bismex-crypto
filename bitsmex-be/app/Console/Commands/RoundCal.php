<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;
use App\User;
use App\Events\Result;
use App\Events\Adjust;
use App\Http\Controllers\Vuta\Vuta;
use Modules\Trading\Entities\Order;
use App\Jobs\CopyTrading;
use App\Jobs\UpdateOrder;
use App\TransactionHistory;

class RoundCal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'round:calculating';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now();
        $orders = DB::table('orders')->where('status', 0)->where('expired_at', '<=', $now)->get();
        $list = [];
        $user_event = [];
        $saveList = [];
        if(count($orders)) {
            $result = null;
            foreach($orders as $key => $value) {
                $candle_time = ($value->expired*60) - 30;
                $round_result = DB::table('tb_candle')->where('time', $value->round + $candle_time)->where('marketname', $value->market_name)->orderBy('id', 'desc')->first();
                if(!is_null($round_result)) {
                    if($round_result->close < $round_result->open) {
                        $result = 'SELL';
                    } else {
                        $result = 'BUY';
                    }
                    if(!is_null($result)) {
                        if($value->action == $result) {
                            $profit = $value->amount * $value->profit_percent / 100;
                            $total = $value->amount + $profit;
                            $balance_type = ($value->type == 'live') ? 'live_balance' : 'demo_balance';
                            if($balance_type == 'live_balance'){
                                TransactionHistory::historyLiveBalance($value->userid,'ROUND_CAL',$total);
                            }
                            DB::table('users')->where('id', $value->userid)->increment($balance_type, $total);
                            $order_status = 1;
                        } else {
                            $order_status = 2;
                            $profit = -$value->amount;
                            $total = 0;
                        }
                        // tri update ngày 02/01/2020
                        // set total cho từng user để biết user đó thắng hay thua
                        if (!isset($user_event[$value->userid]))
                            $user_event[$value->userid] = [
                                'total' => $total,
                                'round' => $value->round
                            ];
                        else
                            $user_event[$value->userid]['total']+= $total;

                        $user_event[$value->userid]['shapeid'][] = $value->shapeid;
                        // $expert_id = 0;
                        // $user = DB::table('users')->where('id', $value->userid)->first();
                        // DB::table('orders')->where('id', $value->id)->update([
                        //     'expert_id' => $expert_id,
                        //     'status' => $order_status,
                        //     'close_price' => $round_result->close,
                        //     'total_balance' => $user->{$value->type.'_balance'},
                        //     'updated_at' => date(now())
                        // ]);
                        $value->status = $order_status;
                        $value->close = $round_result->close;
                        $saveList[] = $value;
                    }
                } else{
                    $balance_type = ($value->type == 'live') ? 'live_balance' : 'demo_balance';
                    if($balance_type == 'live_balance'){
                        TransactionHistory::historyLiveBalance($value->userid,'REFUND_PLACED',$value->amount,$balance_type,'ORDERID: '.$value->orderid);
                    }
                    DB::table('users')->where('id', $value->userid)->increment($balance_type, $value->amount);
                    DB::table('orders')->where('id', $value->id)->update([
                        'status' => 3,
                        'updated_at' => date(now())
                    ]);
                }
            }
            // tri update ngày 02/01/2020
            // push socket cho user kết quả sau cùng thắng hay thua
            if (!empty($user_event))
            {
                foreach ($user_event as $userid => $row)
                {
                    event(new Result(json_encode([
                        'result' => [
                            'show' => true,
                            'status' => $row['total']>0?'WIN':'LOSE',
                            'total' => abs($row['total']),
                            'round' => $row['round'],
                            'shapeid' => $row['shapeid']
                        ]
                    ]), $userid));
                }
            }

            if (!empty($saveList))
            {
                foreach ($saveList as $order)
                {
                    UpdateOrder::dispatch($order);
                }
            }
        }
    }
}