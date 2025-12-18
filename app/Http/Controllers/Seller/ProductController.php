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

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Hanya penjual dan admin yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        // Jika user adalah penjual
        if ($user->role->name === 'seller') {
            // Ambil ID penjual dari tabel sellers berdasarkan user_id
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                \Log::info("Seller record not found for user: " . $user->id);
                // Jika tidak ada seller record, kembalikan koleksi kosong
                $products = collect();
                $categories = \App\Models\Category::all();
                return view('penjual.manajemen_produk', compact('products', 'categories'));
            }

            \Log::info("Seller found for user " . $user->id . ", seller_id: " . $seller->id);

            // Query builder untuk produk milik penjual saat ini
            $query = Product::where('seller_id', $seller->id)
                ->with(['variants', 'category', 'subcategory', 'images', 'approvedReviews']);
        }
        // Jika user adalah admin
        else {
            // Tampilkan semua produk dari semua penjual
            $query = Product::with(['variants', 'category', 'subcategory', 'images', 'approvedReviews']);
        }

        // Filter berdasarkan pencarian jika ada
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter berdasarkan kategori jika ada
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter berdasarkan status jika ada
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Urutkan berdasarkan yang terbaru
        $query->orderBy('created_at', 'desc');

        // Paginate hasil
        $products = $query->paginate(10)->appends($request->query());

        // Ambil kategori untuk filter dropdown
        $categories = \App\Models\Category::all();

        return view('penjual.manajemen_produk', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Hanya penjual dan admin yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        $categories = Category::all();
        return view('penjual.tambah_produk', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Hanya penjual dan admin yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory' => 'nullable|string|max:255', // Sub-kategori opsional
            'warranty' => 'nullable|string|max:255', // Garansi opsional
            'origin' => 'nullable|string|max:255', // Asal wilayah opsional
            'material' => 'nullable|string|max:255', // Material produk
            'size' => 'nullable|string|max:255', // Ukuran produk
            'color' => 'nullable|string|max:255', // Warna produk
            'specifications' => 'nullable|string', // Spesifikasi produk
            'features' => 'nullable|string', // Fitur produk
            'stock' => 'required|integer|min:0',
            'weight' => 'required|numeric|min:0',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB per image
            'status' => 'sometimes|in:active,inactive,draft',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Konversi status dari bahasa Indonesia ke bahasa Inggris jika diperlukan
            $status = $request->status;
            if ($status === 'aktif') {
                $status = 'active';
            } elseif ($status === 'draft') {
                $status = 'draft';
            } else {
                $status = $status ?? 'active';
            }

            // Jika user adalah penjual, ambil seller record
            if ($user->role->name === 'seller') {
                $seller = \App\Models\Seller::where('user_id', $user->id)->first();
                if (!$seller) {
                    throw new \Exception('Seller record not found for this user');
                }
                $sellerId = $seller->id;
            }
            // Jika user adalah admin, kita perlu menentukan seller mana produk akan dibuat
            // Untuk saat ini, mungkin perlu ada field tambahan untuk menentukan penjualnya
            // Tapi untuk sementara, kita bisa menetapkan ke penjual pertama atau meminta input dari form
            else {
                // Untuk kasus admin, mungkin perlu mengambil seller_id dari input atau menetapkan ke seller tertentu
                // Kita akan tetap memerlukan seller_id, karena produk harus terkait dengan penjual
                $sellerId = $request->input('seller_id');
                if (!$sellerId) {
                    // Jika tidak ada seller_id ditentukan, kita bisa gunakan penjual pertama sebagai fallback
                    $seller = \App\Models\Seller::first();
                    if (!$seller) {
                        throw new \Exception('Tidak ada penjual ditemukan, admin tidak bisa membuat produk tanpa menentukan penjual.');
                    }
                    $sellerId = $seller->id;
                }
            }

            // Buat produk
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'subcategory' => $request->subcategory, // Tambahkan sub-kategori
                'warranty' => $request->warranty, // Tambahkan garansi
                'origin' => $request->origin, // Tambahkan asal wilayah
                'material' => $request->material, // Tambahkan material
                'size' => $request->size, // Tambahkan ukuran
                'color' => $request->color, // Tambahkan warna
                'specifications' => $request->specifications ? explode("\n", $request->specifications) : null, // Tambahkan spesifikasi
                'features' => $request->features ? explode("\n", $request->features) : null, // Tambahkan fitur
                'stock' => $request->stock,
                'weight' => $request->weight,
                'seller_id' => $sellerId,
                'status' => $status
            ]);

            // Upload dan proses gambar
            if ($request->hasFile('images')) {
                $files = $request->file('images');

                foreach ($files as $index => $image) {
                    // Simpan gambar
                    $path = $this->processImageTo16By9($image, 'products/' . $product->id);

                    // Simpan semua gambar ke tabel product_images
                    $productImage = $product->images()->create([
                        'image_path' => $path,
                        'alt_text' => $request->name
                    ]);

                    // Tandai gambar pertama sebagai gambar utama
                    if ($index === 0) {
                        $productImage->update([
                            'is_primary' => true
                        ]);
                    }
                }
            }

            // Commit transaksi
            DB::commit();

            return redirect()->route('penjual.produk')->with('success', 'Produk berhasil ditambahkan');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();

            \Log::error('Error creating product: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan produk: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        $product = Product::where('id', $id)
            ->with(['variants', 'category', 'subcategory', 'images', 'reviews.user']);

        // Jika user adalah penjual, hanya boleh melihat produk miliknya sendiri
        if ($user->role->name === 'seller') {
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
            }
            $product = $product->where('seller_id', $seller->id);
        }

        $product = $product->firstOrFail();

        // Hitung statistik produk
        $totalSales = $product->orderItems()->sum('quantity');
        $totalRevenue = $product->orderItems()->sum(\DB::raw('quantity * price'));
        $averageRating = $product->reviews()->avg('rating') ?? 0;
        $totalReviews = $product->reviews()->count();

        return view('penjual.detail_produk', compact('product', 'totalSales', 'totalRevenue', 'averageRating', 'totalReviews'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        $product = Product::where('id', $id)
            ->with(['variants', 'category', 'subcategory', 'images']);

        // Jika user adalah penjual, hanya boleh mengedit produk miliknya sendiri
        if ($user->role->name === 'seller') {
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
            }
            $product = $product->where('seller_id', $seller->id);
        }

        $product = $product->firstOrFail();

        $categories = Category::all();

        return view('penjual.edit_produk', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        $product = Product::where('id', $id);

        // Jika user adalah penjual, hanya boleh mengupdate produk miliknya sendiri
        if ($user->role->name === 'seller') {
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
            }
            $product = $product->where('seller_id', $seller->id);
        }

        $product = $product->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory' => 'nullable|string|max:255', // Sub-kategori opsional
            'warranty' => 'nullable|string|max:255', // Garansi opsional
            'origin' => 'nullable|string|max:255', // Asal wilayah opsional
            'material' => 'nullable|string|max:255', // Material produk
            'size' => 'nullable|string|max:255', // Ukuran produk
            'color' => 'nullable|string|max:255', // Warna produk
            'specifications' => 'nullable|string', // Spesifikasi produk
            'features' => 'nullable|string', // Fitur produk
            'stock' => 'required|integer|min:0',
            'weight' => 'required|numeric|min:0',
            'images' => 'array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'sometimes|in:active,inactive,draft',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Konversi status dari bahasa Indonesia ke bahasa Inggris jika diperlukan
            $status = $request->status;
            if ($status === 'aktif') {
                $status = 'active';
            } elseif ($status === 'draft') {
                $status = 'draft';
            } else {
                $status = $status ?? $product->status;
            }

            // Update produk
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'subcategory' => $request->subcategory, // Update sub-kategori
                'warranty' => $request->warranty, // Update garansi
                'origin' => $request->origin, // Update asal wilayah
                'material' => $request->material, // Update material
                'size' => $request->size, // Update ukuran
                'color' => $request->color, // Update warna
                'specifications' => $request->specifications ? explode("\n", $request->specifications) : null, // Update spesifikasi
                'features' => $request->features ? explode("\n", $request->features) : null, // Update fitur
                'stock' => $request->stock,
                'weight' => $request->weight,
                'status' => $status,
            ]);

            // Upload dan proses gambar baru jika ada
            if ($request->hasFile('images')) {
                $files = $request->file('images');

                foreach ($files as $index => $image) {
                    // Simpan gambar
                    $path = $this->processImageTo16By9($image, 'products/' . $product->id);

                    // Simpan semua gambar ke tabel product_images
                    $productImage = $product->images()->create([
                        'image_path' => $path,
                        'alt_text' => $request->name
                    ]);

                    // Tandai gambar pertama sebagai gambar utama
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
            // Rollback transaksi jika terjadi error
            DB::rollBack();

            \Log::error('Error updating product: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui produk: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $product = Product::where('id', $id);

        // Jika user adalah penjual, hanya boleh menghapus produk miliknya sendiri
        if ($user->role->name === 'seller') {
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Seller record tidak ditemukan.'], 403);
            }
            $product = $product->where('seller_id', $seller->id);
        }

        $product = $product->firstOrFail();

        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Hapus semua gambar produk
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            // Hapus produk dan semua relasinya
            $product->images()->delete();
            $product->variants()->delete();
            $product->delete();

            // Commit transaksi
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus']);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();

            \Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus produk: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update product stock
     */
    public function updateStock(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        $product = Product::where('id', $id);

        // Jika user adalah penjual, hanya boleh mengupdate produk miliknya sendiri
        if ($user->role->name === 'seller') {
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
            }
            $product = $product->where('seller_id', $seller->id);
        }

        $product = $product->firstOrFail();

        $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        $product->update(['stock' => $request->stock]);

        return response()->json([
            'success' => true,
            'message' => 'Stok produk berhasil diperbarui',
            'product' => $product
        ]);
    }

    /**
     * Remove product image
     */
    public function destroyImage($id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $image = \App\Models\ProductImage::where('id', $id);

        // Jika user adalah penjual, hanya boleh menghapus gambar dari produk miliknya sendiri
        if ($user->role->name === 'seller') {
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak. Seller record tidak ditemukan.'], 403);
            }

            $image = $image->whereHas('product', function ($query) use ($seller) {
                $query->where('seller_id', $seller->id);
            });
        }

        $image = $image->first();

        if (!$image) {
            return response()->json(['success' => false, 'message' => 'Gambar tidak ditemukan'], 404);
        }

        try {
            // Hapus file dari storage
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
     * Store image without processing - just save as is
     */
    private function processImageTo16By9($image, $directory)
    {
        // Simply store the image as is to ensure it appears
        return $image->store($directory, 'public');
    }
    
    /**
     * Fix missing product images in product_images table
     */
    public function fixMissingImages()
    {
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

            // Ambil semua produk milik penjual ini yang memiliki gambar di kolom image
            // tetapi tidak memiliki gambar di tabel product_images
            $products = \App\Models\Product::where('seller_id', $seller->id)
                ->whereNotNull('image')  // Produk memiliki gambar di kolom image
                ->whereDoesntHave('images')  // Tetapi tidak memiliki entri di tabel product_images
                ->get();
        }
        // Jika user adalah admin, perbaiki semua produk
        else {
            // Ambil semua produk yang memiliki gambar di kolom image
            // tetapi tidak memiliki gambar di tabel product_images
            $products = \App\Models\Product::whereNotNull('image')  // Produk memiliki gambar di kolom image
                ->whereDoesntHave('images')  // Tetapi tidak memiliki entri di tabel product_images
                ->get();
        }

        $fixedCount = 0;
        foreach ($products as $product) {
            // Buat entri di tabel product_images untuk gambar utama jika tersedia
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
     * Assign products without seller to the first seller in database
     */
    public function assignProductsToSellers()
    {
        $user = Auth::user();
        if (!$user || !$user->role || !in_array($user->role->name, ['seller', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya penjual dan admin yang dapat mengakses halaman ini.');
        }

        // Ambil semua produk yang tidak memiliki seller_id
        $productsWithoutSeller = \App\Models\Product::whereNull('seller_id')->get();

        $assignedCount = 0;
        foreach ($productsWithoutSeller as $product) {
            // Coba cari seller berdasarkan user yang terkait dengan produk jika ada
            // Kita asumsikan produk dibuat saat penjual sudah terdaftar
            // Dalam skenario ini, kita akan assign ke seller pertama (ini hanya untuk data dummy)

            // Jika user adalah penjual, assign ke seller tersebut
            if ($user->role->name === 'seller') {
                $seller = \App\Models\Seller::where('user_id', $user->id)->first();
                if (!$seller) {
                    return response()->json(['success' => false, 'message' => 'Akses ditolak. Seller record tidak ditemukan.'], 403);
                }
                $product->update(['seller_id' => $seller->id]);
            }
            // Jika user adalah admin, assign ke seller pertama
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