<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Review;
use App\Models\Models\ProductReturn; // Sesuaikan dengan model yang benar
use Illuminate\Support\Facades\DB;

class DynamicReviewSeeder extends Seeder
{
    public function run()
    {
        // Hapus data lama
        Review::truncate();
        ProductReturn::truncate();

        // Buat atau ambil user penjual
        $user = User::where('role_id', 3)->first(); // Misalnya role_id 3 adalah seller
        if (!$user) {
            $user = User::create([
                'name' => 'Seller Test',
                'email' => 'seller@test.com',
                'password' => bcrypt('password'),
                'role_id' => 3
            ]);
        }

        // Buat atau ambil seller
        $seller = Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            $seller = Seller::create([
                'user_id' => $user->id,
                'store_name' => 'Toko Test',
                'owner_name' => 'Owner Test',
                'email' => 'owner@test.com',
                'status' => 'aktif',
                'join_date' => now()
            ]);
        }

        // Buat produk untuk penjual
        $products = [];
        for ($i = 1; $i <= 5; $i++) {
            $products[] = Product::create([
                'name' => "Produk Test $i",
                'description' => "Deskripsi produk test $i",
                'price' => 100000,
                'stock' => 100,
                'weight' => 1.0,
                'category_id' => 1,
                'seller_id' => $seller->id,
                'status' => 'active'
            ]);
        }

        // Buat user pelanggan (non-seller)
        $customerUsers = [];
        for ($i = 1; $i <= 10; $i++) {
            $customerUsers[] = User::create([
                'name' => "Customer $i",
                'email' => "customer$i@test.com",
                'password' => bcrypt('password'),
                'role_id' => 2  // role customer
            ]);
        }

        // Buat ulasan dengan rating bervariasi (termasuk komplain rating 1-2)
        for ($i = 1; $i <= 20; $i++) {
            $rating = rand(1, 5); // Acak rating 1-5
            $product = $products[array_rand($products)];
            $user = $customerUsers[array_rand($customerUsers)];
            
            $comments = [
                1 => 'Produk sangat buruk, tidak sesuai harapan.',
                2 => 'Kurang memuaskan, ada banyak kekurangan.',
                3 => 'Biasa saja, tidak terlalu istimewa.',
                4 => 'Bagus, cukup memuaskan.',
                5 => 'Sangat bagus! Kualitasnya mantap.'
            ];
            
            Review::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'order_id' => rand(1, 10), // Asumsi ada order
                'rating' => $rating,
                'review_text' => $comments[$rating],
                'status' => 'approved',
                'reply' => ($rating <= 2) ? 'Terima kasih atas masukannya, kami akan perbaiki kualitasnya.' : null,
                'replied_at' => ($rating <= 2) ? now()->subDays(rand(1, 5)) : null
            ]);
        }

        // Buat permintaan retur
        for ($i = 1; $i <= 8; $i++) {
            $product = $products[array_rand($products)];
            $user = $customerUsers[array_rand($customerUsers)];
            
            $statuses = ['pending', 'approved', 'rejected', 'completed'];
            $status = $statuses[array_rand($statuses)];
            
            ProductReturn::create([
                'user_id' => $user->id,
                'order_item_id' => rand(1, 20), // Asumsi ada order items
                'reason' => 'Produk cacat/rusak',
                'description' => 'Barang yang saya terima dalam keadaan rusak.',
                'status' => $status,
                'requested_at' => now()->subDays(rand(1, 30)),
                'processed_at' => in_array($status, ['approved', 'rejected', 'completed']) ? now()->subDays(rand(1, 10)) : null,
                'refund_amount' => rand(50000, 200000)
            ]);
        }

        $this->command->info('Berhasil membuat data dummy untuk testing ulasan dan retur');
    }
}