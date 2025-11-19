<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use App\Models\Review;
use App\Models\Product;

class DebugReviewController extends Controller
{
    public function testFilter(Request $request)
    {
        // Hanya untuk debugging
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Not authenticated']);
        }

        $seller = Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            return response()->json(['error' => 'Seller not found']);
        }

        $products = $seller->products()->pluck('id')->toArray();

        // Test filter langsung
        $filterRating = $request->get('rating');
        $filterReply = $request->get('reply');

        // Query base
        $query = Review::whereIn('product_id', $products);

        // Log informasi dasar
        $totalAll = $query->count();
        $allRatings = $query->pluck('rating')->toArray();
        $ratingDist = array_count_values($allRatings);

        $result = [
            'debug_info' => [
                'total_reviews_before_filter' => $totalAll,
                'all_ratings_distribution' => $ratingDist,
                'filter_rating' => $filterRating,
                'filter_reply' => $filterReply,
                'seller_product_count' => count($products),
                'seller_products' => $products
            ]
        ];

        // Terapkan filter rating jika ada
        if ($filterRating) {
            $query->where('rating', $filterRating);
        }

        // Terapkan filter reply jika ada
        if ($filterReply) {
            if ($filterReply === 'replied') {
                $query->whereNotNull('reply');
            } else if ($filterReply === 'pending') {
                $query->whereNull('reply');
            }
        }

        // Hitung hasil setelah filter
        $totalAfter = $query->count();
        $filteredRatings = $query->pluck('rating')->toArray();
        $filteredRatingDist = array_count_values($filteredRatings);

        $result['filtered_result'] = [
            'total_reviews_after_filter' => $totalAfter,
            'filtered_ratings_distribution' => $filteredRatingDist
        ];

        // Ambil beberapa contoh data untuk verifikasi
        $examples = $query->limit(5)->get()->map(function($r) {
            return [
                'id' => $r->id,
                'rating' => $r->rating,
                'replied' => !empty($r->reply),
                'product_id' => $r->product_id
            ];
        })->toArray();

        $result['sample_data'] = $examples;

        return response()->json($result);
    }
}