<?php
// Script untuk menguji endpoint /api/user-reviews

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Auth;
use App\Models\Review;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulasikan user ID 13 sedang login
Auth::loginUsingId(13);

// Ambil data seperti di method getUserReviews
$reviews = Review::with(['product', 'order'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

echo "User ID yang sedang login: " . Auth::id() . "\n";
echo "Jumlah ulasan ditemukan: " . $reviews->count() . "\n";

$formattedReviews = $reviews->map(function($review) {
    echo "Processing review ID: " . $review->id . "\n";
    echo "  Product: " . ($review->product ? $review->product->name : 'NULL') . "\n";
    echo "  Media: " . ($review->media ? 'Ada (' . count((array)$review->media) . ' file)' : 'Tidak ada') . "\n";
    
    return [
        'id' => $review->id,
        'timeISO' => $review->created_at->toISOString(),
        'rating' => $review->rating,
        'kv' => $review->review_text ? [['Ulasan', $review->review_text]] : [],
        'product' => [
            'title' => $review->product->name ?? 'Produk Tidak Ditemukan',
            'variant' => '', // Jika ada varian bisa ditambahkan
            'url' => $review->product ? route('produk.detail', $review->product->id) : '#'
        ],
        'images' => $review->media ? collect((array)$review->media)->map(function($path) {
            return [
                'name' => basename($path),
                'size' => 0, // Tidak ada info ukuran dari storage
                'type' => 'image',
                'url' => asset('storage/' . $path)
            ];
        })->toArray() : []
    ];
});

$user = [
    'name' => Auth::user()->name
];

$response = [
    'success' => true,
    'user' => $user,
    'reviews' => $formattedReviews
];

echo "\nData yang akan dikembalikan oleh API /api/user-reviews:\n";
foreach ($response['reviews'] as $reviewData) {
    echo "- ID: " . $reviewData['id'] . "\n";
    echo "  Product Title: " . $reviewData['product']['title'] . "\n";
    echo "  Rating: " . $reviewData['rating'] . "\n";
    echo "  Review Text: " . (isset($reviewData['kv'][0][1]) ? $reviewData['kv'][0][1] : 'Tidak ada') . "\n";
    echo "  Images: " . (count($reviewData['images']) > 0 ? count($reviewData['images']) . ' gambar' : 'Tidak ada') . "\n";
    echo "  ---\n";
}

echo "\nJumlah ulasan dalam response: " . count($response['reviews']) . "\n";
echo "Response JSON:\n";
echo json_encode($response, JSON_PRETTY_PRINT);