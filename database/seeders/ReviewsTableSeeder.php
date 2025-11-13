<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user dan product untuk membuat review
        $users = User::limit(10)->get(); // Ambil 10 user pertama
        $products = Product::limit(20)->get(); // Ambil 20 produk pertama

        // Buat beberapa review dummy untuk testing
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
            'Sangat senang dengan pembelian ini. Akan menjadi pelanggan tetap.'
        ];

        foreach ($users as $user) {
            foreach ($products->random(3) as $product) { // Pilih 3 produk random per user
                $order = Order::where('user_id', $user->id)->first();

                // Jika tidak ada order, buat order dummy atau gunakan order_id null
                if (!$order) {
                    // Dapatkan order_id dari order yang ada atau buat order dummy
                    $existingOrder = Order::first();
                    $orderId = $existingOrder ? $existingOrder->id : 1; // Gunakan order_id 1 jika tidak ada
                } else {
                    $orderId = $order->id;
                }

                // Hanya buat review jika belum ada
                $existingReview = Review::where('user_id', $user->id)
                                      ->where('product_id', $product->id)
                                      ->first();

                if (!$existingReview) {
                    Review::create([
                        'user_id' => $user->id,
                        'product_id' => $product->id,
                        'order_id' => $orderId, // Gunakan order_id yang valid
                        'rating' => rand(1, 5),
                        'review_text' => $reviewTexts[array_rand($reviewTexts)],
                        'status' => ['pending', 'approved', 'rejected'][array_rand(['pending', 'approved', 'rejected'])],
                        'media' => null, // No media for basic reviews
                        'created_at' => Carbon::now()->subDays(rand(1, 30)),
                        'updated_at' => Carbon::now()->subDays(rand(0, 30))
                    ]);
                }
            }
        }

        // Juga tambahkan beberapa review dengan media dan reply
        $users = User::limit(5)->get();
        $products = Product::limit(10)->get();

        $reviewWithMediaAndReply = [
            [
                'text' => 'Produk luar biasa! Kualitas terbaik dan pengiriman super cepat. Saya sangat merekomendasikan produk ini.',
                'media' => ['images/review_images/review1.jpg', 'images/review_images/review2.jpg'],
                'reply' => 'Terima kasih atas ulasan dan feedback yang sangat positif. Kami sangat menghargai pengalaman Anda!',
                'replied_at' => Carbon::now()->subDays(rand(1, 15))
            ],
            [
                'text' => 'Kualitas produk sangat memuaskan. Harga terjangkau dengan hasil yang memuaskan.',
                'media' => ['images/review_images/review3.jpg'],
                'reply' => 'Kami senang Anda menyukai produk kami. Silakan berkunjung kembali ke toko kami.',
                'replied_at' => Carbon::now()->subDays(rand(1, 10))
            ],
            [
                'text' => 'Pelayanan pelanggan sangat baik. Produk juga sesuai deskripsi dan kemasan rapi.',
                'media' => ['images/review_images/review4.jpg', 'images/review_images/review5.jpg', 'images/review_images/review6.jpg'],
                'reply' => 'Terima kasih atas kepercayaan Anda. Kami akan terus meningkatkan kualitas layanan.',
                'replied_at' => Carbon::now()->subDays(rand(1, 7))
            ]
        ];

        foreach ($users as $user) {
            foreach ($products->random(2) as $product) {
                $order = Order::first();
                $orderId = $order ? $order->id : 1;

                $reviewData = $reviewWithMediaAndReply[array_rand($reviewWithMediaAndReply)];

                Review::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'order_id' => $orderId,
                    'rating' => rand(4, 5), // Rating tinggi untuk review dengan media
                    'review_text' => $reviewData['text'],
                    'status' => 'approved',
                    'media' => json_encode($reviewData['media']),
                    'reply' => $reviewData['reply'],
                    'replied_at' => $reviewData['replied_at'],
                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
    }
}
