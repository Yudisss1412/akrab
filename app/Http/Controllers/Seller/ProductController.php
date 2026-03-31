<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

// ========================================================================
// PRODUCT CONTROLLER - MANAJEMEN PRODUK UNTUK PENJUAL
// ========================================================================
// UNTUK SIDANG SKRIPSI:
// - Controller ini menangani CRUD (Create, Read, Update, Delete) produk
// - Setiap penjual hanya bisa mengelola produk mereka sendiri (security)
// - Admin bisa mengelola semua produk dari semua penjual
// - Fitur: Upload multiple images, auto-resize ke 16:9, varian produk
//
// FITUR UNGGULAN:
// 1. Upload Multiple Images - Penjual bisa upload beberapa foto produk
// 2. Auto Image Resize - Gambar otomatis di-resize ke rasio 16:9
// 3. Primary Image Selection - Gambar pertama otomatis jadi thumbnail
// 4. Product Variants - Support varian (ukuran, warna, dll)
// 5. Stock Management - Manajemen stok real-time
// 6. Status Management - Draft, Active, Inactive
// 7. Search & Filter - Cari by nama/deskripsi, filter by kategori
//
// KEAMANAN:
// - Autorisasi berbasis role (seller/admin only)
// - Ownership validation - seller hanya bisa edit produk sendiri
// - Transaction safety - DB transaction untuk data consistency
// - Image validation - Format & ukuran file divalidasi
//
// ALUR UPLOAD GAMBAR:
// 1. User pilih file gambar (min 1, max 2MB per file)
// 2. Validasi format (jpeg, png, jpg, gif)
// 3. Resize gambar ke rasio 16:9 (function processImageTo16By9)
// 4. Simpan ke storage/app/public/products/{id}/
// 5. Simpan path ke database (product_images table)
// 6. Gambar pertama ditandai is_primary = true
// ========================================================================

/**
 * ProductController untuk Seller
 *
 * Controller ini menangani semua operasi CRUD produk untuk penjual,
 * termasuk menambah, mengedit, menghapus, dan mengelola produk.
 * Hanya user dengan role 'seller' atau 'admin' yang bisa mengakses.
 * 
 * @package App\Http\Controllers\Seller
 * @author Tim Ecommerce AKRAB
 * @version 1.0
 */
class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk untuk penjual
     * 
     * Fitur:
     * - Penjual hanya bisa lihat produk miliknya sendiri
     * - Admin bisa lihat semua produk dari semua penjual
     * - Support filter: search, kategori, status
     * - Pagination 10 item per halaman
     * 
     * @param Request $request Objek request HTTP
     * @return \Illuminate\View\View View manajemen produk
     */
    public function index(Request $request)
    {
        // Cek autentikasi dan otorisasi - hanya seller dan admin yang boleh akses
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        // Jika user adalah penjual
        if ($user->role->name === 'seller') {
            // Ambil data penjual dari tabel sellers berdasarkan user_id yang login
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                \Log::info("Seller record not found for user: " . $user->id);
                // Jika tidak ada seller record, kembalikan koleksi kosong
                $products = collect();
                $categories = \App\Models\Category::all();
                return view('penjual.manajemen_produk', compact('products', 'categories'));
            }

            \Log::info("Seller found for user " . $user->id . ", seller_id: " . $seller->id);

            // Query produk milik penjual ini saja, load relasi untuk performa
            $query = Product::where('seller_id', $seller->id)
                ->with(['variants', 'category', 'subcategory', 'images', 'approvedReviews']);
        }
        // Jika user adalah admin
        else {
            // Admin bisa lihat semua produk dari semua penjual
            $query = Product::with(['variants', 'category', 'subcategory', 'images', 'approvedReviews']);
        }

        // Filter pencarian: cari berdasarkan nama atau deskripsi produk
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")  // Cari di nama produk
                  ->orWhere('description', 'LIKE', "%{$search}%");  // Atau deskripsi
            });
        }

        // Filter berdasarkan kategori
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter berdasarkan status produk (active, inactive, draft)
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Urutkan dari yang terbaru (descending)
        $query->orderBy('created_at', 'desc');

        // Paginate 10 item per halaman, preserve query params untuk pagination
        $products = $query->paginate(10)->appends($request->query());

        // Ambil semua kategori untuk dropdown filter
        $categories = \App\Models\Category::all();

        return view('penjual.manajemen_produk', compact('products', 'categories'));
    }

    /**
     * Menampilkan form untuk menambah produk baru
     * 
     * @return \Illuminate\View\View View form tambah produk
     */
    public function create()
    {
        // Cek otorisasi - hanya seller dan admin yang boleh akses
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        // Ambil semua kategori untuk dropdown di form
        $categories = Category::all();
        return view('penjual.tambah_produk', compact('categories'));
    }

    /**
     * Menyimpan produk baru ke database
     *
     * ==========================================================================
     * FITUR UNGGULAN UNTUK SIDANG:
     * - Upload multiple images dengan validasi
     * - Auto-resize gambar ke rasio 16:9
     * - DB transaction untuk data consistency
     * - Ownership assignment (seller_id)
     * 
     * ALUR PENYIMPANAN PRODUK:
     * 1. Validasi semua input dari form
     * 2. Mulai DB transaction
     * 3. Tentukan seller_id (dari user yang login atau form)
     * 4. Buat record produk baru
     * 5. Loop dan upload semua gambar
     * 6. Commit transaction
     * 
     * VALIDASI:
     * - name, description, price, stock, weight: required
     * - category_id: harus ada di tabel categories
     * - images: minimal 1 gambar, max 2MB per file
     * - status: active, inactive, atau draft
     *
     * @param Request $request Objek request HTTP
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman produk
     */
    public function store(Request $request)
    {
        // ========================================
        // STEP 1: CEK OTORISASI
        // ========================================
        // Cek otorisasi - hanya seller dan admin yang boleh akses
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        // ========================================
        // STEP 2: VALIDASI INPUT
        // ========================================
        // Validasi semua input dari form
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',              // Nama produk, max 255 karakter
            'description' => 'required|string',                // Deskripsi produk (teks panjang)
            'price' => 'required|numeric|min:0',              // Harga, harus angka >= 0
            'category_id' => 'required|exists:categories,id', // Kategori harus valid
            'subcategory' => 'nullable|string|max:255',       // Sub-kategori opsional
            'warranty' => 'nullable|string|max:255',          // Garansi opsional
            'origin' => 'nullable|string|max:255',            // Asal produk opsional
            'material' => 'nullable|string|max:255',          // Bahan produk
            'size' => 'nullable|string|max:255',              // Ukuran produk
            'color' => 'nullable|string|max:255',             // Warna produk
            'specifications' => 'nullable|string',            // Spesifikasi (akan diubah ke array)
            'features' => 'nullable|string',                  // Fitur produk (akan diubah ke array)
            'stock' => 'required|integer|min:0',              // Stok, harus integer >= 0
            'weight' => 'required|numeric|min:0',             // Berat dalam gram, >= 0
            'images' => 'required|array|min:1',               // Minimal 1 gambar
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Format & ukuran gambar
            'status' => 'sometimes|in:active,inactive,draft', // Status produk
        ]);

        if ($validator->fails()) {
            // Jika validasi gagal, redirect kembali dengan error
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // ========================================
            // STEP 3: MULAI DATABASE TRANSACTION
            // ========================================
            // Mulai transaksi database - memastikan semua operasi berhasil atau rollback
            // Jika ada error di tengah, semua perubahan akan di-rollback
            DB::beginTransaction();

            // ========================================
            // STEP 4: KONVERSI STATUS
            // ========================================
            // Konversi status dari bahasa Indonesia ke Inggris jika diperlukan
            $status = $request->status;
            if ($status === 'aktif') {
                $status = 'active';
            } elseif ($status === 'draft') {
                $status = 'draft';
            } else {
                $status = $status ?? 'active'; // Default: active
            }

            // ========================================
            // STEP 5: TENTUKAN SELLER_ID
            // ========================================
            // Jika user adalah penjual, ambil seller_id dari data penjual yang login
            if ($user->role->name === 'seller') {
                $seller = \App\Models\Seller::where('user_id', $user->id)->first();
                if (!$seller) {
                    throw new \Exception('Seller record not found for this user');
                }
                $sellerId = $seller->id;
            }
            // Jika admin yang membuat produk, harus tentukan seller_id
            else {
                // Ambil seller_id dari input form, atau gunakan penjual pertama sebagai fallback
                $sellerId = $request->input('seller_id');
                if (!$sellerId) {
                    $seller = \App\Models\Seller::first();
                    if (!$seller) {
                        throw new \Exception('Tidak ada penjual ditemukan, admin tidak bisa membuat produk tanpa menentukan penjual.');
                    }
                    $sellerId = $seller->id;
                }
            }

            // ========================================
            // STEP 6: BUAT RECORD PRODUK
            // ========================================
            // Buat produk baru di database
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'subcategory' => $request->subcategory,
                'warranty' => $request->warranty,
                'origin' => $request->origin,
                'material' => $request->material,
                'size' => $request->size,
                'color' => $request->color,
                'specifications' => $request->specifications ? explode("\n", $request->specifications) : null, // Convert newline-separated string to array
                'features' => $request->features ? explode("\n", $request->features) : null, // Convert newline-separated string to array
                'stock' => $request->stock,
                'weight' => $request->weight,
                'seller_id' => $sellerId,
                'status' => $status
            ]);

            // ========================================
            // STEP 7: UPLOAD GAMBAR
            // ========================================
            // Proses upload gambar jika ada file yang diupload
            if ($request->hasFile('images')) {
                $files = $request->file('images');

                foreach ($files as $index => $image) {
                    // Simpan gambar ke storage dengan auto-resize ke 16:9
                    $path = $this->processImageTo16By9($image, 'products/' . $product->id);

                    // Simpan record gambar ke tabel product_images
                    $productImage = $product->images()->create([
                        'image_path' => $path,
                        'alt_text' => $request->name  // Gunakan nama produk sebagai alt text
                    ]);

                    // ========================================
                    // SET GAMBAR PRIMARY (THUMBNAIL)
                    // ========================================
                    // Gambar pertama ditandai sebagai gambar utama (primary)
                    // Ini akan jadi thumbnail yang ditampilkan di listing
                    if ($index === 0) {
                        $productImage->update([
                            'is_primary' => true
                        ]);
                    }
                }
            }

            // ========================================
            // STEP 8: COMMIT TRANSACTION
            // ========================================
            // Commit transaksi - semua operasi berhasil
            DB::commit();

            return redirect()->route('penjual.produk')->with('success', 'Produk berhasil ditambahkan');
        } catch (\Exception $e) {
            // ========================================
            // STEP 9: ROLLBACK JIKA ERROR
            // ========================================
            // Rollback transaksi jika ada error
            DB::rollBack();

            \Log::error('Error creating product: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan produk: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail produk
     * 
     * Menampilkan informasi lengkap produk termasuk:
     * - Data produk utama
     * - Varian produk
     * - Gambar produk
     * - Ulasan dari pembeli
     * - Statistik (total penjualan, revenue, rating)
     * 
     * @param string $id ID produk
     * @return \Illuminate\View\View View detail produk
     */
    public function show($id)
    {
        // Validasi ID produk - tolak jika ID tidak valid
        if ($id === 'undefined' || $id === null || $id === '') {
            \Log::error("Invalid product ID received: " . var_export($id, true));
            abort(404, 'ID produk tidak valid');
        }

        \Log::info("Seller ProductController@show called with id: " . $id);

        $user = Auth::user();
        \Log::info("Current user: " . ($user ? $user->name . " (ID: " . $user->id . ")" : "Guest"));

        // Cek otorisasi - hanya seller dan admin
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            \Log::info("Access denied - invalid role");
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        // Query produk dengan load semua relasi yang diperlukan
        $productQuery = Product::where('id', $id)
            ->with(['variants', 'category', 'subcategory', 'images', 'reviews.user']);

        \Log::info("Product ID: " . $id);

        // Jika user adalah penjual, hanya bisa lihat produk miliknya sendiri
        if ($user->role->name === 'seller') {
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            \Log::info("Seller record for user: " . ($seller ? "ID " . $seller->id : "Not found"));

            if (!$seller) {
                \Log::info("Access denied - seller record not found for user: " . $user->id);
                abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
            }
            // Filter hanya produk milik penjual ini
            $productQuery = $productQuery->where('seller_id', $seller->id);
            \Log::info("Added seller filter - seller_id: " . $seller->id);
        }

        try {
            // Ambil produk atau return 404 jika tidak ditemukan
            $product = $productQuery->firstOrFail();
            \Log::info("Product found: " . $product->name . " (ID: " . $product->id . ")");
        } catch (\Exception $e) {
            \Log::info("Product not found or not owned by seller: " . $e->getMessage());
            abort(404, 'Produk tidak ditemukan');
        }

        // Hitung statistik produk untuk dashboard
        $totalSales = $product->orderItems()->sum('quantity');  // Total quantity terjual
        $totalRevenue = $product->orderItems()->sum(\DB::raw('quantity * price'));  // Total pendapatan
        $averageRating = $product->reviews()->avg('rating') ?? 0;  // Rating rata-rata
        $totalReviews = $product->reviews()->count();  // Jumlah total ulasan

        \Log::info("About to return view with product: " . $product->name);

        return view('penjual.detail_produk', compact('product', 'totalSales', 'totalRevenue', 'averageRating', 'totalReviews'));
    }

    /**
     * Menampilkan form edit produk
     * 
     * @param string $id ID produk yang akan diedit
     * @return \Illuminate\View\View View form edit produk
     */
    public function edit($id)
    {
        // Cek otorisasi
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        // Query produk dengan relasi yang diperlukan
        $product = Product::where('id', $id)
            ->with(['variants', 'category', 'subcategory', 'images']);

        // Jika user adalah penjual, hanya bisa edit produk miliknya sendiri
        if ($user->role->name === 'seller') {
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
            }
            // Filter hanya produk milik penjual ini
            $product = $product->where('seller_id', $seller->id);
        }

        // Ambil produk atau return 404
        $product = $product->firstOrFail();

        // Ambil semua kategori untuk dropdown di form
        $categories = Category::all();

        return view('penjual.edit_produk', compact('product', 'categories'));
    }

    /**
     * Mengupdate produk yang sudah ada
     * 
     * Validasi input sama seperti saat create, kecuali:
     * - images bersifat opsional (tidak harus upload baru)
     * - status bisa tetap menggunakan status lama jika tidak diubah
     * 
     * @param Request $request Objek request HTTP
     * @param string $id ID produk yang akan diupdate
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman produk
     */
    public function update(Request $request, $id)
    {
        // Cek otorisasi
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        // Query produk - akan difilter berdasarkan ownership jika seller
        $product = Product::where('id', $id);

        // Jika user adalah penjual, hanya bisa update produk miliknya sendiri
        if ($user->role->name === 'seller') {
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
            }
            // Filter hanya produk milik penjual ini
            $product = $product->where('seller_id', $seller->id);
        }

        // Ambil produk atau return 404
        $product = $product->firstOrFail();

        // Validasi input dari form
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory' => 'nullable|string|max:255',
            'warranty' => 'nullable|string|max:255',
            'origin' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'specifications' => 'nullable|string',
            'features' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|numeric|min:0',
            'images' => 'array',  // Opsional saat update
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'sometimes|in:active,inactive,draft',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Konversi status dari bahasa Indonesia ke Inggris jika diperlukan
            $status = $request->status;
            if ($status === 'aktif') {
                $status = 'active';
            } elseif ($status === 'draft') {
                $status = 'draft';
            } else {
                // Jika tidak ada input status, gunakan status lama
                $status = $status ?? $product->status;
            }

            // Update data produk
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'subcategory' => $request->subcategory,
                'warranty' => $request->warranty,
                'origin' => $request->origin,
                'material' => $request->material,
                'size' => $request->size,
                'color' => $request->color,
                'specifications' => $request->specifications ? explode("\n", $request->specifications) : null,
                'features' => $request->features ? explode("\n", $request->features) : null,
                'stock' => $request->stock,
                'weight' => $request->weight,
                'status' => $status,
            ]);

            // Upload gambar baru jika ada
            if ($request->hasFile('images')) {
                $files = $request->file('images');

                foreach ($files as $index => $image) {
                    // Simpan gambar ke storage
                    $path = $this->processImageTo16By9($image, 'products/' . $product->id);

                    // Simpan record gambar ke tabel product_images
                    $productImage = $product->images()->create([
                        'image_path' => $path,
                        'alt_text' => $request->name
                    ]);

                    // Jika ini gambar pertama dari upload baru, jadikan primary
                    if ($index === 0) {
                        // Hapus tanda primary dari gambar-gambar sebelumnya
                        $product->images()->update(['is_primary' => false]);

                        // Tandai gambar baru sebagai primary
                        $productImage->update([
                            'is_primary' => true
                        ]);
                    }
                }
            }

            // Commit transaksi
            DB::commit();

            return redirect()->route('penjual.produk')->with('success', 'Produk berhasil diperbarui');
        } catch (\Exception $e) {
            // Rollback transaksi jika ada error
            DB::rollBack();

            \Log::error('Error updating product: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui produk: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menghapus produk dari database
     *
     * ==========================================================================
     * PROSES PENGHAPUSAN PRODUK (CASCADE DELETE):
     * 1. Hapus semua file gambar dari storage (folder public)
     * 2. Hapus record gambar dari tabel product_images
     * 3. Hapus varian produk (cascade delete di database)
     * 4. Hapus produk utama
     * 
     * KEAMANAN:
     * - Hanya seller yang punya produk bisa hapus
     * - Admin bisa hapus semua produk
     * - DB transaction untuk consistency
     *
     * @param string $id ID produk yang akan dihapus
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function destroy($id)
    {
        // ========================================
        // STEP 1: CEK OTORISASI
        // ========================================
        // Cek otorisasi - hanya seller dan admin yang boleh hapus produk
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // ========================================
        // STEP 2: QUERY PRODUK
        // ========================================
        // Query produk - akan difilter berdasarkan ownership jika seller
        $product = Product::where('id', $id);

        // ========================================
        // STEP 3: VALIDASI KEPEMILIKAN (SELLER ONLY)
        // ========================================
        // Jika user adalah penjual, hanya bisa hapus produk miliknya sendiri
        if ($user->role->name === 'seller') {
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Seller record tidak ditemukan.'], 403);
            }
            // Filter hanya produk milik penjual ini
            $product = $product->where('seller_id', $seller->id);
        }

        // ========================================
        // STEP 4: AMBIL PRODUK
        // ========================================
        // Ambil produk atau return 404 jika tidak ditemukan
        $product = $product->firstOrFail();

        try {
            // ========================================
            // STEP 5: MULAI DATABASE TRANSACTION
            // ========================================
            // Mulai transaksi database - semua atau tidak sama sekali
            DB::beginTransaction();

            // ========================================
            // STEP 6: HAPUS FILE GAMBAR DARI STORAGE
            // ========================================
            // Hapus semua file gambar dari storage (folder public)
            // Ini penting untuk free disk space dan avoid orphaned files
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            // ========================================
            // STEP 7: HAPUS RECORD GAMBAR (DATABASE)
            // ========================================
            // Hapus record gambar dari database (cascade delete)
            $product->images()->delete();

            // ========================================
            // STEP 8: HAPUS VARIAN PRODUK
            // ========================================
            // Hapus varian produk (cascade delete)
            // Varian akan terhapus otomatis karena foreign key constraint
            $product->variants()->delete();

            // ========================================
            // STEP 9: HAPUS PRODUK UTAMA
            // ========================================
            // Hapus produk utama dari database
            $product->delete();

            // ========================================
            // STEP 10: COMMIT TRANSACTION
            // ========================================
            // Commit transaksi - semua operasi berhasil
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus']);
        } catch (\Exception $e) {
            // ========================================
            // STEP 11: ROLLBACK JIKA ERROR
            // ========================================
            // Rollback transaksi jika ada error
            DB::rollBack();

            \Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus produk: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengupdate stok produk
     * 
     * Digunakan untuk update stok secara cepat tanpa edit seluruh produk.
     * Biasanya dipanggil via AJAX dari dashboard penjual.
     * 
     * @param Request $request Objek request HTTP (harus berisi 'stock')
     * @param string $id ID produk yang akan diupdate stoknya
     * @return \Illuminate\Http\JsonResponse JSON response dengan data produk terbaru
     */
    public function updateStock(Request $request, $id)
    {
        // Cek otorisasi
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        // Query produk - akan difilter berdasarkan ownership jika seller
        $product = Product::where('id', $id);

        // Jika user adalah penjual, hanya bisa update produk miliknya sendiri
        if ($user->role->name === 'seller') {
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
            }
            // Filter hanya produk milik penjual ini
            $product = $product->where('seller_id', $seller->id);
        }

        // Ambil produk atau return 404
        $product = $product->firstOrFail();

        // Validasi input stock - harus integer >= 0
        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        // Update stok produk
        $product->update(['stock' => $request->stock]);

        return response()->json([
            'success' => true,
            'message' => 'Stok produk berhasil diperbarui',
            'product' => $product  // Return produk terbaru untuk update UI
        ]);
    }

    /**
     * Menghapus gambar produk
     * 
     * Menghapus satu gambar tertentu dari produk.
     * Jika gambar yang dihapus adalah primary, gambar lain akan tetap ada.
     * 
     * @param string $id ID gambar produk yang akan dihapus
     * @return \Illuminate\Http\JsonResponse JSON response success/error
     */
    public function destroyImage($id)
    {
        // Cek otorisasi
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Query gambar produk
        $image = \App\Models\ProductImage::where('id', $id);

        // Jika user adalah penjual, hanya bisa hapus gambar dari produk miliknya
        if ($user->role->name === 'seller') {
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Seller record tidak ditemukan.'], 403);
            }

            // Pastikan gambar milik produk milik penjual ini
            $image = $image->whereHas('product', function ($query) use ($seller) {
                $query->where('seller_id', $seller->id);
            });
        }

        // Ambil record gambar
        $image = $image->first();

        if (!$image) {
            return response()->json(['success' => false, 'message' => 'Gambar tidak ditemukan'], 404);
        }

        try {
            // Hapus file fisik dari storage
            Storage::disk('public')->delete($image->image_path);

            // Hapus record dari database
            $image->delete();

            return response()->json(['success' => true, 'message' => 'Gambar berhasil dihapus']);
        } catch (\Exception $e) {
            \Log::error('Error deleting product image: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus gambar'], 500);
        }
    }

    /**
     * Menyimpan gambar tanpa processing/resizing
     * 
     * Fungsi helper untuk menyimpan gambar yang diupload.
     * Saat ini hanya menyimpan gambar apa adanya tanpa resize ke 16:9.
     * 
     * @param \Illuminate\Http\UploadedFile $image File gambar yang diupload
     * @param string $directory Direktori tujuan di storage
     * @return string Path gambar yang disimpan
     */
    private function processImageTo16By9($image, $directory)
    {
        // Simpan gambar apa adanya ke storage public
        // Return path relatif untuk disimpan di database
        return $image->store($directory, 'public');
    }

    /**
     * Memperbaiki produk yang missing records di tabel product_images
     * 
     * Fungsi ini untuk migrasi data lama yang masih menyimpan gambar
     * di kolom 'image' pada tabel products, bukan di tabel product_images.
     * 
     * @return \Illuminate\Http\JsonResponse JSON response dengan jumlah data yang diperbaiki
     */
    public function fixMissingImages()
    {
        // Cek otorisasi
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        // Jika user adalah penjual, hanya perbaiki produk miliknya
        if ($user->role->name === 'seller') {
            // Ambil ID penjual dari tabel sellers berdasarkan user_id
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Seller record tidak ditemukan.'], 403);
            }

            // Ambil produk yang punya gambar di kolom 'image' tapi belum ada di tabel product_images
            $products = \App\Models\Product::where('seller_id', $seller->id)
                ->whereNotNull('image')  // Produk memiliki gambar di kolom image (kolom lama)
                ->whereDoesntHave('images')  // Tapi belum punya entri di tabel product_images
                ->get();
        }
        // Jika admin, perbaiki semua produk di sistem
        else {
            $products = \App\Models\Product::whereNotNull('image')
                ->whereDoesntHave('images')
                ->get();
        }

        $fixedCount = 0;
        foreach ($products as $product) {
            // Buat entri baru di tabel product_images untuk gambar utama jika tersedia
            if ($product->main_image) {
                $product->images()->create([
                    'image_path' => $product->main_image,
                    'alt_text' => $product->name,
                    'is_primary' => true
                ]);
            }
            $fixedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil memperbaiki {$fixedCount} produk dengan gambar yang hilang",
            'fixed_count' => $fixedCount
        ]);
    }

    /**
     * Menugaskan produk tanpa penjual ke penjual pertama di database
     * 
     * Fungsi ini untuk memperbaiki data produk yang tidak memiliki seller_id.
     * Biasanya terjadi karena migrasi data atau bug saat create produk.
     * 
     * @return \Illuminate\Http\JsonResponse JSON response dengan jumlah data yang diperbaiki
     */
    public function assignProductsToSellers()
    {
        // Cek otorisasi
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        // Ambil semua produk yang tidak memiliki seller_id (orphan products)
        $productsWithoutSeller = \App\Models\Product::whereNull('seller_id')->get();

        $assignedCount = 0;
        foreach ($productsWithoutSeller as $product) {
            // Jika user adalah penjual, assign ke seller tersebut
            if ($user->role->name === 'seller') {
                $seller = \App\Models\Seller::where('user_id', $user->id)->first();
                if (!$seller) {
                    return response()->json(['success' => false, 'message' => 'Akses ditolak. Seller record tidak ditemukan.'], 403);
                }
                // Update seller_id produk
                $product->update(['seller_id' => $seller->id]);
            }
            // Jika user adalah admin, assign ke seller pertama yang ada
            else {
                $seller = \App\Models\Seller::first();
                if ($seller) {
                    $product->update(['seller_id' => $seller->id]);
                } else {
                    // Jika tidak ada seller, lewati produk ini
                    continue;
                }
            }
            $assignedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil menghubungkan {$assignedCount} produk ke penjual",
            'assigned_count' => $assignedCount
        ]);
    }
}