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

class SellerProductReviewSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding produk dan rating khusus penjual...');

        // Pastikan semua dependensi tersedia
        $this->ensureDependencies();

        // Ambil penjual pertama
        $seller = Seller::first();
        if (!$seller) {
            $this->command->error('Tidak ada penjual ditemukan. Harap pastikan seeder penjual telah dijalankan.');
            return;
        }

        // Ambil produk-produk dari penjual ini
        $products = Product::where('seller_id', $seller->id)->get();
        
        // Jika produk kosong, buat beberapa produk dummy untuk penjual ini
        if ($products->count() === 0) {
            $this->createSellerProducts($seller);
            $products = Product::where('seller_id', $seller->id)->get();
        }

        // Ambil pengguna pembeli
        $buyers = User::whereHas('role', function($query) {
            $query->where('name', 'buyer');
        })->get();

        // Ambil pesanan
        $orders = Order::all();

        // Buat ulasan untuk produk penjual
        $this->createSellerReviews($products, $buyers, $orders);

        $this->command->info('Seeder produk dan rating khusus penjual selesai.');
        $this->command->info('Jumlah produk penjual: ' . $products->count());
        $this->command->info('Jumlah ulasan untuk produk penjual: ' . Review::whereHas('product', function($query) use ($seller) {
                                $query->where('seller_id', $seller->id);
                            })->count());
    }

    private function ensureDependencies(): void
    {
        // Pastikan penjual ada
        $sellers = Seller::all();
        if ($sellers->count() === 0) {
            $this->command->info('Menjalankan UserSeeder untuk membuat penjual...');
            $this->call(UserSeeder::class);
        }

        // Pastikan pembeli ada
        $buyers = User::whereHas('role', function($query) {
            $query->where('name', 'buyer');
        })->get();
        
        if ($buyers->count() === 0) {
            $this->command->info('Menjalankan UserSeeder untuk membuat pembeli...');
            $this->call(UserSeeder::class);
        }

        // Pastikan produk ada
        $products = Product::all();
        if ($products->count() === 0) {
            $this->command->info('Menjalankan ProductSeeder untuk membuat produk...');
            $this->call(ProductSeeder::class);
        }

        // Pastikan pesanan ada
        $orders = Order::all();
        if ($orders->count() === 0) {
            $this->command->info('Menjalankan OrderSeeder untuk membuat pesanan...');
            $this->call(OrderSeeder::class);
        }
    }

    private function createSellerProducts($seller): void
    {
        $productNames = [
            'Smartphone Terbaru',
            'Laptop Gaming',
            'Kamera Digital',
            'Headphone Wireless',
            'Tablet Premium',
            'Smart Watch',
            'Speaker Bluetooth',
            'Kabel Charger Fast',
            'Power Bank 10000mAh',
            'Case Handphone Stylish'
        ];

        $descriptions = [
            'Produk elektronik terbaru dengan kualitas premium',
            'Perangkat canggih dengan teknologi terkini',
            'Kualitas terbaik untuk kebutuhan digital',
            'Spesifikasi tinggi dengan harga terjangkau',
            'Produk orisinal bergaransi resmi'
        ];

        foreach ($productNames as $index => $name) {
            Product::create([
                'name' => $name,
                'description' => $descriptions[array_rand($descriptions)],
                'price' => rand(100000, 5000000),
                'stock' => rand(10, 100),
                'weight' => rand(100, 1000),
                'image' => 'products/product_' . ($index + 1) . '.jpg',
                'status' => 'active',
                'seller_id' => $seller->id,
                'specifications' => [
                    'Brand' => 'Generic',
                    'Model' => 'Model-' . ($index + 1),
                    'Color' => ['Hitam', 'Putih', 'Emas'][array_rand(['Hitam', 'Putih', 'Emas'])],
                    'Size' => 'Standar'
                ],
                'material' => 'Plastic / Metal',
                'size' => 'Standar',
                'color' => ['Hitam', 'Putih', 'Emas'][array_rand(['Hitam', 'Putih', 'Emas'])],
                'brand' => 'Generic Brand',
                'origin' => 'China',
                'warranty' => '1 Tahun Garansi Resmi',
                'min_order' => 1,
                'ready_stock' => rand(10, 100),
                'features' => [
                    'Kualitas terjamin',
                    'Bergaransi resmi',
                    'Original',
                    'Harga bersaing'
                ]
            ]);
        }
    }

    private function createSellerReviews($products, $buyers, $orders): void
    {
        $reviews = [];
        $totalReviews = min(50, $products->count() * 5); // Minimal 5 ulasan per produk

        $usedCombinations = [];

        for ($i = 0; $i < $totalReviews; $i++) {
            $attempt = 0;
            $validCombination = false;

            while(!$validCombination && $attempt < 20) {
                $product = $products->random();
                $buyer = $buyers->random();
                $order = $orders->random();

                // Kombinasi untuk dicek
                $combination = $buyer->id . '-' . $product->id . '-' . $order->id;

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

            $rating = rand(1, 5);

            // Komentar berdasarkan rating
            $comments = [
                1 => [
                    'Sangat buruk, tidak sesuai dengan deskripsi',
                    'Produk cacat dan tidak layak pakai',
                    'Beli di sini menyesal, tidak direkomendasikan',
                    'Barang tidak sesuai gambar, sangat mengecewakan',
                    'Kualitas murahan, tidak akan beli lagi'
                ],
                2 => [
                    'Kurang memuaskan, banyak kekurangan',
                    'Biasa saja, tidak spesial',
                    'Ada beberapa masalah kecil',
                    'Harga dan kualitas tidak seimbang',
                    'Standar lah, tidak lebih tidak kurang'
                ],
                3 => [
                    'Lumayanlah untuk harga segini',
                    'Sesuai dengan harga yang ditawarkan',
                    'Biasa aja, tidak buruk juga tidak bagus',
                    'Tidak ada yang istimewa tapi tidak buruk',
                    'Cukup memuaskan'
                ],
                4 => [
                    'Bagus, puas dengan pembelian',
                    'Kualitas oke, pengiriman cepat',
                    'Sangat recommended untuk harga segini',
                    'Banyak kelebihan, hanya sedikit kekurangan',
                    'Nyaman dipakai dan kemasan rapi'
                ],
                5 => [
                    'Sangat memuaskan, sesuai ekspektasi',
                    'Kualitas top banget, pasti beli lagi',
                    'Sempurna! Tidak menyesal sama sekali',
                    'Luar biasa, kualitas terbaik',
                    'Mantap, produk original dan berkualitas'
                ]
            ];

            $comment = $comments[$rating][array_rand($comments[$rating])];

            $reviews[] = [
                'user_id' => $buyer->id,
                'product_id' => $product->id,
                'order_id' => $order->id,
                'rating' => $rating,
                'review_text' => $comment,
                'status' => 'approved',
                'created_at' => now()->subDays(rand(1, 60))->subHours(rand(1, 24))->subMinutes(rand(1, 60)),
                'updated_at' => now(),
            ];
        }

        // Insert ulasan ke database
        if (!empty($reviews)) {
            DB::table('reviews')->insert($reviews);
            $this->command->info('Berhasil membuat ' . count($reviews) . ' ulasan untuk produk penjual.');
        }
    }
}