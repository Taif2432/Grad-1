<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Run every minute (or adjust to every five minutes, hourly, etc.)
        $schedule->command('sessions:complete-past')->everyMinute();
    }
}
