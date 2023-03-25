<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\Vuta\VutaMail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $subject;
    protected $blade;
    protected $data;

    public function __construct($email, $subject, $blade, $data = [])
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->blade = $blade;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        VutaMail::send($this->email, $this->subject, $this->blade, $this->data);
    }
}
