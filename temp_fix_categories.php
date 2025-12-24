<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;

// Dapatkan mapping kategori
$categories = Category::pluck('id', 'name')->toArray();

// Ambil semua produk yang tidak memiliki kategori
$products = Product::whereNull('category_id')->get();

echo "Memperbarui " . $products->count() . " produk...\n";

foreach ($products as $product) {
    $name = strtolower($product->name);
    
    // Tentukan kategori berdasarkan nama produk
    $categoryId = null;
    
    if (strpos($name, 'keripik') !== false || strpos($name, 'oleh') !== false || 
        strpos($name, 'sambal') !== false || strpos($name, 'madu') !== false || 
        strpos($name, 'minyak') !== false || strpos($name, 'jahe') !== false) {
        $categoryId = $categories['Kuliner'] ?? null;
    } elseif (strpos($name, 'kemeja') !== false || strpos($name, 'rok') !== false || 
              strpos($name, 'jas') !== false) {
        $categoryId = $categories['Fashion'] ?? null;
    } elseif (strpos($name, 'anyaman') !== false || strpos($name, 'lukisan') !== false) {
        $categoryId = $categories['Kerajinan Tangan'] ?? null;
    } elseif (strpos($name, 'pupuk') !== false || strpos($name, 'alat') !== false || 
              strpos($name, 'bibit') !== false) {
        $categoryId = $categories['Produk Berkebun'] ?? null;
    } elseif (strpos($name, 'madu') !== false || strpos($name, 'minyak') !== false || 
              strpos($name, 'jahe') !== false) {
        $categoryId = $categories['Produk Kesehatan'] ?? null;
    } elseif (strpos($name, 'boneka') !== false || strpos($name, 'puzzle') !== false || 
              strpos($name, 'robot') !== false) {
        $categoryId = $categories['Mainan'] ?? null;
    } elseif (strpos($name, 'hamper') !== false) {
        $categoryId = $categories['Hampers'] ?? null;
    } elseif (strpos($name, 'speaker') !== false || strpos($name, 'power') !== false) {
        $categoryId = $categories['Elektronik'] ?? null;
    } elseif (strpos($name, 'gelang') !== false) {
        $categoryId = $categories['Aksesoris'] ?? null;
    } elseif (strpos($name, 'tas') !== false || strpos($name, 'dompet') !== false || 
              strpos($name, 'ikat') !== false) {
        $categoryId = $categories['Kulit'] ?? null;
    }
    
    if ($categoryId) {
        $product->update(['category_id' => $categoryId]);
        echo "Updated: {$product->name} -> Category ID: $categoryId\n";
    } else {
        echo "No category match for: {$product->name}\n";
    }
}

echo "Proses selesai.\n";