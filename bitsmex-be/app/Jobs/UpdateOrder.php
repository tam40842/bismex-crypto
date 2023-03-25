<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = DB::table('users')->where('id', $this->order->userid)->first();
        DB::table('orders')->where('id', $this->order->id)->update([
            'status' => $this->order->status,
            'close_price' => $this->order->close,
            'total_balance' => $user->{$this->order->type.'_balance'},
            'updated_at' => date(now())
        ]);
    }
}
