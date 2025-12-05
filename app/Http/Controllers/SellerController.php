<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function show($sellerId)
    {
        $seller = null;

        // Cek apakah parameter adalah numerik (ID) atau string (nama toko)
        if (is_numeric($sellerId)) {
            // Jika numerik, cari berdasarkan ID
            $seller = Seller::find($sellerId);
        } else {
            // Jika string, cari berdasarkan store_name
            $seller = Seller::where('store_name', $sellerId)->first();
        }

        // Jika tidak ditemukan, tampilkan error
        if (!$seller) {
            abort(404, 'Toko tidak ditemukan');
        }

        // Pastikan bahwa $seller adalah objek Seller
        if (!is_a($seller, 'App\Models\Seller')) {
            abort(500, 'Data toko tidak dalam format yang benar');
        }

        // Ambil produk-produk dari seller ini yang aktif
        $products = Product::with(['variants', 'category', 'subcategory', 'approvedReviews', 'images', 'seller'])
                          ->where('seller_id', $seller->id)
                          ->where('status', 'active')
                          ->paginate(12); // Paginasi 12 produk per halaman

        // Ambil kategori-kategori yang tersedia untuk filter
        $categories = Category::all();

        // Ambil ulasan untuk toko ini
        $reviews = Review::with(['product', 'user'])
                        ->whereHas('product', function($q) use ($seller) {
                            $q->where('seller_id', $seller->id);
                        })
                        ->where('status', 'approved')
                        ->latest()
                        ->limit(5)
                        ->get();

        // Hitung rating rata-rata untuk toko
        $averageRating = $reviews->avg(function($review) {
            return $review->rating;
        }) ?? 0;

        // Hitung jumlah produk
        $productCount = Product::where('seller_id', $seller->id)->where('status', 'active')->count();

        return view('customer.toko.show', compact('seller', 'products', 'categories', 'reviews', 'averageRating', 'productCount'));
    }
}