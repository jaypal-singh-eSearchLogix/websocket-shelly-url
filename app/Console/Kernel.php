<?php


namespace App\Console;

use App\Console\Commands\RunWebSocket;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    protected $commands = [
        RunWebSocket::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Define your scheduled tasks here
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
