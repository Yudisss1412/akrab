<?php
// Script untuk memeriksa relasi produk dan seller

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Review;
use App\Models\Product;
use App\Models\Seller;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Ambil ulasan untuk user ID 13
$reviews = Review::with(['product', 'product.seller'])
    ->where('user_id', 13)
    ->latest()
    ->get();

echo "Jumlah ulasan ditemukan: " . $reviews->count() . "\n";

foreach ($reviews as $review) {
    echo "\nReview ID: " . $review->id . "\n";
    echo "Product ID: " . $review->product_id . "\n";
    
    if ($review->product) {
        echo "  Product Name: " . $review->product->name . "\n";
        echo "  Product Seller ID: " . $review->product->seller_id . "\n";
        
        if ($review->product->seller) {
            echo "  Seller Name: " . $review->product->seller->name . "\n";
            echo "  Seller Store Name: " . $review->product->seller->store_name . "\n";
        } else {
            echo "  Seller: NULL (Tidak ditemukan)\n";
            
            // Cek apakah seller_id valid di database
            $seller = Seller::find($review->product->seller_id);
            if ($seller) {
                echo "  Seller ditemukan di database: " . $seller->name . "\n";
            } else {
                echo "  Seller TIDAK ditemukan di database dengan ID: " . $review->product->seller_id . "\n";
            }
        }
    } else {
        echo "  Product: NULL (Tidak ditemukan)\n";
    }
}