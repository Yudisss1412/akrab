<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Menampilkan daftar wishlist pengguna
     */
    public function index()
    {
        $wishlists = Wishlist::with(['product.seller'])
            ->where('user_id', Auth::id())
            ->get()
            ->map(function($wishlist) {
                $product = $wishlist->product;
                return [
                    'id' => $wishlist->id,
                    'product_id' => $product->id,
                    'title' => $product->name,
                    'price' => $product->price,
                    'img' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
                    'shop' => $product->seller ? $product->seller->name : 'Toko Tidak Diketahui',
                    'date' => $wishlist->created_at->format('d M Y'),
                    'liked' => true,
                    'url' => '/produk/' . $product->id
                ];
            });

        return response()->json($wishlists);
    }

    /**
     * Menambahkan produk ke wishlist
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        // Cek apakah produk sudah ada di wishlist
        $existingWishlist = Wishlist::where('user_id', Auth::id())
                                   ->where('product_id', $request->product_id)
                                   ->first();
        
        if ($existingWishlist) {
            return response()->json([
                'message' => 'Produk sudah ada di wishlist'
            ], 400);
        }

        $wishlist = Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id
        ]);

        // Format data untuk dikembalikan
        $product = $wishlist->product;
        $wishlistData = [
            'id' => $wishlist->id,
            'product_id' => $product->id,
            'title' => $product->name,
            'price' => $product->price,
            'img' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
            'shop' => $product->seller ? $product->seller->name : 'Toko Tidak Diketahui',
            'date' => $wishlist->created_at->format('d M Y'),
            'liked' => true,
            'url' => '/produk/' . $product->id
        ];

        return response()->json([
            'message' => 'Produk berhasil ditambahkan ke wishlist',
            'wishlist' => $wishlistData
        ]);
    }

    /**
     * Menghapus produk dari wishlist berdasarkan product_id
     */
    public function destroyByProductId(Request $request, $productId)
    {
        $wishlist = Wishlist::where('product_id', $productId)
                           ->where('user_id', Auth::id())
                           ->first();

        if (!$wishlist) {
            return response()->json([
                'message' => 'Produk tidak ditemukan di wishlist'
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus dari wishlist'
        ]);
    }

    /**
     * Memindahkan produk dari wishlist ke keranjang
     */
    public function moveToCart($id)
    {
        $wishlist = Wishlist::where('id', $id)
                           ->where('user_id', Auth::id())
                           ->first();

        if (!$wishlist) {
            return response()->json([
                'message' => 'Wishlist tidak ditemukan'
            ], 404);
        }

        // Tambahkan ke keranjang
        $cartService = app('App\Services\CartService');
        $cartService->add(Auth::id(), $wishlist->product_id, 1);

        // Hapus dari wishlist
        $wishlist->delete();

        return response()->json([
            'message' => 'Produk berhasil dipindahkan ke keranjang'
        ]);
    }
}
