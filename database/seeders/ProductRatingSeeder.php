<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use App\Models\Review;
use App\Models\Order;
use App\Models\OrderItem;

class ProductRatingSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding produk dan rating...');

        // Pastikan penjual tersedia
        $sellers = Seller::all();
        if ($sellers->count() === 0) {
            $this->command->info('Tidak ada penjual ditemukan, memanggil UserSeeder...');
            $this->call(UserSeeder::class);
            $sellers = Seller::all();
        }

        // Pastikan pengguna tersedia
        $users = User::whereHas('role', function($query) {
            $query->where('name', 'buyer');
        })->get();
        
        if ($users->count() === 0) {
            $this->command->info('Tidak ada pembeli ditemukan, memanggil UserSeeder...');
            $this->call(UserSeeder::class);
            $users = User::whereHas('role', function($query) {
                $query->where('name', 'buyer');
            })->get();
        }

        // Pastikan produk tersedia
        $products = Product::all();
        if ($products->count() === 0) {
            $this->command->info('Tidak ada produk ditemukan, memanggil ProductSeeder...');
            $this->call(ProductSeeder::class);
            $products = Product::all();
        }

        // Pastikan pesanan tersedia
        $orders = Order::all();
        if ($orders->count() === 0) {
            $this->command->info('Tidak ada pesanan ditemukan, memanggil OrderSeeder...');
            $this->call(OrderSeeder::class);
            $orders = Order::all();
        }

        // Jika tetap tidak ada produk, buat produk dummy
        if ($products->count() === 0) {
            $this->createDummyProducts();
            $products = Product::all();
        }

        // Hubungkan produk ke penjual jika belum
        $seller = $sellers->first();
        if ($seller) {
            foreach ($products as $product) {
                if (!$product->seller_id) {
                    $product->update(['seller_id' => $seller->id]);
                }
            }
        }

        // Buat ulasan dummy untuk produk
        $this->createDummyReviews($products, $users, $orders);

        $this->command->info('Seeder produk dan rating selesai.');
        $this->command->info('Jumlah produk: ' . $products->count());
        $this->command->info('Jumlah ulasan: ' . Review::count());
    }

    private function createDummyProducts(): void
    {
        $sellers = Seller::all();
        if ($sellers->count() === 0) {
            $this->command->info('Tidak ada penjual, membuat penjual dummy...');
            $sellerUser = User::factory()->create([
                'name' => 'Seller Dummy',
                'email' => 'seller.dummy@example.com',
                'password' => bcrypt('password'),
            ]);

            $sellerRole = \App\Models\Role::where('name', 'seller')->first();
            if (!$sellerRole) {
                $sellerRole = \App\Models\Role::create(['name' => 'seller', 'display_name' => 'Seller']);
            }
            
            $sellerUser->role_id = $sellerRole->id;
            $sellerUser->save();

            $seller = \App\Models\Seller::create([
                'user_id' => $sellerUser->id,
                'name' => 'Seller Dummy',
                'phone' => '081234567890',
                'address' => 'Alamat Dummy',
                'store_name' => 'Toko Dummy',
                'store_description' => 'Deskripsi Toko Dummy',
                'status' => 'active',
            ]);
        } else {
            $seller = $sellers->first();
        }

        // Data produk dummy
        $dummyProducts = [
            [
                'name' => 'Kaos Polos Premium',
                'description' => 'Kaos polos berkualitas tinggi dengan bahan katun yang nyaman dipakai sehari-hari',
                'price' => 85000,
                'stock' => 50,
                'weight' => 200,
                'image' => 'products/kaos-polos.jpg',
                'status' => 'active',
                'seller_id' => $seller->id,
            ],
            [
                'name' => 'Celana Jeans Premium',
                'description' => 'Celana jeans fashionable dengan model yang modis dan nyaman dipakai',
                'price' => 150000,
                'stock' => 30,
                'weight' => 400,
                'image' => 'products/celana-jeans.jpg',
                'status' => 'active',
                'seller_id' => $seller->id,
            ],
            [
                'name' => 'Topi Baseball Trendy',
                'description' => 'Topi baseball model terbaru dengan desain yang stylish',
                'price' => 65000,
                'stock' => 40,
                'weight' => 100,
                'image' => 'products/topi-baseball.jpg',
                'status' => 'active',
                'seller_id' => $seller->id,
            ],
            [
                'name' => 'Sepatu Sneakers Casual',
                'description' => 'Sepatu sneakers casual yang cocok untuk aktivitas sehari-hari',
                'price' => 220000,
                'stock' => 20,
                'weight' => 600,
                'image' => 'products/sepatu-sneakers.jpg',
                'status' => 'active',
                'seller_id' => $seller->id,
            ],
            [
                'name' => 'Jam Tangan Digital',
                'description' => 'Jam tangan digital dengan fitur lengkap dan desain modern',
                'price' => 250000,
                'stock' => 15,
                'weight' => 150,
                'image' => 'products/jam-tangan-digital.jpg',
                'status' => 'active',
                'seller_id' => $seller->id,
            ],
            [
                'name' => 'Dompet Kulit Asli',
                'description' => 'Dompet kulit asli dengan kualitas terbaik dan desain elegan',
                'price' => 120000,
                'stock' => 25,
                'weight' => 80,
                'image' => 'products/dompet-kulit.jpg',
                'status' => 'active',
                'seller_id' => $seller->id,
            ],
        ];

        foreach ($dummyProducts as $productData) {
            Product::create($productData);
        }
    }

    private function createDummyReviews($products, $users, $orders): void
    {
        // Bersihkan ulasan lama jika perlu
        Review::truncate();

        $reviews = [];
        $totalReviews = 30; // Membuat 30 ulasan dummy

        $usedCombinations = [];

        for ($i = 0; $i < $totalReviews; $i++) {
            $attempt = 0;
            $validCombination = false;

            while(!$validCombination && $attempt < 20) {
                $productId = $products->random()->id;
                $userId = $users->random()->id;
                $orderId = $orders->random()->id;

                // Kombinasi untuk dicek
                $combination = $userId . '-' . $productId . '-' . $orderId;

                // Cek apakah kombinasi sudah digunakan
                if (!in_array($combination, $usedCombinations)) {
                    $usedCombinations[] = $combination;
                    $validCombination = true;
                } else {
                    $attempt++;
                }
            }

            // Jika tidak bisa menemukan kombinasi unik setelah beberapa percobaan, lewati
            if (!$validCombination) {
                continue;
            }

            $rating = rand(1, 5); // Rating antara 1-5

            // Sesuaikan komentar dengan rating
            $comments = [
                1 => ['Produknya buruk sekali, tidak sesuai harapan', 'Sangat mengecewakan, kualitas rendah', 'Tidak akan beli lagi, sangat buruk'],
                2 => ['Produknya biasa saja, ada yang lebih baik', 'Agak kecewa dengan pembelian ini', 'Sedikit cacat dan tidak sebanding dengan harga'],
                3 => ['Biasa saja, tidak terlalu buruk tapi juga tidak istimewa', 'Lumayan lah untuk harga segini', 'Sesuai harga, tidak lebih tidak kurang'],
                4 => ['Bagus, puas dengan pembelian ini', 'Produknya berkualitas, rekomended', 'Nyaman dipakai dan kemasan rapi'],
                5 => ['Sangat bagus, melebihi ekspektasi', 'Kualitas nomor satu, pasti beli lagi', 'Sempurna! Tidak menyesal sama sekali']
            ];

            $comment = $comments[$rating][array_rand($comments[$rating])];

            $reviews[] = [
                'user_id' => $userId,
                'product_id' => $productId,
                'order_id' => $orderId,
                'rating' => $rating,
                'review_text' => $comment,
                'status' => 'approved',
                'created_at' => now()->subDays(rand(1, 30))->subHours(rand(1, 24))->subMinutes(rand(1, 60)),
                'updated_at' => now(),
            ];
        }

        // Insert semua ulasan
        DB::table('reviews')->insert($reviews);

        $this->command->info('Berhasil membuat ' . $totalReviews . ' ulasan dummy dengan rating bervariasi.');
    }
}