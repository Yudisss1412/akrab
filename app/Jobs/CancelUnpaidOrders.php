<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CancelUnpaidOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting automatic cancellation of unpaid orders');

        // Temukan pesanan yang statusnya waiting_payment_verification lebih dari 24 jam
        $timeLimit = Carbon::now()->subHours(24); // Batas waktu 24 jam
        $ordersToCancel = Order::where('status', 'waiting_payment_verification')
                              ->where('created_at', '<', $timeLimit)
                              ->get();

        foreach ($ordersToCancel as $order) {
            // Update status pesanan menjadi 'cancelled'
            $order->update([
                'status' => 'cancelled',
            ]);

            // Tambahkan log untuk mencatat pembatalan
            $order->logs()->create([
                'status' => 'cancelled',
                'description' => 'Pesanan dibatalkan otomatis karena pembayaran tidak dilakukan dalam 24 jam'
            ]);

            Log::info("Order {$order->order_number} has been cancelled automatically due to payment timeout");
        }

        Log::info('Automatic cancellation of unpaid orders completed');
    }
}
