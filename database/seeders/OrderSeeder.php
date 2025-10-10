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

class OrderSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user dan produk untuk membuat pesanan
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $products = Product::all();
        if ($products->count() === 0) {
            $this->call(ProductSeeder::class);
            $products = Product::all();
        }

        // Membuat beberapa pesanan dummy sesuai dengan contoh di manajemen pesanan
        $order1 = Order::create([
            'order_number' => 'ORD-202301001',
            'user_id' => $user->id,
            'status' => 'pending',
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
            'product_id' => $products[0]->id, // Kaos Polos Premium
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
            'user_id' => $user->id,
            'description' => 'Pesanan dibuat oleh pembeli',
            'created_at' => now()->subDays(5)->setTime(14, 30),
        ]);


        $order2 = Order::create([
            'order_number' => 'ORD-202301002',
            'user_id' => $user->id,
            'status' => 'confirmed',
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
            'product_id' => $products[1]->id, // Celana Jeans Premium
            'quantity' => 1,
            'unit_price' => 150000,
            'subtotal' => 150000,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $products[2]->id, // Topi Baseball Trendy
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
            'user_id' => $user->id,
            'description' => 'Pesanan dibuat oleh pembeli',
            'created_at' => now()->subDays(4)->setTime(9, 15),
        ]);

        OrderLog::create([
            'order_id' => $order2->id,
            'user_id' => $user->id,
            'description' => 'Pembayaran dikonfirmasi',
            'created_at' => now()->subDays(4)->setTime(9, 16),
        ]);


        $order3 = Order::create([
            'order_number' => 'ORD-202301003',
            'user_id' => $user->id,
            'status' => 'shipped',
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
            'product_id' => $products[3]->id, // Sepatu Sneakers Casual
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
            'user_id' => $user->id,
            'description' => 'Pesanan dibuat oleh pembeli',
            'created_at' => now()->subDays(4)->setTime(15, 45),
        ]);

        OrderLog::create([
            'order_id' => $order3->id,
            'user_id' => $user->id,
            'description' => 'Pembayaran dikonfirmasi',
            'created_at' => now()->subDays(4)->setTime(15, 46),
        ]);

        OrderLog::create([
            'order_id' => $order3->id,
            'user_id' => $user->id,
            'description' => 'Nomor resi ditambahkan',
            'created_at' => now()->subDays(3)->setTime(10, 30),
        ]);


        $order4 = Order::create([
            'order_number' => 'ORD-202301004',
            'user_id' => $user->id,
            'status' => 'delivered',
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
            'product_id' => $products[4]->id, // Jam Tangan Digital
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
            'user_id' => $user->id,
            'description' => 'Pesanan dibuat oleh pembeli',
            'created_at' => now()->subDays(3)->setTime(10, 30),
        ]);

        OrderLog::create([
            'order_id' => $order4->id,
            'user_id' => $user->id,
            'description' => 'Pembayaran dikonfirmasi',
            'created_at' => now()->subDays(3)->setTime(10, 31),
        ]);

        OrderLog::create([
            'order_id' => $order4->id,
            'user_id' => $user->id,
            'description' => 'Pesanan telah dikirim',
            'created_at' => now()->subDays(2)->setTime(14, 20),
        ]);

        OrderLog::create([
            'order_id' => $order4->id,
            'user_id' => $user->id,
            'description' => 'Pesanan telah diterima pembeli',
            'created_at' => now()->subDays(1)->setTime(9, 15),
        ]);


        $order5 = Order::create([
            'order_number' => 'ORD-202301005',
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
            'product_id' => $products[5]->id, // Dompet Kulit Asli
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
            'user_id' => $user->id,
            'description' => 'Pesanan dibuat oleh pembeli',
            'created_at' => now()->subDays(2)->setTime(11, 15),
        ]);

        OrderLog::create([
            'order_id' => $order5->id,
            'user_id' => $user->id,
            'description' => 'Pesanan dibatalkan oleh pembeli',
            'created_at' => now()->subDays(1)->setTime(16, 45),
        ]);
    }
}
