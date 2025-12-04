<?php
// Script untuk memperbaiki produk-produk yang tidak memiliki kategori atau subkategori yang valid

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;

try {
    // Ambil semua kategori dan subkategori yang tersedia
    $categories = Category::all();
    $subcategories = Subcategory::all();
    
    if ($categories->isEmpty()) {
        echo "Tidak ada kategori yang ditemukan di database.\n";
        exit;
    }
    
    // Ambil produk-produk yang tidak memiliki kategori valid atau subkategori valid
    $products = Product::with(['category', 'subcategory'])
                      ->whereDoesntHave('category')
                      ->orWhereNull('category_id')
                      ->get();
    
    echo "Menemukan {$products->count()} produk tanpa kategori valid.\n";
    
    foreach ($products as $product) {
        // Pilih kategori dan subkategori secara acak
        $randomCategory = $categories->random();
        
        // Pilih subkategori secara acak yang sesuai dengan kategori yang dipilih
        $categorySubcategories = $subcategories->where('category_id', $randomCategory->id);
        
        if ($categorySubcategories->isNotEmpty()) {
            $randomSubcategory = $categorySubcategories->random();
            $product->subcategory_id = $randomSubcategory->id;
        } else {
            // Jika tidak ada subkategori untuk kategori ini, coba set subkategori secara umum atau abaikan
            $product->subcategory_id = null;
        }
        
        $product->category_id = $randomCategory->id;
        $product->save();
        
        echo "Produk ID {$product->id} diperbarui dengan kategori: {$randomCategory->name}";
        if (isset($randomSubcategory)) {
            echo ", subkategori: {$randomSubcategory->name}";
        }
        echo "\n";
    }
    
    // Sekarang periksa produk-produk dengan subkategori yang tidak valid
    $products2 = Product::with(['category', 'subcategory'])
                       ->whereHas('category') // Pastikan kategori valid
                       ->where(function($query) {
                           $query->whereDoesntHave('subcategory')
                                 ->orWhereNull('subcategory_id');
                       })
                       ->get();
    
    echo "Menemukan {$products2->count()} produk dengan kategori valid tetapi subkategori tidak valid.\n";
    
    foreach ($products2 as $product) {
        // Cari subkategori yang sesuai dengan kategori produk ini
        $categorySubcategories = $subcategories->where('category_id', $product->category_id);
        
        if ($categorySubcategories->isNotEmpty()) {
            $randomSubcategory = $categorySubcategories->random();
            $product->subcategory_id = $randomSubcategory->id;
            $product->save();
            
            echo "Produk ID {$product->id} diperbarui dengan subkategori: {$randomSubcategory->name}\n";
        }
    }
    
    echo "Proses selesai.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}