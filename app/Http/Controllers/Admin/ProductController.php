<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Hanya admin yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        // Check which tab is active
        $tab = $request->get('tab', 'products');

        if ($tab === 'reviews') {
            // Fetch reviews with related product and user
            $query = Review::with(['product', 'user']);
            
            // Filter by review status
            if ($request->filled('review_status')) {
                $status = $request->get('review_status');
                if ($status === 'pending') {
                    $query->whereNull('approved_at');
                } elseif ($status === 'approved') {
                    $query->whereNotNull('approved_at');
                } elseif ($status === 'rejected') {
                    $query->whereNotNull('rejected_at');
                }
            }
            
            // Filter by rating
            if ($request->filled('rating')) {
                $query->where('rating', $request->get('rating'));
            }
            
            // Search in review text, product name, or user name
            if ($request->filled('review_search')) {
                $search = $request->get('review_search');
                $query->where(function($q) use ($search) {
                    $q->where('review_text', 'LIKE', "%{$search}%")
                      ->orWhereHas('product', function($p) use ($search) {
                          $p->where('name', 'LIKE', "%{$search}%");
                      })
                      ->orWhereHas('user', function($u) use ($search) {
                          $u->where('name', 'LIKE', "%{$search}%");
                      });
                });
            }
            
            $reviews = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());
            
            return view('admin.produk.index', compact('reviews', 'tab'));
        } elseif ($tab === 'categories') {
            // For categories tab, we'll return categories data
            $categories = Category::with('subcategories')->orderBy('name')->get();
            $mainCategories = Category::whereNull('parent_id')->orderBy('name')->get();
            
            return view('admin.produk.index', compact('categories', 'mainCategories', 'tab'));
        } else { // products tab
            // Query builder untuk semua produk
            $query = Product::with(['seller', 'category', 'images'])
                ->leftJoin('sellers', 'products.seller_id', '=', 'sellers.id')
                ->select('products.*', 'sellers.store_name as seller_name');

            // Filter berdasarkan pencarian jika ada
            if ($request->has('search') && $request->search) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('products.name', 'LIKE', "%{$search}%")
                      ->orWhere('products.sku', 'LIKE', "%{$search}%")
                      ->orWhere('sellers.store_name', 'LIKE', "%{$search}%");
                });
            }

            // Filter berdasarkan penjual jika ada
            if ($request->has('seller_id') && $request->seller_id) {
                $query->where('products.seller_id', $request->seller_id);
            }

            // Filter berdasarkan status produk jika ada
            if ($request->has('status') && $request->status) {
                $query->where('products.status', $request->status);
            }

            // Filter berdasarkan kategori jika ada
            if ($request->has('category') && $request->category) {
                $query->where('products.category_id', $request->category);
            }

            // Urutkan berdasarkan yang terbaru
            $query->orderBy('products.created_at', 'desc');

            // Paginate hasil
            $products = $query->paginate(10)->appends($request->query());

            // Ambil semua penjual untuk filter dropdown
            $sellers = Seller::select('id', 'store_name')->get();
            
            // Ambil semua kategori untuk filter dropdown
            $categories = Category::all();

            return view('admin.produk.index', compact('products', 'sellers', 'categories', 'tab'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $product = Product::findOrFail($id);

        if ($request->has('status')) {
            $product->status = $request->status;
            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Status produk berhasil diperbarui',
                'product' => $product
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada data yang diperbarui']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }

    /**
     * Approve product
     */
    public function approveProduct($id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $product = Product::findOrFail($id);
        $product->status = 'active';
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil disetujui',
            'product' => $product
        ]);
    }

    /**
     * Reject product
     */
    public function rejectProduct($id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $product = Product::findOrFail($id);
        $product->status = 'rejected';
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditolak',
            'product' => $product
        ]);
    }

    /**
     * Suspend product
     */
    public function suspendProduct($id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $product = Product::findOrFail($id);
        $product->status = 'suspended';
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditangguhkan',
            'product' => $product
        ]);
    }

    /**
     * Approve review
     */
    public function approveReview($id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $review = Review::findOrFail($id);
        $review->approved_at = now();
        $review->rejected_at = null;
        $review->save();

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil disetujui',
            'review' => $review
        ]);
    }

    /**
     * Reject review
     */
    public function rejectReview($id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $review = Review::findOrFail($id);
        $review->rejected_at = now();
        $review->approved_at = null;
        $review->save();

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil ditolak',
            'review' => $review
        ]);
    }

    /**
     * Delete review
     */
    public function deleteReview($id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil dihapus'
        ]);
    }
}