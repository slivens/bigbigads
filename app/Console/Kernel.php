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
        \App\Console\Commands\SyncByUser::class,
        \App\Console\Commands\CheckUsage::class,
        \App\Console\Commands\CheckBookmark::class,
        \App\Console\Commands\GenerateInvoice::class,
        \App\Console\Commands\ChangeTag::class,
        \App\Console\Commands\PolicyCommand::class,
        \App\Console\Commands\SessionCommand::class,
        \App\Console\Commands\UpgradeCommand::class
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
        // 后续换成laravel 的crontab 任务
        // $schedule->command('bba:sync-subscriptions')
        //     ->saturdays()
        //     ->withoutOverlapping();
        // $schedule->command('bba:sync-payments')
        //     ->saturdays()
        //     ->withoutOverlapping();
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
