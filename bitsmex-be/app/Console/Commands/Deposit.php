<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Modules\TelegramBot\Entities\Telegram;
use App\User;
use App\Jobs\SendEmail;
use Carbon\Carbon;

class Deposit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deposit:run';

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
     * @return int
     */
    public function handle()
    {
        $node_jobs = DB::table('waiting_deposit')->where('is_deposit', 0)->get();
        foreach($node_jobs as $key => $value) {
            $check_exist = DB::table('deposit')->where('txhash', $value->txhash)->first();
            if(is_null($check_exist)) {
                $wallet = DB::table('wallet_address')->where('symbol', $value->symbol)->where('input_address', $value->address)->first();
                if(is_null($wallet)) {
                    return false;
                }
                $userid = $wallet->userid;
                $user = User::find($userid);
                $symbol = strtoupper($value->symbol);
                // $coin = "App\\Currencies\\".$symbol;
                // $coin = new $coin;
                // $rate = $coin->rate();
                // $total = round($value->amount * $rate, 2);
                // DB::table('deposit')->insert([
                //     'deposit_id' => strtoupper(uniqid('D')),
                //     'action' => 'DEPOSIT',
                //     'userid' => $userid,
                //     'symbol' => $symbol,
                //     'amount' => $value->amount,
                //     'rate' => $rate,
                //     'total' => $total,
                //     'status' => 1,
                //     'txhash' => $value->txhash,
                //     'type' => 'deposit',
                //     'created_at' => date(now()),
                //     'updated_at' => date(now())
                // ]);
                DB::table('waiting_deposit')->where('id', $value->id)->update(['is_deposit' => 1]);
                DB::table('user_balance')->where('userid', $userid)->increment($symbol, $value->amount);
                // $this->sponsorBonus($user->sponsor_id, $total);
                Telegram::handle($userid, 'deposit', [
                    'user' => $user,
                    'amount' => $value->amount,
                    'symbol' => $symbol,
                ]);
                SendEmail::dispatch($user->email, 'Your account has just been credited', 'deposit', ['user' => $user, 'amount' => $value->amount, 'symbol' => $symbol]);
            }
        }
    }

    private $bonus_percent = 5;

    public function sponsorBonus($sponsor_id, $amount) {
        if($sponsor_id > 0) {
            $user = User::find($sponsor_id);
            $bonus_amount = $amount * $this->bonus_percent / 100;
            $user->bonus_balance += $bonus_amount;
            $user->save();
            DB::table('commissions')->insert([
                'name' => 'Deposit bonus',
                'userid' => $sponsor_id,
                'amount' => $bonus_amount,
                'message' => 'Deposit bonus',
                'commission_type' => 'deposit', 
                'status' => 1,
                'yearweek' => Carbon::now()->format("YW")
            ]);
        }        
    }
}
