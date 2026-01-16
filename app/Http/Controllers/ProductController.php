<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Seller;
use Illuminate\Http\Request;

/**
 * Controller Produk
 *
 * Controller ini menangani semua operasi terkait produk dalam sistem e-commerce AKRAB,
 * termasuk menampilkan daftar produk, detail produk, pencarian produk, dan filter
 * berdasarkan kategori. Controller ini juga menyediakan endpoint API untuk mengambil
 * data produk dalam format JSON.
 */
class ProductController extends Controller
{
    /**
     * Menampilkan halaman daftar produk
     *
     * @param Request $request Objek request HTTP
     * @return \Illuminate\View\View Tampilan halaman produk
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
        $product = Product::with(['variants', 'seller', 'category', 'subcategory', 'reviews.user', 'images'])->find($id);

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
     * Format data produk tunggal secara konsisten untuk tampilan dan API
     *
     * @param Product $product Objek produk
     * @return array Data produk yang telah diformat
     */
    private function formatSingleProduct($product)
    {
        // Format spesifikasi produk
        // $specs = [];
        // if ($product->specifications) {
        //     foreach ($product->specifications as $key => $value) {
        //         $specs[] = ['nama' => $key, 'nilai' => $value];
        //     }
        // }
        $specs = [];

        if (is_array($product->specifications)) {
            foreach ($product->specifications as $key => $value) {

                // Jika array numerik → list biasa
                if (is_int($key)) {
                    $specs[] = [
                        'nama' => 'Spesifikasi',
                        'nilai' => $value
                    ];
                }
                // Jika associative array → key : value
                else {
                    $specs[] = [
                        'nama' => $key,
                        'nilai' => $value
                    ];
                }
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
            'rating' => count($product->reviews) > 0 ? round($product->averageRating, 1) : 0, // Rating rata-rata dari semua ulasan
            'average_rating' => count($product->reviews) > 0 ? round($product->averageRating, 1) : 0, // Rating rata-rata dari semua ulasan
            'jumlah_ulasan' => count($product->reviews) > 0 ? $product->reviews->count() : 0, // Jumlah semua ulasan
            'review_count' => count($product->reviews) > 0 ? $product->reviews->count() : 0, // Jumlah semua ulasan
            'ulasan' => count($product->reviews) > 0
                ? $product->reviews->map(function($review) {
                    $userName = is_object($review->user) ? $review->user->name : ($review->user ?? 'User Tidak Tersedia');
                    return [
                        'id' => $review->id,
                        'user' => $userName,
                        'rating' => $review->rating,
                        'review_text' => $review->review_text,
                        'created_at' => $review->created_at->format('d M Y')
                    ];
                })->toArray()
                : [],
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
     * Mencari produk berdasarkan nama
     *
     * @param Request $request Objek request HTTP
     * @return \Illuminate\View\View Tampilan hasil pencarian produk
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
     * Menampilkan produk berdasarkan kategori
     *
     * @param string $category Nama kategori
     * @return \Illuminate\View\View Tampilan produk berdasarkan kategori
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
     * Menampilkan halaman kategori berdasarkan nama kategori
     *
     * @param string $categoryName Nama kategori
     * @param Request|null $request Objek request HTTP
     * @return \Illuminate\View\View Tampilan halaman kategori
     */
    public function showCategoryPage($categoryName, Request $request = null)
    {
        // Debug info - untuk melihat apa yang sedang dicari
        \Log::info("Mencari kategori dengan nama: " . $categoryName);

        // Coba temukan kategori dengan pendekatan menyeluruh
        $category = null;

        // Pendekatan 1: Coba mapping untuk kategori spesifik
        $specificMappings = [
            'kuliner' => ['Kuliner', 'kuliner', 'KULINER', 'Makanan', 'Menu Makanan', 'Produk Kuliner', 'Food', 'Beverage', 'Makanan & Minuman', 'Food & Beverage', 'F&B'],
            'fashion' => ['Fashion', 'Pakaian', 'Busana', 'Fashion & Aksesoris', 'Pakaian & Aksesoris'],
            'kerajinan' => ['Kerajinan Tangan', 'Kerajinan', 'Handicraft', 'Craft', 'Produk Kerajinan'],
            'berkebun' => ['Berkebun', 'Produk Berkebun', 'Tanaman', 'Peralatan Berkebun', 'Taman'],
            'kesehatan' => ['Kesehatan', 'Produk Kesehatan', 'Alat Kesehatan', 'Obat Herbal'],
            'mainan' => ['Mainan', 'Toys', 'Permainan', 'Toy'],
            'hampers' => ['Hampers', 'Gift Hampers', 'Paket Hadiah', 'Gift Package'],
        ];

        // Cari menggunakan mapping spesifik
        if (isset($specificMappings[$categoryName])) {
            foreach ($specificMappings[$categoryName] as $potentialName) {
                $category = Category::where('name', $potentialName)->first();
                \Log::info("Mencoba nama kategori: " . $potentialName . ", hasil: " . ($category ? "DITEMUKAN" : "TIDAK"));
                if ($category) {
                    break;
                }
            }
        }

        // Jika masih belum ditemukan, coba pendekatan umum
        if (!$category) {
            $generalSearchTerms = [
                $categoryName,
                ucfirst($categoryName),
                ucwords($categoryName),
                strtoupper($categoryName),
                'Kuliner', // Karena ini kasus spesifik untuk kuliner
            ];

            foreach ($generalSearchTerms as $term) {
                $category = Category::where('name', $term)->first();
                if ($category) {
                    break;
                }
            }
        }

        // Jika masih belum ditemukan, coba pencarian partial match
        if (!$category) {
            $category = Category::where('name', 'like', '%' . $categoryName . '%')
                        ->orWhere('name', 'like', '%' . ucfirst($categoryName) . '%')
                        ->first();
        }

        if (!$category) {
            // Sebagai fallback ekstrem, ambil kategori dengan ID 1 jika memang tidak ada
            $allCategories = Category::all();
            if ($allCategories->count() > 0) {
                $category = $allCategories->first(); // Ambil kategori pertama sebagai fallback
                \Log::warning("Kategori tidak ditemukan, menggunakan fallback: " . $category->name);
            } else {
                abort(404, 'Tidak ada kategori ditemukan di sistem');
            }
        }

        if (!$category) {
            abort(404, 'Kategori tidak ditemukan');
        }
        
        \Log::info("ID Kategori ditemukan: " . $category->id . ", Nama: " . $category->name);

        // Ambil produk berdasarkan kategori dengan informasi lengkap
        // Tambahkan penanganan jika produk tidak ditemukan melalui category_id
        $query = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
                         ->where(function($q) use ($category) {
                             $q->where('category_id', $category->id)
                               ->orWhereHas('category', function($subQ) use ($category) {
                                   $subQ->where('name', $category->name);
                               });
                         })
                         ->where('status', 'active');

        // Debug: Hitung produk sebelum filter
        $totalProductsBeforeFilter = Product::where('category_id', $category->id)->count();
        $activeProductsCount = Product::where('category_id', $category->id)->where('status', 'active')->count();
        \Log::info("Total produk untuk kategori ini: {$totalProductsBeforeFilter}, Produk aktif: {$activeProductsCount}");

        // Filter berdasarkan subkategori jika ada di query string
        $request = $request ?: request(); // Gunakan $request jika disediakan, jika tidak gunakan instance baru
        $subkategoriFromQuery = $request->query('subkategori');
        if ($subkategoriFromQuery) {
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

            $name = $getSubcategoryNameFromSlug($subkategoriFromQuery);
            if ($name) {
                $query->where(function($q) use ($name) {
                    $q->whereHas('subcategory', function($relQuery) use ($name) {
                        $relQuery->where('name', $name);
                    })->orWhere('subcategory', $name);
                });
            }
        }

        $products = $query->get();

        // Debug: Info produk setelah query
        \Log::info("Jumlah produk yang diambil: " . $products->count());
        if ($products->count() === 0) {
            \Log::info("Tidak ditemukan produk dalam kategori {$category->name} (ID: {$category->id}) melalui query utama");

            // Cari produk dengan pendekatan yang lebih luas
            // Mungkin produk tidak memiliki relasi yang benar atau nama kategori yang berbeda

            // Pendekatan 1: Cari produk yang nama atau deskripsinya berkaitan dengan kategori
            $keywordProducts = collect([]); // Buat koleksi kosong dulu
            $keywords = explode(' ', strtolower($category->name));

            // Cari produk untuk setiap kata kunci
            foreach ($keywords as $keyword) {
                if (strlen($keyword) > 1) {  // Hanya gunakan kata dengan panjang > 1
                    $foundProducts = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
                                            ->where('status', 'active')
                                            ->where(function($q) use ($keyword) {
                                                $q->where('name', 'like', "%{$keyword}%")
                                                  ->orWhere('description', 'like', "%{$keyword}%");
                                            })
                                            ->get();
                    $keywordProducts = $keywordProducts->concat($foundProducts);
                }
            }

            // Hilangkan duplikat produk
            $keywordProducts = $keywordProducts->unique('id');
            \Log::info("Ditemukan {$keywordProducts->count()} produk berdasarkan keyword pencarian");

            if ($keywordProducts->count() > 0) {
                $products = $keywordProducts;
            } else {
                // Pendekatan 2: Coba temukan produk dengan mengabaikan kategori dan menampilkan produk acak
                \Log::warning("Tidak ditemukan produk berdasarkan keyword, menampilkan produk umum");
                $products = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
                                 ->where('status', 'active')
                                 ->limit(12)  // Ambil 12 produk acak sebagai fallback
                                 ->get();

                \Log::info("Menemukan {$products->count()} produk umum sebagai fallback");
            }

            // Jika tetap tidak ada produk, coba produk spesifik seperti "produk test"
            if ($products->count() === 0) {
                \Log::warning("Tetap tidak ada produk ditemukan, mencari produk dengan nama tertentu");

                // Coba cari produk dengan nama yang mungkin berkaitan
                $specialProducts = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
                                        ->where(function($q) {
                                            $q->where('name', 'like', '%produk test%')
                                              ->orWhere('name', 'like', '%test%')
                                              ->orWhere('name', 'like', '%produk%');
                                        })
                                        ->where('status', 'active')
                                        ->get();

                \Log::info("Ditemukan {$specialProducts->count()} produk dengan nama 'test' atau 'produk'");

                if ($specialProducts->count() > 0) {
                    $products = $specialProducts;
                } else {
                    // Fallback terakhir - ambil semua produk aktif
                    $products = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
                                     ->where('status', 'active')
                                     ->get();

                    \Log::info("Fallback terakhir: mengambil semua produk aktif, total: " . $products->count());
                }
            }
        }
        
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
     * Format gambar produk untuk ditampilkan
     *
     * @param Product $product Objek produk
     * @return array Array path gambar produk
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
     * Memperbarui produk lama yang tidak memiliki seller_id yang ditetapkan
     * Ini untuk tujuan migrasi untuk menghubungkan produk-produk yang tidak memiliki penjual ke penjual yang valid
     *
     * @return \Illuminate\Http\JsonResponse Respons JSON yang menunjukkan hasil operasi
     */
    public function updateProductSellerRelationships()
    {
        // Dapatkan semua produk yang tidak memiliki seller_id yang ditetapkan
        $productsWithoutSeller = Product::whereNull('seller_id')->get();

        $updatedCount = 0;

        foreach ($productsWithoutSeller as $product) {
            // Temukan penjual default untuk ditetapkan ke produk ini
            // Sebagai contoh, kita bisa menggunakan penjual pertama dalam sistem
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

    /**
     * API endpoint untuk pencarian produk
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiSearch(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('kategori');
        $subcategory = $request->get('subkategori');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $rating = $request->get('rating');
        $sort = $request->get('sort', 'popular');

        $query = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
                        ->whereHas('seller'); // Hanya produk dengan seller yang valid

        // Tambahkan pencarian jika ada
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Tambahkan filter kategori jika ada
        if ($category && $category !== 'all') {
            $query->whereHas('category', function($q) use ($category) {
                $q->where('name', $category);
            });
        }

        // Tambahkan filter subkategori jika ada
        if ($subcategory) {
            $query->whereHas('subcategory', function($q) use ($subcategory) {
                $q->where('name', $subcategory);
            });
        }

        // Tambahkan filter harga minimum jika ada
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }

        // Tambahkan filter harga maksimum jika ada
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        // Tambahkan filter rating jika ada
        if ($rating) {
            $query = $query->whereHas('approvedReviews', function($q) use ($rating) {
                $q->selectRaw('product_id')
                  ->groupBy('product_id')
                  ->havingRaw('AVG(rating) >= ?', [$rating]);
            });
        }

        // Tambahkan sorting
        switch ($sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderByRaw('COALESCE(average_rating, 0) DESC');
                break;
            default: // popular
                $query->orderByRaw('(COALESCE(average_rating, 0) * 10) + COALESCE(review_count, 0) DESC');
                break;
        }

        $products = $query->get();

        // Tambahkan average rating dan review count ke setiap produk
        $products = $products->map(function($product) {
            $product->setAttribute('average_rating', $product->averageRating);
            $product->setAttribute('review_count', $product->reviews_count);
            $product->setAttribute('formatted_images', $this->formatProductImages($product));
            $product->setAttribute('rating', $product->averageRating);
            $product->setAttribute('harga', $product->price);
            $product->setAttribute('toko', $product->seller && is_object($product->seller) ? $product->seller->store_name : 'Toko Umum');
            return $product;
        });

        return response()->json($products);
    }

    /**
     * API endpoint untuk filter produk (semua produk dengan opsi filter)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiFilter(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('kategori');
        $subcategory = $request->get('subkategori');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $rating = $request->get('rating');
        $sort = $request->get('sort', 'popular');

        $query = Product::with(['variants', 'seller', 'category', 'subcategory', 'approvedReviews', 'images'])
                        ->whereHas('seller'); // Hanya produk dengan seller yang valid

        // Tambahkan pencarian jika ada
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Tambahkan filter kategori jika ada
        if ($category && $category !== 'all') {
            $query->whereHas('category', function($q) use ($category) {
                $q->where('name', $category);
            });
        }

        // Tambahkan filter subkategori jika ada
        if ($subcategory) {
            $query->whereHas('subcategory', function($q) use ($subcategory) {
                $q->where('name', $subcategory);
            });
        }

        // Tambahkan filter harga minimum jika ada
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }

        // Tambahkan filter harga maksimum jika ada
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        // Tambahkan filter rating jika ada
        if ($rating) {
            $query = $query->whereHas('approvedReviews', function($q) use ($rating) {
                $q->selectRaw('product_id')
                  ->groupBy('product_id')
                  ->havingRaw('AVG(rating) >= ?', [$rating]);
            });
        }

        // Tambahkan sorting
        switch ($sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderByRaw('COALESCE(average_rating, 0) DESC');
                break;
            default: // popular
                $query->orderByRaw('(COALESCE(average_rating, 0) * 10) + COALESCE(review_count, 0) DESC');
                break;
        }

        $products = $query->get();

        // Tambahkan average rating dan review count ke setiap produk
        $products = $products->map(function($product) {
            $product->setAttribute('average_rating', $product->averageRating);
            $product->setAttribute('review_count', $product->reviews_count);
            $product->setAttribute('formatted_images', $this->formatProductImages($product));
            $product->setAttribute('rating', $product->averageRating);
            $product->setAttribute('harga', $product->price);
            $product->setAttribute('toko', $product->seller && is_object($product->seller) ? $product->seller->store_name : 'Toko Umum');
            return $product;
        });

        return response()->json($products);
    }
}