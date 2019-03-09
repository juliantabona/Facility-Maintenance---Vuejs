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
        'App\Console\Commands\SendRecurringInvoices',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * Use this link to learn how to do the scheduler using Windows Scheduler for localhost
     * Link: https://quantizd.com/how-to-use-laravel-task-scheduler-on-windows-10/
     */
    protected function schedule(Schedule $schedule)
    {
        //  Send any due recurring invoices - Run this every 30 minutes
        $schedule->command('send:recurringInvoices')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
