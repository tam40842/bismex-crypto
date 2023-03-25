<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Market extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto On/Off Forex Market';

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
        $this->info('Update market status.');
        if(in_array(Carbon::now()->dayOfWeek, [0, 6])) { 
            // từ 00:00 thử 7 và chủ nhật
            DB::table('markets')->where('type', 'FOREX')->update(['actived' => 0]);
            $this->info('Inactived FOREX market');
        }
        if(Carbon::now()->dayOfWeek == 1 && Carbon::now()->hour == 11) { 
            // từ 11:00 ngày thứ 2
            DB::table('markets')->where('type', 'FOREX')->update(['actived' => 1]);
            $this->info('Actived FOREX market');
        }
        DB::table('markets')->update(['result' => Null]);
    }
}
