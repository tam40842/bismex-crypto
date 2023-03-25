<?php

namespace App\Console\Commands;

use App\TransactionHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class CommissionPay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commission:pay {start_date?}';

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
        // Get time input
        $this->info('Start calculation commission level.');
        $monthDay = Carbon::now()->subDay()->format("Ymd");
        if (!is_null($this->argument('start_date'))) {
            $monthDay = Carbon::parse($this->argument('start_date'))->format("Ymd");
        }

        // Get commission
        $headersCommissions = ['username', 'commission_type', 'amount', 'date'];
        $resultCommissions = [];
        $users = DB::select(DB::raw("SELECT 
                                        u.id, u.username, t.trade, t.trade_bonus
                                    FROM
                                        users AS u
                                            INNER JOIN
                                        (SELECT 
                                            c.userid,
                                                IFNULL(SUM(CASE
                                                    WHEN c.commission_type = 'trade' THEN amount
                                                    ELSE 0
                                                END), 0) AS trade,
                                                IFNULL(SUM(CASE
                                                    WHEN c.commission_type = 'trade_bonus' THEN amount
                                                    ELSE 0
                                                END), 0) AS trade_bonus
                                        FROM
                                            commissions AS c
                                        WHERE
                                            c.com_date = '".$monthDay."' AND c.status = 0
                                        GROUP BY c.userid) AS t ON t.userid = u.id"));

        $bar = $this->output->createProgressBar(count($users));
        foreach ($users as $user) {
            $bar->advance();
            $resultCommissions = array_merge($resultCommissions, $this->processCommission($user, $monthDay));
        }

        $bar->finish();
        $this->info('');
        $this->info('<]==[ LIST COMMISSION ]==[>');
        $this->table($headersCommissions, $resultCommissions);
        $this->info('The command was successful!');
    }

    public function processCommission($user, $monthDay){
        $resultCommissions = [];
        if($user->trade > 0){
            $resultCommissions = array_merge($resultCommissions,$this->processTradeCommission($user, $monthDay));
        }

        if($user->trade_bonus > 0){
            $resultCommissions = array_merge($resultCommissions,$this->processTradeBonus($user, $monthDay));
        }

        return $resultCommissions;
    }

    public function processTradeCommission($user, $monthDay){
        $result = [];
        $trade = round($user->trade, 2);
        $paid = DB::table('transactions')
                ->where('type','TRADE BONUS')
                ->where('userid',$user->id)
                ->where('message',$monthDay)
                ->sum('change');

        $hasPay = round($trade - $paid, 2);
        if ($hasPay <= 0) {
            return $result;
        }

        // Insert bonus
        DB::beginTransaction();
        try {
            TransactionHistory::historyLiveBalance($user->id, 'TRADE BONUS', $hasPay, 'primary_balance',$monthDay);
            DB::table('users')->where('id', $user->id)->increment('primary_balance', $hasPay);
            DB::table('commissions')
            ->where('com_date', $monthDay)
            ->where('userid', $user->id)
            ->where('commission_type', 'trade')
            ->update([
                'status' => 1
            ]);
            DB::commit();
            $result[] = [$user->username, 'TRADE BONUS', $hasPay, $monthDay];
            return $result;
        } catch (QueryException $e) {
            DB::rollback();
        }
        return $result;
    }

    public function processTradeBonus($user, $monthDay){
        $result = [];
        $trade_bonus = round($user->trade_bonus, 2);
        $paid = DB::table('transactions')
                ->where('type','IB BONUS')
                ->where('userid',$user->id)
                ->where('message',$monthDay)
                ->sum('change');

        $hasPay = round($trade_bonus - $paid, 2);
        if ($hasPay <= 0) {
            return $result;
        }

        // Insert bonus
        DB::beginTransaction();
        try {
            TransactionHistory::historyLiveBalance($user->id, 'IB BONUS', $hasPay, 'primary_balance',$monthDay);
            DB::table('users')->where('id', $user->id)->increment('primary_balance', $hasPay);
            DB::table('commissions')
            ->where('com_date', $monthDay)
            ->where('userid', $user->id)
            ->where('commission_type', 'trade_bonus')
            ->update([
                'status' => 1
            ]);
            DB::commit();
            $result[] = [$user->username, 'IB BONUS', $hasPay, $monthDay];
            return $result;
        } catch (QueryException $e) {
            DB::rollback();
        }
        return $result;
    }
}
