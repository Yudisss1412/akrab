<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan form untuk memberikan ulasan
     */
    public function create($orderItemId)
    {
        $orderItem = OrderItem::with(['order', 'product'])->findOrFail($orderItemId);
        
        // Pastikan hanya pembeli yang bisa memberikan ulasan
        if ($orderItem->order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan memberikan ulasan untuk produk ini');
        }

        // Cek apakah sudah ada ulasan untuk produk ini
        $existingReview = Review::where('user_id', Auth::id())
                                ->where('product_id', $orderItem->product_id)
                                ->where('order_id', $orderItem->order_id)
                                ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini.');
        }

        return view('customer.ulasan.create', compact('orderItem'));
    }

    /**
     * Menyimpan ulasan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
            'media.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120' // Max 5MB per image
        ]);

        // Pastikan order milik user ini
        $order = Order::where('id', $request->order_id)
                      ->where('user_id', Auth::id())
                      ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak diizinkan memberikan ulasan untuk produk ini'
            ], 403);
        }

        // Cek apakah sudah ada ulasan untuk produk ini dalam order ini
        $existingReview = Review::where('user_id', Auth::id())
                                ->where('product_id', $request->product_id)
                                ->where('order_id', $request->order_id)
                                ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memberikan ulasan untuk produk ini'
            ], 400);
        }

        // Handle media uploads
        $mediaPaths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $mediaFile) {
                if ($mediaFile->isValid()) {
                    $path = $mediaFile->store('reviews', 'public');
                    $mediaPaths[] = $path;
                }
            }
        }

        // Buat ulasan
        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
            'media' => !empty($mediaPaths) ? $mediaPaths : null,
            'status' => 'approved' // Ulasan langsung disetujui dan muncul di produk
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil ditambahkan',
            'review' => $review
        ]);
    }

    /**
     * Menampilkan daftar ulasan untuk produk tertentu
     */
    public function showByProduct($productId)
    {
        $product = Product::findOrFail($productId);
        $reviews = Review::with('user')
                        ->where('product_id', $productId)
                        ->where('status', 'approved')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('customer.ulasan.show_by_product', compact('product', 'reviews'));
    }

    /**
     * Menampilkan daftar ulasan milik user
     */
    public function index()
    {
        $reviews = Review::with(['product', 'order'])
                        ->where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('customer.ulasan.index', compact('reviews'));
    }

    /**
     * API endpoint untuk mendapatkan ulasan produk
     */
    public function getReviewsByProduct($productId)
    {
        $reviews = Review::with('user')
                        ->where('product_id', $productId)
                        ->where('status', 'approved')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return response()->json([
            'success' => true,
            'reviews' => $reviews
        ]);
    }

    /**
     * Menampilkan halaman ulasan
     */
    public function halamanUlasan()
    {
        return view('customer.koleksi.halaman_ulasan');
    }

    /**
     * API endpoint untuk mendapatkan ulasan milik user
     */
    public function getUserReviews()
    {
        \Log::info('API /api/user-reviews: Fetching reviews for user ID: ' . Auth::id());

        $reviews = Review::with(['product', 'order'])
                        ->where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        \Log::info('API /api/user-reviews: Found ' . $reviews->count() . ' reviews for user ID: ' . Auth::id());

        // Format data untuk frontend
        $formattedReviews = $reviews->map(function($review) {
            \Log::debug('API /api/user-reviews: Processing review ID: ' . $review->id . ' for product: ' . ($review->product ? $review->product->name : 'NULL'));

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

        \Log::info('API /api/user-reviews: Returning ' . $formattedReviews->count() . ' formatted reviews');

        $user = [
            'name' => Auth::user()->name
        ];

        return response()->json([
            'success' => true,
            'user' => $user,
            'reviews' => $formattedReviews
        ]);
    }

    /**
     * Update user's review
     */
    public function updateReview(Request $request, $reviewId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
            'media.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120' // Max 5MB per image
        ]);

        $review = Review::where('id', $reviewId)
                        ->where('user_id', Auth::id())
                        ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Ulasan tidak ditemukan atau Anda tidak memiliki izin untuk mengedit ulasan ini'
            ], 404);
        }

        // Handle media uploads
        $mediaPaths = $review->media ? json_decode($review->media, true) : [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $mediaFile) {
                if ($mediaFile->isValid()) {
                    $path = $mediaFile->store('reviews', 'public');
                    $mediaPaths[] = $path;
                }
            }
        }

        // Update review
        $review->update([
            'rating' => $request->rating,
            'review_text' => $request->review_text,
            'media' => !empty($mediaPaths) ? $mediaPaths : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil diperbarui',
            'review' => $review
        ]);
    }


    /**
     * Menampilkan halaman untuk memberikan ulasan untuk semua item dalam pesanan
     */
    public function showReviewPageForOrder($orderNumber)
    {
        // Cari order berdasarkan nomor pesanan
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Pastikan hanya pembeli yang bisa memberikan ulasan
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan memberikan ulasan untuk pesanan ini');
        }

        // Ambil semua item dalam pesanan
        $orderItems = $order->items()->with(['product', 'product.seller'])->get();

        // Cek apakah sudah ada ulasan untuk masing-masing produk
        $itemsWithReviewStatus = $orderItems->map(function ($item) use ($order) {
            $existingReview = Review::where('user_id', Auth::id())
                                    ->where('product_id', $item->product_id)
                                    ->where('order_id', $order->id)
                                    ->first();

            return [
                'id' => $item->id,
                'product' => $item->product,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'has_review' => $existingReview !== null,
                'review_id' => $existingReview ? $existingReview->id : null
            ];
        });

        return view('customer.ulasan.create_for_order', compact('order', 'itemsWithReviewStatus'));
    }
}