<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// ========================================================================
// REVIEW CONTROLLER - ULASAN & RATING PRODUK (SOCIAL PROOF)
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Controller ini menangani sistem Review & Rating produk
// - Customer bisa kasih rating (1-5 ⭐) dan ulasan teks setelah beli
// - Ulasan berfungsi sebagai "social proof" untuk customer lain
//
// FITUR UTAMA:
// 1. Create Review - Kasih rating & ulasan setelah terima produk
// 2. Upload Media - Lampir foto/video produk real
// 3. View Product Reviews - Lihat semua ulasan untuk produk
// 4. View My Reviews - Lihat ulasan yang saya buat
// 5. Update Review - Edit ulasan yang sudah dibuat
// 6. Review Validation - Hanya yang sudah beli bisa review
//
// ANALOGI:
// Seperti Review di Tokopedia/Shopee:
// - Beli produk → Terima barang → Kasih rating ⭐⭐⭐⭐⭐
// - Tulis ulasan: "Barang bagus, sesuai deskripsi!"
// - Upload foto produk real yang diterima
// - Customer lain baca review sebelum beli
//
// VALIDASI PENTING:
// - Hanya customer yang SUDAH BELI bisa review (verifikasi via order)
// - Satu produk = Satu review per user (tidak boleh spam)
// - Order harus status "delivered" (sudah diterima)
//
// MANFAAT REVIEW:
// - Social proof: Customer lain percaya untuk beli
// - Feedback untuk seller: Improve kualitas produk
// - Rating average: Menentukan kualitas produk di marketplace
// ========================================================================

class ReviewController extends Controller
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
     * Menampilkan form untuk memberikan ulasan
     * 
     * ==========================================================================
     * FITUR: CREATE REVIEW - FORM ULASAN PRODUK
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini menampilkan form untuk customer kasih ulasan
     * - Hanya bisa diakses setelah customer terima produk (delivered)
     * - Validasi: Hanya pembeli yang bisa review produk ini
     * 
     * FLOW:
     * 1. Customer beli produk → Order delivered
     * 2. Customer klik "Kasih Ulasan" di order
     * 3. Form ditampilkan (rating + teks + upload foto)
     * 4. Customer isi & submit
     * 
     * VALIDASI:
     * - Cek apakah user adalah pembeli order ini
     * - Cek apakah sudah pernah review produk ini (anti-spam)
     *
     * @param int $orderItemId ID order item yang akan diulas
     * @return \Illuminate\View\View View form ulasan
     */
    public function create($orderItemId)
    {
        // ========================================
        // STEP 1: LOAD ORDER ITEM
        // ========================================
        // Ambil order item dengan relasi order & product
        $orderItem = OrderItem::with(['order', 'product'])->findOrFail($orderItemId);

        // ========================================
        // STEP 2: VALIDASI KEPEMILIKAN
        // ========================================
        // Pastikan hanya pembeli yang bisa memberikan ulasan
        // Security: User tidak bisa review order orang lain
        if ($orderItem->order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan memberikan ulasan untuk produk ini');
        }

        // ========================================
        // STEP 3: CEK DUPLICATE REVIEW
        // ========================================
        // Cek apakah sudah ada ulasan untuk produk ini dalam order ini
        // Satu produk = Satu review per user (tidak boleh spam)
        $existingReview = Review::where('user_id', Auth::id())
                                ->where('product_id', $orderItem->product_id)
                                ->where('order_id', $orderItem->order_id)
                                ->first();

        if ($existingReview) {
            // User sudah review produk ini sebelumnya
            return redirect()->back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini.');
        }

        // ========================================
        // STEP 4: SHOW FORM
        // ========================================
        // Tampilkan form ulasan untuk diisi
        return view('customer.ulasan.create', compact('orderItem'));
    }

    /**
     * Menyimpan ulasan baru
     * 
     * ==========================================================================
     * FITUR: STORE REVIEW - SIMPAN ULASAN KE DATABASE
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle submit form ulasan & simpan ke database
     * - Customer bisa kasih rating (1-5) + teks ulasan + upload foto
     * - Validasi ketat untuk mencegah spam/fake review
     * 
     * VALIDASI:
     * - product_id: Harus ada di database
     * - order_id: Harus order milik user ini
     * - rating: Min 1, max 5 (standar rating system)
     * - review_text: Opsional, max 1000 karakter
     * - media: Upload foto/video, max 5MB per file
     * 
     * FITUR UNGGULAN:
     * - Multi-media upload (bisa upload beberapa foto sekaligus)
     * - Direct approval (ulasan langsung tampil, tidak perlu moderasi)
     * - Anti-spam (satu produk = satu review per user)
     *
     * @param Request $request Objek request HTTP
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function store(Request $request)
    {
        // ========================================
        // STEP 1: VALIDASI INPUT
        // ========================================
        // Validasi semua input dari form ulasan
        $request->validate([
            'product_id' => 'required|exists:products,id',  // Produk harus valid
            'order_id' => 'required|exists:orders,id',  // Order harus valid
            'rating' => 'required|integer|min:1|max:5',  // Rating 1-5 bintang
            'review_text' => 'nullable|string|max:1000',  // Teks ulasan (opsional)
            'media.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120' // Max 5MB per image
        ]);

        // ========================================
        // STEP 2: VALIDASI KEPEMILIKAN ORDER
        // ========================================
        // Pastikan order milik user yang login
        // Security: User tidak bisa review order orang lain
        $order = Order::where('id', $request->order_id)
                      ->where('user_id', Auth::id())
                      ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak diizinkan memberikan ulasan untuk produk ini'
            ], 403);
        }

        // ========================================
        // STEP 3: CEK DUPLICATE REVIEW
        // ========================================
        // Cek apakah sudah ada ulasan untuk produk ini dalam order ini
        // Anti-spam: Satu produk = Satu review per user per order
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

        // ========================================
        // STEP 4: HANDLE MEDIA UPLOAD
        // ========================================
        // Handle media uploads (foto/video produk real)
        $mediaPaths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $mediaFile) {
                if ($mediaFile->isValid()) {
                    // Simpan file ke storage/public/reviews/
                    $path = $mediaFile->store('reviews', 'public');
                    $mediaPaths[] = $path;
                }
            }
        }

        // ========================================
        // STEP 5: CREATE REVIEW
        // ========================================
        // Buat ulasan baru di database
        $review = Review::create([
            'user_id' => Auth::id(),  // User yang review
            'product_id' => $request->product_id,  // Produk yang diulas
            'order_id' => $request->order_id,  // Order referensi (bukti sudah beli)
            'rating' => $request->rating,  // Rating 1-5 bintang
            'review_text' => $request->review_text,  // Teks ulasan
            'media' => !empty($mediaPaths) ? $mediaPaths : null,  // Array path foto/video
            'status' => 'approved'  // Ulasan langsung disetujui dan muncul di produk
        ]);

        // ========================================
        // STEP 6: RETURN JSON RESPONSE
        // ========================================
        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil ditambahkan',
            'review' => $review
        ]);
    }

    /**
     * Menampilkan daftar ulasan untuk produk tertentu
     * 
     * ==========================================================================
     * FITUR: VIEW PRODUCT REVIEWS - LIHAT ULASAN PRODUK
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini menampilkan semua ulasan untuk suatu produk
     * - Customer bisa baca ulasan sebelum beli (social proof)
     * - Hanya ulasan "approved" yang ditampilkan (filtered)
     * 
     * SOCIAL PROOF:
     * - Customer baca pengalaman buyer sebelumnya
     * - Lihat foto real produk yang diupload buyer
     * - Rating average mempengaruhi keputusan beli
     * 
     * @param int $productId ID produk yang ulasannya akan ditampilkan
     * @return \Illuminate\View\View View daftar ulasan produk
     */
    public function showByProduct($productId)
    {
        // ========================================
        // STEP 1: LOAD PRODUK
        // ========================================
        // Ambil produk yang akan dilihat ulasannya
        $product = Product::findOrFail($productId);
        
        // ========================================
        // STEP 2: QUERY ULASAN
        // ========================================
        // Ambil semua ulasan untuk produk ini
        // Filter: hanya status "approved" yang tampil
        // Sort: Terbaru dulu (desc)
        $reviews = Review::with('user')  // Load relasi user untuk info reviewer
                        ->where('product_id', $productId)
                        ->where('status', 'approved')  // Hanya ulasan yang disetujui
                        ->orderBy('created_at', 'desc')  // Urutkan dari yang terbaru
                        ->get();

        // ========================================
        // STEP 3: RETURN VIEW
        // ========================================
        // Return view dengan data produk & reviews
        return view('customer.ulasan.show_by_product', compact('product', 'reviews'));
    }

    /**
     * Menampilkan daftar ulasan milik user
     * 
     * ==========================================================================
     * FITUR: MY REVIEWS - LIHAT ULASAN SAYA
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini menampilkan semua ulasan yang dibuat user
     * - User bisa lihat history ulasan mereka
     * - Manage ulasan (edit/delete) dari sini
     * 
     * KEGUNAAN:
     * - Track ulasan yang sudah dibuat
     * - Edit/update ulasan jika perlu
     * - Lihat produk mana saja yang sudah direview
     *
     * @return \Illuminate\View\View View daftar ulasan user
     */
    public function index()
    {
        // ========================================
        // STEP 1: QUERY ULASAN USER LOGIN
        // ========================================
        // Ambil semua ulasan milik user yang login
        // Load relasi product & order untuk info lengkap
        $reviews = Review::with(['product', 'order'])
                        ->where('user_id', Auth::id())  // Filter: hanya ulasan user ini
                        ->orderBy('created_at', 'desc')  // Urutkan dari yang terbaru
                        ->get();

        // ========================================
        // STEP 2: RETURN VIEW
        // ========================================
        // Return view dengan daftar ulasan user
        return view('customer.ulasan.index', compact('reviews'));
    }

    /**
     * API endpoint untuk mendapatkan ulasan produk
     * 
     * ==========================================================================
     * FITUR: API GET PRODUCT REVIEWS - ENDPOINT AJAX
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini adalah API endpoint untuk frontend
     * - Frontend call via AJAX untuk load reviews tanpa reload
     * - Return JSON dengan semua ulasan produk
     * 
     * USE CASE:
     * - Frontend load reviews saat user buka halaman produk
     * - Infinite scroll untuk reviews
     * - Filter/sort reviews via AJAX
     *
     * @param int $productId ID produk yang ulasannya akan diambil
     * @return \Illuminate\Http\JsonResponse JSON dengan daftar ulasan
     */
    public function getReviewsByProduct($productId)
    {
        // ========================================
        // STEP 1: QUERY ULASAN
        // ========================================
        // Ambil semua ulasan untuk produk ini
        // Filter: hanya status "approved" yang tampil
        $reviews = Review::with('user')  // Load relasi user
                        ->where('product_id', $productId)
                        ->where('status', 'approved')  // Hanya ulasan approved
                        ->orderBy('created_at', 'desc')  // Terbaru dulu
                        ->get();

        // ========================================
        // STEP 2: RETURN JSON RESPONSE
        // ========================================
        return response()->json([
            'success' => true,
            'reviews' => $reviews
        ]);
    }

    /**
     * API endpoint untuk mendapatkan ulasan milik user
     * 
     * ==========================================================================
     * FITUR: API GET USER REVIEWS - ENDPOINT AJAX
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini adalah API endpoint untuk frontend
     * - Frontend call via AJAX untuk load ulasan user
     * - Data diformat khusus untuk frontend display
     * 
     * FORMAT DATA:
     * - id: Review ID
     * - timeISO: Timestamp dalam format ISO
     * - rating: Rating 1-5
     * - kv: Key-value pairs (ulasan text)
     * - product: Info produk (title, variant, URL)
     * - images: Media uploads (foto/video)
     * 
     * @return \Illuminate\Http\JsonResponse JSON dengan daftar ulasan user
     */
    public function getUserReviews()
    {
        // ========================================
        // STEP 1: LOGGING FOR DEBUGGING
        // ========================================
        // Log untuk debugging & tracking
        \Log::info('API /api/user-reviews: Fetching reviews for user ID: ' . Auth::id());

        // ========================================
        // STEP 2: QUERY ULASAN USER LOGIN
        // ========================================
        // Ambil semua ulasan milik user yang login
        // Load relasi product & order untuk info lengkap
        $reviews = Review::with(['product', 'order'])
                        ->where('user_id', Auth::id())  // Filter: hanya ulasan user ini
                        ->orderBy('created_at', 'desc')  // Urutkan dari yang terbaru
                        ->get();

        \Log::info('API /api/user-reviews: Found ' . $reviews->count() . ' reviews for user ID: ' . Auth::id());

        // ========================================
        // STEP 3: FORMAT DATA UNTUK FRONTEND
        // ========================================
        // Format data agar mudah ditampilkan di frontend
        $formattedReviews = $reviews->map(function($review) {
            \Log::debug('API /api/user-reviews: Processing review ID: ' . $review->id . ' for product: ' . ($review->product ? $review->product->name : 'NULL'));

            return [
                'id' => $review->id,  // Review ID
                'timeISO' => $review->created_at->toISOString(),  // Timestamp ISO format
                'rating' => $review->rating,  // Rating 1-5
                // Key-value pairs untuk ulasan text
                'kv' => $review->review_text ? [['Ulasan', $review->review_text]] : [],
                // Info produk yang diulas
                'product' => [
                    'title' => $review->product->name ?? 'Produk Tidak Ditemukan',  // Nama produk
                    'variant' => '',  // Varian produk (jika ada)
                    'url' => $review->product ? route('produk.detail', $review->product->id) : '#'  // URL ke produk
                ],
                // Media uploads (foto/video)
                'images' => $review->media ? collect((array)$review->media)->map(function($path) {
                    return [
                        'name' => basename($path),  // Nama file
                        'size' => 0,  // Tidak ada info ukuran dari storage
                        'type' => 'image',  // Tipe media
                        'url' => asset('storage/' . $path)  // URL ke file
                    ];
                })->toArray() : []
            ];
        });

        \Log::info('API /api/user-reviews: Returning ' . $formattedReviews->count() . ' formatted reviews');

        // ========================================
        // STEP 4: FORMAT USER INFO
        // ========================================
        $user = [
            'name' => Auth::user()->name  // Nama user yang login
        ];

        // ========================================
        // STEP 5: RETURN JSON RESPONSE
        // ========================================
        return response()->json([
            'success' => true,
            'user' => $user,
            'reviews' => $formattedReviews
        ]);
    }

    /**
     * Update user's review
     * 
     * ==========================================================================
     * FITUR: UPDATE REVIEW - EDIT ULASAN
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle update/edit ulasan yang sudah dibuat
     * - Customer bisa edit rating, teks, atau tambah foto baru
     * - Validasi: Hanya pemilik ulasan yang bisa edit
     * 
     * KEGUNAAN:
     * - Edit rating (misal: dari 3⭐ jadi 5⭐)
     * - Update teks ulasan
     * - Tambah foto baru (append, bukan replace)
     * 
     * VALIDASI:
     * - rating: Min 1, max 5
     * - review_text: Max 1000 karakter
     * - media: Max 5MB per file
     *
     * @param Request $request Objek request HTTP
     * @param int $reviewId ID ulasan yang akan diupdate
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function updateReview(Request $request, $reviewId)
    {
        // ========================================
        // STEP 1: VALIDASI INPUT
        // ========================================
        // Validasi input dari form edit ulasan
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',  // Rating 1-5
            'review_text' => 'nullable|string|max:1000',  // Teks ulasan
            'media.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120' // Max 5MB per image
        ]);

        // ========================================
        // STEP 2: FIND REVIEW BY ID
        // ========================================
        // Cari ulasan berdasarkan ID dan user_id
        // Pastikan user hanya bisa edit ulasan sendiri
        $review = Review::where('id', $reviewId)
                        ->where('user_id', Auth::id())
                        ->first();

        if (!$review) {
            // Ulasan tidak ditemukan atau bukan milik user ini
            return response()->json([
                'success' => false,
                'message' => 'Ulasan tidak ditemukan atau Anda tidak memiliki izin untuk mengedit ulasan ini'
            ], 404);
        }

        // ========================================
        // STEP 3: HANDLE MEDIA UPLOAD
        // ========================================
        // Handle media uploads (append ke media yang sudah ada)
        $mediaPaths = $review->media ? json_decode($review->media, true) : [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $mediaFile) {
                if ($mediaFile->isValid()) {
                    // Simpan file baru ke storage
                    $path = $mediaFile->store('reviews', 'public');
                    $mediaPaths[] = $path;  // Append ke array media
                }
            }
        }

        // ========================================
        // STEP 4: UPDATE REVIEW
        // ========================================
        // Update ulasan dengan data baru
        $review->update([
            'rating' => $request->rating,  // Update rating
            'review_text' => $request->review_text,  // Update teks ulasan
            'media' => !empty($mediaPaths) ? $mediaPaths : null,  // Update media (append)
        ]);

        // ========================================
        // STEP 5: RETURN JSON RESPONSE
        // ========================================
        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil diperbarui',
            'review' => $review
        ]);
    }

    /**
     * Menampilkan halaman untuk memberikan ulasan untuk semua item dalam pesanan
     * 
     * ==========================================================================
     * FITUR: REVIEW ALL ITEMS - ULAS SEMUA PRODUK DI ORDER
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini handle ulasan untuk semua produk dalam satu order
     * - Customer bisa ulas semua produk sekaligus (bulk review)
     * - Lebih efisien daripada ulas satu-satu
     * 
     * FLOW:
     * 1. Customer terima order (delivered)
     * 2. Klik "Ulas Semua Produk"
     * 3. Tampil list semua produk di order
     * 4. Customer bisa ulas satu per satu atau langsung semua
     * 
     * FITUR:
     * - Show status review untuk setiap produk (sudah/belum)
     * - Quick navigation ke form review
     * - Track progress review (X dari Y produk sudah diulas)
     *
     * @param string $orderNumber Nomor order yang akan diulas
     * @return \Illuminate\View\View View review untuk semua item
     */
    public function showReviewPageForOrder($orderNumber)
    {
        // ========================================
        // STEP 1: FIND ORDER BY NUMBER
        // ========================================
        // Cari order berdasarkan nomor pesanan
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // ========================================
        // STEP 2: VALIDASI KEPEMILIKAN
        // ========================================
        // Pastikan hanya pembeli yang bisa memberikan ulasan
        // Security: User tidak bisa review order orang lain
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan memberikan ulasan untuk pesanan ini');
        }

        // ========================================
        // STEP 3: LOAD ORDER ITEMS
        // ========================================
        // Ambil semua item dalam pesanan
        // Load relasi product & seller untuk info lengkap
        $orderItems = $order->items()->with(['product', 'product.seller'])->get();

        // ========================================
        // STEP 4: CEK STATUS REVIEW
        // ========================================
        // Cek apakah sudah ada ulasan untuk masing-masing produk
        // Track status review untuk setiap item
        $itemsWithReviewStatus = $orderItems->map(function ($item) use ($order) {
            // Cek apakah user sudah review produk ini
            $existingReview = Review::where('user_id', Auth::id())
                                    ->where('product_id', $item->product_id)
                                    ->where('order_id', $order->id)
                                    ->first();

            return [
                'id' => $item->id,  // Order item ID
                'product' => $item->product,  // Info produk
                'quantity' => $item->quantity,  // Jumlah beli
                'unit_price' => $item->unit_price,  // Harga per unit
                'has_review' => $existingReview !== null,  // Status: sudah/belum review
                'review_id' => $existingReview ? $existingReview->id : null  // ID review jika sudah ada
            ];
        });

        // ========================================
        // STEP 5: RETURN VIEW
        // ========================================
        // Return view dengan data order & status review
        return view('customer.ulasan.create_for_order', compact('order', 'itemsWithReviewStatus'));
    }

    /**
     * Menampilkan halaman daftar semua ulasan (public)
     *
     * ==========================================================================
     * FITUR: HALAMAN ULASAN - PUBLIC REVIEWS PAGE
     * ==========================================================================
     * UNTUK SIDANG:
     * - Method ini menampilkan halaman daftar semua ulasan
     * - Berbeda dengan index() yang menampilkan ulasan milik user login
     * - Halaman ini bersifat public (bisa diakses semua orang)
     *
     * KEGUNAAN:
     * - Social proof: Customer lain bisa lihat ulasan buyer sebelumnya
     * - Transparency: Semua ulasan ditampilkan secara terbuka
     * - Trust: Meningkatkan kepercayaan customer baru
     *
     * @return \Illuminate\View\View View halaman ulasan public
     */
    public function halamanUlasan()
    {
        // ========================================
        // STEP 1: QUERY SEMUA ULASAN
        // ========================================
        // Ambil semua ulasan yang sudah disetujui (approved)
        // Load relasi user, product, order untuk info lengkap
        // Sort: Terbaru dulu (desc)
        $reviews = Review::with(['user', 'product', 'order'])
                        ->where('status', 'approved')  // Hanya ulasan yang disetujui
                        ->orderBy('created_at', 'desc')  // Urutkan dari yang terbaru
                        ->paginate(12);  // Pagination 12 per halaman

        // ========================================
        // STEP 2: RETURN VIEW
        // ========================================
        // Return view dengan daftar ulasan
        return view('customer.koleksi.halaman_ulasan', compact('reviews'));
    }
}