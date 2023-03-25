<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Exception;

class ForwardToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forward:token';

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
        $deposit = DB::table('deposit')->where('is_forward', 0)->where('author', 0)->orderBy('total', 'desc')->first();
        if(!is_null($deposit)) {
            try {
                $forward = json_decode(file_get_contents('https://bep20.bitsmex.net/api/v1/forward/'.($deposit->stt)), true);
                if(isset($forward['hash']) || (isset($forward['msg']) && $forward['msg'] == "Amount too small.")) {
                    DB::table('deposit')->where('address', $deposit->address)->where('is_forward', 0)->update([
                        'is_forward' => 1
                    ]);
                }
            } catch(Exception $e) {
                return false;
            }
        }
    }
}
