<?php
// File untuk mengecek data pesanan: temp_check_order.php

require_once __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Set up Laravel environment
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;

// Cari pesanan dengan nomor tertentu
$orderNumber = 'ORD-20251227-85252';
$order = Order::where('order_number', $orderNumber)->first();

if (!$order) {
    echo "Pesanan {$orderNumber} tidak ditemukan.\n";
    exit;
}

echo "=== Informasi Pesanan {$orderNumber} ===\n";
echo "Status: {$order->status}\n";
echo "Kurir: {$order->shipping_courier}\n";
echo "Dibuat: {$order->created_at}\n";
echo "Diupdate: {$order->updated_at}\n";

echo "\n=== Log Status Pesanan ===\n";
foreach ($order->logs as $log) {
    echo "Status: {$log->status}, Deskripsi: {$log->description}, Waktu: {$log->created_at}\n";
}

// Cek log dengan status 'shipped'
$shippedLogs = $order->logs()->where('status', 'shipped')->get();
echo "\n=== Log dengan status 'shipped' ===\n";
if ($shippedLogs->count() > 0) {
    foreach ($shippedLogs as $log) {
        echo "Waktu shipped: {$log->created_at}\n";
    }
} else {
    echo "Tidak ditemukan log dengan status 'shipped'\n";
}

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

if ($shippedLogs->count() > 0) {
    $latestShippedLog = $shippedLogs->sortByDesc('created_at')->first();
    $timeLimitInMinutes = getDeliveryTimeLimit($order->shipping_courier);
    $deadline = $latestShippedLog->created_at->addMinutes($timeLimitInMinutes);
    $now = now();
    
    echo "\n=== Perhitungan Waktu ===\n";
    echo "Waktu shipped terakhir: {$latestShippedLog->created_at}\n";
    echo "Batas waktu: {$timeLimitInMinutes} menit\n";
    echo "Deadline: {$deadline}\n";
    echo "Waktu sekarang: {$now}\n";
    echo "Selisih: {$now->diffInMinutes($deadline)} menit\n";
    echo "Apakah deadline terlewati: " . ($now->gte($deadline) ? 'YA' : 'TIDAK') . "\n";
}

echo "\nProses pengecekan selesai.\n";