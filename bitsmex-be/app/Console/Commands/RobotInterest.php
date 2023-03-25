<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use App\Jobs\SendEmail;

class RobotInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interest:robot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculation robot interest';

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
    private $list_otc = ['Binance', 'Bittrex', 'HitBTC', 'Huobi', 'Kraken', 'Bitfinex', 'Bitstamp', 'KuCoin', 'Poloniex', 'OKEx'];
    public function handle()
    {
        $yesterday = Carbon::now()->subDay();
        $orders = DB::table('robot_order')->where('updated_at', '<=', $yesterday)->where('status', 1)->get();
        foreach($orders as $key => $value) {
            DB::beginTransaction();
            try {
                $interest_amount = round($value->amount * $value->interest / 100, 2);
                DB::table('users')->where('id', $value->userid)->lockForUpdate()->increment('robot_profit_balance', $interest_amount);
                DB::table('robot_commission_histories')->lockForUpdate()->insert([
                    'userid' => $value->userid,
                    'robotid' => $value->orderid,
                    'amount' => $interest_amount,
                    'content' => 'AI BOT '.$value->orderid.' make a profit from '.$this->list_otc[array_rand($this->list_otc)],
                    'created_at' => date(now()),
                    'updated_at' => date(now()),
                ]);
                DB::table('robot_order')->where('id', $value->id)->lockForUpdate()->update(['updated_at' => date(now())]);
                $user = DB::table('users')->where('id', $value->userid)->where('status', 1)->first();
                DB::commit();
                SendEmail::dispatch($user->email, 'AI BOT Profit', 'interest', [
                    'user' => $user, 
                    'amount' => $interest_amount,
                    'created_at' => date(now()),
                    'robot_id' => $value->orderid
                ]);
            } catch (\QueryException $e) {
                DB::rollBack();
            }
        }
    }
}
