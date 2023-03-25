<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Vuta\Vuta;
use App\Http\Controllers\Vuta\CryptoMap;
use DB;
use Exception;
// use App\Events\Rate as RateEvent;

class Rate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rate:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currencies rate';

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
        $currencies = DB::table('currencies')->where('actived', 1)->get();
        $data = [];
        foreach($currencies as $key => $value) {
            try {
                $get_rate = CryptoMap::currencyRate($value->symbol);
                $percent_change = CryptoMap::percent_change($value->symbol);
                DB::table('currencies')->where('id', $value->id)->update([
                    'usd_rate' => $get_rate,
                    'price_change_percent' => round($percent_change, 2)
                ]);
                $data[$value->symbol] = [
                    'sellPrice' => $value->sellPrice,
                    'buyPrice' => $value->buyPrice,
                    'usd_rate' => $get_rate,
                    'price_change_percent' => round($percent_change, 2)
                ];
            } catch(\Exception $e) {

            }
        }
        
        // event(new RateEvent(json_encode($data)));
    }
}
