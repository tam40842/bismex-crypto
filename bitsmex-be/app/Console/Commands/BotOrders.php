<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Cache;
use App\Http\Controllers\Vuta\Vuta;

class BotOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:orders';

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
        $botOrders = [];
        $time = Carbon::now();
        $action = array("BUY","SELL");
        for ($i=1; $i <= 50; $i++) {
            $amount = rand(1, 100);
            $botOrders[$i] = [
                'created_at' => $time->addSecond(rand(0,1))->format('H:i:s'),
                'orderid' => Vuta::random_code(),
                'action' => $action[array_rand($action)],
                'amount' => $amount,
                'status' => 0,
            ];
        }
        $botOrders = collect($botOrders)->sortBy('created_at')->all();
        $expiresAt = Carbon::now()->addMinutes(1);
        $listOrders = Cache::put('bot-orders', $botOrders);
    }
}
