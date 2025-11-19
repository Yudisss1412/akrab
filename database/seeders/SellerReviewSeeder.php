<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Seller;

class SellerReviewSeeder extends Seeder
{
    /**
     * Run the database seeds for a specific seller.
     *
     * @return void
     */
    public function run()
    {
        // Hapus data lama jika ada
        Review::truncate();

        // Ambil penjual pertama dan produknya
        $seller = Seller::first();
        if (!$seller) {
            $this->command->info('Tidak ada penjual ditemukan, tidak bisa membuat review dummy.');
            return;
        }

        // Ambil produk dari penjual
        $products = $seller->products;
        $users = User::where('role_id', '!=', 1)->get(); // Ambil user non-admin
        $orders = Order::all();

        if ($products->count() == 0) {
            $this->command->info('Penjual tidak memiliki produk, tidak bisa membuat review dummy.');
            return;
        }

        if ($users->count() == 0 || $orders->count() == 0) {
            $this->command->info('Tidak cukup user atau order untuk membuat review dummy.');
            return;
        }

        // Ambil beberapa order untuk digunakan dalam review
        $orderIds = $orders->pluck('id')->toArray();

        // Buat review dummy untuk produk penjual
        $reviews = [];
        
        // Buat 4 review untuk tiap rating (1-5), total 20 review
        $reviewCounter = 0;
        foreach ([1, 2, 3, 4, 5] as $rating) {
            foreach (range(1, 4) as $i) { // 4 review per rating
                $productId = $products[$reviewCounter % $products->count()]->id;
                $userId = $users[$reviewCounter % $users->count()]->id;
                $orderId = $orderIds[$reviewCounter % count($orderIds)];
                
                $reviews[] = [
                    'product_id' => $productId,
                    'user_id' => $userId,
                    'order_id' => $orderId,
                    'rating' => $rating,
                    'review_text' => match($rating) {
                        1 => 'Sangat buruk, tidak sesuai harapan. Kualitasnya jelek sekali. (' . ($i) . ')',
                        2 => 'Kurang memuaskan, ada beberapa kekurangan. (' . ($i) . ')',
                        3 => 'Biasa saja, lumayan lah. (' . ($i) . ')',
                        4 => 'Bagus, cukup memuaskan. (' . ($i) . ')',
                        5 => 'Sangat bagus! Kualitasnya mantap. (' . ($i) . ')',
                        default => 'Review dengan rating ' . $rating . ' (' . ($i) . ')'
                    },
                    'status' => 'approved',
                    'reply' => $i % 2 === 0 ? 'Terima kasih atas ulasannya!' : null,
                    'replied_at' => $i % 2 === 0 ? now()->subDays($reviewCounter) : null,
                    'created_at' => now()->subDays($reviewCounter),
                    'updated_at' => now()->subDays($reviewCounter)
                ];
                
                $reviewCounter++;
            }
        }

        foreach ($reviews as $review) {
            Review::create($review);
        }

        $this->command->info('Berhasil membuat ' . count($reviews) . ' review dummy untuk produk penjual.');
    }
}