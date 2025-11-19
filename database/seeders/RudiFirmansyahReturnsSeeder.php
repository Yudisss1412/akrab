<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Seller;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Models\ProductReturn;

class RudiFirmansyahReturnsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find Rudi Firmansyah's seller record
        $seller = Seller::where('owner_name', 'Rudi Firmansyah')->first();

        if (!$seller) {
            $this->command->error('Rudi Firmansyah seller not found');
            return;
        }

        // Get Rudi Firmansyah's products
        $products = $seller->products;

        if ($products->count() === 0) {
            $this->command->error('No products found for Rudi Firmansyah');
            return;
        }

        // Get some user IDs for the returns (using existing users)
        $users = User::whereHas('role', function($query) {
            $query->where('name', 'buyer');
        })->get();

        if ($users->count() === 0) {
            $this->command->error('No buyer users found for returns');
            return;
        }

        // Create dummy orders and order items if needed
        foreach ($users as $user) {
            // Create an order for each user if not already exists
            $order = Order::firstOrCreate([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . time() . rand(1000, 9999),
            ], [
                'total_amount' => rand(100000, 1000000),
                'sub_total' => rand(80000, 800000),
                'shipping_cost' => rand(10000, 20000),
                'status' => 'delivered',  // Use 'delivered' instead of 'completed'
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create order items for Rudi Firmansyah's products
            $product = $products->random();
            $orderItem = OrderItem::firstOrCreate([
                'order_id' => $order->id,
                'product_id' => $product->id,
            ], [
                'quantity' => rand(1, 3),
                'unit_price' => $product->price,
                'subtotal' => $product->price * rand(1, 3)
            ]);

            // Create return request for this order item
            $return = ProductReturn::create([
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,
                'user_id' => $user->id,
                'reason' => 'Produk cacat/rusak',
                'description' => 'Saya ingin mengembalikan produk ini karena ternyata tidak sesuai dengan yang saya harapkan. Produknya rusak saat diterima.',
                'status' => 'pending',
                'requested_at' => now(),
                'refund_amount' => $orderItem->subtotal,
            ]);

            $this->command->info("Created return request: {$return->id} for user {$user->name} and product {$product->name}");
        }

        // Create more returns with different statuses
        foreach ($users->take(2) as $user) {
            $order = Order::where('user_id', $user->id)->first();
            if (!$order) continue;

            $orderItem = OrderItem::where('order_id', $order->id)
                                ->whereHas('product', function($q) use ($seller) {
                                    $q->where('seller_id', $seller->id);
                                })->first();
            if (!$orderItem) continue;

            $status = ['approved', 'completed'][array_rand(['approved', 'completed'])];
            $return = ProductReturn::create([
                'order_id' => $order->id,
                'order_item_id' => $orderItem->id,
                'user_id' => $user->id,
                'reason' => 'Tidak cocok',
                'description' => 'Produk datang dalam kondisi baik tetapi tidak sesuai dengan kebutuhan saya.',
                'status' => $status,
                'requested_at' => now()->subDays(rand(1, 10)),
                'processed_at' => now()->subDays(rand(0, 5)),
                'processed_by' => $seller->user_id,
                'refund_amount' => $orderItem->subtotal * 0.9, // 90% refund
            ]);

            $this->command->info("Created return request: {$return->id} with status {$return->status}");
        }

        $this->command->info('Rudi Firmansyah returns created successfully!');
    }
}
