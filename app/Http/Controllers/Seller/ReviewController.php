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
        $seller = Auth::user()->seller;
        $products = $seller->products()->pluck('id')->toArray();
        
        $reviewsQuery = Review::with(['user', 'product'])
            ->whereIn('product_id', $products);
        
        if ($request->filled('filter_star')) {
            $reviewsQuery->where('rating', $request->filter_star);
        }
        
        if ($request->filled('filter_reply')) {
            if ($request->filter_reply === 'replied') {
                $reviewsQuery->whereNotNull('reply');
            } else if ($request->filter_reply === 'pending') {
                $reviewsQuery->whereNull('reply');
            }
        }
        
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
                'date' => $review->created_at->format('j M Y'),
                'product' => [
                    'name' => $review->product->name,
                    'image' => $review->product->image ? asset('storage/' . $review->product->image) : asset('src/placeholder_produk.png')
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
    
    /**
     * API endpoint untuk mendapatkan ulasan terbaru untuk ditampilkan di profil penjual
     */
    public function getRecentReviews()
    {
        $seller = Auth::user()->seller;
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
}