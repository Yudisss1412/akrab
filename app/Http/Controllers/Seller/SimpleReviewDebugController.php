<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use App\Models\Review;

class SimpleReviewDebugController extends Controller
{
    public function simpleReviewsApi(Request $request)
    {
        \Log::info('=== SIMPLE DEBUG API START ===');
        \Log::info('Request params:', $request->all());

        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Not authenticated'], 401);
            }

            $seller = Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                return response()->json(['error' => 'Seller not found'], 403);
            }

            $productIds = $seller->products()->pluck('id')->toArray();
            \Log::info('Seller product IDs:', $productIds);

            // Bangun query dasar
            $query = Review::whereIn('product_id', $productIds);

            $totalBeforeFilter = $query->count();
            \Log::info('Total reviews before filter:', $totalBeforeFilter);

            // Ambil sample sebelum filter
            $sampleBefore = $query->limit(5)->get()->map(function($r) {
                return ['id' => $r->id, 'rating' => $r->rating, 'replied' => !empty($r->reply)];
            })->toArray();
            \Log::info('Sample before filter:', $sampleBefore);

            // Terapkan filter
            $filterStar = $request->get('filter_star');
            $filterReply = $request->get('filter_reply');
            $sortBy = $request->get('sort_by', 'newest');

            if (!empty($filterStar)) {
                $query->where('rating', $filterStar);
                \Log::info('Applied rating filter:', $filterStar);
            }

            if (!empty($filterReply)) {
                if ($filterReply === 'replied') {
                    $query->whereNotNull('reply');
                    \Log::info('Applied replied filter');
                } elseif ($filterReply === 'pending') {
                    $query->whereNull('reply');
                    \Log::info('Applied pending filter');
                }
            }

            switch ($sortBy) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'highest':
                    $query->orderBy('rating', 'desc');
                    break;
                case 'lowest':
                    $query->orderBy('rating', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            $totalAfterFilter = $query->count();
            \Log::info('Total reviews after filter:', $totalAfterFilter);

            // Ambil hasil
            $reviews = $query->paginate(10);

            \Log::info('Paginated reviews count:', $reviews->count());

            // Format hasil
            $formattedReviews = $reviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->review_text,
                    'replied' => !empty($review->reply),
                    'reply' => $review->reply,
                    'user_name' => $review->user->name ?? 'Unknown User',
                    'product_name' => $review->product->name ?? 'Unknown Product'
                ];
            });

            \Log::info('=== SIMPLE DEBUG API END ===');

            return response()->json([
                'reviews' => $formattedReviews,
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'total' => $reviews->total(),
                    'per_page' => $reviews->perPage()
                ],
                'debug' => [
                    'params_received' => $request->all(),
                    'total_before' => $totalBeforeFilter,
                    'total_after' => $totalAfterFilter,
                    'sample_after_filter' => $reviews->take(3)->map(function($r) {
                        return ['id' => $r->id, 'rating' => $r->rating, 'replied' => $r->replied];
                    })->toArray()
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Simple API Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal error occurred'], 500);
        }
    }
}