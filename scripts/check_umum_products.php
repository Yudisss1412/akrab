<?php
// Script untuk memeriksa produk-produk yang menampilkan "Umum"

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;

echo "Memeriksa produk-produk yang menampilkan 'Umum'...\n";

// Cari produk-produk dengan field subcategory yang bernilai "Umum"
$productsWithUmumSubcategory = Product::where('subcategory', 'Umum')->get();
echo "Ditemukan " . $productsWithUmumSubcategory->count() . " produk dengan field subcategory bernilai 'Umum':\n";
foreach ($productsWithUmumSubcategory as $product) {
    echo "- Produk ID {$product->id}: {$product->name} (category_id: {$product->category_id})\n";
}

// Cari produk-produk dengan kategori valid tapi tidak dimuat relasinya
$products = Product::whereHas('category')  // Memiliki kategori valid
                  ->where(function($query) {
                      $query->where('subcategory', 'Umum')
                            ->orWhereNull('subcategory')
                            ->orWhere('subcategory', '');
                  })
                  ->with('category')  // Muat relasi kategori
                  ->get();

echo "\nDitemukan " . $products->count() . " produk dengan kategori valid tapi subkategori bermasalah:\n";
foreach ($products as $product) {
    $categoryName = $product->category ? $product->category->name : 'Tidak ada kategori';
    echo "- Produk ID {$product->id}: {$product->name} (Kategori: {$categoryName}, Subcategory field: {$product->subcategory})\n";
}

// Cari produk dengan category_id tapi tidak memiliki relasi kategori yang valid
$productsNoCategoryRel = Product::whereNotNull('category_id')
                                ->whereDoesntHave('category')
                                ->get();
echo "\nDitemukan " . $productsNoCategoryRel->count() . " produk dengan category_id tapi tidak memiliki kategori terkait:\n";
foreach ($productsNoCategoryRel as $product) {
    echo "- Produk ID {$product->id}: {$product->name} (category_id: {$product->category_id})\n";
}

// Cek produk secara umum untuk melihat contoh struktur
echo "\nContoh beberapa produk untuk referensi:\n";
$sampleProducts = Product::with('category', 'subcategory')->limit(5)->get();
foreach ($sampleProducts as $product) {
    $categoryName = $product->category ? $product->category->name : 'NULL';
    $subcategoryName = $product->subcategory ? $product->subcategory : 'NULL (field)';
    $subcategoryRelName = $product->subcategoryRelasi ? $product->subcategoryRelasi->name : 'NULL (relasi)';
    
    echo "- Produk ID {$product->id}: {$product->name}\n";
    echo "  Kategori (relasi): {$categoryName}\n";
    echo "  Subkategori (field): {$subcategoryName}\n";
    echo "  Subkategori (relasi): {$subcategoryRelName}\n";
    echo "  \n";
}