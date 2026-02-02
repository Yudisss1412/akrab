<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class SimulateMidtransCallback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:simulate-midtrans-callback
                            {order_number? : Order number to simulate callback for (leave empty to select from pending orders)}
                            {--status=settlement : Transaction status (settlement, capture, pending, expire, cancel, deny)}
                            {--fraud=accept : Fraud status (accept, deny)}
                            {--all : Apply to all pending orders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate Midtrans payment callback for development/testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderNumber = $this->argument('order_number');
        $status = $this->option('status');
        $fraudStatus = $this->option('fraud');
        $applyToAll = $this->option('all');

        // If --all flag is used, process all pending orders
        if ($applyToAll) {
            $pendingOrders = Order::where('status', 'pending')
                ->whereHas('payment', function($query) {
                    $query->whereIn('payment_method', ['midtrans', 'bank_transfer', 'e_wallet']);
                })
                ->get();

            if ($pendingOrders->isEmpty()) {
                $this->info("No pending orders with Midtrans/bank_transfer/e_wallet payment method found.");
                return 0;
            }

            $this->info("Found {$pendingOrders->count()} pending orders with Midtrans/bank_transfer/e_wallet payment method.");

            foreach ($pendingOrders as $order) {
                $this->processOrder($order, $status, $fraudStatus);
            }

            $this->info("\nAll pending orders processed successfully!");
            return 0;
        }

        // If no order number provided, show list of pending orders
        if (!$orderNumber) {
            $pendingOrders = Order::where('status', 'pending')
                ->whereHas('payment', function($query) {
                    $query->whereIn('payment_method', ['midtrans', 'bank_transfer', 'e_wallet']);
                })
                ->get();

            if ($pendingOrders->isEmpty()) {
                $this->info("No pending orders with Midtrans/bank_transfer/e_wallet payment method found.");
                return 0;
            }

            $orderNumbers = $pendingOrders->pluck('order_number')->toArray();
            $selectedOrderNumber = $this->choice(
                'Which pending order would you like to simulate callback for?',
                $orderNumbers
            );

            $orderNumber = $selectedOrderNumber;
        }

        // Process single order
        return $this->processOrderWithNumber($orderNumber, $status, $fraudStatus);
    }

    /**
     * Process a single order by order number
     */
    private function processOrderWithNumber($orderNumber, $status, $fraudStatus)
    {
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            $this->error("Order with number {$orderNumber} not found!");
            return 1;
        }

        return $this->processOrder($order, $status, $fraudStatus);
    }

    /**
     * Process a single order
     */
    private function processOrder($order, $status, $fraudStatus)
    {
        $orderNumber = $order->order_number;

        $this->info("\nSimulating Midtrans callback for order: {$orderNumber}");
        $this->info("Transaction status: {$status}");
        $this->info("Fraud status: {$fraudStatus}");

        // Find the payment record
        $payment = Payment::where('order_id', $order->id)->first();

        if (!$payment) {
            $this->error("Payment record not found for order {$orderNumber}!");
            return 1;
        }

        $this->info("Order found: {$order->order_number} (Current status: {$order->status})");
        $this->info("Payment found: {$payment->payment_method} (Current status: {$payment->payment_status})");

        // Log the status change
        $this->info("Processing callback simulation...");

        // Map Midtrans status to our local order status
        $newOrderStatus = $order->status; // Default to current status
        $newPaymentStatus = $payment->payment_status; // Default to current status

        switch ($status) {
            case 'settlement':
            case 'capture':
                if ($fraudStatus === 'accept') {
                    $newOrderStatus = 'confirmed'; // Changed from 'paid' to 'confirmed' to match seller system
                    $newPaymentStatus = 'success';
                } else {
                    $newOrderStatus = 'pending'; // Keep pending if fraud check fails
                    $newPaymentStatus = 'pending';
                }
                break;

            case 'pending':
                $newOrderStatus = 'pending';
                $newPaymentStatus = 'pending';
                break;

            case 'expire':
                $newOrderStatus = 'cancelled';
                $newPaymentStatus = 'expired';
                break;

            case 'cancel':
            case 'deny':
                $newOrderStatus = 'cancelled';
                $newPaymentStatus = 'failed';
                break;

            case 'refund':
            case 'partial_refund':
            case 'chargeback':
            case 'partial_chargeback':
                $newOrderStatus = 'cancelled'; // Or 'refunded' if you have that status
                $newPaymentStatus = 'refunded';
                break;
        }

        // Update payment record
        $payment->update([
            'payment_status' => $newPaymentStatus,
            'payment_gateway_response' => [
                'order_id' => $orderNumber,
                'transaction_status' => $status,
                'fraud_status' => $fraudStatus,
                'simulated' => true,
                'timestamp' => now()->toISOString()
            ],
            'paid_at' => in_array($status, ['settlement', 'capture']) && $fraudStatus === 'accept' ? now() : $payment->paid_at
        ]);

        // Update order status if it's changing
        $orderStatusChanged = $order->status !== $newOrderStatus;

        if ($orderStatusChanged) {
            $order->update([
                'status' => $newOrderStatus,
                'paid_at' => in_array($status, ['settlement', 'capture']) && $fraudStatus === 'accept' ? now() : $order->paid_at
            ]);

            // Add log entry for the status change
            $order->logs()->create([
                'status' => $newOrderStatus,
                'description' => "Status pesanan diubah oleh sistem simulasi Midtrans dari '{$order->status}' menjadi '{$newOrderStatus}'. Transaction status: {$status}, Fraud status: {$fraudStatus}",
                'updated_by' => 'system'
            ]);
        }

        $this->info('');
        $this->info("Callback simulation completed!");
        $this->info("Order status: {$order->status} (was {$order->getOriginal('status')})");
        $this->info("Payment status: {$payment->payment_status} (was {$payment->getOriginal('payment_status')})");
        $this->info('');

        if ($orderStatusChanged) {
            $this->info("✓ Order status changed from '{$order->getOriginal('status')}' to '{$newOrderStatus}'");
        } else {
            $this->info("- Order status remained the same: '{$newOrderStatus}'");
        }

        return 0;
    }
}
