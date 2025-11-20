<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AddDummyOrdersToSpecificSellerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dummy:orders {name?} {--seller_id=} {--count=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add dummy orders to a specific seller';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $seller = null;
        
        // Determine seller based on options or arguments
        if ($this->option('seller_id')) {
            $seller = Seller::find($this->option('seller_id'));
        } else if ($this->argument('name')) {
            $user = User::where('name', $this->argument('name'))->first();
            if ($user) {
                $seller = Seller::where('user_id', $user->id)->first();
            }
        }
        
        if (!$seller) {
            $this->error('Seller not found!');
            return 1;
        }
        
        $this->info("Adding dummy orders for seller: " . $seller->id . " (user: " . $seller->user->name . ")");

        // Ensure seller has a product
        $product = Product::where('seller_id', $seller->id)->first();
        if (!$product) {
            $this->info("Creating a product for seller...");
            $category = \App\Models\Category::first();
            if (!$category) {
                $category = \App\Models\Category::create([
                    'name' => 'General',
                    'description' => 'Kategori Umum',
                    'image' => null
                ]);
            }
            
            $product = Product::create([
                'name' => 'Produk Testing untuk ' . $seller->user->name,
                'description' => 'Deskripsi produk untuk testing manajemen pesanan',
                'price' => 50000,
                'stock' => 100,
                'weight' => 1,
                'category_id' => $category->id,
                'seller_id' => $seller->id,
                'status' => 'active'
            ]);
        }

        // Determine count
        $count = $this->option('count') ?? 5;
        
        // Definisikan status dan jumlah data untuk masing-masing
        $statuses = [
            ['status' => 'pending', 'name' => 'Belum Dibayar', 'count' => rand(5, $count)],
            ['status' => 'confirmed', 'name' => 'Perlu Diproses', 'count' => rand(5, $count)],  
            ['status' => 'shipped', 'name' => 'Sedang Dikirim', 'count' => rand(5, $count)],
            ['status' => 'delivered', 'name' => 'Selesai', 'count' => rand(5, $count)],
            ['status' => 'cancelled', 'name' => 'Dibatalkan', 'count' => rand(5, $count)]
        ];

        foreach ($statuses as $statusInfo) {
            $this->info("Creating {$statusInfo['count']} orders with status {$statusInfo['status']} ({$statusInfo['name']})");
            
            for ($i = 0; $i < $statusInfo['count']; $i++) {
                $randomNumber = rand(1000, 9999);
                
                // Buat user customer jika belum ada
                $customerEmail = 'customer_' . $statusInfo['status'] . '_' . $i . '_' . $randomNumber . '@test.com';
                $customerName = 'Customer ' . $statusInfo['name'] . ' ' . ($i + 1);
                
                $user = User::firstOrCreate([
                    'email' => $customerEmail
                ], [
                    'name' => $customerName,
                    'email' => $customerEmail,
                    'password' => Hash::make('password123')
                ]);
                
                // Buat order
                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(dechex(time())) . '-' . $randomNumber,
                    'user_id' => $user->id,
                    'status' => $statusInfo['status'],
                    'sub_total' => $product->price,
                    'shipping_cost' => 10000,
                    'insurance_cost' => 2000,
                    'discount' => 0,
                    'total_amount' => $product->price + 10000 + 2000,
                    'notes' => 'Pesanan untuk testing status ' . $statusInfo['name'],
                    'shipping_courier' => 'JNE',
                    'tracking_number' => 'TRK' . rand(100000000, 999999999)
                ]);
                
                // Buat order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price' => $product->price,
                    'subtotal' => $product->price
                ]);
            }
        }
        
        $this->info("Successfully added dummy orders for seller ID " . $seller->id . "!");
        return 0;
    }
}