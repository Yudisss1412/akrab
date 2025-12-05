<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Seller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Tampilkan halaman daftar produk
     */
    public function index(Request $request)
    {

        
        // Bangun query produk awal
        $query = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
                       ->whereHas('seller'); // Hanya produk dengan seller yang valid

        // Tambahkan filter berdasarkan kategori jika ada
        $kategori = $request->query('kategori');
        if ($kategori && $kategori !== 'all') {
            // Jika kategori yang diminta adalah nama alternatif, gunakan nama sebenarnya
            $actualCategory = $kategori;

            $query->whereHas('category', function($query) use ($actualCategory) {
                $query->where('name', $actualCategory);
            });
        }

        // Ambil produk berdasarkan query yang telah difilter
        $products = $query->get();
        
        // Tambahkan average rating dan review count ke setiap produk
        $products = $products->map(function($product) {
            $product->setAttribute('average_rating', $product->averageRating);
            $product->setAttribute('review_count', $product->reviews_count);
            $product->setAttribute('formatted_images', $this->formatProductImages($product));
            return $product;
        });
        
        // Ambil kategori-kategori yang sesuai dengan dashboard customer
        // Kita akan coba beberapa variasi nama kategori
        $mainCategories = [
            'Kuliner', 'Fashion', 'Kerajinan Tangan', 
            'Produk Berkebun', 'Produk Kesehatan', 
            'Mainan', 'Hampers'
        ];
        
        // Cari kategori-kategori yang ada di database termasuk variasi nama
        $categories = Category::where(function($query) use ($mainCategories) {
            foreach ($mainCategories as $category) {
                $query->orWhere('name', $category);
            }

        })
        ->orderByRaw("CASE 
            WHEN name = 'Kuliner' THEN 1
            WHEN name = 'Fashion' THEN 2
            WHEN name = 'Kerajinan Tangan' THEN 3
            WHEN name = 'Produk Berkebun' THEN 4
            WHEN name = 'Produk Kesehatan' THEN 5
            WHEN name = 'Mainan' THEN 6
            WHEN name = 'Hampers' THEN 7
            ELSE 8
        END")
        ->get();
        
        return view('customer.produk.halaman_produk', compact('products', 'categories'));
    }

    /**
     * Tampilkan detail produk
     */
    public function show($id)
    {
        // Ambil produk berdasarkan ID dengan relasi yang diperlukan
        $product = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews.user', 'images'])->find($id);

        if (!$product) {
            abort(404, 'Produk tidak ditemukan');
        }

        // Format data produk menggunakan metode konsisten
        $produk = $this->formatSingleProduct($product);

        // Cek apakah produk ada dalam wishlist pengguna
        if (auth()->check()) {
            $userId = auth()->user()->id;
            $isInWishlist = \App\Models\Wishlist::where('user_id', $userId)
                                                ->where('product_id', $product->id)
                                                ->exists();
            $produk['di_wishlist'] = $isInWishlist;
        } else {
            $produk['di_wishlist'] = false;
        }

        return view('customer.produk.produk_detail', compact('produk'));
    }

    /**
     * Format single product data consistently for both views and APIs
     */
    private function formatSingleProduct($product)
    {
        // Format spesifikasi produk
        $specs = [];
        if ($product->specifications) {
            foreach ($product->specifications as $key => $value) {
                $specs[] = ['nama' => $key, 'nilai' => $value];
            }
        }

        // Tambahkan informasi kategori dan spesifikasi dasar jika tidak ada spesifikasi khusus
        if (empty($specs)) {
            $specs = [
                ['nama' => 'Kategori', 'nilai' => ($product->category && is_object($product->category) ? $product->category->name : 'Umum')],
                ['nama' => 'Stok', 'nilai' => $product->stock],
                ['nama' => 'Berat', 'nilai' => $product->weight . 'g'],
                ['nama' => 'Status', 'nilai' => ucfirst($product->status)]
            ];

            // Tambahkan informasi tambahan jika tersedia
            if ($product->material) $specs[] = ['nama' => 'Bahan', 'nilai' => $product->material];
            if ($product->size) $specs[] = ['nama' => 'Ukuran', 'nilai' => $product->size];
            if ($product->color) $specs[] = ['nama' => 'Warna', 'nilai' => $product->color];
            if ($product->brand) $specs[] = ['nama' => 'Merek', 'nilai' => $product->brand];
            if ($product->origin) $specs[] = ['nama' => 'Asal', 'nilai' => $product->origin];
            if ($product->warranty) $specs[] = ['nama' => 'Garansi', 'nilai' => $product->warranty];
        }

        // Format fitur produk
        $features = [];
        if ($product->features) {
            $features = array_values($product->features);
        }

        $mainImage = $product->main_image;

        // Fallback kategori dan subkategori yang lebih bijak
        $categoryName = 'Umum';
        $subcategoryName = 'Umum';

        if ($product->category && is_object($product->category)) {
            $categoryName = $product->category->name;
        } elseif ($product->category_id) {
            // Jika category_id ada tapi relasi tidak ditemukan, coba ambil kategori
            $category = Category::find($product->category_id);
            if ($category) {
                $categoryName = $category->name;
            }
        }

        if ($product->subcategory && is_object($product->subcategory)) {
            $subcategoryName = $product->subcategory->name;
        } elseif ($product->subcategory_id) {
            // Jika subcategory_id ada tapi relasi tidak ditemukan, coba ambil subkategori
            $subcategory = Subcategory::find($product->subcategory_id);
            if ($subcategory) {
                $subcategoryName = $subcategory->name;
            }
        } elseif (!empty($product->subcategory) && $product->subcategory !== 'Umum') {
            // Cek apakah produk memiliki field subcategory string yang bukan 'Umum'
            $subcategoryName = $product->subcategory;
        }

        // Pastikan tidak menampilkan "Umum" jika produk sebenarnya memiliki kategori valid
        // Ambil kategori/subkategori terkait dari database jika hanya fallback ke "Umum"
        if ($categoryName === 'Umum' && $product->category_id) {
            $category = Category::find($product->category_id);
            if ($category) {
                $categoryName = $category->name;
            }
        }

        if ($subcategoryName === 'Umum' && $product->subcategory_id) {
            $subcategory = Subcategory::find($product->subcategory_id);
            if ($subcategory) {
                $subcategoryName = $subcategory->name;
            }
        }

        // Jika masih "Umum" dan produk tidak memiliki ID kategori/subkategori,
        // coba ambil dari field string subcategory
        if ($subcategoryName === 'Umum' && !empty($product->subcategory) && $product->subcategory !== 'Umum') {
            $subcategoryName = $product->subcategory;
        }

        $formattedProduct = [
            'id' => $product->id,
            'nama' => $product->name,
            'name' => $product->name,
            'kategori' => $categoryName,
            'category_name' => $categoryName,
            'subkategori' => $subcategoryName,
            'subcategory_name' => $subcategoryName,
            'harga' => $product->price,
            'price' => $product->price, // Hanya angka untuk perhitungan, bukan teks dengan format
            'deskripsi' => $product->description,
            'description' => $product->description,
            'gambar_utama' => $mainImage ? asset('storage/' . $mainImage) : asset('src/placeholder.png'),
            'gambar' => collect($product->all_images)->map(function($img) {
                return asset('storage/' . $img);
            })->toArray(), // Format gambar untuk view produk_detail
            'formatted_images' => collect($product->all_images)->map(function($img) {
                return asset('storage/' . $img);
            })->toArray(), // Tambahkan semua gambar untuk keperluan galeri
            'spesifikasi' => $specs, // Ubah nama key untuk konsistensi dengan view
            'specifications' => $specs,
            'features' => $features,
            'material' => $product->material,
            'size' => $product->size,
            'color' => $product->color,
            'brand' => $product->brand,
            'origin' => $product->origin,
            'warranty' => $product->warranty,
            'min_order' => $product->min_order,
            'stock' => $product->stock,
            'weight' => $product->weight,
            'status' => $product->status,
            'view_count' => $product->view_count,
            'discount_price' => $product->discount_price ? $product->discount_price : null,
            'discount_start_date' => $product->discount_start_date,
            'discount_end_date' => $product->discount_end_date,
            'rating' => count($product->approvedReviews) > 0 ? round($product->averageRating, 1) : round((rand(40, 50))/10, 1), // Rating dummy untuk produk tanpa review
            'average_rating' => count($product->approvedReviews) > 0 ? round($product->averageRating, 1) : round((rand(40, 50))/10, 1), // Rating dummy untuk produk tanpa review
            'jumlah_ulasan' => count($product->approvedReviews) > 0 ? $product->reviews_count : 3, // Jumlah review dummy
            'review_count' => count($product->approvedReviews) > 0 ? $product->reviews_count : 3, // Jumlah review dummy
            'ulasan' => count($product->approvedReviews) > 0
                ? $product->approvedReviews->map(function($review) {
                    $userName = is_object($review->user) ? $review->user->name : ($review->user ?? 'User Tidak Tersedia');
                    return [
                        'id' => $review->id,
                        'user' => $userName,
                        'rating' => $review->rating,
                        'review_text' => $review->review_text,
                        'created_at' => $review->created_at->format('d M Y')
                    ];
                })->toArray()
                : [
                    [
                        'id' => 1,
                        'user' => 'Ahmad Hidayat',
                        'rating' => rand(4, 5),
                        'review_text' => 'Produknya berkualitas tinggi dan pengemasan sangat rapi. Saya sangat puas dengan pembelian ini.',
                        'created_at' => now()->subDays(rand(1, 30))->format('d M Y')
                    ],
                    [
                        'id' => 2,
                        'user' => 'Siti Rahmah',
                        'rating' => rand(4, 5),
                        'review_text' => 'Barang datang dengan cepat dan sesuai deskripsi. Harga terjangkau dengan kualitas yang bagus.',
                        'created_at' => now()->subDays(rand(1, 60))->format('d M Y')
                    ],
                    [
                        'id' => 3,
                        'user' => 'Budi Santoso',
                        'rating' => rand(3, 5),
                        'review_text' => 'Produk bagus, cuma kemasan bisa lebih diperkuat agar tidak rusak dalam perjalanan.',
                        'created_at' => now()->subDays(rand(1, 90))->format('d M Y')
                    ]
                ],
            'toko' => $product->seller && is_object($product->seller) ? $product->seller->store_name : 'Toko Umum',
            'seller_name' => $product->seller && is_object($product->seller) ? $product->seller->store_name : 'Toko Umum',
            'varian' => $product->variants->map(function($variant) {
                return [
                    'id' => $variant->id,
                    'nama' => $variant->name,
                    'name' => $variant->name,
                    'harga_tambahan' => $variant->additional_price,
                    'additional_price' => $variant->additional_price,
                    'stok' => $variant->stock
                ];
            })->toArray(),
            'di_wishlist' => false // Nilai default, nanti di view akan dicek secara dinamis
        ];

        return $formattedProduct;
    }

    /**
     * Cari produk berdasarkan nama
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $products = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
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
        $query = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
                        ->whereHas('seller') // Hanya produk dengan seller yang valid
                        ->whereHas('category', function($query) use ($category) {
                            $query->where('name', $category);
                        });

        $products = $query->get();
        
        // Tambahkan average rating dan review count ke setiap produk
        $products = $products->map(function($product) {
            $product->setAttribute('average_rating', $product->averageRating);
            $product->setAttribute('review_count', $product->reviews_count);
            $product->setAttribute('formatted_images', $this->formatProductImages($product));
            $product->setAttribute('subcategory_name', $product->subcategory ? $product->subcategory->name : ($product->subcategory ?? 'Umum'));
            return $product;
        });
        
        return view('customer.produk.halaman_produk', compact('products'));
    }
    
    /**
     * Tampilkan halaman kategori berdasarkan nama kategori
     */
    public function showCategoryPage($categoryName, Request $request)
    {
        // Mapping antara nama route dan nama kategori di database
        $categoryNameMap = [
            'kuliner' => 'Kuliner',
            'fashion' => 'Fashion',
            'kerajinan' => 'Kerajinan Tangan',
            'berkebun' => 'Produk Berkebun',
            'kesehatan' => 'Produk Kesehatan',
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
        $query = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
                         ->where('category_id', $category->id)
                         ->where('status', 'active');

        // Filter berdasarkan subkategori jika ada di query string
        $request = request(); // Mendapatkan request instance
        $subkategori = $request->query('subkategori');
        if ($subkategori) {
            // Ambil semua subkategori sekali saja untuk efisiensi
            $allSubcategories = Subcategory::all();

            // Fungsi bantu untuk mencari nama subkategori dari slug
            $getSubcategoryNameFromSlug = function($slug) use ($allSubcategories) {
                foreach ($allSubcategories as $subcategory) {
                    // Buat slug dari nama subkategori yang sebenarnya
                    $actualSlug = strtolower(str_replace(' ', '-', trim($subcategory->name)));
                    if ($actualSlug === $slug) {
                        return $subcategory->name; // return the actual name as stored in DB
                    }
                }

                // Jika tidak ditemukan, kembalikan null
                return null;
            };

            $name = $getSubcategoryNameFromSlug($subkategori);
            if ($name) {
                $query->where(function($q) use ($name) {
                    $q->whereHas('subcategory', function($relQuery) use ($name) {
                        $relQuery->where('name', $name);
                    })->orWhere('subcategory', $name);
                });
            }
        }

        $products = $query->get();
        
        // Tambahkan average rating dan review count ke setiap produk
        $products = $products->map(function($product) {
            $product->setAttribute('average_rating', $product->averageRating);
            $product->setAttribute('review_count', $product->reviews_count);
            $product->setAttribute('formatted_images', $this->formatProductImages($product));
            return $product;
        });
        
        // Format data produk untuk ditampilkan di halaman kategori
        $formattedProducts = $products->map(function($product) {
            $categoryName = 'Umum';
            $subcategoryName = 'Umum';

            if ($product->category && is_object($product->category)) {
                $categoryName = $product->category->name;
            } elseif ($product->category_id) {
                // Jika category_id ada tapi relasi tidak ditemukan, coba ambil kategori
                $category = \App\Models\Category::find($product->category_id);
                if ($category) {
                    $categoryName = $category->name;
                }
            }

            if ($product->subcategory && is_object($product->subcategory)) {
                $subcategoryName = $product->subcategory->name;
            } elseif ($product->subcategory_id) {
                // Jika subcategory_id ada tapi relasi tidak ditemukan, coba ambil subkategori
                $subcategory = \App\Models\Subcategory::find($product->subcategory_id);
                if ($subcategory) {
                    $subcategoryName = $subcategory->name;
                }
            } elseif (!empty($product->subcategory) && $product->subcategory !== 'Umum') {
                // Cek apakah produk memiliki field subcategory string yang bukan 'Umum'
                $subcategoryName = $product->subcategory;
            }

            // Pastikan tidak menampilkan "Umum" jika produk sebenarnya memiliki kategori valid
            // Ambil kategori/subkategori terkait dari database jika hanya fallback ke "Umum"
            if ($categoryName === 'Umum' && $product->category_id) {
                $category = \App\Models\Category::find($product->category_id);
                if ($category) {
                    $categoryName = $category->name;
                }
            }

            if ($subcategoryName === 'Umum' && $product->subcategory_id) {
                $subcategory = \App\Models\Subcategory::find($product->subcategory_id);
                if ($subcategory) {
                    $subcategoryName = $subcategory->name;
                }
            }

            // Jika masih "Umum" dan produk tidak memiliki ID kategori/subkategori,
            // coba ambil dari field string subcategory
            if ($subcategoryName === 'Umum' && !empty($product->subcategory) && $product->subcategory !== 'Umum') {
                $subcategoryName = $product->subcategory;
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                'image' => $product->main_image ? asset('storage/' . $product->main_image) : asset('src/placeholder.png'),
                'average_rating' => round($product->averageRating, 1),
                'review_count' => $product->reviews_count,
                'category_name' => $categoryName,
                'subcategory_name' => $subcategoryName,
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
        
        // Ambil subkategori untuk kategori ini
        $subcategories = $category->subcategories;

        // Kirim data ke view base
        return view('customer.kategori.base', array_merge([
            'categoryTitle' => $categoryTitle,
            'categoryDescription' => $categoryDescription,
            'subcategories' => $subcategories,
        ], $pageData));
    }

    /**
     * API endpoint untuk mendapatkan produk populer
     */
    public function popular()
    {
        // Ambil produk terbaru atau produk dengan penjualan terbanyak sebagai produk populer
        // Pastikan produk memiliki seller yang valid
        $products = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
                        ->whereHas('seller') // Hanya produk dengan seller yang valid
                        ->orderBy('created_at', 'desc') // Urutkan berdasarkan tanggal terbaru
                        ->limit(10)
                        ->get();

        // Gunakan accessor dan relasi untuk menghitung rating dan jumlah ulasan
        $formattedProducts = $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                'image' => $product->main_image ? asset('storage/' . $product->main_image) : asset('src/placeholder.png'),
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
                ], // Spesifikasi sederhana
                'toko' => $product->seller->store_name ?? 'Toko Umum' // Tambahkan informasi toko
            ];
        });

        return response()->json($formattedProducts);
    }

    /**
     * Tampilkan daftar produk dalam format JSON untuk keperluan JavaScript
     */
    public function getAllProducts(Request $request)
    {
        // Ambil semua produk tanpa filter status untuk memastikan produk baru muncul
        // Pastikan produk memiliki seller yang valid
        $query = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
                       ->whereHas('seller'); // Hanya produk dengan seller yang valid

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

        // Format untuk keperluan JavaScript - menggunakan accessor dan logika yang konsisten
        $formattedProducts = $products->map(function($product) {
            // Gunakan accessor dari model untuk mendapatkan data yang konsisten
            $mainImage = $product->main_image;  // Ini menggunakan accessor dari model
            $gambarUrl = null;

            if ($mainImage) {
                // Jika gambar utama ada, gunakan asset() untuk menghasilkan URL yang benar
                $gambarUrl = asset('storage/' . $mainImage);
            } else {
                // Jika tidak ada gambar utama dari accessor, coba gunakan gambar dari formatProductImages
                $formattedImages = $this->formatProductImages($product);
                if (!empty($formattedImages)) {
                    $gambarUrl = $formattedImages[0];
                }
            }

            // Jika tetap tidak ada gambar, gunakan placeholder
            if (!$gambarUrl) {
                $gambarUrl = asset('src/placeholder.png');
            }

            return [
                'id' => $product->id,
                'nama' => $product->name,
                'kategori' => $product->category->name ?? 'Umum',
                'subkategori' => $product->subcategory ? $product->subcategory->name : ($product->subcategory ?? 'Umum'), // Gunakan relasi subcategory atau field subcategory string
                'harga' => $product->price,
                'gambar' => $gambarUrl,
                'rating' => $product->averageRating,
                'toko' => $product->seller->store_name ?? 'Toko Umum',
                'deskripsi' => $product->description,
                'average_rating' => $product->averageRating,
                'review_count' => $product->reviews_count,
                'all_images' => $product->all_images,  // Ini juga dari accessor di model
            ];
        });

        return response()->json($formattedProducts);
    }
    
    /**
     * API endpoint untuk mendapatkan detail produk
     */
    public function apiShow($id)
    {
        $product = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
                         ->whereHas('seller') // Hanya produk dengan seller yang valid
                         ->find($id);

        if (!$product) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        // Format data produk menggunakan metode konsisten
        $formattedProduct = $this->formatSingleProduct($product);

        return response()->json($formattedProduct);
    }
    
    /**
     * API endpoint untuk filtering produk berdasarkan berbagai parameter
     */
    public function filter(Request $request)
    {
        $query = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
                      ->where('status', 'active') // Hanya produk aktif
                      ->whereHas('seller'); // Hanya produk dengan seller yang valid
        
        // Filter berdasarkan kategori
        $kategori = $request->query('kategori');
        if ($kategori && $kategori !== 'all') {
            // Mapping kategori jika diperlukan
            $categoryNameMap = [
                'kuliner' => 'Kuliner',
                'fashion' => 'Fashion',
                'kerajinan' => 'Kerajinan Tangan',
                'berkebun' => 'Berkebun',
                'kesehatan' => 'Kesehatan',
                'mainan' => 'Mainan',
                'hampers' => 'Hampers',
            ];
            
            $actualCategory = $categoryNameMap[$kategori] ?? $kategori;
            
            $query = $query->whereHas('category', function($q) use ($actualCategory) {
                $q->where('name', $actualCategory);
            });
        }
        // Ambil semua subkategori sekali saja untuk efisiensi
        $allSubcategories = Subcategory::all();

        // Fungsi bantu untuk mencari nama subkategori dari slug
        $getSubcategoryNameFromSlug = function($slug) use ($allSubcategories) {
            foreach ($allSubcategories as $subcategory) {
                // Buat slug dari nama subkategori yang sebenarnya
                $actualSlug = strtolower(str_replace(' ', '-', trim($subcategory->name)));
                if ($actualSlug === $slug) {
                    return $subcategory->name; // return the actual name as stored in DB
                }
            }

            // Jika tidak ditemukan, kembalikan null
            return null;
        };

        // Filter berdasarkan subkategori - periksa menggunakan relasi subcategory (subcategory_id) dan field subcategory (string)
        $subkategori = $request->query('subkategori');
        if ($subkategori && is_array($subkategori) && count($subkategori) > 0) {
            // Mencari subkategori berdasarkan slug
            $query = $query->where(function($q) use ($subkategori, $getSubcategoryNameFromSlug) {
                $foundNames = [];
                foreach ($subkategori as $slug) {
                    $name = $getSubcategoryNameFromSlug($slug);
                    if ($name) {
                        $foundNames[] = $name;
                    }
                }
                if (!empty($foundNames)) {
                    // Gabungkan filter untuk relasi subcategory_id dan field subcategory string
                    $q->where(function($subQuery) use ($foundNames) {
                        $subQuery->whereHas('subcategory', function($relQuery) use ($foundNames) {
                            $relQuery->whereIn('name', $foundNames);
                        })->orWhereIn('subcategory', $foundNames);
                    });
                }
            });
        } elseif ($request->has('subkategori') && !empty($request->query('subkategori'))) {
            // Kondisi jika subkategori bukan array
            $singleSubkategori = $request->query('subkategori');
            if (!is_array($singleSubkategori)) {
                $name = $getSubcategoryNameFromSlug($singleSubkategori);
                if ($name) {
                    $query = $query->where(function($q) use ($name) {
                        $q->whereHas('subcategory', function($relQuery) use ($name) {
                            $relQuery->where('name', $name);
                        })->orWhere('subcategory', $name);
                    });
                }
            }
        }
        
        // Filter berdasarkan rentang harga
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        
        if ($minPrice !== null && $minPrice !== '') {
            $query = $query->where('price', '>=', (int)$minPrice);
        }
        
        if ($maxPrice !== null && $maxPrice !== '') {
            $query = $query->where('price', '<=', (int)$maxPrice);
        }
        
        // Filter berdasarkan rating
        $ratings = $request->query('rating');
        if ($ratings && is_array($ratings) && count($ratings) > 0) {
            $ratingConditions = [];
            foreach ($ratings as $rating) {
                $rating = (int)$rating;
                switch ($rating) {
                    case 4:
                        $query = $query->whereHas('approvedReviews', function($q) {
                            $q->selectRaw('products.id, AVG(rating) as avg_rating')
                              ->from('reviews')
                              ->where('status', 'approved')
                              ->groupBy('product_id')
                              ->havingRaw('AVG(rating) >= 4');
                        });
                        break;
                    case 3:
                        $query = $query->whereHas('approvedReviews', function($q) {
                            $q->selectRaw('products.id, AVG(rating) as avg_rating')
                              ->from('reviews')
                              ->where('status', 'approved')
                              ->groupBy('product_id')
                              ->havingRaw('AVG(rating) >= 3');
                        });
                        break;
                    case 2:
                        $query = $query->whereHas('approvedReviews', function($q) {
                            $q->selectRaw('products.id, AVG(rating) as avg_rating')
                              ->from('reviews')
                              ->where('status', 'approved')
                              ->groupBy('product_id')
                              ->havingRaw('AVG(rating) >= 2');
                        });
                        break;
                    case 1:
                        $query = $query->whereHas('approvedReviews', function($q) {
                            $q->selectRaw('products.id, AVG(rating) as avg_rating')
                              ->from('reviews')
                              ->where('status', 'approved')
                              ->groupBy('product_id')
                              ->havingRaw('AVG(rating) >= 1');
                        });
                        break;
                }
            }
        }
        
        // Filter berdasarkan lokasi penjual (opsional)
        $lokasi = $request->query('lokasi');
        if ($lokasi && is_array($lokasi) && count($lokasi) > 0) {
            $query = $query->whereHas('seller', function($q) use ($lokasi) {
                $q->whereIn('city', $lokasi); // Asumsi field kota di tabel sellers adalah 'city'
            });
        } elseif ($request->has('lokasi') && !empty($request->query('lokasi'))) {
            // Kondisi jika lokasi bukan array
            $singleLokasi = $request->query('lokasi');
            if (!is_array($singleLokasi)) {
                $query = $query->whereHas('seller', function($q) use ($singleLokasi) {
                    $q->where('city', $singleLokasi);
                });
            }
        }
        
        // Filter berdasarkan nama toko/seller jika disediakan
        $sellerName = $request->query('seller_store_name');
        if ($sellerName) {
            $query = $query->whereHas('seller', function($q) use ($sellerName) {
                $q->where('store_name', $sellerName);
            });
        }

        // Urutkan produk
        $sortBy = $request->query('sort', 'popular'); // default sort by popular
        switch ($sortBy) {
            case 'newest':
                $query = $query->orderBy('created_at', 'desc');
                break;
            case 'price-low':
                $query = $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query = $query->orderBy('price', 'desc');
                break;
            case 'highest-rated':
                $query = $query->leftJoin('reviews', 'products.id', '=', 'reviews.product_id')
                              ->select('products.*')
                              ->groupBy('products.id')
                              ->orderByRaw('AVG(reviews.rating) DESC NULLS LAST');
                break;
            case 'popular': // Ini bisa berdasarkan jumlah penjualan, rating, dll
            default:
                // Sort berdasarkan rating rata-rata dan jumlah review
                $query = $query->leftJoin('reviews', 'products.id', '=', 'reviews.product_id')
                              ->select('products.*')
                              ->groupBy('products.id')
                              ->orderByRaw('AVG(reviews.rating) DESC NULLS LAST, COUNT(reviews.id) DESC');
                break;
        }
        
        // Ambil hasil query
        $products = $query->get();
        
        // Hitung total produk untuk keperluan tampilan
        $total = $products->count();
        
        // Format produk untuk ditampilkan
        $formattedProducts = $products->map(function($product) {
            $product->setAttribute('average_rating', $product->averageRating);
            $product->setAttribute('review_count', $product->reviews_count);

            $mainImage = $product->main_image;
            $imageUrl = $mainImage ? asset('storage/' . $mainImage) : asset('src/placeholder.png');

            // Fallback kategori dan subkategori yang lebih bijak
            $categoryName = 'Umum';
            $subcategoryName = 'Umum';

            if ($product->category && is_object($product->category)) {
                $categoryName = $product->category->name;
            } elseif ($product->category_id) {
                // Jika category_id ada tapi relasi tidak ditemukan, coba ambil kategori
                $category = \App\Models\Category::find($product->category_id);
                if ($category) {
                    $categoryName = $category->name;
                }
            }

            if ($product->subcategory && is_object($product->subcategory)) {
                $subcategoryName = $product->subcategory->name;
            } elseif ($product->subcategory_id) {
                // Jika subcategory_id ada tapi relasi tidak ditemukan, coba ambil subkategori
                $subcategory = \App\Models\Subcategory::find($product->subcategory_id);
                if ($subcategory) {
                    $subcategoryName = $subcategory->name;
                }
            } elseif (!empty($product->subcategory) && $product->subcategory !== 'Umum') {
                // Cek apakah produk memiliki field subcategory string yang bukan 'Umum'
                $subcategoryName = $product->subcategory;
            }

            // Pastikan tidak menampilkan "Umum" jika produk sebenarnya memiliki kategori valid
            // Ambil kategori/subkategori terkait dari database jika hanya fallback ke "Umum"
            if ($categoryName === 'Umum' && $product->category_id) {
                $category = \App\Models\Category::find($product->category_id);
                if ($category) {
                    $categoryName = $category->name;
                }
            }

            if ($subcategoryName === 'Umum' && $product->subcategory_id) {
                $subcategory = \App\Models\Subcategory::find($product->subcategory_id);
                if ($subcategory) {
                    $subcategoryName = $subcategory->name;
                }
            }

            // Jika masih "Umum" dan produk tidak memiliki ID kategori/subkategori,
            // coba ambil dari field string subcategory
            if ($subcategoryName === 'Umum' && !empty($product->subcategory) && $product->subcategory !== 'Umum') {
                $subcategoryName = $product->subcategory;
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'nama' => $product->name, // Using both naming conventions for compatibility
                'description' => $product->description,
                'deskripsi' => $product->description, // Using both naming conventions for compatibility
                'price' => $product->price, // Raw price for calculations
                'harga' => $product->price, // Raw price for calculations, using both naming conventions
                'image' => $imageUrl,
                'gambar' => $imageUrl, // Using both naming conventions for compatibility
                'average_rating' => round($product->averageRating, 1),
                'rating' => round($product->averageRating, 1), // Using both naming conventions for compatibility
                'review_count' => $product->reviews_count,
                'jumlah_ulasan' => $product->reviews_count, // Using both naming conventions for compatibility
                'kategori' => $categoryName, // Using kategori key to match getAllProducts format
                'category' => $categoryName, // Using both naming conventions for compatibility
                'subkategori' => $subcategoryName, // Using subkategori key to match getAllProducts format
                'subcategory' => $subcategoryName, // Using both naming conventions for compatibility
                'toko' => $product->seller->store_name ?? 'Toko Umum', // Using toko key to match getAllProducts format
                'seller' => $product->seller->store_name ?? 'Toko Umum', // Using both naming conventions for compatibility
                'specifications' => [
                    'Kategori: ' . $categoryName,
                    'Stok: ' . $product->stock,
                    'Berat: ' . $product->weight . 'g'
                ], // Spesifikasi sederhana
            ];
        });

        return response()->json($formattedProducts);
    }
    
    /**
     * Format product images for display
     */
    private function formatProductImages($product)
    {
        $images = [];
        
        // Tambahkan gambar utama jika ada
        if ($product->main_image) {
            $images[] = asset('storage/' . $product->main_image);
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

    /**
     * Update old products that don't have seller_id assigned
     * This is for migration purposes to connect orphaned products to valid sellers
     */
    public function updateProductSellerRelationships()
    {
        // Get all products that don't have a seller_id assigned
        $productsWithoutSeller = Product::whereNull('seller_id')->get();

        $updatedCount = 0;

        foreach ($productsWithoutSeller as $product) {
            // Find a default seller to assign to this product
            // For example, we can use the first seller in the system
            $defaultSeller = \App\Models\Seller::first();

            if ($defaultSeller) {
                $product->update(['seller_id' => $defaultSeller->id]);
                $updatedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengupdate {$updatedCount} produk yang tidak memiliki penjual",
            'updated_count' => $updatedCount
        ]);
    }
}