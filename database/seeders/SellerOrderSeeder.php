<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use App\Models\OrderLog;
use App\Models\User;
use App\Models\Product;
use App\Models\Seller;

class SellerOrderSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure users exist before creating orders
        $users = User::whereHas('role', function($query) {
            $query->where('name', 'buyer');
        })->get();

        if ($users->count() === 0) {
            $this->command->info('No buyers found, calling UserSeeder to create users...');
            $this->call(UserSeeder::class);
            $users = User::whereHas('role', function($query) {
                $query->where('name', 'buyer');
            })->get();
        }

        // Ensure products exist before creating orders
        $products = Product::all();
        if ($products->count() === 0) {
            $this->command->info('No products found, calling ProductSeeder to create products...');
            $this->call(ProductSeeder::class);
            $products = Product::all();
        }

        // Use the first available user as the customer for orders (preferably a buyer)
        $user = $users->first();
        if (!$user) {
            $this->command->info('No buyer users found, creating a test user...');
            $user = User::factory()->create([
                'name' => 'Test Customer',
                'email' => 'customer@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Ambil penjual pertama untuk mengaitkan produk
        $seller = Seller::first();
        if (!$seller) {
            $this->command->info('No seller found, creating a test seller...');
            $sellerUser = User::factory()->create([
                'name' => 'Test Seller',
                'email' => 'seller@example.com',
                'password' => bcrypt('password'),
            ]);
            
            // Pastikan role seller ada
            $sellerRole = \App\Models\Role::where('name', 'seller')->first();
            if (!$sellerRole) {
                $sellerRole = \App\Models\Role::create(['name' => 'seller', 'display_name' => 'Seller']);
            }
            if ($sellerUser->role_id !== $sellerRole->id) {
                $sellerUser->role_id = $sellerRole->id;
                $sellerUser->save();
            }
            
            $seller = \App\Models\Seller::create([
                'user_id' => $sellerUser->id,
                'name' => 'Test Seller',
                'phone' => '081234567890',
                'address' => 'Jl. Contoh Alamat Seller No. 123',
                'store_name' => 'Toko Contoh',
                'store_description' => 'Deskripsi toko contoh',
                'status' => 'active',
            ]);

            // Hubungkan produk ke penjual ini
            foreach($products as $product) {
                if (!$product->seller_id) {
                    $product->update(['seller_id' => $seller->id]);
                }
            }
        }

        // Jika produk belum memiliki seller_id, atur ke seller yang ditemukan
        foreach($products as $product) {
            if (!$product->seller_id) {
                $product->update(['seller_id' => $seller->id]);
            }
        }

        // Membuat beberapa pesanan dummy sesuai dengan status yang digunakan di SellerOrderController
        $order1 = Order::create([
            'order_number' => 'ORD-202312001',
            'user_id' => $user->id,
            'status' => 'pending_payment',
            'sub_total' => 170000,
            'shipping_cost' => 15000,
            'insurance_cost' => 2000,
            'discount' => 0,
            'total_amount' => 187000,
            'notes' => 'Mohon dikemas rapi',
            'shipping_courier' => 'jne',
            'created_at' => now()->subDays(5)->setTime(14, 30),
        ]);

        // Tambahkan item pesanan
        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $products[0]->id, // Produk pertama dari penjual
            'quantity' => 2,
            'unit_price' => 85000,
            'subtotal' => 170000,
        ]);

        // Tambahkan alamat pengiriman
        ShippingAddress::create([
            'order_id' => $order1->id,
            'recipient_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Cidadap',
            'ward' => 'Ciumbuleuit',
            'full_address' => 'Jl. Ciumbuleuit No. 123',
        ]);

        // Tambahkan log pesanan
        OrderLog::create([
            'order_id' => $order1->id,
            'description' => 'Pesanan dibuat oleh pembeli',
            'status' => 'pending_payment',
            'updated_by' => 'customer',
            'created_at' => now()->subDays(5)->setTime(14, 30),
        ]);


        $order2 = Order::create([
            'order_number' => 'ORD-202312002',
            'user_id' => $user->id,
            'status' => 'processing',
            'sub_total' => 215000,
            'shipping_cost' => 12000,
            'insurance_cost' => 1500,
            'discount' => 0,
            'total_amount' => 228500,
            'notes' => 'Segera diproses',
            'shipping_courier' => 'tiki',
            'paid_at' => now()->subDays(4)->setTime(9, 15),
            'created_at' => now()->subDays(4)->setTime(9, 15),
        ]);

        // Tambahkan item pesanan
        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $products[1]->id, // Produk kedua dari penjual
            'quantity' => 1,
            'unit_price' => 150000,
            'subtotal' => 150000,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $products[2]->id, // Produk ketiga dari penjual
            'quantity' => 1,
            'unit_price' => 65000,
            'subtotal' => 65000,
        ]);

        // Tambahkan alamat pengiriman
        ShippingAddress::create([
            'order_id' => $order2->id,
            'recipient_name' => 'Siti Aminah',
            'phone' => '082345678901',
            'province' => 'Jawa Tengah',
            'city' => 'Semarang',
            'district' => 'Candisari',
            'ward' => 'Jatingaleh',
            'full_address' => 'Jl. Jatingaleh Raya No. 45',
        ]);

        // Tambahkan log pesanan
        OrderLog::create([
            'order_id' => $order2->id,
            'description' => 'Pembayaran dikonfirmasi',
            'status' => 'processing',
            'updated_by' => 'customer',
            'created_at' => now()->subDays(4)->setTime(9, 16),
        ]);


        $order3 = Order::create([
            'order_number' => 'ORD-202312003',
            'user_id' => $user->id,
            'status' => 'shipping',
            'sub_total' => 220000,
            'shipping_cost' => 18000,
            'insurance_cost' => 2500,
            'discount' => 0,
            'total_amount' => 240500,
            'notes' => 'Jangan lupa dicek dulu sebelum dikirim',
            'shipping_courier' => 'jnt',
            'tracking_number' => 'JNT1234567890',
            'paid_at' => now()->subDays(4)->setTime(15, 45),
            'created_at' => now()->subDays(4)->setTime(15, 45),
        ]);

        // Tambahkan item pesanan
        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $products[3]->id, // Produk keempat dari penjual
            'quantity' => 1,
            'unit_price' => 220000,
            'subtotal' => 220000,
        ]);

        // Tambahkan alamat pengiriman
        ShippingAddress::create([
            'order_id' => $order3->id,
            'recipient_name' => 'Ahmad Fauzi',
            'phone' => '083456789012',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'district' => 'Kebayoran Baru',
            'ward' => 'Gandaria Utara',
            'full_address' => 'Jl. Sultan Iskandar Muda No. 78',
        ]);

        // Tambahkan log pesanan
        OrderLog::create([
            'order_id' => $order3->id,
            'description' => 'Pesanan dalam pengiriman',
            'status' => 'shipping',
            'updated_by' => 'seller',
            'created_at' => now()->subDays(3)->setTime(10, 30),
        ]);


        $order4 = Order::create([
            'order_number' => 'ORD-202312004',
            'user_id' => $user->id,
            'status' => 'completed',
            'sub_total' => 250000,
            'shipping_cost' => 11000,
            'insurance_cost' => 1800,
            'discount' => 0,
            'total_amount' => 262800,
            'notes' => 'Barang sudah diterima pembeli',
            'shipping_courier' => 'sicepat',
            'tracking_number' => 'SP1234567890',
            'paid_at' => now()->subDays(3)->setTime(10, 30),
            'created_at' => now()->subDays(3)->setTime(10, 30),
        ]);

        // Tambahkan item pesanan
        OrderItem::create([
            'order_id' => $order4->id,
            'product_id' => $products[4]->id, // Produk kelima dari penjual
            'quantity' => 1,
            'unit_price' => 250000,
            'subtotal' => 250000,
        ]);

        // Tambahkan alamat pengiriman
        ShippingAddress::create([
            'order_id' => $order4->id,
            'recipient_name' => 'Dewi Lestari',
            'phone' => '084567890123',
            'province' => 'Jawa Timur',
            'city' => 'Surabaya',
            'district' => 'Genteng',
            'ward' => 'Genteng Wetan',
            'full_address' => 'Jl. Genteng Kali No. 56',
        ]);

        // Tambahkan log pesanan
        OrderLog::create([
            'order_id' => $order4->id,
            'description' => 'Pesanan telah selesai',
            'status' => 'completed',
            'updated_by' => 'system',
            'created_at' => now()->subDays(1)->setTime(9, 15),
        ]);


        $order5 = Order::create([
            'order_number' => 'ORD-202312005',
            'user_id' => $user->id,
            'status' => 'cancelled',
            'sub_total' => 120000,
            'shipping_cost' => 10000,
            'insurance_cost' => 1200,
            'discount' => 0,
            'total_amount' => 131200,
            'notes' => 'Pembeli membatalkan pesanan',
            'shipping_courier' => 'pos',
            'created_at' => now()->subDays(2)->setTime(11, 15),
        ]);

        // Tambahkan item pesanan
        OrderItem::create([
            'order_id' => $order5->id,
            'product_id' => $products[5]->id, // Produk keenam dari penjual
            'quantity' => 1,
            'unit_price' => 120000,
            'subtotal' => 120000,
        ]);

        // Tambahkan alamat pengiriman
        ShippingAddress::create([
            'order_id' => $order5->id,
            'recipient_name' => 'Rina Kartika',
            'phone' => '085678901234',
            'province' => 'Banten',
            'city' => 'Tangerang',
            'district' => 'Karawaci',
            'ward' => 'Karawaci',
            'full_address' => 'Jl. Raden Patah No. 90',
        ]);

        // Tambahkan log pesanan
        OrderLog::create([
            'order_id' => $order5->id,
            'description' => 'Pesanan dibatalkan oleh pembeli',
            'status' => 'cancelled',
            'updated_by' => 'customer',
            'created_at' => now()->subDays(1)->setTime(16, 45),
        ]);
    }
}