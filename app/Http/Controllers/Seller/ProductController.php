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
        // Hanya penjual yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = \App\Models\Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            // Jika tidak ada seller record, kembalikan koleksi kosong
            $products = collect();
            $categories = \App\Models\Category::all();
            return view('penjual.manajemen_produk', compact('products', 'categories'));
        }
        
        // Query builder untuk produk milik penjual saat ini
        $query = Product::where('seller_id', $seller->id)
            ->with(['variants', 'category', 'subcategory', 'images', 'approvedReviews']);

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
        // Hanya penjual yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

        $categories = Category::all();
        return view('penjual.tambah_produk', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Hanya penjual yang bisa mengakses
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory' => 'nullable|string|max:255', // Sub-kategori opsional
            'warranty' => 'nullable|string|max:255', // Garansi opsional
            'origin' => 'nullable|string|max:255', // Asal wilayah opsional
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
            
            // Ambil ID penjual dari tabel sellers berdasarkan user_id
            $seller = \App\Models\Seller::where('user_id', $user->id)->first();
            if (!$seller) {
                throw new \Exception('Seller record not found for this user');
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
                'stock' => $request->stock,
                'weight' => $request->weight,
                'seller_id' => $seller->id,
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
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = \App\Models\Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
        }

        $product = Product::where('id', $id)
            ->where('seller_id', $seller->id)
            ->with(['variants', 'category', 'subcategory', 'images', 'reviews.user'])
            ->firstOrFail();

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
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = \App\Models\Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
        }

        $product = Product::where('id', $id)
            ->where('seller_id', $seller->id)
            ->with(['variants', 'category', 'subcategory', 'images'])
            ->firstOrFail();
        
        $categories = Category::all();

        return view('penjual.edit_produk', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

        $product = Product::where('id', $id)
            ->where('seller_id', $user->id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory' => 'nullable|string|max:255', // Sub-kategori opsional
            'warranty' => 'nullable|string|max:255', // Garansi opsional
            'origin' => 'nullable|string|max:255', // Asal wilayah opsional
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
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = \App\Models\Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Seller record tidak ditemukan.'], 403);
        }

        $product = Product::where('id', $id)
            ->where('seller_id', $seller->id)
            ->firstOrFail();

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
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = \App\Models\Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            abort(403, 'Akses ditolak. Seller record tidak ditemukan.');
        }

        $product = Product::where('id', $id)
            ->where('seller_id', $seller->id)
            ->firstOrFail();

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
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }
        
        // Temukan gambar produk yang dimiliki oleh penjual ini
        // Ambil ID penjual dari tabel sellers berdasarkan user_id
        $seller = \App\Models\Seller::where('user_id', $user->id)->first();
        if (!$seller) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Seller record tidak ditemukan.'], 403);
        }
        
        $image = \App\Models\ProductImage::where('id', $id)
            ->whereHas('product', function ($query) use ($seller) {
                $query->where('seller_id', $seller->id);
            })
            ->first();
        
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
        if (!$user || !$user->role || $user->role->name !== 'seller') {
            abort(403, 'Akses ditolak. Hanya penjual yang dapat mengakses halaman ini.');
        }

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
}