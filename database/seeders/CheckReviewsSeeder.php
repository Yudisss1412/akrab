<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;

class CheckReviewsSeeder extends Seeder
{
    public function run()
    {
        echo "Total reviews in database: " . Review::count() . "\n";
        
        $reviews = Review::with(['product', 'product.seller'])->limit(10)->get();
        
        foreach ($reviews as $review) {
            $sellerId = $review->product && $review->product->seller ? $review->product->seller->id : 'N/A';
            $productName = $review->product ? $review->product->name : 'N/A';
            
            echo "Review ID: " . $review->id . 
                 ", Product ID: " . $review->product_id . 
                 ", Product Name: " . $productName .
                 ", Seller ID: " . $sellerId . 
                 ", Rating: " . $review->rating . 
                 ", User ID: " . $review->user_id . "\n";
        }
    }
}