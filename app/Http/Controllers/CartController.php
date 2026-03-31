<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// ========================================================================
// CART CONTROLLER - SHOPPING CART (KERANJANG BELANJA)
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Controller ini menangani "Keranjang Belanja" customer
// - Seperti keranjang di supermarket: ambil produk, kumpulkan, baru bayar
// - Cart bersifat temporary, akan kosong setelah checkout
//
// FITUR UTAMA:
// 1. Add to Cart - Tambah produk ke keranjang
// 2. View Cart - Lihat isi keranjang dengan total
// 3. Update Quantity - Tambah/kurang jumlah produk
// 4. Remove from Cart - Hapus produk dari keranjang
// 5. Clear Cart - Kosongkan keranjang setelah checkout
// 6. Sync Cart - Sinkronisasi cart guest → logged in user
//
// ANALOGI:
// Seperti belanja di Supermarket:
// - Add to Cart = Ambil produk dari rak, masuk keranjang
// - View Cart = Lihat isi keranjang (cek sebelum bayar)
// - Update Qty = Tambah/kurang jumlah produk di keranjang
// - Remove = Taruh kembali produk ke rak
// - Checkout = Jalan ke kasir, bayar, keranjang kosong
//
// PENYIMPANAN:
// - Guest user: Cart disimpan di localStorage browser
// - Logged in user: Cart disimpan di database (tabel carts)
// - Setelah login: Sync cart dari localStorage ke database
// ========================================================================

/**
 * CartController - Keranjang Belanja
 *
 * Controller ini menangani semua operasi terkait keranjang belanja (shopping cart):
 * - Menampilkan isi keranjang (index)
 * - Menambah produk ke keranjang (add)
 * - Update kuantitas produk (update)
 * - Menghapus produk dari keranjang (remove)
 * - Mengosongkan keranjang (clear)
 * - Sinkronisasi cart dari localStorage ke session (syncCart)
 *
 * Cart disimpan berdasarkan user yang login (user_id).
 * 
 * @package App\Http\Controllers
 * @author Tim Ecommerce AKRAB
 * @version 1.0
 */
class CartController extends Controller
{
    protected $cartService;

    /**
     * Inject CartService untuk operasi cart
     * 
     * @param CartService $cartService Service untuk operasi cart
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Menampilkan halaman keranjang belanja
     * 
     * ==========================================================================
     * FITUR: VIEW CART - LIHAT ISI KERANJANG
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini menampilkan semua item di keranjang customer
     * - Seperti "cek keranjang belanja" sebelum ke kasir
     * - Hitung subtotal, total berat, discount, total akhir
     * 
     * KALKULASI:
     * 1. Subtotal = Σ (harga produk × quantity) untuk semua item
     * 2. Total Berat = Σ (berat × quantity) untuk kalkulasi ongkir
     * 3. Discount = 0 (placeholder, bisa dikembangkan untuk voucher)
     * 4. Total Akhir = Subtotal - Discount
     * 5. Total Items = Jumlah satuan produk (bukan jenis produk)
     *
     * @return \Illuminate\View\View View keranjang belanja
     */
    public function index()
    {
        // ========================================
        // STEP 1: AMBIL SEMUA ITEM DI CART
        // ========================================
        // Ambil semua item di keranjang dari CartService
        $cartItems = $this->cartService->getCartItems();

        // ========================================
        // STEP 2: HITUNG SUBTOTAL
        // ========================================
        // Hitung subtotal untuk semua item (total harga sebelum diskon)
        $cartSubtotal = $this->cartService->getSubtotal();

        // ========================================
        // STEP 3: HITUNG TOTAL BERAT
        // ========================================
        // Hitung total berat produk di keranjang (untuk kalkulasi ongkir)
        // Berat penting karena ongkir JNE/J&T berdasarkan berat
        $totalWeight = 0;
        foreach ($cartItems as $item) {
            $product = $item['product'] ?? null;
            if ($product) {
                $weightPerUnit = $product->weight ?? 0;  // Berat per unit produk dalam gram
                $quantity = $item['quantity'];
                $totalWeight += $weightPerUnit * $quantity;
            }
        }

        // ========================================
        // STEP 4: HITUNG DISCOUNT & TOTAL AKHIR
        // ========================================
        // Nilai-nilai lain yang mungkin ditampilkan di keranjang
        $discount = 0; // Placeholder untuk diskon (bisa dikembangkan untuk promo/voucher)
        $cartTotal = $cartSubtotal - $discount; // Total akhir yang harus dibayar

        // ========================================
        // STEP 5: HITUNG TOTAL ITEMS
        // ========================================
        // Hitung total item (jumlah satuan produk, bukan jenis produk)
        // Contoh: 5 Indomie + 1 Telur = 6 items (bukan 2 jenis)
        $totalItems = $this->cartService->getTotalItems();

        // ========================================
        // STEP 6: RETURN VIEW DENGAN DATA
        // ========================================
        // Return view keranjang dengan semua data yang diperlukan
        return view('customer.keranjang', compact('cartItems', 'cartSubtotal', 'discount', 'totalWeight', 'cartTotal', 'totalItems'));
    }

    /**
     * Menambahkan produk ke keranjang
     * 
     * ==========================================================================
     * FITUR: ADD TO CART - TAMBAH PRODUK KE KERANJANG
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini menangani "Add to Cart" seperti di e-commerce
     * - Customer klik tombol "+ Keranjang", produk masuk cart
     * - Jika produk sudah ada, quantity bertambah
     * - Jika produk belum ada, ditambahkan sebagai item baru
     * 
     * VALIDASI:
     * - product_id: Harus ada di database (exist check)
     * - product_variant_id: Opsional (jika produk punya varian)
     * - quantity: Min 1, max 99 (mencegah stockpiling berlebihan)
     *
     * @param Request $request Objek request HTTP
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function add(Request $request)
    {
        // ========================================
        // STEP 1: VALIDASI INPUT
        // ========================================
        // Validasi input dari form/modal add to cart
        $request->validate([
            'product_id' => 'required|exists:products,id',  // Produk harus valid
            'product_variant_id' => 'nullable|exists:product_variants,id', // Varian opsional
            'quantity' => 'required|integer|min:1|max:99',  // Qty 1-99
        ]);

        // ========================================
        // STEP 2: ADD TO CART VIA SERVICE
        // ========================================
        // Tambahkan produk ke keranjang via CartService
        // Service akan handle logic:
        // - Cek apakah produk sudah ada di cart
        // - Jika ada → update quantity
        // - Jika belum → create new cart item
        $cartItem = $this->cartService->addItem(
            $request->product_id,
            $request->quantity,
            $request->product_variant_id
        );

        // ========================================
        // STEP 3: RETURN JSON RESPONSE
        // ========================================
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'data' => $cartItem
        ]);
    }

    /**
     * Mengupdate kuantitas produk di keranjang
     * 
     * ==========================================================================
     * FITUR: UPDATE CART - EDIT KUANTITAS PRODUK
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle update quantity produk di cart
     * - Customer bisa tambah (+) atau kurang (-) quantity
     * - Jika quantity = 0, produk otomatis dihapus dari cart
     * - Max quantity 99 untuk mencegah stockpiling berlebihan
     * 
     * KASUS PENGGUNAAN:
     * 1. Customer klik "+" → Quantity bertambah
     * 2. Customer klik "-" → Quantity berkurang
     * 3. Customer input manual → Update sesuai input
     * 4. Quantity = 0 → Produk dihapus (seperti "Remove")
     *
     * @param Request $request Objek request HTTP (harus berisi 'quantity')
     * @param int $id ID item cart yang akan diupdate
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function update(Request $request, $id)
    {
        // ========================================
        // STEP 1: VALIDASI QUANTITY
        // ========================================
        // Validasi quantity (min 0, max 99)
        $request->validate([
            'quantity' => 'required|integer|min:0|max:99',
        ]);

        // ========================================
        // STEP 2: UPDATE QUANTITY VIA SERVICE
        // ========================================
        // Update quantity via CartService
        $success = $this->cartService->updateItem($id, $request->quantity);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kuantitas'
            ], 404);
        }

        // ========================================
        // STEP 3: HANDLE QUANTITY = 0 (REMOVE)
        // ========================================
        // Jika quantity = 0, item dihapus dari keranjang
        // Ini seperti fitur "Remove from Cart"
        if ($request->quantity <= 0) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari keranjang'
            ]);
        }

        // ========================================
        // STEP 4: RETURN UPDATED ITEM
        // ========================================
        // Ambil ulang item yang diperbarui untuk response
        $cartItems = $this->cartService->getCartItems();
        $updatedItem = $cartItems->firstWhere(function ($item) use ($id) {
            return ($item['id'] ?? $item->id) == $id;
        });

        return response()->json([
            'success' => true,
            'message' => 'Kuantitas berhasil diperbarui',
            'data' => $updatedItem
        ]);
    }

    /**
     * Menghapus produk dari keranjang
     * 
     * ==========================================================================
     * FITUR: REMOVE FROM CART - HAPUS PRODUK
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle penghapusan produk dari cart
     * - Seperti "taruh kembali produk ke rak" di supermarket
     * - Customer batal beli produk ini
     * 
     * ANALOGI:
     * Seperti di supermarket: Ambil produk dari keranjang, taruh kembali di rak
     *
     * @param int $id ID item cart yang akan dihapus
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function remove($id)
    {
        // ========================================
        // STEP 1: REMOVE VIA SERVICE
        // ========================================
        // Hapus item dari keranjang via CartService
        $success = $this->cartService->removeItem($id);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus item'
            ], 404);
        }

        // ========================================
        // STEP 2: RETURN RESPONSE
        // ========================================
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus dari keranjang'
        ]);
    }

    /**
     * Mengosongkan keranjang belanja
     * 
     * ==========================================================================
     * FITUR: CLEAR CART - KOSONGKAN KERANJANG
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle pengosongan cart setelah checkout
     * - Seperti "kembalikan keranjang kosong" setelah bayar di kasir
     * - Cart di-DELETE semua, siap untuk belanja berikutnya
     * 
     * KAPAN DIPANGGIL:
     * - Setelah checkout berhasil
     * - Customer mau batalin semua belanjaan
     * - Reset cart manual
     *
     * @return \Illuminate\Http\JsonResponse JSON response success
     */
    public function clear()
    {
        // ========================================
        // STEP 1: CLEAR ALL ITEMS
        // ========================================
        // Hapus semua item dari keranjang
        $this->cartService->clearCart();

        // ========================================
        // STEP 2: RETURN RESPONSE
        // ========================================
        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan'
        ]);
    }

    /**
     * Mendapatkan jumlah total item di keranjang
     * 
     * ==========================================================================
     * FITUR: CART COUNT - BADGE JUMLAH ITEM
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle pengambilan total item di cart
     * - Digunakan untuk badge di icon keranjang (navbar/header)
     * - Contoh: Icon keranjang dengan angka "5" = ada 5 item
     * 
     * KAPAN DIPANGGIL:
     * - Setiap kali halaman di-load
     * - Setelah add to cart (update badge)
     * - Setelah update/remove item (update badge)
     *
     * @return \Illuminate\Http\JsonResponse JSON dengan total count
     */
    public function getCartCount()
    {
        // ========================================
        // STEP 1: GET TOTAL ITEMS
        // ========================================
        // Hitung total item di keranjang
        $totalItems = $this->cartService->getTotalItems();

        // ========================================
        // STEP 2: RETURN JSON RESPONSE
        // ========================================
        return response()->json([
            'success' => true,
            'count' => $totalItems
        ]);
    }

    /**
     * Sinkronisasi keranjang dari localStorage ke session database
     * 
     * ==========================================================================
     * FITUR: SYNC CART - GUEST → LOGGED IN USER
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle sinkronisasi cart dari guest user ke logged in user
     * - Guest user: Cart disimpan di localStorage browser (temporary)
     * - Setelah login: Cart di-sync dari localStorage ke database
     * - Cart di-merge (digabung), bukan ditimpa
     * 
     * CONTOH KASUS:
     * 1. User belum login → Add 3 produk ke cart (localStorage)
     * 2. User login → Cart di-sync ke database
     * 3. Cart localStorage + Cart database = Digabung
     * 4. User sekarang punya cart di database (permanent)
     * 
     * FLOW:
     * 1. Guest user add to cart → localStorage
     * 2. User klik login → Form login
     * 3. Setelah login sukses → Sync cart ke database
     * 4. localStorage di-clear, cart sekarang di database
     *
     * @param Request $request Objek request HTTP (harus berisi 'cart_data' JSON string)
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function syncCart(Request $request)
    {
        // ========================================
        // STEP 1: VALIDASI INPUT
        // ========================================
        // Validasi input cart_data (JSON string dari localStorage)
        $request->validate([
            'cart_data' => 'required|string'
        ]);

        try {
            // ========================================
            // STEP 2: SYNC CART TO DATABASE
            // ========================================
            // Simpan data keranjang dari localStorage ke session database
            // CartService akan handle merge logic
            $this->cartService->saveCartFromLocalStorage($request->cart_data);

            // ========================================
            // STEP 3: RETURN RESPONSE
            // ========================================
            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil disinkronkan',
                'merged' => true  // Flag bahwa cart sudah di-merge
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyinkronkan keranjang: ' . $e->getMessage()
            ], 500);
        }
    }
}