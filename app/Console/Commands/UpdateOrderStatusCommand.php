<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class UpdateOrderStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:update-status {--test : Hanya menampilkan pesanan yang akan diupdate tanpa benar-benar mengupdate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengupdate status pesanan otomatis berdasarkan estimasi waktu pengiriman';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isTest = $this->option('test');

        if ($isTest) {
            $this->info('Menjalankan dalam mode test - hanya menampilkan pesanan yang akan diupdate...');
        } else {
            $this->info('Memulai proses update status pesanan...');
        }

        // Ambil semua pesanan dengan status 'shipped'
        $orders = Order::where('status', 'shipped')->get();

        $updatedCount = 0;

        foreach ($orders as $order) {
            // Hitung batas waktu berdasarkan kurir
            $timeLimitInMinutes = $this->getDeliveryTimeLimit($order->shipping_courier);

            // Cari log terakhir dengan status 'shipped' untuk mendapatkan waktu pengiriman yang akurat
            $shippedLog = $order->logs()
                ->where('status', 'shipped')
                ->orderBy('created_at', 'desc')
                ->first();

            // Jika tidak ditemukan log dengan status 'shipped', coba cari berdasarkan deskripsi
            if (!$shippedLog) {
                $shippedLog = $order->logs()
                    ->where('description', 'like', '%dikirim%')
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            // Jika tetap tidak ditemukan log 'shipped', lewati pesanan ini
            if (!$shippedLog) {
                if ($isTest) {
                    $this->info("Order {$order->order_number} tidak memiliki log 'shipped', dilewati");
                }
                continue;
            }

            $shippedAt = $shippedLog->created_at;

            // Hitung waktu batas (waktu saat status berubah menjadi shipped + batas waktu)
            $deadline = $shippedAt->addMinutes($timeLimitInMinutes);

            if ($isTest) {
                $this->info("Order {$order->order_number}: kurir='{$order->shipping_courier}', waktu shipped='{$shippedAt}', deadline='{$deadline}', sekarang='".now()."', selisih='".(now()->diffInMinutes($shippedAt))." menit'");
            }

            // Jika waktu sekarang sudah melewati batas waktu
            if (now()->gte($deadline)) {
                if ($isTest) {
                    $this->info("Order {$order->order_number} akan diupdate menjadi delivered (waktu: {$deadline}, kurir: {$order->shipping_courier})");
                } else {
                    // Update status menjadi 'delivered'
                    $order->update([
                        'status' => 'delivered'
                    ]);

                    // Tambahkan log untuk perubahan status
                    $order->logs()->create([
                        'status' => 'delivered',
                        'description' => 'Status pesanan diubah otomatis menjadi diterima karena telah melewati batas waktu pengiriman',
                        'updated_by' => 'system',
                    ]);

                    $this->info("Order {$order->order_number} status diubah menjadi delivered");
                }
                $updatedCount++;
            }
        }

        if ($isTest) {
            $this->info("Proses test selesai. {$updatedCount} pesanan akan diupdate jika dijalankan tanpa mode test.");
        } else {
            $this->info("Proses update status pesanan selesai. {$updatedCount} pesanan diperbarui.");
        }
    }

    /**
     * Fungsi untuk menghitung batas waktu berdasarkan kurir
     */
    private function getDeliveryTimeLimit($courier) {
        $courier = strtolower($courier);

        // Cek apakah mengandung kata kunci tertentu
        if (strpos($courier, 'same day') !== false || strpos($courier, 'sameday') !== false) {
            return 2; // 2 menit untuk same day
        } elseif (strpos($courier, 'kilat') !== false || strpos($courier, 'express') !== false || strpos($courier, 'instant') !== false) {
            return 5; // 5 menit untuk express
        } elseif (strpos($courier, 'reguler') !== false || strpos($courier, 'regular') !== false) {
            return 10; // 10 menit untuk reguler
        } else {
            return 10; // Default ke 10 menit untuk reguler
        }
    }
}
