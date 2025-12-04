<?php
// Script untuk memeriksa detail produk tertentu

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;

// Cari produk dengan nama "produk test 1" atau bagian dari namanya
$products = Product::where('name', 'like', '%produk test 1%')->get();

if ($products->isEmpty()) {
    echo "Produk dengan nama 'produk test 1' tidak ditemukan.\n";
    // Cari produk secara umum untuk melihat contoh struktur
    $products = Product::limit(5)->get();
    echo "Berikut beberapa produk contoh:\n";
}

foreach ($products as $product) {
    echo "ID: {$product->id}, Nama: {$product->name}\n";
    echo "  Category ID: {$product->category_id}\n";
    echo "  Subcategory ID: {$product->subcategory_id}\n";
    echo "  Subcategory field: {$product->subcategory}\n";
    
    // Periksa apakah relasi kategorinya valid
    $category = Category::find($product->category_id);
    if ($category) {
        echo "  Kategori dari relasi: {$category->name}\n";
    } else {
        echo "  Kategori dari relasi: NULL (tidak ditemukan)\n";
    }
    
    // Periksa apakah relasi subkategori valid
    $subcategory = Subcategory::find($product->subcategory_id);
    if ($subcategory) {
        echo "  Subkategori dari relasi: {$subcategory->name}\n";
    } else {
        echo "  Subkategori dari relasi: NULL (tidak ditemukan)\n";
    }
    
    echo "---\n";
}