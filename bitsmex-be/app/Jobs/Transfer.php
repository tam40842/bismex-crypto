<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\User;
use DB;

class Transfer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $transfer_id;

    public function __construct($transfer_id)
    {
        $this->transfer_id = $transfer_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transfer = DB::table('transfers')->where('transfer_id', $this->transfer_id)->where('status', 0)->first();
        if(is_null($transfer)) {
            return false;
        }
        $user = User::find($transfer->userid);
        if($transfer->amount > $user->live_balance) {
            DB::table('transfers')->where('transfer_id', $this->transfer_id)->update([
                'status' => 2
            ]);
            return false;
        }
        $user->live_balance -= $transfer->amount;
        $user->save();
        DB::table('users')->where('id', $transfer->recipient_id)->increment('live_balance', $transfer->amount);
        DB::table('transfers')->where('transfer_id', $this->transfer_id)->update([
            'status' => 1
        ]);
    }
}
