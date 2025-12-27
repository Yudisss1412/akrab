<?php
// Script sederhana untuk memeriksa data ulasan di database

require_once 'vendor/autoload.php';

// Set up Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Review;

// Ambil semua ulasan
$reviews = Review::with(['user', 'product'])->get();

echo "Jumlah total ulasan di database: " . $reviews->count() . "\n";

if ($reviews->count() > 0) {
    echo "\nDetail ulasan:\n";
    foreach ($reviews as $review) {
        echo "- ID: " . $review->id . "\n";
        echo "  User ID: " . $review->user_id . "\n";
        echo "  User Name: " . ($review->user ? $review->user->name : 'User tidak ditemukan') . "\n";
        echo "  Product ID: " . $review->product_id . "\n";
        echo "  Product Name: " . ($review->product ? $review->product->name : 'Produk tidak ditemukan') . "\n";
        echo "  Rating: " . $review->rating . "\n";
        echo "  Review Text: " . $review->review_text . "\n";
        echo "  Status: " . $review->status . "\n";
        echo "  Created At: " . $review->created_at . "\n";
        echo "  ---\n";
    }
} else {
    echo "Tidak ada ulasan ditemukan di database.\n";
}