<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('round:calculating')->cron('* * * * *');
        $schedule->command('forward:token')->cron('* * * * *');
        $schedule->command('bot:orders')->cron('* * * * *');
        $schedule->command('market:update')->cron('* * * * *');
        $schedule->command('rate:update')->cron('* * * * *');
        $schedule->command('commission:interval')->everyFiveMinutes();
        $schedule->command('commission:statistics')->everyFiveMinutes();
        $schedule->command('commission:level')->daily()->at('00:00'); // lúc 00:00 hàng ngày
        $schedule->command('candle:clean')->hourly();
        // $schedule->command('candle:clean')->daily()->at('00:00');
        $schedule->command('backup:run')->daily()->at('00:00');
        $schedule->command('autotrade:over')->cron('* * * * *');
        $schedule->command('autotrade:pay')->monthly();
        $schedule->command('autotrade:day')->daily()->at('00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
