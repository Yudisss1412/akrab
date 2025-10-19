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
        // Ambil semua produk dengan relasi variants dan seller
        $products = Product::with(['variants', 'seller', 'category'])->get();
        
        // Tambahkan filter berdasarkan kategori jika ada
        $kategori = $request->query('kategori');
        if ($kategori && $kategori !== 'all') {
            $products = Product::with(['variants', 'seller', 'category'])
                            ->whereHas('category', function($query) use ($kategori) {
                                $query->where('name', $kategori);
                            })
                            ->get();
        }
        
        return view('customer.produk.halaman_produk', compact('products'));
    }

    /**
     * Tampilkan detail produk
     */
    public function show($id)
    {
        // Ambil produk berdasarkan ID dengan relasi yang diperlukan
        $product = Product::with(['variants', 'seller', 'category', 'reviews.user'])->find($id);
        
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
            'gambar' => [$product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png')], // Tambahkan lebih banyak gambar jika ada
            'rating' => $product->averageRating, // Gunakan averageRating dari accessor
            'jumlah_ulasan' => $product->reviews_count, // Gunakan reviews_count dari accessor
            'ulasan' => $product->reviews->where('status', 'approved')->map(function($review) {
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
        
        $products = Product::with(['variants', 'seller', 'category'])
                        ->where('name', 'LIKE', "%{$query}%")
                        ->get();
        
        return view('customer.produk.halaman_produk', compact('products'));
    }

    /**
     * Tampilkan produk berdasarkan kategori
     */
    public function byCategory($category)
    {
        $products = Product::with(['variants', 'seller', 'category'])
                        ->whereHas('category', function($query) use ($category) {
                            $query->where('name', $category);
                        })
                        ->get();
        
        return view('customer.produk.halaman_produk', compact('products'));
    }

    /**
     * API endpoint untuk mendapatkan produk populer
     */
    public function popular()
    {
        // Ambil produk terbaru atau produk dengan penjualan terbanyak sebagai produk populer
        $products = Product::with(['variants', 'seller', 'category'])
                        ->orderBy('created_at', 'desc') // Urutkan berdasarkan tanggal terbaru
                        ->limit(10)
                        ->get();
        
        $formattedProducts = $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                'image' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
                'description' => $product->description,
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
     * API endpoint untuk mendapatkan detail produk
     */
    public function apiShow($id)
    {
        $product = Product::with(['variants', 'seller', 'category'])->find($id);
        
        if (!$product) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }
        
        $formattedProduct = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
            'image' => $product->image ? asset('storage/' . $product->image) : asset('src/placeholder.png'),
            'description' => $product->description,
            'specifications' => [
                'Kategori: ' . ($product->category->name ?? 'Umum'),
                'Stok: ' . $product->stock,
                'Berat: ' . $product->weight . 'g',
                'Status: ' . ucfirst($product->status)
            ]
        ];
        
        return response()->json($formattedProduct);
    }
}