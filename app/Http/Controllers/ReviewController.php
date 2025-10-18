<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Menyimpan ulasan produk baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:500'
        ]);

        // Validasi bahwa user benar-benar telah memesan produk ini dan order telah dikirim/diterima
        $order = Order::findOrFail($request->order_id);
        if ($order->user_id !== Auth::id()) {
            return response()->json(['error' => 'Anda tidak memiliki izin untuk memberikan ulasan'], 403);
        }

        // Cek apakah produk ini termasuk dalam order
        $orderItemExists = $order->items()->where('product_id', $request->product_id)->exists();
        if (!$orderItemExists) {
            return response()->json(['error' => 'Produk ini tidak ada dalam pesanan Anda'], 400);
        }

        // Cek apakah user sudah memberikan ulasan untuk produk ini dalam order ini
        $existingReview = Review::where('user_id', Auth::id())
                                ->where('product_id', $request->product_id)
                                ->where('order_id', $request->order_id)
                                ->first();
        
        if ($existingReview) {
            return response()->json(['error' => 'Anda telah memberikan ulasan untuk produk ini'], 400);
        }

        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
            'status' => 'approved' // Bisa diubah sesuai kebijakan moderation
        ]);

        // Update rating produk
        $this->updateProductRating($request->product_id);

        return response()->json([
            'message' => 'Ulasan berhasil ditambahkan', 
            'review' => $review->load(['user', 'product'])
        ]);
    }

    /**
     * Menampilkan semua ulasan untuk produk tertentu
     */
    public function show($productId)
    {
        $product = Product::findOrFail($productId);
        
        $reviews = Review::with(['user'])
                        ->where('product_id', $productId)
                        ->where('status', 'approved')
                        ->orderBy('created_at', 'desc')
                        ->get();

        $averageRating = $reviews->avg('rating');
        $totalReviews = $reviews->count();

        return response()->json([
            'product' => $product,
            'reviews' => $reviews,
            'average_rating' => $averageRating,
            'total_reviews' => $totalReviews
        ]);
    }

    /**
     * Memperbarui rating produk berdasarkan ulasan
     */
    private function updateProductRating($productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $avgRating = Review::where('product_id', $productId)
                              ->where('status', 'approved')
                              ->avg('rating');
            
            $product->update(['rating' => $avgRating ? round($avgRating, 2) : 0]);
        }
    }
}
