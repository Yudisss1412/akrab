<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class DummyOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ambil penjual pertama (kita asumsikan penjual dengan ID 5 seperti di log sebelumnya)
        $seller = Seller::first();
        if (!$seller) {
            echo "Seller not found. Creating one...\n";
            $user = User::firstOrCreate([
                'email' => 'seller@test.com'
            ], [
                'name' => 'Test Seller',
                'email' => 'seller@test.com',
                'password' => Hash::make('password123'),
                'phone' => '081234567890'
            ]);
            
            $role = \App\Models\Role::firstOrCreate(['name' => 'seller'], [
                'display_name' => 'Seller',
                'description' => 'Penjual produk'
            ]);
            
            $user->role_id = $role->id;
            $user->save();
            
            $seller = Seller::create([
                'user_id' => $user->id,
                'store_name' => 'Toko Testing',
                'phone' => '081234567890',
                'address' => 'Alamat Toko Testing',
                'status' => 'active'
            ]);
        }
        
        // Pastikan penjual memiliki produk
        $product = Product::where('seller_id', $seller->id)->first();
        if (!$product) {
            echo "Creating a product for seller...\n";
            $category = \App\Models\Category::first();
            if (!$category) {
                $category = \App\Models\Category::create(['name' => 'General', 'description' => 'Kategori Umum']);
            }
            
            $product = Product::create([
                'name' => 'Produk Testing',
                'description' => 'Deskripsi produk untuk testing',
                'price' => 50000,
                'stock' => 100,
                'weight' => 1,
                'category_id' => $category->id,
                'seller_id' => $seller->id,
                'status' => 'active'
            ]);
        }

        // Definisikan status dan jumlah data untuk masing-masing
        $statuses = [
            ['status' => 'pending', 'name' => 'Belum Dibayar', 'count' => rand(5, 10)],
            ['status' => 'confirmed', 'name' => 'Perlu Diproses', 'count' => rand(5, 10)],  
            ['status' => 'shipped', 'name' => 'Sedang Dikirim', 'count' => rand(5, 10)],
            ['status' => 'delivered', 'name' => 'Selesai', 'count' => rand(5, 10)],
            ['status' => 'cancelled', 'name' => 'Dibatalkan', 'count' => rand(5, 10)]
        ];

        foreach ($statuses as $statusInfo) {
            echo "Creating {$statusInfo['count']} orders with status {$statusInfo['status']} ({$statusInfo['name']})...\n";
            
            for ($i = 0; $i < $statusInfo['count']; $i++) {
                // Buat user jika belum ada
                $user = User::firstOrCreate([
                    'email' => 'customer_' . $statusInfo['status'] . '_' . $i . '@test.com'
                ], [
                    'name' => 'Customer ' . $statusInfo['name'] . ' ' . ($i + 1),
                    'email' => 'customer_' . $statusInfo['status'] . '_' . $i . '@test.com',
                    'password' => Hash::make('password123')
                ]);
                
                // Buat order
                $order = Order::create([
                    'order_number' => 'ORD-' . strtoupper(dechex(time())) . '-' . rand(1000, 9999),
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
        
        echo "Dummy orders seeding completed successfully!\n";
    }
}