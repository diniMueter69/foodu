<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Ping: zeigt, dass der Scheduler läuft
        $schedule->call(function () {
            \Log::info('Scheduler OK', ['ts' => now()->toDateTimeString()]);
        })->everyMinute()->name('scheduler-ping');

        // Preise aktualisieren (Command-Signature muss existieren)
        $schedule->command('app:prices-refresh')
            ->hourly()
            ->withoutOverlapping()
            ->name('prices-refresh-hourly');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
