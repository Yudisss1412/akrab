<?php

require_once 'vendor/autoload.php';

// Set up Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ViolationReport;
use App\Models\ProductReturn;

// Ambil penjual pertama
$seller = Seller::first();
if (!$seller) {
    echo "Tidak ada penjual ditemukan\n";
    exit;
}

// Ambil produk milik penjual
$products = Product::where('seller_id', $seller->id)->get();
if ($products->isEmpty()) {
    echo "Tidak ada produk milik penjual\n";
    exit;
}

// Ambil user pembeli
$buyer = User::whereHas('role', function($query) {
    $query->where('name', 'buyer');
})->first();

if (!$buyer) {
    echo "Tidak ada pembeli ditemukan\n";
    exit;
}

// Ambil sebuah order
$order = Order::first();
if (!$order) {
    echo "Tidak ada order ditemukan\n";
    exit;
}

// Ambil order item dari produk penjual ini
$orderItem = OrderItem::whereHas('product', function($query) use ($seller) {
    $query->where('seller_id', $seller->id);
})->first();

if (!$orderItem) {
    echo "Tidak ada order item dari produk penjual\n";
    exit;
}

// Buat beberapa data dummy untuk violation report
$violationReport1 = ViolationReport::create([
    'report_number' => 'VR-' . now()->format('Y-m-d') . '-' . str_pad(1, 3, '0', STR_PAD_LEFT),
    'reporter_user_id' => $buyer->id,
    'violator_user_id' => $seller->user_id,
    'product_id' => $products->first()->id,
    'order_id' => $order->id,
    'violation_type' => 'late_delivery',
    'description' => 'Produk datang terlambat dari yang dijanjikan',
    'evidence' => ['bukti1.jpg'],
    'status' => 'pending',
    'admin_notes' => null,
    'handled_by' => null,
    'handled_at' => null,
    'resolution' => null,
    'fine_amount' => 0.00
]);

$violationReport2 = ViolationReport::create([
    'report_number' => 'VR-' . now()->format('Y-m-d') . '-' . str_pad(2, 3, '0', STR_PAD_LEFT),
    'reporter_user_id' => $buyer->id,
    'violator_user_id' => $seller->user_id,
    'product_id' => $products->first()->id,
    'order_id' => $order->id,
    'violation_type' => 'wrong_item',
    'description' => 'Produk yang diterima tidak sesuai dengan yang dipesan',
    'evidence' => ['bukti2.jpg'],
    'status' => 'pending',
    'admin_notes' => null,
    'handled_by' => null,
    'handled_at' => null,
    'resolution' => null,
    'fine_amount' => 0.00
]);

// Buat beberapa data dummy untuk product return
$productReturn1 = ProductReturn::create([
    'order_id' => $order->id,
    'order_item_id' => $orderItem->id,
    'user_id' => $buyer->id,
    'reason' => 'wrong_size',
    'description' => 'Ukuran produk tidak sesuai',
    'status' => 'pending',
    'refund_amount' => 50000,
    'return_method' => 'store_pickup',
    'requested_at' => now(),
    'processed_at' => null,
    'processed_by' => null,
    'admin_notes' => null,
    'tracking_number' => null,
]);

$productReturn2 = ProductReturn::create([
    'order_id' => $order->id,
    'order_item_id' => $orderItem->id,
    'user_id' => $buyer->id,
    'reason' => 'defective',
    'description' => 'Produk rusak saat diterima',
    'status' => 'pending',
    'refund_amount' => 75000,
    'return_method' => 'store_pickup',
    'requested_at' => now(),
    'processed_at' => null,
    'processed_by' => null,
    'admin_notes' => null,
    'tracking_number' => null,
]);

echo "Data dummy berhasil dibuat:\n";
echo "- {$violationReport1->id} Violation Report ({$violationReport1->violation_type})\n";
echo "- {$violationReport2->id} Violation Report ({$violationReport2->violation_type})\n";
echo "- {$productReturn1->id} Product Return ({$productReturn1->reason})\n";
echo "- {$productReturn2->id} Product Return ({$productReturn2->reason})\n";

// Hapus file ini setelah selesai
unlink(__FILE__);