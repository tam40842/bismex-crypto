<?php

namespace App\Console\Commands;

use App\TransactionHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class AutoTradeOverTime extends Command
{
    protected $packageFee = 1000;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autotrade:over';

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
        $endOfMonth = Carbon::now()->endOfMonth();
        $auto_packages = DB::table('autotrade_package')->where('status', 1)->where('borrow_amount', '>', 0)->where(function ($query) use ($now, $endOfMonth) {
            return $query->orWhere('borrow_overtime', '<=', $now)->orWhere('borrow_overtime', '>=', $endOfMonth);
        })->get();

        foreach ($auto_packages as $package) {
            DB::beginTransaction();
            try {
                DB::table('autotrade_package')->where('package_id', $package->package_id)->where('status', 1)->update([
                    'status' => 0
                ]);
                DB::table('commissions')
                    ->where('autotrade_id', '=', $package->package_id)
                    ->where('commission_type', 'autotrade_com')->delete();
                TransactionHistory::historyLiveBalance($package->userid, 'AUTOTRADE_CANCEL', $this->packageFee - $package->borrow_amount, 'autotrade_balance');
                DB::table('users')->where('id', $package->userid)->where('status', 1)->increment('autotrade_balance', $this->packageFee - $package->borrow_amount);
                DB::commit();
            } catch (QueryException $ex) {
                DB::rollBack();
            }
        }
    }
}
