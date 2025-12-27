<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoUpdateDeliveredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-update-delivered';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatisasi update status pesanan yang telah melewati estimasi waktu pengiriman';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses otomatisasi update status pesanan...');

        // Ambil pesanan dengan status 'shipped' yang sudah melewati estimasi waktu pengiriman
        $orders = Order::where('status', 'shipped')
            ->get();

        $updatedCount = 0;

        foreach ($orders as $order) {
            // Tentukan threshold berdasarkan jenis pengiriman (untuk pengujian, gunakan menit)
            $thresholdMinutes = 15; // default

            // Jika ada kolom shipping_method di order, gunakan untuk menentukan threshold
            if ($order->shipping_courier) {
                $shippingMethod = strtolower($order->shipping_courier);

                if (strpos($shippingMethod, 'same_day') !== false || strpos($shippingMethod, 'same day') !== false || strpos($shippingMethod, 'sameday') !== false) {
                    $thresholdMinutes = 2; // Same day: 2 menit threshold
                } elseif (strpos($shippingMethod, 'kilat') !== false || strpos($shippingMethod, 'express') !== false || strpos($shippingMethod, 'instant') !== false) {
                    $thresholdMinutes = 5; // Express/Kilat: 5 menit threshold
                } elseif (strpos($shippingMethod, 'reguler') !== false || strpos($shippingMethod, 'regular') !== false) {
                    $thresholdMinutes = 10; // Reguler: 10 menit threshold
                }
            }

            // Cari waktu ketika status pesanan pertama kali menjadi 'shipped' dari log
            $shippedLog = $order->logs()
                ->where('status', 'shipped')
                ->orderBy('created_at', 'asc')
                ->first();

            if ($shippedLog) {
                // Periksa apakah sudah melewati threshold sejak status menjadi shipped
                $shippedAt = $shippedLog->created_at;
                if ($shippedAt->addMinutes($thresholdMinutes)->lte(now())) {
                    // Update status ke delivered
                    $order->update(['status' => 'delivered']);

                    // Tambahkan log bahwa status telah diupdate otomatis
                    $order->logs()->create([
                        'status' => 'delivered',
                        'description' => "Status pesanan diubah otomatis menjadi diterima setelah melewati estimasi waktu pengiriman ({$order->shipping_courier}: {$thresholdMinutes} menit)",
                        'updated_by' => 'system',
                    ]);

                    $updatedCount++;
                    $this->info("Order {$order->order_number} diupdate ke status delivered ({$order->shipping_courier}, {$thresholdMinutes} menit, shipped on {$shippedAt->format('Y-m-d H:i:s')})");
                }
            } else {
                // Jika tidak ditemukan log 'shipped', gunakan created_at sebagai fallback
                $shippedAt = $order->created_at;
                if ($shippedAt->addMinutes($thresholdMinutes)->lte(now())) {
                    // Update status ke delivered
                    $order->update(['status' => 'delivered']);

                    // Tambahkan log bahwa status telah diupdate otomatis
                    $order->logs()->create([
                        'status' => 'delivered',
                        'description' => "Status pesanan diubah otomatis menjadi diterima setelah melewati estimasi waktu pengiriman (fallback, {$order->shipping_courier}: {$thresholdMinutes} menit)",
                        'updated_by' => 'system',
                    ]);

                    $updatedCount++;
                    $this->info("Order {$order->order_number} diupdate ke status delivered (fallback, {$order->shipping_courier}, {$thresholdMinutes} menit, created on {$shippedAt->format('Y-m-d H:i:s')})");
                }
            }
        }

        $this->info("Proses otomatisasi selesai. {$updatedCount} pesanan diupdate.");
        Log::info("Auto-update delivered orders completed. {$updatedCount} orders updated.");
    }
}
