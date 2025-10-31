<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Seller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Tampilkan halaman daftar produk
     */
    public function index(Request $request)
    {
        // Ambil semua produk dengan relasi variants, seller, category, approvedReviews dan images untuk rating
        $products = Product::with(['variants', 'seller', 'category', 'approvedReviews', 'images'])->get();
        
        // Tambahkan filter berdasarkan kategori jika ada
        $kategori = $request->query('kategori');
        if ($kategori && $kategori !== 'all') {
            $products = Product::with(['variants', 'seller', 'category', 'approvedReviews', 'images'])
                            ->whereHas('category', function($query) use ($kategori) {
                                $query->where('name', $kategori);
                            })
                            ->get();
        }
        
        // Tambahkan average rating dan review count ke setiap produk
        $products = $products->map(function($product) {
            $product->setAttribute('average_rating', $product->averageRating);
            $product->setAttribute('review_count', $product->reviews_count);
            $product->setAttribute('formatted_images', $this->formatProductImages($product));
            return $product;
        });
        
        return view('customer.produk.halaman_produk', compact('products'));
    }

    /**
     * Tampilkan detail produk
     */
    public function show($id)
    {
        // Ambil produk berdasarkan ID dengan relasi yang diperlukan
        $product = Product::with(['variants', 'seller', 'category', 'approvedReviews.user', 'images'])->find($id);
        
        if (!$product) {
            abort(404, 'Produk tidak ditemukan');
        }
        
        // Format data produk sesuai dengan struktur yang digunakan di view
        $produk = [
            'id' => $product->id,
            'nama' => $product->name,
            'kategori' => $product->category->name ?? 'Umum',
            'harga' => $product->price,
            'deskripsi' => $product->description,
            'gambar_utama' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
            'gambar' => $this->formatProductImages($product), // Tampilkan semua gambar termasuk tambahan
            'rating' => $product->averageRating, // Gunakan averageRating dari accessor
            'jumlah_ulasan' => $product->reviews_count, // Gunakan reviews_count dari accessor
            'ulasan' => $product->approvedReviews->map(function($review) {
                return [
                    'id' => $review->id,
                    'user' => $review->user->name,
                    'rating' => $review->rating,
                    'review_text' => $review->review_text,
                    'created_at' => $review->created_at->format('d M Y')
                ];
            })->toArray(),
            'stok' => $product->stock,
            'berat' => $product->weight,
            'varian' => $product->variants->map(function($variant) {
                return [
                    'id' => $variant->id,
                    'nama' => $variant->name,
                    'harga_tambahan' => $variant->additional_price,
                    'stok' => $variant->stock
                ];
            })->toArray()
        ];
        
        return view('customer.produk.produk_detail', compact('produk'));
    }

    /**
     * Cari produk berdasarkan nama
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $products = Product::with(['variants', 'seller', 'category', 'approvedReviews', 'images'])
                        ->where('name', 'LIKE', "%{$query}%")
                        ->get();
        
        // Tambahkan average rating dan review count ke setiap produk
        $products = $products->map(function($product) {
            $product->setAttribute('average_rating', $product->averageRating);
            $product->setAttribute('review_count', $product->reviews_count);
            $product->setAttribute('formatted_images', $this->formatProductImages($product));
            // Agar kompatibel dengan view halaman_produk, tambahkan property rating
            $product->setAttribute('rating', $product->averageRating);
            return $product;
        });
        
        return view('customer.produk.halaman_produk', compact('products'));
    }

    /**
     * Tampilkan produk berdasarkan kategori
     */
    public function byCategory($category)
    {
        $products = Product::with(['variants', 'seller', 'category', 'approvedReviews', 'images'])
                        ->whereHas('category', function($query) use ($category) {
                            $query->where('name', $category);
                        })
                        ->get();
        
        // Tambahkan average rating dan review count ke setiap produk
        $products = $products->map(function($product) {
            $product->setAttribute('average_rating', $product->averageRating);
            $product->setAttribute('review_count', $product->reviews_count);
            $product->setAttribute('formatted_images', $this->formatProductImages($product));
            return $product;
        });
        
        return view('customer.produk.halaman_produk', compact('products'));
    }
    
    /**
     * Tampilkan halaman kategori berdasarkan nama kategori
     */
    public function showCategoryPage($categoryName)
    {
        // Mapping antara nama route dan nama kategori di database
        $categoryNameMap = [
            'kuliner' => 'Kuliner',
            'fashion' => 'Fashion',
            'kerajinan' => 'Kerajinan Tangan',
            'berkebun' => 'Berkebun',
            'kesehatan' => 'Kesehatan',
            'mainan' => 'Mainan',
            'hampers' => 'Hampers',
        ];
        
        $categoryNameInDB = $categoryNameMap[$categoryName] ?? $categoryName;
        
        // Ambil kategori berdasarkan nama
        $category = Category::where('name', $categoryNameInDB)->first();
        
        if (!$category) {
            abort(404, 'Kategori tidak ditemukan');
        }
        
        // Ambil produk berdasarkan kategori dengan informasi lengkap
        $products = Product::with(['variants', 'seller', 'category', 'approvedReviews', 'images'])
                         ->where('category_id', $category->id)
                         ->where('status', 'active')
                         ->get();
        
        // Tambahkan average rating dan review count ke setiap produk
        $products = $products->map(function($product) {
            $product->setAttribute('average_rating', $product->averageRating);
            $product->setAttribute('review_count', $product->reviews_count);
            $product->setAttribute('formatted_images', $this->formatProductImages($product));
            return $product;
        });
        
        // Format data produk untuk ditampilkan di halaman kategori
        $formattedProducts = $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                'image' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
                'average_rating' => round($product->averageRating, 1),
                'review_count' => $product->reviews_count,
            ];
        });
        
        // Bagi produk ke dalam chunk untuk pagination
        $productChunks = $formattedProducts->chunk(8);
        
        // Siapkan data untuk setiap halaman
        $pageData = [];
        for ($i = 1; $i <= min(5, count($productChunks)); $i++) { // Maksimal 5 halaman
            $chunk = $productChunks->get($i - 1, collect([]))->values();
            $pageData["page_{$i}_products"] = $chunk->toArray();
        }
        
        // Tentukan nama kategori untuk ditampilkan
        $categoryTitle = ucfirst(str_replace('-', ' ', $categoryName));
        $categoryDescription = "Temukan berbagai produk {$categoryTitle} dari UMKM lokal";
        
        // Kirim data ke view base
        return view('customer.kategori.base', array_merge([
            'categoryTitle' => $categoryTitle,
            'categoryDescription' => $categoryDescription,
        ], $pageData));
    }

    /**
     * API endpoint untuk mendapatkan produk populer
     */
    public function popular()
    {
        // Ambil produk terbaru atau produk dengan penjualan terbanyak sebagai produk populer
        $products = Product::with(['variants', 'seller', 'category', 'approvedReviews', 'images'])
                        ->orderBy('created_at', 'desc') // Urutkan berdasarkan tanggal terbaru
                        ->limit(10)
                        ->get();
        
        // Tambahkan average rating dan review count ke setiap produk
        $products = $products->map(function($product) {
            $product->setAttribute('average_rating', $product->averageRating);
            $product->setAttribute('review_count', $product->reviews_count);
            $product->setAttribute('formatted_images', $this->formatProductImages($product));
            return $product;
        });
        
        $formattedProducts = $products->map(function($product) {
            $mainImage = $product->main_image; // Menggunakan accessor yang baru dibuat
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                'image' => $mainImage ? asset('storage/' . $mainImage) : asset('src/placeholder.png'),
                'formatted_images' => collect($product->all_images)->map(function($img) {
                    return asset('storage/' . $img);
                })->toArray(), // Tambahkan semua gambar untuk keperluan modal
                'description' => $product->description,
                'average_rating' => round($product->averageRating, 1),
                'review_count' => $product->reviews_count,
                'specifications' => [
                    'Kategori: ' . ($product->category->name ?? 'Umum'),
                    'Stok: ' . $product->stock,
                    'Berat: ' . $product->weight . 'g'
                ] // Spesifikasi sederhana
            ];
        });
        
        return response()->json($formattedProducts);
    }

    /**
     * Tampilkan daftar produk dalam format JSON untuk keperluan JavaScript
     */
    public function getAllProducts(Request $request)
    {
        // Ambil semua produk dengan relasi yang diperlukan
        $query = Product::with(['variants', 'seller', 'category', 'approvedReviews', 'images']);
        
        // Tambahkan filter berdasarkan kategori jika ada
        $kategori = $request->query('kategori');
        if ($kategori && $kategori !== 'all') {
            $query = $query->whereHas('category', function($q) use ($kategori) {
                $q->where('name', $kategori);
            });
        }
        
        // Tambahkan pencarian jika ada
        $search = $request->query('search');
        if ($search) {
            $query = $query->where('name', 'LIKE', "%{$search}%");
        }
        
        $products = $query->get();
        
        // Format untuk keperluan JavaScript - menggunakan format yang konsisten dengan sistem lain
        $formattedProducts = $products->map(function($product) {
            // Format gambar menggunakan metode yang sama seperti di controller lain
            $gambar = null;
            if ($product->image) {
                // Jika produk memiliki gambar utama
                $gambar = asset('storage/' . $product->image);
            } else {
                // Cek apakah ada gambar tambahan
                $firstImage = $product->images->first();
                if ($firstImage) {
                    $gambar = asset('storage/' . $firstImage->image_path);
                }
            }
            
            // Jika tetap tidak ada gambar, gunakan placeholder
            if (!$gambar) {
                $gambar = asset('src/placeholder.png');
            }
            
            return [
                'id' => $product->id,
                'nama' => $product->name,
                'kategori' => $product->category->name ?? 'Umum',
                'harga' => $product->price, // Kirim harga dalam bentuk angka, nanti format di JS
                'gambar' => $gambar,
                'rating' => $product->averageRating,
                'toko' => $product->seller->name ?? 'Toko Umum',
                'deskripsi' => $product->description,
                'average_rating' => $product->averageRating,
                'review_count' => $product->reviews_count,
            ];
        });
        
        return response()->json($formattedProducts);
    }
    
    /**
     * API endpoint untuk mendapatkan detail produk
     */
    public function apiShow($id)
    {
        $product = Product::with(['variants', 'seller', 'category', 'approvedReviews', 'images'])->find($id);
        
        if (!$product) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }
        
        // Format spesifikasi produk
        $specs = [];
        if ($product->specifications) {
            foreach ($product->specifications as $key => $value) {
                $specs[] = "$key: $value";
            }
        }
        
        // Tambahkan informasi kategori dan spesifikasi dasar jika tidak ada spesifikasi khusus
        if (empty($specs)) {
            $specs = [
                'Kategori: ' . ($product->category->name ?? 'Umum'),
                'Stok: ' . $product->stock,
                'Berat: ' . $product->weight . 'g',
                'Status: ' . ucfirst($product->status)
            ];
            
            // Tambahkan informasi tambahan jika tersedia
            if ($product->material) $specs[] = 'Bahan: ' . $product->material;
            if ($product->size) $specs[] = 'Ukuran: ' . $product->size;
            if ($product->color) $specs[] = 'Warna: ' . $product->color;
            if ($product->brand) $specs[] = 'Merek: ' . $product->brand;
            if ($product->origin) $specs[] = 'Asal: ' . $product->origin;
            if ($product->warranty) $specs[] = 'Garansi: ' . $product->warranty;
        }
        
        // Format fitur produk
        $features = [];
        if ($product->features) {
            $features = array_values($product->features);
        }
        
        $mainImage = $product->main_image; // Menggunakan accessor yang baru dibuat
        $formattedProduct = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
            'image' => $mainImage ? asset('storage/' . $mainImage) : asset('src/placeholder.png'),
            'formatted_images' => collect($product->all_images)->map(function($img) {
                return asset('storage/' . $img);
            })->toArray(), // Tambahkan semua gambar untuk keperluan galeri
            'description' => $product->description,
            'specifications' => $specs,
            'features' => $features,
            'material' => $product->material,
            'size' => $product->size,
            'color' => $product->color,
            'brand' => $product->brand,
            'origin' => $product->origin,
            'warranty' => $product->warranty,
            'min_order' => $product->min_order,
            'ready_stock' => $product->ready_stock,
            'stock' => $product->stock,
            'weight' => $product->weight,
            'status' => $product->status,
            'view_count' => $product->view_count,
            'discount_price' => $product->discount_price ? 'Rp ' . number_format($product->discount_price, 0, ',', '.') : null,
            'discount_start_date' => $product->discount_start_date,
            'discount_end_date' => $product->discount_end_date,
            'average_rating' => round($product->averageRating, 1),
            'review_count' => $product->reviews_count,
        ];
        
        return response()->json($formattedProduct);
    }
    
    /**
     * Format product images for display
     */
    private function formatProductImages($product)
    {
        $images = [];
        
        // Tambahkan gambar utama jika ada
        if ($product->image) {
            $images[] = asset('storage/' . $product->image);
        }
        
        // Tambahkan gambar tambahan dari tabel product_images
        foreach ($product->images as $productImage) {
            $images[] = asset('storage/' . $productImage->image_path);
        }
        
        // Jika tidak ada gambar sama sekali, gunakan placeholder
        if (empty($images)) {
            $images[] = asset('src/placeholder.png');
        }
        
        return $images;
    }
}