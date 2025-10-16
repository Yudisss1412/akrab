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
        $product = Product::with(['variants', 'seller', 'category'])->find($id);
        
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
            'rating' => $product->rating ?? 4.5, // Nilai dummy untuk sekarang, bisa diganti dengan rating sebenarnya
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
}