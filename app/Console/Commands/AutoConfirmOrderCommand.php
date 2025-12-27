<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class AutoConfirmOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:auto-confirm {--test : Hanya menampilkan pesanan yang akan diupdate tanpa benar-benar mengupdate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengkonfirmasi otomatis pesanan yang belum dilaporkan sebagai belum diterima setelah 15 menit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isTest = $this->option('test');

        if ($isTest) {
            $this->info('Menjalankan dalam mode test - hanya menampilkan pesanan yang akan diupdate...');
        } else {
            $this->info('Memulai proses konfirmasi otomatis pesanan...');
        }

        // Ambil semua pesanan dengan status 'delivered'
        $orders = Order::where('status', 'delivered')->get();

        $updatedCount = 0;

        foreach ($orders as $order) {
            // Kita hanya perlu memeriksa pesanan yang statusnya sudah 'delivered'
            // Cari log terakhir dengan status 'delivered' untuk mendapatkan waktu konfirmasi otomatis
            $deliveredLog = $order->logs()
                ->where('status', 'delivered')
                ->where('description', 'Status pesanan diubah otomatis menjadi diterima karena telah melewati batas waktu pengiriman') // Ini dari command update-status
                ->orderBy('created_at', 'desc')
                ->first();

            // Jika tidak ditemukan log dengan status 'delivered', coba cari berdasarkan deskripsi
            if (!$deliveredLog) {
                $deliveredLog = $order->logs()
                    ->where('description', 'Status pesanan diubah otomatis menjadi diterima karena telah melewati batas waktu pengiriman')
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            // Jika ditemukan log 'delivered' dari update-status otomatis
            if ($deliveredLog) {
                // Hitung waktu batas (waktu saat status berubah menjadi delivered + 15 menit)
                $deadline = $deliveredLog->created_at->addMinutes(15);

                // Jika waktu sekarang sudah melewati batas waktu 15 menit
                if (now()->gte($deadline)) {
                    // Cek apakah pesanan ini pernah dilaporkan sebagai belum diterima setelah status menjadi delivered
                    $hasReport = $order->logs()
                        ->where('description', 'Status pesanan dikembalikan ke shipped karena pelapor mengatakan belum menerima barang')
                        ->where('created_at', '>', $deliveredLog->created_at) // Hanya laporan setelah status delivered
                        ->exists();

                    // Jika tidak ada laporan bahwa barang belum diterima setelah status menjadi delivered, maka konfirmasi otomatis
                    if (!$hasReport) {
                        if ($isTest) {
                            $this->info("Order {$order->order_number} akan dikonfirmasi otomatis setelah 15 menit dari status delivered (waktu: {$deadline})");
                        } else {
                            // Tambahkan log bahwa pesanan dikonfirmasi otomatis
                            $order->logs()->create([
                                'status' => 'delivered',
                                'description' => 'Status pesanan dikonfirmasi otomatis setelah 15 menit tanpa laporan bahwa barang belum diterima',
                                'updated_by' => 'system',
                            ]);

                            $this->info("Order {$order->order_number} dikonfirmasi otomatis setelah 15 menit dari status delivered");
                        }
                        $updatedCount++;
                    } else {
                        if ($isTest) {
                            $this->info("Order {$order->order_number} tidak akan dikonfirmasi karena pernah dilaporkan belum diterima setelah status delivered");
                        }
                    }
                }
            }
        }

        if ($isTest) {
            $this->info("Proses test selesai. {$updatedCount} pesanan akan dikonfirmasi jika dijalankan tanpa mode test.");
        } else {
            $this->info("Proses konfirmasi otomatis pesanan selesai. {$updatedCount} pesanan dikonfirmasi.");
        }
    }
}
