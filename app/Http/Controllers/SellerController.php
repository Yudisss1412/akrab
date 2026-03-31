<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;

/**
 * SellerController - Halaman Toko Penjual (Public View)
 * 
 * Controller ini menangani tampilan halaman toko penjual yang dapat diakses
 * oleh customer/public. Customer dapat melihat:
 * - Profil toko penjual
 * - Produk-produk yang dijual
 * - Ulasan untuk toko ini
 * - Rating rata-rata toko
 * 
 * Get Directions:
 * - Customer dapat melihat lokasi toko di halaman ini
 * - Koordinat toko digunakan untuk menampilkan peta
 * - Customer bisa mendapatkan arah ke toko
 */
class SellerController extends Controller
{
    /**
     * Menampilkan halaman toko penjual
     * 
     * Method ini dapat menerima parameter berupa:
     * - ID penjual (numerik)
     * - Nama toko (string)
     * 
     * Data yang ditampilkan:
     * - Profil penjual (store_name, owner_name, rating, dll)
     * - Produk-produk aktif dari penjual (paginated)
     * - Ulasan terbaru untuk produk-produk penjual
     * - Rating rata-rata toko
     * - Jumlah produk aktif
     * 
     * @param mixed $sellerId ID atau nama toko penjual
     * @return \Illuminate\View\View View halaman toko
     */
    public function show($sellerId)
    {
        $seller = null;

        // Cek apakah parameter adalah numerik (ID) atau string (nama toko)
        if (is_numeric($sellerId)) {
            // Jika numerik, cari berdasarkan ID penjual
            $seller = Seller::with('user')->find($sellerId);
        } else {
            // Jika string, cari berdasarkan nama toko (store_name)
            $seller = Seller::with('user')->where('store_name', $sellerId)->first();
        }

        // Jika tidak ditemukan, tampilkan error 404
        if (!$seller) {
            abort(404, 'Toko tidak ditemukan');
        }

        // Pastikan bahwa $seller adalah objek Seller yang valid
        if (!is_a($seller, 'App\Models\Seller')) {
            abort(500, 'Data toko tidak dalam format yang benar');
        }

        // Ambil produk-produk dari seller ini yang statusnya active
        // Load relasi untuk performa: variants, category, subcategory, reviews, images, seller
        $products = Product::with(['variants', 'category', 'subcategory', 'approvedReviews', 'images', 'seller'])
                          ->where('seller_id', $seller->id)
                          ->where('status', 'active')
                          ->paginate(12); // Paginasi 12 produk per halaman

        // Ambil semua kategori untuk filter/sidebar
        $categories = Category::all();

        // Ambil 5 ulasan terbaru untuk produk-produk dari seller ini
        // Hanya ulasan yang sudah approved (disetujui admin)
        $reviews = Review::with(['product', 'user'])
                        ->whereHas('product', function($q) use ($seller) {
                            // Filter produk yang milik seller ini
                            $q->where('seller_id', $seller->id);
                        })
                        ->where('status', 'approved')
                        ->latest()
                        ->limit(5)
                        ->get();

        // Hitung rating rata-rata untuk toko (dari semua ulasan)
        $averageRating = $reviews->avg(function($review) {
            return $review->rating;
        }) ?? 0;

        // Hitung jumlah produk aktif dari seller ini
        $productCount = Product::where('seller_id', $seller->id)->where('status', 'active')->count();

        // Return view dengan semua data yang diperlukan
        return view('customer.toko.show', compact('seller', 'products', 'categories', 'reviews', 'averageRating', 'productCount'));
    }
}