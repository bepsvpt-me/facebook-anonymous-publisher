<?php

namespace App\Console;

use Carbon\Carbon;
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
        Commands\Facebook\PostDailyTop::class,
        Commands\Facebook\SyncRanks::class,
        Commands\UpdateLang::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('facebook:sync-ranks')->everyMinute()->withoutOverlapping();

        $schedule->command('facebook:post-daily-top')->dailyAt('22:45')->skip(function () {
            return $this->isLateAtNight();
        })->withoutOverlapping();
    }

    /**
     * Check it is late at night or not.
     *
     * @return bool
     */
    protected function isLateAtNight()
    {
        return 3 >= Carbon::now()->diffInHours(Carbon::createFromTime(4), true);
    }
}
