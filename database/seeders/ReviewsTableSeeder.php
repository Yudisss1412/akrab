<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user, product, dan order untuk membuat review
        $users = User::all();
        $products = Product::all();
        $orders = Order::all();
        
        foreach ($users as $user) {
            foreach ($products as $product) {
                // Hanya buat review jika ada order yang cocok
                $order = $orders->where('user_id', $user->id)->first();
                
                if ($order) {
                    // Cek apakah item produk ada di order
                    $orderItemExists = $order->items()->where('product_id', $product->id)->exists();
                    
                    if ($orderItemExists) {
                        // Hanya buat review jika belum ada
                        $existingReview = Review::where('user_id', $user->id)
                                              ->where('product_id', $product->id)
                                              ->where('order_id', $order->id)
                                              ->first();
                        
                        if (!$existingReview) {
                            Review::create([
                                'user_id' => $user->id,
                                'product_id' => $product->id,
                                'order_id' => $order->id,
                                'rating' => rand(1, 5),
                                'review_text' => 'Produk ini sangat bagus! Kualitasnya sesuai dengan deskripsi. Pengiriman cepat dan kemasan rapi.',
                                'status' => 'approved'
                            ]);
                        }
                    }
                }
            }
        }
    }
}
