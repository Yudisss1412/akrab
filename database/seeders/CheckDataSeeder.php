<?php

use Illuminate\Database\Seeder;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Review;

class CheckDataSeeder extends Seeder
{
    public function run()
    {
        echo "Seller count: " . Seller::count() . "\n";
        echo "Product count: " . Product::count() . "\n";
        echo "Review count: " . Review::count() . "\n";
        
        $seller = Seller::first();
        if ($seller) {
            echo "First seller id: " . $seller->id . "\n";
            echo "First seller products count: " . $seller->products()->count() . "\n";
            echo "First seller user id: " . $seller->user_id . "\n";
            
            // Cek produk milik seller ini
            $products = $seller->products;
            echo "Product IDs for this seller: ";
            foreach ($products as $product) {
                echo $product->id . " ";
            }
            echo "\n";
            
            // Cek review untuk produk ini
            $productIds = $products->pluck('id')->toArray();
            $reviews = Review::whereIn('product_id', $productIds)->get();
            echo "Reviews count for this seller's products: " . $reviews->count() . "\n";
            
            foreach ($reviews as $review) {
                echo "Review ID: " . $review->id . ", Product ID: " . $review->product_id . ", Rating: " . $review->rating . "\n";
            }
        } else {
            echo "No seller found\n";
        }
    }
}