<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus data lama jika ada
        Review::truncate();

        // Ambil produk, user, dan order untuk membuat review dummy
        $products = Product::all();
        $users = User::all();
        $orders = Order::all();

        if ($products->count() == 0 || $users->count() == 0 || $orders->count() == 0) {
            $this->command->info('Tidak ada produk, user, atau order. Silakan buat minimal satu order dulu.');
            return;
        }

        // Ambil beberapa order untuk digunakan dalam review
        $orderIds = $orders->pluck('id')->toArray();

        // Ambil penjual (seller) pertama untuk mengaitkan produk mereka
        $sellers = \App\Models\Seller::all();

        // Jika ada seller, ambil produk milik salah satu seller
        if ($sellers->count() > 0) {
            $firstSeller = $sellers->first();
            $sellerProducts = $firstSeller->products()->get();

            if ($sellerProducts->count() > 0) {
                $products = $sellerProducts;
            } else {
                // Jika tidak ada produk milik seller, ambil produk pertama dan tambahkan ke seller
                $firstProduct = $products->first();
                $firstProduct->seller_id = $firstSeller->id;
                $firstProduct->save();
                $products = collect([$firstProduct]);
            }
        }

        // Buat review dummy untuk testing filter
        $reviews = [];

        // Tambahkan review untuk produk yang dipilih (hingga 12 review)
        foreach (range(1, 12) as $index) {
            $rating = [1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 5, 1][$index - 1]; // Distribusi rating
            $productId = $products[$index % $products->count()]->id ?? $products->first()->id;
            $userId = $users[$index % $users->count()]->id ?? $users->first()->id;
            $orderId = $orderIds[$index % count($orderIds)] ?? $orderIds[array_key_first($orderIds)];

            $reviews[] = [
                'product_id' => $productId,
                'user_id' => $userId,
                'order_id' => $orderId,
                'rating' => $rating,
                'review_text' => match($rating) {
                    1 => 'Sangat buruk, tidak sesuai harapan. Kualitasnya jelek sekali.',
                    2 => 'Lumayan buruk, ada beberapa kekurangan.',
                    3 => 'Biasa saja, ada bagusnya ada jeleknya.',
                    4 => 'Bagus, tapi masih ada sedikit kekurangan.',
                    5 => 'Sangat bagus! Kualitasnya mantap, pengiriman cepat.',
                    default => 'Review dengan rating ' . $rating
                } . ' (' . ($index + 1) . ')',
                'status' => 'approved',
                'reply' => $index % 3 === 0 ? 'Terima kasih atas ulasannya!' : null,
                'replied_at' => $index % 3 === 0 ? now()->subDays($index) : null,
                'created_at' => now()->subDays($index * 2),
                'updated_at' => now()->subDays($index * 2)
            ];
        }

        foreach ($reviews as $review) {
            Review::create($review);
        }

        $this->command->info('Berhasil membuat ' . count($reviews) . ' review dummy untuk testing filter.');
    }
}