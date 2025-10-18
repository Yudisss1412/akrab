<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Order;

class PaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua order
        $orders = Order::all();
        
        foreach ($orders as $order) {
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'bank_transfer', // bisa juga 'e_wallet' atau 'cod'
                'payment_status' => 'success', // bisa juga 'pending', 'processing', 'failed', 'cancelled', 'refunded'
                'transaction_id' => 'TRX-' . $order->order_number . '-' . time(),
                'amount' => $order->total_amount,
                'paid_at' => now(),
                'payment_gateway_response' => [
                    'status_code' => '200',
                    'transaction_status' => 'settlement',
                    'fraud_status' => 'accept'
                ]
            ]);
        }
    }
}
