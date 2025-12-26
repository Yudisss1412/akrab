<?php
// File untuk cron job: temp_update_order_status.php

require_once __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Set up Laravel environment
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;

// Fungsi untuk menghitung batas waktu berdasarkan kurir
function getDeliveryTimeLimit($courier) {
    $courier = strtolower($courier);

    switch ($courier) {
        case 'reguler':
        case 'regular':
            return 10; // 10 menit
        case 'kilat':
        case 'express':
        case 'instant':
            return 5; // 5 menit
        case 'same day':
        case 'sameday':
            return 2; // 2 menit
        default:
            return 10; // Default ke 10 menit untuk reguler
    }
}

// Ambil semua pesanan dengan status 'shipped'
$orders = Order::where('status', 'shipped')->get();

foreach ($orders as $order) {
    // Hitung batas waktu berdasarkan kurir
    $timeLimitInMinutes = getDeliveryTimeLimit($order->shipping_courier);

    // Hitung waktu batas (waktu pembuatan pesanan + batas waktu)
    // Kita gunakan created_at sebagai waktu awal pengiriman jika tidak ada informasi waktu pengiriman
    $shippedAt = $order->created_at; // Gunakan created_at sebagai waktu awal
    $deadline = $shippedAt->addMinutes($timeLimitInMinutes);

    // Jika waktu sekarang sudah melewati batas waktu
    if (now()->gte($deadline)) {
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

        echo "Order {$order->order_number} status diubah menjadi delivered\n";
    }
}

echo "Proses pengecekan status pesanan selesai.\n";