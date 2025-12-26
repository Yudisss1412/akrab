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
        // Jalankan command pembatalan otomatis setiap jam
        $schedule->command('orders:cancel-unpaid')
                 ->hourly()
                 ->withoutOverlapping();

        // Jalankan command otomatisasi update status pesanan setiap hari
        $schedule->command('orders:auto-update-delivered')
                 ->daily()
                 ->withoutOverlapping();

        // Jalankan command update status pesanan otomatis berdasarkan estimasi waktu pengiriman
        $schedule->command('order:update-status')
                 ->everyMinute() // Jalankan setiap menit untuk update status otomatis
                 ->withoutOverlapping();
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