<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Menampilkan daftar wishlist pengguna
     */
    public function index()
    {
        $wishlists = Wishlist::with(['product' => function($query) {
            $query->with(['seller', 'images']);
        }])
        ->where('user_id', Auth::id())
        ->paginate(10);

        return response()->json([
            'wishlists' => $wishlists
        ]);
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

        return response()->json([
            'message' => 'Produk berhasil ditambahkan ke wishlist',
            'wishlist' => $wishlist->load(['product'])
        ]);
    }

    /**
     * Menghapus produk dari wishlist
     */
    public function destroy($id)
    {
        $wishlist = Wishlist::where('id', $id)
                           ->where('user_id', Auth::id())
                           ->first();

        if (!$wishlist) {
            return response()->json([
                'message' => 'Wishlist tidak ditemukan'
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
