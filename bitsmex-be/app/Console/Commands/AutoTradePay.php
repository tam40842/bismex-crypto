<?php

namespace App\Console\Commands;

use App\TransactionHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class AutoTradePay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autotrade:pay';

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
        $now = Carbon::now();
        $auto_packages = DB::table('autotrade_package')->where('status', 1)->where('end_date', '>=', $now)->get();

        foreach ($auto_packages as $package) {
            DB::beginTransaction();
            try {
                DB::table('autotrade_package')->where('package_id',$package->package_id)->where('status', 1)->update([
                    'status' => 2
                ]);
                TransactionHistory::historyLiveBalance($package->userid, 'AUTOTRADE_COM', $package->received, 'autotrade_balance');
                DB::table('users')->where('id', $package->userid)->where('status', 1)->increment('autotrade_balance', $package->received);
                DB::commit();
            } catch (QueryException $ex) {
                DB::rollBack();
            }
        }
    }
}
