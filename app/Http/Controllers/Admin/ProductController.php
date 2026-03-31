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

// ========================================================================
// ADMIN PRODUCT CONTROLLER - MONITORING PRODUK & MODERASI
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Controller ini untuk admin monitor & moderate SEMUA produk di platform
// - Admin bisa lihat produk dari SEMUA seller (global view)
// - Fitur moderasi: approve, reject, suspend produk & review
//
// FITUR UTAMA:
// 1. Product Management - Lihat semua produk, filter, search
// 2. Category Management - Kelola kategori & subkategori
// 3. Review Moderation - Approve/reject ulasan produk
// 4. Product Moderation - Suspend produk yang melanggar
// 5. Global Search - Cari produk dari semua seller
//
// ANALOGI:
// Seperti manajer mall yang bisa:
// - Cek semua produk di semua toko (monitoring)
// - Tutup toko yang jual barang terlarang (suspend)
// - Kelola kategori toko di mall (category management)
// - Moderasi review customer tentang toko (review moderation)
//
// KEAMANAN:
// - Hanya admin yang bisa akses
// - Seller lain tidak bisa lihat produk seller lain
// - Admin punya full control (edit, delete, suspend)
// ========================================================================

/**
 * ProductController untuk Admin
 *
 * Controller ini menangani semua operasi manajemen produk untuk admin:
 * - Melihat semua produk dari semua penjual
 * - Approve/reject/suspend produk
 * - Manajemen kategori dan subkategori
 * - Moderasi ulasan produk (approve, reject, spam, delete)
 * - Filter dan search produk
 *
 * Hanya user dengan role 'admin' yang bisa mengakses controller ini.
 * 
 * @package App\Http\Controllers\Admin
 * @author Tim Ecommerce AKRAB
 * @version 1.0
 */
class ProductController extends Controller
{
    /**
     * Menampilkan halaman manajemen produk untuk admin
     * 
     * Method ini menangani 3 tab utama:
     * 1. Products tab - Daftar semua produk dengan filter dan search
     * 2. Categories tab - Manajemen kategori dan subkategori
     * 3. Reviews tab - Moderasi ulasan produk
     * 
     * Fitur admin:
     * - Filter: search, seller, status, kategori
     * - Pagination 10 item per halaman
     * - Optimized query untuk menghindari N+1
     * 
     * @param Request $request Objek request HTTP
     * @return \Illuminate\View\View View admin produk index
     */
    public function index(Request $request)
    {
        // Cek otorisasi - hanya admin yang boleh akses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }

        // Load kategori untuk semua tab (dibutuhkan di category panel)
        // Hanya select field yang diperlukan untuk efisiensi
        $categories = Category::with(['subcategories:id,category_id,name'])->select('id', 'name')->orderBy('name')->get();
        $mainCategories = Category::select('id', 'name')->orderBy('name')->get();

        // Cek tab mana yang aktif (products, categories, atau reviews)
        $tab = $request->get('tab', 'products');

        // Tab Reviews - Moderasi ulasan
        if ($tab === 'reviews') {
            // Fetch ulasan dengan relasi product dan user - hanya load field yang diperlukan
            $query = Review::with(['product:id,name', 'user:id,name,email']);

            // Filter berdasarkan status ulasan
            if ($request->filled('review_status')) {
                $status = $request->get('review_status');
                if ($status === 'pending') {
                    // Ulasan belum diapprove (approved_at null)
                    $query->whereNull('approved_at');
                } elseif ($status === 'approved') {
                    // Ulasan sudah diapprove
                    $query->whereNotNull('approved_at');
                } elseif ($status === 'rejected') {
                    // Ulasan ditolak
                    $query->whereNotNull('rejected_at');
                }
            }

            // Filter berdasarkan rating (1-5)
            if ($request->filled('rating')) {
                $query->where('rating', $request->get('rating'));
            }

            // Search di review text, nama produk, atau nama user
            if ($request->filled('review_search')) {
                $search = $request->get('review_search');
                $query->where(function($q) use ($search) {
                    $q->where('review_text', 'LIKE', "%{$search}%")  // Cari di teks review
                      ->orWhereHas('product', function($p) use ($search) {
                          $p->where('name', 'LIKE', "%{$search}%");  // Cari di nama produk
                      })
                      ->orWhereHas('user', function($u) use ($search) {
                          $u->where('name', 'LIKE', "%{$search}%");  // Cari di nama user
                      });
                });
            }

            // Pagination 10 item per halaman
            $reviews = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());

            return view('admin.produk.index', compact('reviews', 'tab', 'categories', 'mainCategories'));
        
        // Tab Categories - Manajemen kategori
        } elseif ($tab === 'categories') {
            // Untuk tab kategori, load data kategori dengan subkategori
            $categories = Category::with(['subcategories:id,category_id,name'])->select('id', 'name')->orderBy('name')->get();
            $mainCategories = Category::select('id', 'name')->orderBy('name')->get();

            return view('admin.produk.index', compact('categories', 'mainCategories', 'tab'));
        
        // Tab Products - Daftar produk (default)
        } else {
            // Optimized query dengan eager loading untuk mengurangi N+1 queries
            // Load relasi seller dan category dengan field minimal
            $query = Product::with(['seller:id,store_name', 'category:id,name']);

            // Filter pencarian berdasarkan nama produk atau SKU
            if ($request->has('search') && $request->search) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")  // Cari di nama produk
                      ->orWhere('sku', 'LIKE', "%{$search}%");  // Atau SKU
                })->orWhereHas('seller', function($subQuery) use ($search) {
                    // Atau cari di nama toko penjual
                    $subQuery->where('store_name', 'LIKE', "%{$search}%");
                });
            }

            // Filter berdasarkan penjual
            if ($request->has('seller_id') && $request->seller_id) {
                $query->where('seller_id', $request->seller_id);
            }

            // Filter berdasarkan status produk (active, inactive, draft, suspended, rejected)
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Filter berdasarkan kategori
            if ($request->has('category') && $request->category) {
                $query->where('category_id', $request->category);
            }

            // Urutkan dari yang terbaru
            $query->orderBy('created_at', 'desc');

            // Paginate 10 item per halaman, preserve query params
            $products = $query->paginate(10)->appends($request->query());

            // Load gambar pertama untuk setiap produk secara efisien
            // Ambil semua product ID di halaman saat ini
            $productIds = $products->pluck('id')->toArray();

            // Load gambar pertama untuk setiap produk di halaman ini
            if (!empty($productIds)) {
                // Cari ID gambar pertama untuk setiap produk
                $productImages = \DB::table('product_images')
                    ->whereIn('product_id', $productIds)
                    ->selectRaw('product_id, MIN(id) as first_image_id')
                    ->groupBy('product_id')
                    ->pluck('first_image_id', 'product_id');

                // Load record gambar yang sebenarnya
                $images = [];
                if ($productImages->isNotEmpty()) {
                    $imageRecords = \DB::table('product_images')
                        ->whereIn('id', $productImages->values()->toArray())
                        ->pluck('image_path', 'id');

                    // Mapping gambar ke produk
                    foreach ($products as $product) {
                        $firstImageId = $productImages->get($product->id);
                        $imagePath = $firstImageId ? $imageRecords->get($firstImageId) : null;

                        // Buat objek ProductImage untuk kompatibilitas dengan view
                        if ($imagePath) {
                            $firstImage = new \App\Models\ProductImage();
                            $firstImage->image_path = $imagePath;
                            $firstImage->setAttribute('image_path', $imagePath);
                            $product->setRelation('images', collect([$firstImage]));
                        } else {
                            $product->setRelation('images', collect([]));
                        }
                    }
                } else {
                    // Jika tidak ada gambar, set empty collection
                    foreach ($products as $product) {
                        $product->setRelation('images', collect([]));
                    }
                }
            }

            // Load semua penjual untuk dropdown filter (hanya field yang diperlukan)
            $sellers = Seller::select('id', 'store_name')->get();

            // Load semua kategori untuk dropdown filter
            $categories = Category::select('id', 'name')->get();

            return view('admin.produk.index', compact('products', 'sellers', 'categories', 'mainCategories', 'tab'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Tidak digunakan - admin tidak membuat produk langsung
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Tidak digunakan - admin tidak membuat produk langsung
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Tidak digunakan - admin melihat produk via index dengan filter
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Tidak digunakan - admin tidak edit produk langsung
    }

    /**
     * Update status produk (admin only)
     * 
     * Admin dapat mengubah status produk tanpa mengedit seluruh data produk.
     * Digunakan untuk moderasi produk dari seller.
     * 
     * @param Request $request Objek request HTTP (harus berisi 'status')
     * @param string $id ID produk yang akan diupdate
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function update(Request $request, $id)
    {
        // Cek otorisasi - hanya admin
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Ambil produk atau return 404
        $product = Product::findOrFail($id);

        // Update status jika ada di request
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
     * Menghapus produk (admin only)
     * 
     * Admin dapat menghapus produk manapun di sistem.
     * Hapus produk beserta semua relasinya (cascade delete).
     * 
     * @param string $id ID produk yang akan dihapus
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function destroy($id)
    {
        // Cek otorisasi - hanya admin
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Ambil produk atau return 404
        $product = Product::findOrFail($id);
        
        // Hapus produk (cascade delete akan menangani relasi)
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }

    /**
     * Approve produk (admin only)
     * 
     * Mengubah status produk menjadi 'active' sehingga dapat ditampilkan
     * dan dibeli oleh customer.
     * 
     * @param string $id ID produk yang akan diapprove
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function approveProduct($id)
    {
        // Cek otorisasi - hanya admin
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Ambil produk atau return 404
        $product = Product::findOrFail($id);
        
        // Set status active
        $product->status = 'active';
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil disetujui',
            'product' => $product
        ]);
    }

    /**
     * Reject produk (admin only)
     * 
     * Menolak produk sehingga tidak dapat ditampilkan atau dibeli.
     * Status 'rejected' menandakan produk tidak lolos moderasi.
     * 
     * @param string $id ID produk yang akan direject
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function rejectProduct($id)
    {
        // Cek otorisasi - hanya admin
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Ambil produk atau return 404
        $product = Product::findOrFail($id);
        
        // Set status rejected
        $product->status = 'rejected';
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditolak',
            'product' => $product
        ]);
    }

    /**
     * Suspend produk (admin only)
     * 
     * Menangguhkan produk sementara. Berbeda dengan reject,
     * suspend dapat dibalik kembali ke active.
     * 
     * @param string $id ID produk yang akan di-suspend
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function suspendProduct($id)
    {
        // Cek otorisasi - hanya admin
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Ambil produk atau return 404
        $product = Product::findOrFail($id);
        
        // Set status suspended
        $product->status = 'suspended';
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditangguhkan',
            'product' => $product
        ]);
    }

    /**
     * Approve ulasan produk (admin only)
     * 
     * Menyetujui ulasan untuk ditampilkan di halaman produk.
     * Ulasan yang diapprove akan visible untuk public.
     * 
     * @param string $id ID ulasan yang akan diapprove
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function approveReview($id)
    {
        try {
            // Cek otorisasi - harus login dan admin
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            // Muat relasi role untuk validasi
            $user->load('role');

            if (!$user->role || $user->role->name !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            // Ambil ulasan atau return 404
            $review = Review::findOrFail($id);
            
            // Set status approved
            $review->status = 'approved';
            $review->save();
        } catch (\Exception $e) {
            \Log::error('Error approving review: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil disetujui',
            'review' => $review
        ]);
    } // End of approveReview

    /**
     * Reject ulasan produk (admin only)
     * 
     * Menolak ulasan untuk tidak ditampilkan.
     * Ulasan rejected tidak akan muncul di halaman produk.
     * 
     * @param string $id ID ulasan yang akan direject
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function rejectReview($id)
    {
        try {
            // Cek otorisasi
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            // Muat relasi role
            $user->load('role');

            if (!$user->role || $user->role->name !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            // Ambil ulasan atau return 404
            $review = Review::findOrFail($id);
            
            // Set status rejected
            $review->status = 'rejected';
            $review->save();

            return response()->json([
                'success' => true,
                'message' => 'Ulasan berhasil ditolak',
                'review' => $review
            ]);
        } catch (\Exception $e) {
            \Log::error('Error rejecting review: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    } // End of rejectReview

    /**
     * Menghapus ulasan produk (admin only)
     * 
     * Menghapus ulasan secara permanen dari database.
     * Berbeda dengan reject, delete tidak dapat dibalik.
     * 
     * @param string $id ID ulasan yang akan dihapus
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function deleteReview($id)
    {
        try {
            // Cek otorisasi
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            // Muat relasi role untuk validasi
            $user->load('role');

            if (!$user->role || $user->role->name !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            // Ambil ulasan atau return 404
            $review = Review::findOrFail($id);
            
            // Hapus ulasan secara permanen
            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ulasan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting review: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    } // End of deleteReview

    /**
     * Mendapatkan data ulasan via AJAX untuk dynamic loading
     * 
     * Endpoint ini digunakan untuk load ulasan secara asynchronous
     * pada tab Reviews di admin panel. Support filtering dan search.
     * 
     * Filter yang didukung:
     * - review_status: approved, rejected, spam, pending
     * - rating: 1-5
     * - review_search: search text di review, produk, atau user
     * 
     * @param Request $request Objek request HTTP
     * @return \Illuminate\Http\JsonResponse JSON response dengan data ulasan
     */
    public function getReviewsAjax(Request $request)
    {
        try {
            \Log::info('AJAX Reviews Request - Start', [
                'user_id' => Auth::id(),
                'request_params' => $request->all()
            ]);

            // Cek otorisasi - hanya admin
            $user = Auth::user();
            if (!$user || !$user->role || $user->role->name !== 'admin') {
                \Log::warning('Access denied for AJAX reviews request', [
                    'user_id' => $user ? $user->id : null,
                    'role' => $user && $user->role ? $user->role->name : null
                ]);
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            // Query ulasan dengan eager loading product dan user
            $query = Review::with(['product:id,name', 'user:id,name,email']);

            // Filter berdasarkan status ulasan
            if ($request->filled('review_status')) {
                $status = $request->get('review_status');
                \Log::info('Filtering by status: ' . $status);
                $query->where('status', $status);
            }
            // Jika tidak ada filter status, tampilkan semua termasuk spam

            // Filter berdasarkan rating (1-5)
            if ($request->filled('rating')) {
                $rating = $request->get('rating');
                \Log::info('Filtering by rating: ' . $rating);
                $query->where('rating', $rating);
            }

            // Search di review text, nama produk, atau nama user
            if ($request->filled('review_search')) {
                $search = $request->get('review_search');
                \Log::info('Searching for: ' . $search);
                $query->where(function($q) use ($search) {
                    $q->where('review_text', 'LIKE', "%{$search}%")  // Cari di teks review
                      ->orWhereHas('product', function($p) use ($search) {
                          $p->where('name', 'LIKE', "%{$search}%");  // Cari di nama produk
                      })
                      ->orWhereHas('user', function($u) use ($search) {
                          $u->where('name', 'LIKE', "%{$search}%");  // Cari di nama user
                      });
                });
            }

            // Hitung total sebelum pagination untuk logging
            $totalBeforePagination = $query->count();
            \Log::info('Total reviews before pagination: ' . $totalBeforePagination);

            // Pagination 10 item per halaman, urutkan dari yang terbaru
            $reviews = $query->orderBy('created_at', 'desc')->paginate(10);

            \Log::info('Reviews pagination info', [
                'total' => $reviews->total(),
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'count_on_page' => $reviews->count()
            ]);

            // Format data untuk response AJAX
            $formattedReviews = $reviews->map(function($review) {
                \Log::debug('Formatting review: ' . $review->id, [
                    'status' => $review->status,
                    'product_name' => $review->product->name ?? 'NULL',
                    'user_name' => $review->user->name ?? 'NULL'
                ]);

                return [
                    'id' => $review->id,
                    'review_text' => $review->review_text,
                    'rating' => $review->rating,
                    'product_name' => $review->product->name ?? 'Produk Tidak Ditemukan',
                    'user_name' => $review->user->name ?? 'User Tidak Ditemukan',
                    'user_email' => $review->user->email ?? 'N/A',
                    'created_at' => $review->created_at->format('d M Y'),  // Format: 01 Jan 2024
                    'status' => $review->status,
                    'status_label' => $this->getStatusLabel($review->status),  // Label bahasa Indonesia
                    'can_action' => $review->status !== 'approved' && $review->status !== 'rejected'  // Bisa diapprove/reject jika belum diaksi
                ];
            });

            \Log::info('AJAX Reviews Request - Success', [
                'formatted_reviews_count' => count($formattedReviews),
                'pagination_total' => $reviews->total()
            ]);

            return response()->json([
                'success' => true,
                'reviews' => $formattedReviews,
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'total' => $reviews->total(),
                    'per_page' => $reviews->perPage(),
                    'has_more_pages' => $reviews->hasMorePages(),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting reviews via AJAX: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mark review as spam (admin only)
     * 
     * Menandai ulasan sebagai spam. Ulasan spam tidak akan ditampilkan
     * tetapi tidak dihapus dari database untuk keperluan audit.
     * 
     * @param string $id ID ulasan yang akan ditandai sebagai spam
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function markAsSpam($id)
    {
        try {
            // Cek otorisasi
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            // Muat relasi role
            $user->load('role');

            if (!$user->role || $user->role->name !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            // Ambil ulasan atau return 404
            $review = Review::findOrFail($id);
            
            // Set status spam
            $review->status = 'spam';
            $review->save();

            return response()->json([
                'success' => true,
                'message' => 'Ulasan berhasil ditandai sebagai spam',
                'review' => $review
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking review as spam: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper function untuk mendapatkan label status dalam bahasa Indonesia
     * 
     * Mengkonversi status code (approved, rejected, spam, pending)
     * menjadi label yang dapat ditampilkan di UI admin.
     * 
     * @param string $status Status code dari database
     * @return string Label status dalam bahasa Indonesia
     */
    private function getStatusLabel($status)
    {
        switch ($status) {
            case 'approved':
                return 'Disetujui';
            case 'rejected':
                return 'Ditolak';
            case 'spam':
                return 'Spam';
            case 'pending':
                return 'Menunggu Persetujuan';
            default:
                return $status ? ucfirst($status) : 'Menunggu Persetujuan';
        }
    }
}