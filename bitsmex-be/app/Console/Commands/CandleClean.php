<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;

class CandleClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'candle:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean candles everyday';

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
        DB::table('tb_candle')->whereBetween('created_at', [Carbon::now()->subDay(), Carbon::now()->subMinute(60)])->delete();
    }
}
