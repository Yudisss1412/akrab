<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// ========================================================================
// WISHLIST CONTROLLER - DAFTAR PRODUK FAVORIT (BOOKMARK/SAVE)
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Controller ini menangani "Wishlist" atau "Produk Favorit" customer
// - Seperti fitur Save/Bookmark di Pinterest/Instagram
// - Customer save produk yang disukai tapi belum mau beli sekarang
//
// FITUR UTAMA:
// 1. Add to Wishlist - Save produk ke daftar favorit (klik ❤️)
// 2. View Wishlist - Lihat semua produk yang di-save
// 3. Remove from Wishlist - Unsave/unlike produk (hapus ❤️)
// 4. Move to Cart - Beli produk dari wishlist (add to cart)
// 5. Toggle Like - Klik ❤️ untuk save, klik lagi untuk unlike
//
// ANALOGI:
// Seperti Pinterest/Instagram:
// - Browse feed → Ketemu produk bagus → Klik ❤️ Save
// - Buka tab "Saved" → Lihat semua produk yang di-save
// - Klik ❤️ lagi → Unsave/remove dari daftar
// - Klik "Buy Now" → Add to cart → Checkout
//
// PERBEDAAN DENGAN CART:
// - Cart = Mau beli SEKARANG (temporary, hilang setelah checkout)
// - Wishlist = Mau beli NANTI (permanent, sampai user remove manual)
//
// PENYIMPANAN:
// - Data wishlist disimpan di database (tabel wishlists)
// - Berdasarkan user_id (setiap user punya wishlist sendiri)
// - Tidak ada batasan jumlah produk di wishlist
// ========================================================================

class WishlistController extends Controller
{
    public function __construct()
    {
        // ========================================
        // REQUIRE AUTHENTICATION
        // ========================================
        // Semua method di controller ini memerlukan login
        // Middleware 'auth' memastikan hanya user yang login yang bisa akses
        $this->middleware('auth');
    }
    
    /**
     * Menampilkan daftar wishlist pengguna
     * 
     * ==========================================================================
     * FITUR: VIEW WISHLIST - LIHAT PRODUK FAVORIT
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini menampilkan semua produk yang di-save user di wishlist
     * - Seperti buka tab "Saved" di Pinterest/Instagram
     * - Setiap wishlist item diformat dengan info produk lengkap
     * 
     * DATA YANG DIKEMBALIKAN:
     * - Product info: id, title, price, image
     * - Shop info: nama toko/seller
     * - Wishlist info: date added, liked status
     * - Action: URL ke detail produk
     * 
     * @return \Illuminate\Http\JsonResponse JSON dengan daftar wishlist
     */
    public function index()
    {
        // ========================================
        // STEP 1: QUERY WISHLIST USER LOGIN
        // ========================================
        // Ambil semua wishlist milik user yang login
        // Load relasi product.seller untuk info lengkap
        $wishlists = Wishlist::with(['product.seller'])
            ->where('user_id', Auth::id())  // Filter: hanya wishlist user ini
            ->get()
            ->map(function($wishlist) {
                // ========================================
                // STEP 2: FORMAT DATA PRODUK
                // ========================================
                // Format setiap wishlist item agar mudah ditampilkan di frontend
                $product = $wishlist->product;
                return [
                    'id' => $wishlist->id,
                    'product_id' => $product->id,
                    'title' => $product->name,  // Nama produk
                    'price' => $product->price,  // Harga produk
                    'img' => $product->main_image 
                        ? asset('storage/' . $product->main_image)  // Gambar produk
                        : asset('src/placeholder.png'),  // Placeholder jika tidak ada gambar
                    'shop' => $product->seller ? $product->seller->name : 'Toko Tidak Diketahui',  // Nama toko
                    'date' => $wishlist->created_at->format('d M Y'),  // Tanggal save
                    'liked' => true,  // Status: produk ada di wishlist (liked)
                    'url' => '/produk/' . $product->id  // URL ke detail produk
                ];
            });

        // ========================================
        // STEP 3: RETURN JSON RESPONSE
        // ========================================
        // Return daftar wishlist dalam format JSON untuk frontend
        return response()->json($wishlists);
    }

    /**
     * Menambahkan produk ke wishlist
     * 
     * ==========================================================================
     * FITUR: ADD TO WISHLIST - SAVE PRODUK FAVORIT
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini menangani "Add to Wishlist" seperti save/bookmark
     * - Customer klik icon ❤️ di produk, produk masuk wishlist
     * - Jika produk sudah ada, return error (tidak boleh duplicate)
     * 
     * ANALOGI:
     * Seperti klik "Save" di Pinterest:
     * - Lihat produk → Klik ❤️ → Masuk wishlist
     * - Klik ❤️ lagi → Unlike/remove dari wishlist
     * 
     * VALIDASI:
     * - product_id: Harus ada di database (exist check)
     * - Duplicate check: Cek apakah produk sudah ada di wishlist
     *
     * @param Request $request Objek request HTTP
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function store(Request $request)
    {
        // ========================================
        // STEP 1: VALIDASI INPUT
        // ========================================
        // Validasi product_id harus ada di database
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        try {
            // ========================================
            // STEP 2: CEK DUPLICATE
            // ========================================
            // Cek apakah produk sudah ada di wishlist user ini
            // Wishlist tidak boleh duplicate (1 user = 1 produk per wishlist)
            $existingWishlist = Wishlist::where('user_id', Auth::id())
                                       ->where('product_id', $request->product_id)
                                       ->first();

            if ($existingWishlist) {
                // Produk sudah ada di wishlist
                return response()->json([
                    'message' => 'Produk sudah ada di wishlist'
                ], 400);
            }

            // ========================================
            // STEP 3: CREATE WISHLIST
            // ========================================
            // Buat wishlist baru di database
            $wishlist = Wishlist::create([
                'user_id' => Auth::id(),  // User yang save produk
                'product_id' => $request->product_id  // Produk yang di-save
            ]);

            // ========================================
            // STEP 4: FORMAT DATA UNTUK RESPONSE
            // ========================================
            // Format data wishlist untuk dikembalikan ke frontend
            $product = $wishlist->product;
            $wishlistData = [
                'id' => $wishlist->id,
                'product_id' => $product->id,
                'title' => $product->name,  // Nama produk
                'price' => $product->price,  // Harga produk
                'img' => $product->main_image 
                    ? asset('storage/' . $product->main_image)  // Gambar produk
                    : asset('src/placeholder.png'),  // Placeholder jika tidak ada gambar
                'shop' => $product->seller ? $product->seller->name : 'Toko Tidak Diketahui',  // Nama toko
                'date' => $wishlist->created_at->format('d M Y'),  // Tanggal save
                'liked' => true,  // Status: liked
                'url' => '/produk/' . $product->id  // URL ke detail produk
            ];

            // ========================================
            // STEP 5: RETURN JSON RESPONSE
            // ========================================
            return response()->json([
                'message' => 'Produk berhasil ditambahkan ke wishlist',
                'wishlist' => $wishlistData
            ]);
        } catch (\Exception $e) {
            // ========================================
            // STEP 6: HANDLE ERROR
            // ========================================
            return response()->json([
                'message' => 'Gagal menambahkan produk ke wishlist: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus produk dari wishlist berdasarkan product_id
     * 
     * ==========================================================================
     * FITUR: REMOVE FROM WISHLIST - UNSAVE PRODUK
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle penghapusan produk dari wishlist
     * - Seperti "unsave/unlike" di Pinterest/Instagram
     * - Customer batal save produk ini
     * 
     * ANALOGI:
     * Seperti di Instagram: Klik ❤️ lagi untuk unlike
     * 
     * PERBEDAAN DENGAN destroy():
     * - destroyByProductId: Hapus berdasarkan product_id
     * - destroy: Hapus berdasarkan wishlist_id
     *
     * @param Request $request
     * @param int $productId ID produk yang akan dihapus dari wishlist
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function destroyByProductId(Request $request, $productId)
    {
        // ========================================
        // STEP 1: FIND WISHLIST BY PRODUCT_ID
        // ========================================
        // Cari wishlist berdasarkan product_id dan user_id
        // Pastikan user hanya bisa hapus wishlist sendiri
        $wishlist = Wishlist::where('product_id', $productId)
                           ->where('user_id', Auth::id())
                           ->first();

        if (!$wishlist) {
            // Produk tidak ditemukan di wishlist user ini
            return response()->json([
                'message' => 'Produk tidak ditemukan di wishlist'
            ], 404);
        }

        try {
            // ========================================
            // STEP 2: DELETE WISHLIST
            // ========================================
            // Hapus wishlist dari database
            $wishlist->delete();

            // ========================================
            // STEP 3: RETURN RESPONSE
            // ========================================
            return response()->json([
                'message' => 'Produk berhasil dihapus dari wishlist'
            ]);
        } catch (\Exception $e) {
            // ========================================
            // STEP 4: HANDLE ERROR
            // ========================================
            return response()->json([
                'message' => 'Gagal menghapus produk dari wishlist: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memindahkan produk dari wishlist ke keranjang
     * 
     * ==========================================================================
     * FITUR: MOVE TO CART - BELI DARI WISHLIST
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle "Add to Cart" dari wishlist
     * - Customer lihat wishlist → Klik "🛒" → Produk masuk cart
     * - Setelah masuk cart, customer bisa langsung checkout
     * 
     * FLOW:
     * 1. Customer buka wishlist
     * 2. Klik "🛒" di produk yang mau dibeli
     * 3. Produk masuk cart (quantity = 1)
     * 4. Wishlist tetap ada (tidak dihapus otomatis)
     * 5. Customer bisa checkout atau lanjut belanja
     * 
     * CATATAN:
     * - Method ini sudah deprecated (tidak dipakai lagi)
     * - Frontend langsung call CartController untuk add to cart
     *
     * @param int $id ID wishlist yang akan dipindahkan ke cart
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function moveToCart($id)
    {
        // ========================================
        // STEP 1: FIND WISHLIST BY ID
        // ========================================
        // Cari wishlist berdasarkan ID dan user_id
        // Pastikan user hanya bisa akses wishlist sendiri
        $wishlist = Wishlist::where('id', $id)
                           ->where('user_id', Auth::id())
                           ->first();

        if (!$wishlist) {
            // Wishlist tidak ditemukan
            return response()->json([
                'message' => 'Wishlist tidak ditemukan'
            ], 404);
        }

        try {
            // ========================================
            // STEP 2: ADD TO CART
            // ========================================
            // Tambahkan produk ke cart via CartService
            // Quantity = 1 (default)
            $cartService = app('App\Services\CartService');
            $cartService->add(Auth::id(), $wishlist->product_id, 1);

            // ========================================
            // STEP 3: DELETE WISHLIST (OPTIONAL)
            // ========================================
            // Hapus dari wishlist setelah masuk cart
            // NOTE: Ini opsional, bisa juga wishlist tetap dipertahankan
            $wishlist->delete();

            // ========================================
            // STEP 4: RETURN RESPONSE
            // ========================================
            return response()->json([
                'message' => 'Produk berhasil dipindahkan ke keranjang'
            ]);
        } catch (\Exception $e) {
            // ========================================
            // STEP 5: HANDLE ERROR
            // ========================================
            return response()->json([
                'message' => 'Gagal memindahkan produk ke keranjang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus produk dari wishlist
     * 
     * ==========================================================================
     * FITUR: DELETE WISHLIST - HAPUS PRODUK FAVORIT
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle penghapusan wishlist berdasarkan ID
     * - Sama seperti destroyByProductId, tapi pakai wishlist_id
     * - Customer remove produk dari daftar favorit
     * 
     * ANALOGI:
     * Seperti "Unsave" di Instagram - produk hilang dari saved posts
     *
     * @param int $id ID wishlist yang akan dihapus
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function destroy($id)
    {
        // ========================================
        // STEP 1: FIND WISHLIST BY ID
        // ========================================
        // Cari wishlist berdasarkan ID dan user_id
        // Pastikan user hanya bisa hapus wishlist sendiri
        $wishlist = Wishlist::where('id', $id)
                           ->where('user_id', Auth::id())
                           ->first();

        if (!$wishlist) {
            // Wishlist tidak ditemukan
            return response()->json([
                'message' => 'Produk tidak ditemukan di wishlist'
            ], 404);
        }

        try {
            // ========================================
            // STEP 2: DELETE WISHLIST
            // ========================================
            // Hapus wishlist dari database
            $wishlist->delete();

            // ========================================
            // STEP 3: RETURN RESPONSE
            // ========================================
            return response()->json([
                'message' => 'Produk berhasil dihapus dari wishlist'
            ]);
        } catch (\Exception $e) {
            // ========================================
            // STEP 4: HANDLE ERROR
            // ========================================
            return response()->json([
                'message' => 'Gagal menghapus produk dari wishlist: ' . $e->getMessage()
            ], 500);
        }
    }
}
