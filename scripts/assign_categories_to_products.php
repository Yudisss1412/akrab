<?php
// Script untuk menetapkan kategori/subkategori ke produk yang tidak memiliki data valid

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;

echo "Memeriksa dan memperbaiki produk-produk yang menampilkan 'Umum'...\n";

// Ambil semua kategori dan subkategori yang tersedia
$categories = Category::all();
$subcategories = Subcategory::all();

if ($categories->isEmpty()) {
    echo "Tidak ada kategori ditemukan di database.\n";
    exit;
}

echo "Ditemukan " . $categories->count() . " kategori dan " . $subcategories->count() . " subkategori.\n";

// Cari produk-produk yang mungkin menampilkan "Umum" karena tidak memiliki kategori/subkategori valid
$allProducts = Product::all();
$umumProducts = [];
$fixedCount = 0;

foreach ($allProducts as $product) {
    // Cek apakah produk seharusnya menampilkan "Umum"
    $shouldShowUmum = true;
    
    // Cek kategori
    if ($product->category_id) {
        $category = Category::find($product->category_id);
        if ($category) {
            $shouldShowUmum = false; // Kategori valid ditemukan
        }
    }
    
    // Jika kategori tidak valid, cek apakah field subcategory bernilai "Umum"
    if ($product->subcategory == 'Umum') {
        // Ini mungkin kasus produk yang menampilkan "Umum"
        $umumProducts[] = $product;
    } elseif (!$product->category_id && !$product->category) {
        // Produk tanpa kategori apapun
        $umumProducts[] = $product;
    }
}

echo "Ditemukan " . count($umumProducts) . " produk yang kemungkinan menampilkan 'Umum'.\n";

if (count($umumProducts) > 0) {
    foreach ($umumProducts as $product) {
        echo "Memperbaiki produk: ID {$product->id}, Nama: {$product->name}\n";
        
        // Tetapkan kategori dan subkategori acak yang valid
        $randomCategory = $categories->random();
        
        // Tetapkan subkategori acak dari kategori yang dipilih
        $categorySubcategories = $subcategories->where('category_id', $randomCategory->id);
        $randomSubcategory = null;
        
        if ($categorySubcategories->isNotEmpty()) {
            $randomSubcategory = $categorySubcategories->random();
        } else {
            // Jika tidak ada subkategori untuk kategori ini, pilih dari semua subkategori
            if ($subcategories->isNotEmpty()) {
                $randomSubcategory = $subcategories->random();
            }
        }
        
        $product->category_id = $randomCategory->id;
        
        if ($randomSubcategory) {
            $product->subcategory_id = $randomSubcategory->id;
            $product->subcategory = $randomSubcategory->name;
        } else {
            $product->subcategory_id = null;
            $product->subcategory = null;
        }
        
        $product->save();
        $fixedCount++;
        
        echo "  - Ditugaskan ke kategori: {$randomCategory->name}";
        if ($randomSubcategory) {
            echo ", subkategori: {$randomSubcategory->name}";
        }
        echo "\n";
    }
}

echo "\nProses selesai. {$fixedCount} produk telah diperbaiki.\n";

// Cek apakah masih ada produk dengan kategori/subkategori NULL setelah perbaikan
$remainingProducts = Product::where(function($query) {
    $query->whereNull('category_id')
          ->orWhere('category_id', 0);
})->get();

echo "Terdeteksi " . $remainingProducts->count() . " produk masih memiliki category_id NULL atau 0.\n";

if ($remainingProducts->isNotEmpty()) {
    echo "Memperbaiki produk-produk tersisa...\n";
    foreach ($remainingProducts as $product) {
        $randomCategory = $categories->random();
        $product->category_id = $randomCategory->id;
        $product->save();
        
        echo "Produk ID {$product->id} ({$product->name}) diperbaiki ke kategori: {$randomCategory->name}\n";
        $fixedCount++;
    }
}