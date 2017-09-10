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
        \App\Console\Commands\SyncPlans::class,
        \App\Console\Commands\SyncSubscriptions::class,
        \App\Console\Commands\SyncPayments::class,
        \App\Console\Commands\Refund::class,
        \App\Console\Commands\CancelSubscription::class,
        \App\Console\Commands\SyncIcreatife::class,
        \App\Console\Commands\ScanUsers::class,
        \App\Console\Commands\SyncByUser::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('bba:scan-users')
            ->daily()
            ->withoutOverlapping();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
