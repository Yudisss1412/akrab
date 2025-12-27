<?php
// Script untuk mensimulasikan permintaan ke API /api/reviews

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Review;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulasikan user ID 13 sedang login
Auth::loginUsingId(13);

// Ambil user yang sedang login
$currentUser = Auth::user();
echo "User yang sedang login: ID=" . $currentUser->id . ", Name=" . $currentUser->name . "\n";

// Ambil ulasan untuk user ini seperti yang dilakukan di route
$reviews = Review::with(['product', 'product.seller'])
    ->where('user_id', Auth::id())
    ->latest()
    ->get();

echo "Jumlah ulasan ditemukan untuk user ini: " . $reviews->count() . "\n";

// Format ulasan seperti yang dilakukan di route
$formattedReviews = $reviews->map(function($review) {
    $product = $review->product;
    $seller = $product ? $product->seller : null;
    
    echo "Processing review ID: " . $review->id . "\n";
    echo "  Product: " . ($product ? $product->name : 'NULL') . "\n";
    echo "  Seller: " . ($seller ? $seller->name : 'NULL') . "\n";
    echo "  Product main_image: " . ($product && isset($product->main_image) ? $product->main_image : 'NULL') . "\n";
    
    return [
        'id' => $review->id,
        'product_name' => $product ? $product->name : 'Produk Tidak Ditemukan',
        'shop_name' => $seller ? $seller->name : ($product ? $product->seller_name : 'Toko Tidak Diketahui'),
        'product_image' => $product && $product->main_image ? 'STORAGE_PATH/' . $product->main_image : 'PLACEHOLDER_PATH',
        'rating' => $review->rating,
        'review_text' => $review->review_text,
        'media' => $review->media ? array_map(function($path) {
            return 'STORAGE_PATH/' . $path;
        }, (array)$review->media) : null,
        'created_at' => $review->created_at->format('d M Y')
    ];
});

echo "\nFormatted reviews data:\n";
foreach ($formattedReviews as $reviewData) {
    echo "- ID: " . $reviewData['id'] . "\n";
    echo "  Product Name: " . $reviewData['product_name'] . "\n";
    echo "  Shop Name: " . $reviewData['shop_name'] . "\n";
    echo "  Rating: " . $reviewData['rating'] . "\n";
    echo "  Review Text: " . $reviewData['review_text'] . "\n";
    echo "  ---\n";
}

echo "\nTotal formatted reviews: " . count($formattedReviews) . "\n";