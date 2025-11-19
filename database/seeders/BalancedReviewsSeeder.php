<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Seller;

class BalancedReviewsSeeder extends Seeder
{
    /**
     * Create reviews distributed among all sellers.
     *
     * @return void
     */
    public function run()
    {
        // Hapus semua review dummy terlebih dahulu
        Review::truncate();

        // Ambil semua penjual dan pastikan masing-masing punya produk
        $sellers = Seller::with('products')->get();
        
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

        $orderIds = $orders->pluck('id')->toArray();
        $createdReviews = 0;
        $ratings = [1, 2, 3, 4, 5];

        // Buat sekitar 20 reviews secara seimbang di antara penjual
        $reviewsPerSeller = intval(20 / max(1, $sellers->count()));
        
        foreach ($sellers as $seller) {
            $products = $seller->products;
            
            if ($products->count() === 0) {
                continue; // Lewati penjual ini jika tidak punya produk
            }

            $reviewsForThisSeller = 0;
            
            // Buat beberapa review untuk produk produk milik penjual ini
            foreach ($products as $product) {
                if ($reviewsForThisSeller >= $reviewsPerSeller || $createdReviews >= 20) {
                    break;
                }

                foreach ($ratings as $rating) {
                    if ($reviewsForThisSeller >= $reviewsPerSeller || $createdReviews >= 20) {
                        break;
                    }

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
                        'reply' => ($rating <= 2) ? 'Kami mohon maaf atas ketidaknyamanannya.' : (($rating >= 4) ? 'Terima kasih atas kepercayaannya!' : null),
                        'replied_at' => ($rating <= 2) ? now()->subDays(rand(1, 10)) : (($rating >= 4) ? now()->subDays(rand(1, 5)) : null),
                        'created_at' => now()->subDays(rand(1, 30)),
                        'updated_at' => now()->subDays(rand(0, 2))
                    ]);
                    
                    $createdReviews++;
                    $reviewsForThisSeller++;
                }
            }
        }

        $this->command->info("Berhasil membuat {$createdReviews} review dummy yang terkait dengan produk dari berbagai penjual.");
    }
}