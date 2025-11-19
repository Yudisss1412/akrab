<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Seller;
use App\Models\Product;
use App\Models\User;

class RudiFirmansyahReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find Rudi Firmansyah's seller record
        $seller = Seller::where('owner_name', 'Rudi Firmansyah')->first();

        if (!$seller) {
            $this->command->error('Rudi Firmansyah seller not found');
            return;
        }

        // Get Rudi Firmansyah's products
        $products = $seller->products;

        if ($products->count() === 0) {
            $this->command->error('No products found for Rudi Firmansyah');
            return;
        }

        // Get some user IDs for the reviews (using existing users)
        $users = User::whereHas('role', function($query) {
            $query->where('name', 'buyer');
        })->get();

        if ($users->count() === 0) {
            $this->command->error('No buyer users found for reviews');
            return;
        }

        // Get some order IDs for the reviews (using existing orders from the users)
        $orderIds = \App\Models\Order::whereIn('user_id', $users->pluck('id'))->pluck('id')->toArray();

        if (empty($orderIds)) {
            // Create dummy orders if no orders exist
            foreach ($users as $user) {
                $order = \App\Models\Order::create([
                    'user_id' => $user->id,
                    'order_number' => 'ORD-' . time() . rand(1000, 9999),
                    'total_amount' => rand(50000, 500000),
                    'status' => 'completed',
                    'payment_status' => 'paid'
                ]);
                $orderIds[] = $order->id;
            }
        }

        // Prepare review data
        $reviews = [
            // 3 negative reviews (rating 1 or 2)
            [
                'user_id' => $users->random()->id,
                'product_id' => $products->random()->id,
                'order_id' => collect($orderIds)->random(),
                'rating' => 1,
                'review_text' => 'Produk tidak sesuai ekspektasi, kualitasnya buruk dan tidak tahan lama. Tidak merekomendasikan produk ini.',
                'status' => 'approved',
            ],
            [
                'user_id' => $users->random()->id,
                'product_id' => $products->random()->id,
                'order_id' => collect($orderIds)->random(),
                'rating' => 2,
                'review_text' => 'Saya kecewa dengan pembelian ini. Produknya tidak sebagus yang digambarkan, dan kemasan juga kurang rapi.',
                'status' => 'approved',
            ],
            [
                'user_id' => $users->random()->id,
                'product_id' => $products->random()->id,
                'order_id' => collect($orderIds)->random(),
                'rating' => 1,
                'review_text' => 'Harga tidak sesuai dengan kualitas. Produk cepat rusak dan layanan pelanggan tidak membantu ketika saya komplain.',
                'status' => 'approved',
            ],
            // 1 neutral review (rating 3)
            [
                'user_id' => $users->random()->id,
                'product_id' => $products->random()->id,
                'order_id' => collect($orderIds)->random(),
                'rating' => 3,
                'review_text' => 'Produk biasa-biasa saja. Tidak buruk tapi juga tidak istimewa. Sesuai dengan harga yang diberikan.',
                'status' => 'approved',
            ],
            // 1 positive review (rating 4 or 5)
            [
                'user_id' => $users->random()->id,
                'product_id' => $products->random()->id,
                'order_id' => collect($orderIds)->random(),
                'rating' => 5,
                'review_text' => 'Sangat puas dengan produknya! Kualitas bagus, pengiriman cepat, dan penjual ramah. Akan beli lagi di masa depan.',
                'status' => 'approved',
            ]
        ];

        // Insert the reviews
        foreach ($reviews as $reviewData) {
            $review = Review::create($reviewData);
            $this->command->info("Created review: Rating {$reviewData['rating']} for Product ID {$reviewData['product_id']}");
        }

        $this->command->info('Rudi Firmansyah reviews created successfully!');
    }
}
