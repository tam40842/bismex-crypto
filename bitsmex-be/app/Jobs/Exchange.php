<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Exchange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $symbol;
    protected $amount;
    protected $is_swap;

    public function __construct($symbol, $amount, $is_swap = false)
    {
        $this->symbol = strtoupper($symbol);
        $this->amount = $amount;
        $this->is_swap = $is_swap;
    }

    /**
     * Execute the job.
     * is_swap if = false so exchange symbol to USDT
     * is_swap if = true so exchange USDT to symbol
     * @return void
     */
    public function handle()
    {
        if(!$this->is_swap) {
            $currency = "App\\Currencies\\".$this->symbol;
            $currency = new $currency;
            $exchange = $currency->exchange($this->amount);
        } else {
            # nếu swap = 1
            $currency = "App\\Currencies\\VNDC";
            $currency = new $currency;
            $convert_amount = $this->amount * $currency->rate_vnd();
            $exchange = $currency->exchange($this->symbol, $convert_amount);
        }

        if($exchange['status']) {
            // true and return
        }
        // false
    }
}
