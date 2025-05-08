<?php

namespace App\Console;

use App\Console\Commands\SyncFurs;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, string>
     */
    protected $commands = [
        SyncFurs::class,
    ];

    /**
     * Define the application's command scheduling.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(SyncFurs::class)
            ->daily()
            ->at('00:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
