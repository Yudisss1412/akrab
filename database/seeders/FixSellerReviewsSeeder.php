<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Seller;

class FixSellerReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds to ensure reviews are properly associated with seller products.
     *
     * @return void
     */
    public function run()
    {
        // Hapus semua review dummy terlebih dahulu
        Review::truncate();

        // Ambil semua penjual
        $sellers = Seller::all();
        
        if ($sellers->count() === 0) {
            $this->command->info('Tidak ada penjual, tidak bisa membuat review dummy.');
            return;
        }

        // Ambil semua user dan order untuk membuat review
        $users = User::where('role_id', '!=', 1)->get(); // Ambil user non-admin
        $orders = Order::all();

        if ($users->count() === 0 || $orders->count() === 0) {
            $this->command->info('Tidak cukup user atau order untuk membuat review dummy.');
            return;
        }

        $createdReviews = 0;

        // Untuk setiap penjual, buat beberapa review untuk produk mereka
        foreach ($sellers as $index => $seller) {
            $products = $seller->products;

            if ($products->count() === 0) {
                continue; // Lewati penjual ini jika tidak punya produk
            }

            // Buat review dengan kombinasi unik user-product-order
            $ratings = [1, 2, 3, 4, 5];
            foreach ($products as $product) {
                foreach ($ratings as $rating) {
                    if ($createdReviews >= 20) break 2; // Batasi total review

                    $user = $users->random();
                    $order = $orders->random();

                    Review::create([
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'rating' => $rating,
                        'review_text' => match($rating) {
                            1 => 'Sangat buruk, tidak sesuai harapan. Kualitasnya jelek sekali.',
                            2 => 'Kurang memuaskan, ada beberapa kekurangan.',
                            3 => 'Biasa saja, lumayan lah.',
                            4 => 'Bagus, cukup memuaskan.',
                            5 => 'Sangat bagus! Kualitasnya mantap.',
                            default => 'Review dengan rating ' . $rating
                        } . " (Review produk {$product->id}, penjual {$seller->id})",
                        'status' => 'approved',
                        'reply' => ($rating <= 2) ? 'Kami mohon maaf atas ketidaknyamanannya.' : null,
                        'replied_at' => ($rating <= 2) ? now()->subDays(rand(1, 10)) : null,
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now()->subDays(rand(0, 2))
                    ]);

                    $createdReviews++;
                }
            }
        }

        $this->command->info("Berhasil membuat {$createdReviews} review dummy yang terkait dengan produk penjual.");
    }
}