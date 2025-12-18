<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class DummySalesHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil kategori untuk produk (ambil kategori pertama jika ada)
        $category = \App\Models\Category::first();
        if (!$category) {
            // Jika tidak ada kategori, buat kategori dummy
            $category = \App\Models\Category::create([
                'name' => 'Umum',
                'description' => 'Kategori umum untuk produk dummy',
            ]);
        }

        // Ambil atau buat penjual dummy
        $sellerUser = User::where('email', 'seller@example.com')->first();
        if (!$sellerUser) {
            $sellerUser = User::create([
                'name' => 'Seller Dummy',
                'email' => 'seller@example.com',
                'password' => bcrypt('password'),
                'role_id' => 2, // Assumsi role_id 2 adalah seller
            ]);
        }

        $seller = Seller::where('user_id', $sellerUser->id)->first();
        if (!$seller) {
            $seller = Seller::create([
                'user_id' => $sellerUser->id,
                'store_name' => 'Toko Dummy',
                'owner_name' => 'Seller Dummy',
                'email' => 'seller@example.com',
                'status' => 'aktif',
                'join_date' => now()->subMonths(2),
            ]);
        }

        // Buat produk dummy milik penjual ini
        $product = Product::create([
            'name' => 'Produk Dummy',
            'description' => 'Deskripsi produk dummy',
            'price' => 50000,
            'category_id' => $category->id, // Gunakan kategori yang sudah dipastikan ada
            'subcategory' => 'Umum',
            'stock' => 100,
            'weight' => 500,
            'seller_id' => $seller->id,
            'status' => 'active',
        ]);

        // Ambil user untuk order (ambil user pertama atau buat baru jika tidak ada)
        $customer = User::first();
        if (!$customer) {
            $customer = User::create([
                'name' => 'Customer Dummy',
                'email' => 'customer@example.com',
                'password' => bcrypt('password'),
                'role_id' => 1, // Assumsi role_id 1 adalah buyer
            ]);
        }

        // Buat order dummy yang selesai - gunakan order_number yang unik
        $maxOrderId = Order::max('id');
        $order = Order::create([
            'user_id' => $customer->id,
            'order_number' => 'ORD-' . ($maxOrderId + 1),
            'status' => 'delivered', // Status untuk order selesai
            'sub_total' => 100000,
            'shipping_cost' => 0,
            'insurance_cost' => 0,
            'discount' => 0,
            'total_amount' => 100000,
            'paid_at' => now()->subDays(1),
            'notes' => 'Order dummy untuk riwayat penjualan',
            'shipping_courier' => 'JNE',
            'tracking_number' => 'TRK001',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(1),
        ]);

        // Buat item order
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'variant_id' => null,
            'quantity' => 2,
            'unit_price' => 50000,
            'subtotal' => 100000,
        ]);

        // Buat satu order lagi
        $order2 = Order::create([
            'user_id' => $customer->id,
            'order_number' => 'ORD-' . ($maxOrderId + 2),
            'status' => 'delivered',
            'sub_total' => 75000,
            'shipping_cost' => 0,
            'insurance_cost' => 0,
            'discount' => 0,
            'total_amount' => 75000,
            'paid_at' => now()->subDays(4),
            'notes' => 'Order dummy untuk riwayat penjualan',
            'shipping_courier' => 'JNE',
            'tracking_number' => 'TRK002',
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(4),
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $product->id,
            'variant_id' => null,
            'quantity' => 1,
            'unit_price' => 75000,
            'subtotal' => 75000,
        ]);
    }
}
