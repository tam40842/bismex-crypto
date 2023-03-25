<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class AutoTradeDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autotrade:day';

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
        $commissions = DB::table('commissions')
            ->where('commission_type', 'autotrade_com')
            ->where('status', 2)
            ->get();

        foreach ($commissions as $commission) {
            DB::beginTransaction();
            try {
                $package = DB::table('autotrade_package')->where('package_id', $commission->autotrade_id)->where('status', 1)->first();
                if (is_null($package)) {
                    continue;
                }
                DB::table('autotrade_package')->where('package_id', $commission->autotrade_id)->where('status', 1)->update([
                    'received' => $package->received + $commission->amount
                ]);
                DB::table('commissions')
                    ->where('id', $commission->id)
                    ->where('status', 2)
                    ->update([
                        'status' => 1
                    ]);
                DB::commit();
            } catch (QueryException $ex) {
                DB::rollBack();
            }
        }
    }
}
