<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;

class TradeBonus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commission:trade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trade commission calculation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public $bonus_percent = 1;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public $commission = [1.1, 0.5, 0.25, 0.15];
    public $target = [0, 1000, 2000, 4000];

    public function handle() {
        
    }
}
