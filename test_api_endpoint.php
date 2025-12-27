<?php
// Script untuk menguji endpoint /api/reviews

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulasikan request ke endpoint /api/reviews
// Kita akan panggil fungsi yang sama seperti di route

// Simulasikan user ID 13 sedang login
Auth::loginUsingId(13);

// Ambil data seperti di route
$userId = auth()->id();
echo "User ID yang sedang login: " . $userId . "\n";

$reviews = \App\Models\Review::with(['product', 'product.seller'])
    ->where('user_id', $userId)
    ->latest()
    ->get();

echo "Jumlah ulasan ditemukan: " . $reviews->count() . "\n";

$formattedReviews = $reviews->map(function($review) {
    $product = $review->product;
    $seller = $product ? $product->seller : null;
    
    return [
        'id' => $review->id,
        'product_name' => $product ? $product->name : 'Produk Tidak Ditemukan',
        'shop_name' => $seller ? $seller->store_name : ($product ? $product->seller_name : 'Toko Tidak Diketahui'),
        'product_image' => $product && $product->main_image ? asset('storage/' . $product->main_image) : asset('src/placeholder_produk.png'),
        'rating' => $review->rating,
        'review_text' => $review->review_text,
        'media' => $review->media ? array_map(function($path) {
            return asset('storage/' . $path);
        }, (array)$review->media) : null,
        'created_at' => $review->created_at->format('d M Y')
    ];
});

echo "\nData yang akan dikembalikan oleh API:\n";
foreach ($formattedReviews as $reviewData) {
    echo "- ID: " . $reviewData['id'] . "\n";
    echo "  Product Name: " . $reviewData['product_name'] . "\n";
    echo "  Shop Name: " . $reviewData['shop_name'] . "\n";
    echo "  Rating: " . $reviewData['rating'] . "\n";
    echo "  Review Text: " . $reviewData['review_text'] . "\n";
    echo "  Created At: " . $reviewData['created_at'] . "\n";
    echo "  Media: " . ($reviewData['media'] ? 'Ada (' . count($reviewData['media']) . ' file)' : 'Tidak ada') . "\n";
    echo "  Product Image: " . $reviewData['product_image'] . "\n";
    echo "  ---\n";
}

// Buat response seperti yang dilakukan di route
$response = ['reviews' => $formattedReviews];
echo "\nJumlah ulasan dalam response: " . count($response['reviews']) . "\n";
echo "Response JSON:\n";
echo json_encode($response, JSON_PRETTY_PRINT);