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
        try {
            // Debug: Log parameter yang diterima
            \Log::info('Review API Request:', [
                'filter_star' => $request->get('filter_star'),
                'filter_reply' => $request->get('filter_reply'),
                'sort_by' => $request->get('sort_by'),
                'page' => $request->get('page'),
                'user_id' => Auth::id(),
                'all_params' => $request->all()
            ]);

            // Pastikan seller tersedia (seperti di constructor)
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }
            
            $seller = Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                \Log::error('Seller not found for user', ['user_id' => $user->id]);
                return response()->json(['error' => 'Seller not found'], 403);
            }

            $products = $seller->products()->pluck('id')->toArray();
            \Log::info('Seller products', ['product_ids' => $products, 'count' => count($products)]);

            // Bangun query tanpa eager loading dulu untuk menghindari error relasi
            $reviewsQuery = Review::whereIn('product_id', $products);

            // Hitung total sebelum filter
            $totalBeforeFilter = $reviewsQuery->count();
            \Log::info('Total reviews before filters', ['count' => $totalBeforeFilter]);

            // Gunakan pendekatan lebih eksplisit untuk memastikan filtering bekerja
            $filterStar = $request->get('filter_star');
            $filterReply = $request->get('filter_reply');
            $sortBy = $request->get('sort_by');

            if (!is_null($filterStar) && $filterStar !== '') {
                \Log::info('Applying rating filter', ['rating' => $filterStar]);
                $reviewsQuery->where('rating', $filterStar);
            }

            if (!is_null($filterReply) && $filterReply !== '') {
                \Log::info('Applying reply filter', ['type' => $filterReply]);
                if ($filterReply === 'replied') {
                    $reviewsQuery->whereNotNull('reply');
                    \Log::info('Filtering for replied reviews only');
                } else if ($filterReply === 'pending') {
                    $reviewsQuery->whereNull('reply');
                    \Log::info('Filtering for reviews without reply only');
                }
            }

            // Urutkan
            switch ($sortBy) {
                case 'oldest':
                    $reviewsQuery->orderBy('created_at', 'asc');
                    \Log::info('Sorting by oldest');
                    break;
                case 'highest':
                    $reviewsQuery->orderBy('rating', 'desc');
                    \Log::info('Sorting by highest rating');
                    break;
                case 'lowest':
                    $reviewsQuery->orderBy('rating', 'asc');
                    \Log::info('Sorting by lowest rating');
                    break;
                default: // newest
                    $reviewsQuery->orderBy('created_at', 'desc');
                    \Log::info('Sorting by newest');
                    break;
            }

            // Log query sebelum pagination
            $countAfterAllOperations = $reviewsQuery->count();
            \Log::info('Count after filters and sorting', ['count' => $countAfterAllOperations]);

            // Dapatkan ID review yang sudah difilter
            $filteredReviewIds = $reviewsQuery->pluck('id');
            \Log::info('Filtered review IDs', ['ids' => $filteredReviewIds->toArray()]);

            // Sekarang baru gunakan with() untuk eager load relasi
            $reviewsQuery = Review::with(['user', 'product'])->whereIn('id', $filteredReviewIds);

            $reviews = $reviewsQuery->paginate(10);
            \Log::info('Reviews after query', [
                'count' => $reviews->count(), 
                'total' => $reviews->total(),
                'per_page' => $reviews->perPage(),
                'current_page' => $reviews->currentPage()
            ]);
            
            // Log hasil akhir
            \Log::info('Final reviews sample', [
                'count_in_page' => $reviews->count(),
                'sample' => $reviews->items() ? collect($reviews->items())->take(2)->map(function($r) {
                    return ['id' => $r->id, 'rating' => $r->rating, 'reply' => $r->reply, 'status' => $r->status ?? 'unknown'];
                })->toArray() : []
            ]);
            
            $formattedReviews = $reviews->map(function($review) {
                // Pastikan relasi ada sebelum mengakses propertinya
                $userName = $review->user ? $review->user->name : 'Unknown User';
                $productName = $review->product ? $review->product->name : 'Unknown Product';
                $productImage = $review->product && $review->product->main_image ? 
                    asset('storage/' . $review->product->main_image) : 
                    asset('src/product_1.png');

                return [
                    'id' => $review->id,
                    'name' => $userName,
                    'rating' => $review->rating,
                    'comment' => $review->review_text,
                    'date' => $review->created_at->format('j M Y'),
                    'product' => [
                        'name' => $productName,
                        'image' => $productImage
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
        } catch (\Exception $e) {
            \Log::error('Error in getReviewsJson: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
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