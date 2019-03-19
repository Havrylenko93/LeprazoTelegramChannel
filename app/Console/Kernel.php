<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule,
    Illuminate\Foundation\Console\Kernel as ConsoleKernel,
    App\Jobs\SyncMemasiki;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            dispatch(new SyncMemasiki());
        })->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
