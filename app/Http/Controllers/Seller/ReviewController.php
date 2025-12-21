<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'seller.role']);
        $this->middleware(function ($request, $next) {
            // Pastikan penjual juga memiliki record di tabel sellers
            $user = Auth::user();
            $seller = Seller::where('user_id', $user->id)->first();

            if (!$seller) {
                abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
            }

            // Tambahkan seller ke user agar bisa diakses di controller
            $user->seller = $seller;

            return $next($request);
        });
    }

    /**
     * Menampilkan halaman manajemen ulasan untuk penjual
     */
    public function index(Request $request)
    {
        $seller = Auth::user()->seller;

        // Ambil produk milik penjual
        $products = $seller->products()->pluck('id')->toArray();

        // Query untuk ulasan
        $reviewsQuery = Review::with(['user', 'product'])
            ->whereIn('product_id', $products);

        // Filter berdasarkan rating jika ada
        if ($request->filled('filter_star')) {
            $reviewsQuery->where('rating', $request->filter_star);
        }

        // Filter berdasarkan status balasan jika ada
        if ($request->filled('filter_reply')) {
            if ($request->filter_reply === 'replied') {
                $reviewsQuery->whereNotNull('reply');
            } else if ($request->filter_reply === 'pending') {
                $reviewsQuery->whereNull('reply');
            }
        }

        // Urutkan jika ada parameter sort
        switch ($request->get('sort_by')) {
            case 'oldest':
                $reviewsQuery->orderBy('created_at', 'asc');
                break;
            case 'highest':
                $reviewsQuery->orderBy('rating', 'desc');
                break;
            case 'lowest':
                $reviewsQuery->orderBy('rating', 'asc');
                break;
            default: // newest
                $reviewsQuery->orderBy('created_at', 'desc');
                break;
        }

        $reviews = $reviewsQuery->paginate(10);

        // Hitung statistik ulasan
        $totalReviews = Review::whereIn('product_id', $products)->count();
        $averageRating = Review::whereIn('product_id', $products)->avg('rating');
        $ratingStats = [];

        for ($i = 5; $i >= 1; $i--) {
            $ratingStats[$i] = Review::whereIn('product_id', $products)
                ->where('rating', $i)
                ->count();
        }

        return view('penjual.manajemen_ulasan', compact(
            'reviews',
            'totalReviews',
            'averageRating',
            'ratingStats'
        ));
    }

    /**
     * Menyimpan balasan dari penjual untuk ulasan
     */
    public function reply(Request $request, $reviewId)
    {
        $request->validate([
            'reply_text' => 'required|string|max:500'
        ]);

        $review = Review::with(['product'])->findOrFail($reviewId);

        // Pastikan ulasan ini untuk produk milik penjual saat ini
        $seller = Auth::user()->seller;
        if (!$seller->products->contains($review->product->id)) {
            abort(403, 'Anda hanya dapat membalas ulasan untuk produk Anda sendiri.');
        }

        $review->update([
            'reply' => $request->reply_text,
            'replied_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Balasan berhasil disimpan',
            'reply' => $request->reply_text
        ]);
    }

    /**
     * API endpoint untuk mendapatkan data ulasan dalam format JSON
     */
    public function getReviewsJson(Request $request)
    {
        // Tambahkan logging untuk debugging
        \Log::info('=== API CALLED ===');
        \Log::info('Request params:', $request->all());

        try {
            // Pastikan user dan seller tersedia
            $user = Auth::user();
            if (!$user) {
                \Log::info('User not authenticated');
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $seller = Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                \Log::info('Seller not found');
                return response()->json(['error' => 'Seller not found'], 403);
            }

            // Ambil produk penjual
            $products = $seller->products()->pluck('id')->toArray();
            \Log::info('API Request - Seller details:', [
                'seller_id' => $seller->id,
                'user_id' => $user->id,
                'product_ids' => $products,
                'product_count' => count($products)
            ]);

            // Debug: cek apakah produk-produk ini memiliki ulasan
            \Log::info('API Request - Product reviews check:', [
                'checking_for_products' => $products,
                'available_reviews_count' => Review::whereIn('product_id', $products)->count(),
                'specific_ratings_found' => array_count_values(Review::whereIn('product_id', $products)->pluck('rating')->toArray())
            ]);

            // Bangun query dasar - hindari eager loading yang bisa menyebabkan error relasi
            $reviewsQuery = Review::whereIn('product_id', $products);

            // Debug: tampilkan total sebelum filter
            $totalBefore = $reviewsQuery->count();
            $allRatingsBefore = $reviewsQuery->pluck('rating')->toArray();
            \Log::info('API Request - Query details:', [
                'total_before_filter' => $totalBefore,
                'all_ratings_before' => array_count_values($allRatingsBefore),
                'filter_product_ids' => $products
            ]);
            \Log::info('All ratings before: ' . json_encode(array_count_values($allRatingsBefore)));

            // Terapkan filter rating
            $filterStar = $request->get('filter_star');
            if (!empty($filterStar)) {
                \Log::info('Applying rating filter: ' . $filterStar);
                $reviewsQuery->where('rating', $filterStar);
            } else {
                \Log::info('No rating filter applied');
            }

            // Terapkan filter reply
            $filterReply = $request->get('filter_reply');
            if (!empty($filterReply)) {
                \Log::info('Applying reply filter: ' . $filterReply);
                if ($filterReply === 'replied') {
                    $reviewsQuery->whereNotNull('reply');
                } else if ($filterReply === 'pending') {
                    $reviewsQuery->whereNull('reply');
                }
            } else {
                \Log::info('No reply filter applied');
            }

            // Terapkan sorting
            $sortBy = $request->get('sort_by', 'newest');
            \Log::info('Applying sort: ' . $sortBy);
            switch ($sortBy) {
                case 'oldest':
                    $reviewsQuery->orderBy('created_at', 'asc');
                    break;
                case 'highest':
                    $reviewsQuery->orderBy('rating', 'desc');
                    break;
                case 'lowest':
                    $reviewsQuery->orderBy('rating', 'asc');
                    break;
                default:
                    $reviewsQuery->orderBy('created_at', 'desc');
                    break;
            }

            // Debug: tampilkan total setelah filter
            $totalAfter = $reviewsQuery->count();
            $filteredRatings = clone $reviewsQuery;
            $allRatingsAfter = $filteredRatings->pluck('rating')->toArray();
            \Log::info('Total after filter: ' . $totalAfter);
            \Log::info('All ratings after: ' . json_encode(array_count_values($allRatingsAfter)));

            // Ambil hasil dengan pagination
            $reviews = $reviewsQuery->paginate(10);
            \Log::info('Reviews on page: ' . $reviews->count());

            // Validasi hasil
            $resultRatings = $reviews->pluck('rating')->toArray();
            $resultReplies = $reviews->map(function($r) {
                return !empty($r->reply) ? 'replied' : 'pending';
            })->toArray();

            \Log::info('Reviews on page: ' . $reviews->count());
            \Log::info('Result ratings: ' . json_encode(array_count_values($resultRatings)));
            \Log::info('Result reply status: ' . json_encode(array_count_values($resultReplies)));

            // Verifikasi filter berhasil
            if (!empty($filterStar)) {
                $ratingMatches = count(array_filter($resultRatings, function($r) use ($filterStar) { return $r == $filterStar; }));
                \Log::info("Rating filter verification - Expected: $filterStar, Matched: $ratingMatches, Total on page: " . count($resultRatings));
            }

            if (!empty($filterReply)) {
                $expectedReplyStatus = $filterReply === 'replied';
                $actualReplyStatuses = $reviews->map(function($r) {
                    return !empty($r->reply) ? 'replied' : 'pending';
                })->toArray();
                $replyMatches = count(array_filter($actualReplyStatuses, function($r) use ($filterReply) {
                    $shouldBeReplied = $filterReply === 'replied';
                    return $shouldBeReplied ? $r === 'replied' : $r === 'pending';
                }));
                \Log::info("Reply filter verification - Expected: $filterReply, Matched: $replyMatches, Total on page: " . count($actualReplyStatuses));
            }

            // Format hasil - ambil data produk secara individual untuk menghindari error relasi
            $formattedReviews = collect(); // Buat koleksi baru yang aman

            foreach ($reviews as $review) {
                // Ambil data user secara manual untuk menghindari error relasi
                $userName = 'Unknown User';

                try {
                    $user = \App\Models\User::select(['id', 'name'])->find($review->user_id);
                    if ($user) {
                        $userName = $user->name;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Could not load user safely: ' . $e->getMessage());
                    // Gunakan nilai default
                }

                // Ambil data produk secara manual untuk menghindari error relasi
                $productName = 'Unknown Product';
                $productImage = asset('src/product_1.png'); // default image

                try {
                    // Gunakan accessor main_image dari model Product
                    $product = \App\Models\Product::find($review->product_id);
                    if ($product) {
                        $productName = $product->name;

                        // Gunakan accessor main_image yang didefinisikan di model
                        $mainImage = $product->main_image;
                        if ($mainImage) {
                            $productImage = asset('storage/' . $mainImage);
                        } else {
                            // Fallback ke kolom image jika tidak ada di product_images
                            if (isset($product->image) && $product->image) {
                                $productImage = asset('storage/' . $product->image);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Could not load product safely: ' . $e->getMessage());
                    // Gunakan nilai default
                }

                $formattedReviews->push([
                    'id' => $review->id,
                    'name' => $userName,
                    'rating' => $review->rating,
                    'comment' => $review->review_text,
                    'date' => $review->created_at->format('j M Y'),
                    'product' => [
                        'id' => $review->product_id, // Tambahkan ID produk
                        'name' => $productName,
                        'image' => $productImage
                    ],
                    'replied' => !empty($review->reply),
                    'reply' => $review->reply
                ]);
            }

            \Log::info('=== API RESPONSE SENT ===');

            return response()->json([
                'reviews' => $formattedReviews,
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'total' => $reviews->total(),
                    'per_page' => $reviews->perPage()
                ],
                'validation' => [
                    'total_before' => $totalBefore,
                    'total_after' => $totalAfter,
                    'original_ratings' => array_count_values($allRatingsBefore),
                    'filtered_ratings' => array_count_values($allRatingsAfter),
                    'result_ratings' => array_unique($resultRatings),
                    'result_replies' => array_unique($resultReplies),
                    'filter_star_applied' => $filterStar,
                    'filter_reply_applied' => $filterReply
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('API Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * API endpoint untuk mendapatkan ulasan terbaru untuk ditampilkan di profil penjual
     */
    public function getRecentReviews()
    {
        // Pastikan seller tersedia (seperti di constructor)
        $user = Auth::user();
        $seller = Seller::where('user_id', $user->id)->first();

        if (!$seller) {
            return response()->json(['error' => 'Seller not found'], 403);
        }

        $products = $seller->products()->pluck('id')->toArray();

        $recentReviews = Review::with(['user', 'product'])
            ->whereIn('product_id', $products)
            ->orderBy('created_at', 'desc')
            ->limit(3) // Ambil 3 ulasan terbaru
            ->get();

        $formattedReviews = $recentReviews->map(function($review) {
            return [
                'id' => $review->id,
                'user_name' => $review->user->name,
                'created_at' => $review->created_at->format('d M Y'),
                'rating' => $review->rating,
                'review_text' => $review->review_text,
                'reply' => $review->reply
            ];
        });

        return response()->json([
            'success' => true,
            'reviews' => $formattedReviews
        ]);
    }

    /**
     * API endpoint untuk mendapatkan ulasan dengan rating rendah (komplain)
     */
    public function getLowRatingReviews(Request $request)
    {
        // Pastikan seller tersedia (seperti di constructor)
        $user = Auth::user();
        $seller = Seller::where('user_id', $user->id)->first();

        if (!$seller) {
            return response()->json(['error' => 'Seller not found'], 403);
        }

        $products = $seller->products()->pluck('id')->toArray();

        // Query untuk ulasan
        $reviewsQuery = Review::with(['user', 'product'])
            ->whereIn('product_id', $products)
            ->where('rating', '<=', 2); // Rating 2 ke bawah dianggap komplain

        // Filter berdasarkan rating jika ada
        if ($request->filled('filter_star')) {
            $reviewsQuery->where('rating', $request->filter_star);
        }

        // Filter berdasarkan status balasan jika ada
        if ($request->filled('filter_reply')) {
            if ($request->filter_reply === 'replied') {
                $reviewsQuery->whereNotNull('reply');
            } else if ($request->filter_reply === 'pending') {
                $reviewsQuery->whereNull('reply');
            }
        }

        // Urutkan jika ada parameter sort
        switch ($request->get('sort_by')) {
            case 'oldest':
                $reviewsQuery->orderBy('created_at', 'asc');
                break;
            case 'highest':
                $reviewsQuery->orderBy('rating', 'desc');
                break;
            case 'lowest':
                $reviewsQuery->orderBy('rating', 'asc');
                break;
            default: // newest
                $reviewsQuery->orderBy('created_at', 'desc');
                break;
        }

        $reviews = $reviewsQuery->paginate(10);

        $formattedReviews = $reviews->map(function($review) {
            return [
                'id' => $review->id,
                'name' => $review->user->name,
                'rating' => $review->rating,
                'comment' => $review->review_text,
                'date' => $review->created_at->format('d M Y'),
                'product' => [
                    'id' => $review->product->id,
                    'name' => $review->product->name,
                    'image' => $review->product->main_image ?
                        asset('storage/' . $review->product->main_image) :
                        asset('src/placeholder_produk.png')
                ],
                'replied' => !empty($review->reply),
                'reply' => $review->reply
            ];
        });

        return response()->json([
            'reviews' => $formattedReviews,
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
                'per_page' => $reviews->perPage()
            ]
        ]);
    }
}