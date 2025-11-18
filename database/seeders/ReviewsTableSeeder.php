<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Seller;
use Carbon\Carbon;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil buyer users untuk membuat review (hanya pengguna dengan role buyer)
        $buyers = User::whereHas('role', function($query) {
            $query->where('name', 'buyer');
        })->get();

        // Ambil semua produk yang tersedia
        $products = Product::all();

        if ($buyers->count() === 0) {
            $this->command->info('No buyer users found, calling UserSeeder to create buyers...');
            $this->call(UserSeeder::class);
            $buyers = User::whereHas('role', function($query) {
                $query->where('name', 'buyer');
            })->get();
        }

        if ($products->count() === 0) {
            $this->command->info('No products found, calling ProductSeeder to create products...');
            $this->call(ProductSeeder::class);
            $products = Product::all();
        }

        // Jika tetap tidak ada buyers atau products, keluar dari seeder
        if ($buyers->count() === 0 || $products->count() === 0) {
            $this->command->error('Buyers or products still not available. Cannot create reviews.');
            return;
        }

        // Buat beberapa review dummy untuk testing dari buyer yang sebenarnya
        $reviewTexts = [
            'Produk ini sangat bagus! Kualitasnya sesuai dengan deskripsi. Pengiriman cepat dan kemasan rapi.',
            'Kualitas produk sesuai dengan harapan saya. Harga terjangkau dan pengiriman cepat.',
            'Pengalaman belanja yang menyenangkan. Produk sampai dalam kondisi baik.',
            'Sangat puas dengan pembelian ini. Akan beli lagi lain waktu.',
            'Barangnya bagus dan sesuai gambar. Pelayanan toko juga ramah.',
            'Harga sesuai dengan kualitas. Produk yang bagus dengan harga terjangkau.',
            'Kemasan rapi dan produk asli. Rekomendasi sekali.',
            'Tidak mengecewakan. Kualitas bagus dan pengiriman cepat sekali!',
            'Produknya memenuhi ekspektasi. Kualitas mantap!',
            'Sangat senang dengan pembelian ini. Akan menjadi pelanggan tetap.',
            'Sesuai dengan deskripsi. Pengemasan rapi dan aman.',
            'Barang bagus, kualitas oke untuk harga segini.',
            'Pengiriman cepat sekali, barang juga sesuai pesanan.',
            'Bagus banget! Bahan berkualitas dan jahitan rapi.',
            'Sangat recomend sekali deh pokoknya.',
        ];

        // Kumpulkan semua order yang dibuat oleh buyer
        $allOrders = collect();
        foreach ($buyers as $buyer) {
            $buyerOrders = Order::where('user_id', $buyer->id)->get();
            $allOrders = $allOrders->concat($buyerOrders);
        }

        // Set untuk melacak kombinasi user_id, product_id, order_id yang sudah dibuat dalam satu seeder
        $createdReviews = [];

        // Loop melalui setiap buyer untuk membuat review
        foreach ($buyers as $buyer) {
            // Ambil order yang dibuat oleh buyer ini
            $buyerOrders = Order::where('user_id', $buyer->id)->get();

            // Ambil produk acak yang dibeli dalam order
            foreach ($buyerOrders as $order) {
                // Ambil semua item dalam order
                $orderItems = $order->items;

                foreach ($orderItems as $orderItem) {
                    // Pilih produk dari order item
                    $product = $orderItem->product;

                    // Pastikan produk ditemukan dan seller memiliki produk ini
                    if ($product && $product->seller) {
                        // Cek apakah kombinasi user_id, product_id, order_id sudah dibuat di seeder ini
                        $reviewKey = $buyer->id . '-' . $product->id . '-' . $order->id;

                        // Juga cek di database
                        $existingReview = Review::where('user_id', $buyer->id)
                                              ->where('product_id', $product->id)
                                              ->where('order_id', $order->id)
                                              ->first();

                        if (!$existingReview && !in_array($reviewKey, $createdReviews)) {
                            $createdReviews[] = $reviewKey;

                            // Buat sebagian review dengan reply
                            $hasReply = rand(1, 3) === 1; // ~33% chance untuk punya reply
                            $replyText = null;
                            $repliedAt = null;

                            if ($hasReply) {
                                $replyOptions = [
                                    'Terima kasih atas ulasan dan feedback yang sangat positif. Kami sangat menghargai pengalaman Anda!',
                                    'Kami senang Anda menyukai produk kami. Silakan berkunjung kembali ke toko kami.',
                                    'Terima kasih atas kepercayaan Anda. Kami akan terus meningkatkan kualitas layanan.',
                                    'Terima kasih atas kepercayaan Anda. Kami selalu berusaha memberikan yang terbaik.',
                                    'Kami sangat menghargai masukan Anda, dan akan terus berusaha memperbaiki kualitas produk dan layanan kami.'
                                ];
                                $replyText = $replyOptions[array_rand($replyOptions)];
                                $repliedAt = Carbon::now()->subDays(rand(0, 15));
                            }

                            // Buat distribusi rating yang lebih merata
                            $rating = rand(1, 5);

                            // Pastikan status valid
                            $statusOptions = ['pending', 'approved', 'rejected'];
                            $status = $statusOptions[array_rand($statusOptions)];

                            Review::create([
                                'user_id' => $buyer->id,
                                'product_id' => $product->id,
                                'order_id' => $order->id, // Gunakan order_id yang valid dari buyer ini
                                'rating' => $rating,
                                'review_text' => $reviewTexts[array_rand($reviewTexts)],
                                'status' => $status,
                                'media' => null, // No media for basic reviews
                                'reply' => $replyText,
                                'replied_at' => $repliedAt,
                                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                                'updated_at' => Carbon::now()->subDays(rand(0, 30))
                            ]);
                        }
                    }
                }
            }

            // Jika buyer tidak memiliki order, buat beberapa review manual
            if ($buyerOrders->count() === 0) {
                // Ambil produk dari seller acak
                $randomProducts = $products->random(min(rand(1, 3), $products->count()));

                foreach ($randomProducts as $product) {
                    // Pastikan produk ditemkan dan seller memiliki produk ini
                    if ($product && $product->seller) {
                        // Ambil order dari buyer ini sebagai referensi
                        $order = $allOrders->first();

                        if ($order) {
                            // Cek apakah kombinasi user_id, product_id, order_id sudah dibuat di seeder ini
                            $reviewKey = $buyer->id . '-' . $product->id . '-' . $order->id;

                            // Juga cek di database
                            $existingReview = Review::where('user_id', $buyer->id)
                                                  ->where('product_id', $product->id)
                                                  ->where('order_id', $order->id)
                                                  ->first();

                            if (!$existingReview && !in_array($reviewKey, $createdReviews)) {
                                $createdReviews[] = $reviewKey;

                                // Buat sebagian review dengan reply
                                $hasReply = rand(1, 3) === 1; // ~33% chance untuk punya reply
                                $replyText = null;
                                $repliedAt = null;

                                if ($hasReply) {
                                    $replyOptions = [
                                        'Terima kasih atas ulasan dan feedback yang sangat positif. Kami sangat menghargai pengalaman Anda!',
                                        'Kami senang Anda menyukai produk kami. Silakan berkunjung kembali ke toko kami.',
                                        'Terima kasih atas kepercayaan Anda. Kami akan terus meningkatkan kualitas layanan.',
                                        'Terima kasih atas kepercayaan Anda. Kami selalu berusaha memberikan yang terbaik.',
                                        'Kami sangat menghargai masukan Anda, dan akan terus berusaha memperbaiki kualitas produk dan layanan kami.'
                                    ];
                                    $replyText = $replyOptions[array_rand($replyOptions)];
                                    $repliedAt = Carbon::now()->subDays(rand(0, 15));
                                }

                                Review::create([
                                    'user_id' => $buyer->id,
                                    'product_id' => $product->id,
                                    'order_id' => $order->id, // Gunakan order_id dari order yang tersedia
                                    'rating' => rand(1, 5),
                                    'review_text' => $reviewTexts[array_rand($reviewTexts)],
                                    'status' => ['pending', 'approved', 'rejected'][array_rand(['pending', 'approved', 'rejected'])],
                                    'media' => null, // No media for basic reviews
                                    'reply' => $replyText,
                                    'replied_at' => $repliedAt,
                                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                                    'updated_at' => Carbon::now()->subDays(rand(0, 30))
                                ]);
                            }
                        }
                    }
                }
            }
        }

        // Sekarang tambahkan review secara merata ke semua seller untuk memastikan filtering berfungsi
        // Kita akan membuat beberapa review untuk setiap penjual dengan berbagai kombinasi
        $allSellers = Seller::all();
        $replyOptions = [
            'Terima kasih atas ulasan dan feedback yang sangat positif. Kami sangat menghargai pengalaman Anda!',
            'Kami senang Anda menyukai produk kami. Silakan berkunjung kembali ke toko kami.',
            'Terima kasih atas kepercayaan Anda. Kami akan terus meningkatkan kualitas layanan.',
            'Terima kasih atas kepercayaan Anda. Kami selalu berusaha memberikan yang terbaik.',
            'Kami sangat menghargai masukan Anda, dan akan terus berusaha memperbaiki kualitas produk dan layanan kami.'
        ];

        foreach ($allSellers as $seller) {
            // Ambil produk dari seller ini
            $sellerProducts = $seller->products;

            foreach ($sellerProducts as $product) {
                // Buat beberapa review untuk produk ini dari berbagai buyer
                for ($i = 0; $i < 3; $i++) {  // 3 review per produk
                    $buyer = $buyers->random();
                    $order = $allOrders->random(); // Gunakan order yang ada

                    // Buat kombinasi yang mencakup semua skenario filtering
                    $rating = ($i % 5) + 1; // Rating 1-5 berputar
                    $hasReply = ($i % 3) === 0; // ~33% memiliki balasan
                    $status = ['pending', 'approved', 'rejected'][$i % 3];

                    $reviewKey = $buyer->id . '-' . $product->id . '-' . $order->id;

                    // Cek jika sudah ada
                    $existingReview = Review::where('user_id', $buyer->id)
                                          ->where('product_id', $product->id)
                                          ->where('order_id', $order->id)
                                          ->first();

                    if (!$existingReview && !in_array($reviewKey, $createdReviews)) {
                        $createdReviews[] = $reviewKey;

                        $replyText = null;
                        $repliedAt = null;

                        if ($hasReply) {
                            $replyText = $replyOptions[array_rand($replyOptions)];
                            $repliedAt = Carbon::now()->subDays(rand(1, 15));
                        }

                        Review::create([
                            'user_id' => $buyer->id,
                            'product_id' => $product->id,
                            'order_id' => $order->id,
                            'rating' => $rating,
                            'review_text' => $reviewTexts[array_rand($reviewTexts)],
                            'status' => $status,
                            'media' => null,
                            'reply' => $replyText,
                            'replied_at' => $repliedAt,
                            'created_at' => Carbon::now()->subDays(rand(1, 30)),
                            'updated_at' => Carbon::now()->subDays(rand(0, 30))
                        ]);
                    }
                }
            }
        }
    }
}