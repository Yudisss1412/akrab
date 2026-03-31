<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;

$orderNumber = 'ORD-20260303-33155';

$order = Order::where('order_number', $orderNumber)
    ->with('payment')
    ->first();

if (!$order) {
    echo "Order not found!\n";
    exit(1);
}

echo "================================\n";
echo "ORDER STATUS CHECK\n";
echo "================================\n";
echo "Order Number: {$order->order_number}\n";
echo "Order Status: {$order->status}\n";
echo "Order Paid At: {$order->paid_at}\n";
echo "\n";

if ($order->payment) {
    echo "PAYMENT INFO:\n";
    echo "Payment Method: {$order->payment->payment_method}\n";
    echo "Payment Status: {$order->payment->payment_status}\n";
    echo "Payment Paid At: {$order->payment->paid_at}\n";
} else {
    echo "No payment record found.\n";
}

echo "================================\n";
