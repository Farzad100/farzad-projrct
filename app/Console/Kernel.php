<?php

namespace App\Console;

use App\Http\Controllers\Cdns\CdnsController;
use App\Http\Controllers\GhestController;
use App\Http\Controllers\QueueController;
use App\Models\Chunk;
use App\Models\Ghest;
use Illuminate\Console\Scheduling\Schedule; 
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
    ];

    protected function schedule(Schedule $schedule)
    {
        if (config('app.env') != 'production') return 1;
        
        $schedule->call(function () { 
            QueueController::orders_inquiry();
        })->everyMinute()->between('07:10', '20:50');

        $schedule->call(function () {
            //Send Warning To Users For Tomorrow Payments
            QueueController::reminder_tomorrow();
        })->everyMinute()->between('07:30', '08:30');

        $schedule->call(function () {
            //Send Warning To Users For Near Payments
            QueueController::reminder_near();
        })->everyMinute()->between('08:31', '09:30');

        $schedule->call(function () {
            Chunk::cleanup_expired_chunks();
        })->everyFourHours();

        $schedule->call(function () {
            Ghest::recheck_orders();
        })->everyTwoHours()->between('10:00', '20:00');

        $schedule->call(function () {
            CdnsController::clean_cdn_temps();
        })->dailyAt('02:02');

        $schedule->call(function () {
            QueueController::orders_cancel_submitted_after(7);
            // QueueController::orders_cancel_upload_secondary_after(7);
            GhestController::auto_red_badge_1month_delay();
        })->dailyAt('06:00');

        $schedule->call(function () { 
            QueueController::ghests_auto_pass_cheques();
            Ghest::remove_residual_ghests();
        })->dailyAt('06:30');
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
