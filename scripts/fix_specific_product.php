<?php
// Script untuk memperbaiki produk spesifik di database

// Mulai sesi Laravel
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;

// Cek apakah produk "produk test 1" ada
$products = Product::where('name', 'like', '%produk test 1%')->get();

if ($products->isEmpty()) {
    echo "Produk dengan nama mengandung 'produk test 1' tidak ditemukan.\n";
    echo "Berikut beberapa produk contoh:\n";
    $sampleProducts = Product::limit(5)->get();
    foreach ($sampleProducts as $product) {
        echo "ID: {$product->id}, Nama: {$product->name}, Category ID: {$product->category_id}, Subcategory ID: {$product->subcategory_id}\n";
    }
    exit;
}

// Ambil kategori dan subkategori dari database
$categories = Category::all();
$subcategories = Subcategory::all();

if ($categories->isEmpty()) {
    echo "Tidak ada kategori ditemukan di database.\n";
    exit;
}

if ($subcategories->isEmpty()) {
    echo "Tidak ada subkategori ditemukan di database.\n";
    exit;
}

echo "Ditemukan " . $categories->count() . " kategori dan " . $subcategories->count() . " subkategori.\n";

foreach ($products as $product) {
    echo "Memperbaiki produk: ID {$product->id}, Nama: {$product->name}\n";
    echo "  Sebelum - Category ID: {$product->category_id}, Subcategory ID: {$product->subcategory_id}\n";
    
    // Pilih kategori dan subkategori acak
    $randomCategory = $categories->random();
    $categorySubcategories = $subcategories->where('category_id', $randomCategory->id);
    
    // Jika kategori terpilih memiliki subkategori, pilih salah satu
    if ($categorySubcategories->isNotEmpty()) {
        $randomSubcategory = $categorySubcategories->random();
        $product->category_id = $randomCategory->id;
        $product->subcategory_id = $randomSubcategory->id;
        echo "  Setelah - Category: {$randomCategory->name}, Subcategory: {$randomSubcategory->name}\n";
    } else {
        // Jika tidak ada subkategori untuk kategori ini, pilih subkategori acak dari semua subkategori
        $randomSubcategory = $subcategories->random();
        $product->category_id = $randomCategory->id;
        $product->subcategory_id = $randomSubcategory->id;
        echo "  Setelah - Category: {$randomCategory->name}, Subcategory: {$randomSubcategory->name}\n";
    }
    
    $product->save();
    echo "  Produk berhasil diperbarui!\n\n";
}

echo "Proses perbaikan produk selesai.\n";