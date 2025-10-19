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

        // Buat ulasan
        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
            'status' => 'approved' // Otomatis disetujui untuk user
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
}