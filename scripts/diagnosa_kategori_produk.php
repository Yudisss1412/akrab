<?php

// Script diagnose untuk masalah produk tidak muncul di halaman kategori kuliner

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;

// Ambil informasi penting dari database
echo "=== DIAGNOSA MASALAH PRODUK DI KATEGORI KULINER ===\n\n";

// 1. Cek apakah kategori 'Kuliner' benar-benar ada
echo "1. CEK KATEGORI KULINER:\n";
$kategoris = Category::all();
echo "Jumlah total kategori di database: " . $kategoris->count() . "\n";

$kulinerFound = false;
$kulinerCategory = null;
foreach ($kategoris as $kategori) {
    echo "  - ID: {$kategori->id}, Nama: '{$kategori->name}', Slug: '{$kategori->slug}'\n";
    if (strtolower($kategori->name) === 'kuliner' || 
        strtolower($kategori->name) === 'makanan' || 
        strtolower($kategori->name) === 'food' ||
        stripos(strtolower($kategori->name), 'kuliner') !== false ||
        stripos(strtolower($kategori->name), 'makanan') !== false) {
        $kulinerFound = true;
        $kulinerCategory = $kategori;
        echo "    >>> KATEGORI INI SESUAI DENGAN KATEGORI KULINER <<<\n";
    }
}

if (!$kulinerFound) {
    echo "Tidak ditemukan kategori yang sesuai dengan 'Kuliner'\n";
} else {
    echo "Kategori Kuliner ditemukan dengan ID: {$kulinerCategory->id}, Nama: '{$kulinerCategory->name}'\n\n";
}

// 2. Cek produk dengan kategori kuliner
echo "2. CEK PRODUK BERKAITAN DENGAN KATEGORI KULINER:\n";
$productsWithKuliner = collect();

if ($kulinerFound && $kulinerCategory) {
    $productsInCategory = Product::where('category_id', $kulinerCategory->id)
                                ->where('status', 'active')
                                ->get();
    
    echo "Jumlah produk dalam kategori '{$kulinerCategory->name}': {$productsInCategory->count()}\n";
    
    foreach ($productsInCategory as $product) {
        echo "  - Produk ID: {$product->id}, Nama: '{$product->name}', Harga: {$product->price}, Status: {$product->status}\n";
    }
    
    $productsWithKuliner = $productsInCategory;
}

// 3. Cek produk dengan kategori lain
echo "\n3. CEK PRODUK SECARA KESELURUHAN:\n";
$totalProducts = Product::count();
echo "Total produk di database: {$totalProducts}\n";

$activeProducts = Product::where('status', 'active')->count();
echo "Produk aktif: {$activeProducts}\n";

$inactiveProducts = Product::where('status', '!=', 'active')->count();
echo "Produk tidak aktif: {$inactiveProducts}\n";

// Ambil 5 produk pertama sebagai contoh
$sampleProducts = Product::with(['category', 'subcategory'])->limit(5)->get();
echo "\nContoh 5 produk pertama:\n";
foreach ($sampleProducts as $product) {
    $categoryName = $product->category ? $product->category->name : 'TIDAK ADA';
    $subcategoryName = $product->subcategory ? $product->subcategory->name : ($product->subcategory ?? 'TIDAK ADA');
    
    echo "  - Produk ID: {$product->id}, Nama: '{$product->name}', Kategori: '{$categoryName}' (ID: {$product->category_id}), Subkategori: '{$subcategoryName}', Status: {$product->status}\n";
}

// 4. Cek jika ada produk dengan field kategori string yang menyimpan 'kuliner' atau 'Kuliner'
echo "\n4. CEK PRODUK DENGAN STRING KATEGORI:\n";
$productsWithKulinerString = Product::where('subcategory', 'like', '%kuliner%')
                                   ->orWhere('subcategory', 'like', '%Kuliner%')
                                   ->orWhere('subcategory', 'like', '%makanan%')
                                   ->orWhere('subcategory', 'like', '%Makanan%')
                                   ->get();
                                   
echo "Produk yang mungkin memiliki field subcategory berisi kata 'kuliner/makanan': {$productsWithKulinerString->count()}\n";

foreach ($productsWithKulinerString as $product) {
    $categoryName = $product->category ? $product->category->name : 'TIDAK ADA';
    echo "  - Produk ID: {$product->id}, Nama: '{$product->name}', Kategori DB: '{$categoryName}' (ID: {$product->category_id}), Subcategory String: '{$product->subcategory}', Status: {$product->status}\n";
}

// 5. Jika masih tidak ada produk yang ditemukan dalam kategori kuliner, coba dengan pencarian lain
if ($productsWithKuliner->count() === 0) {
    echo "\n5. PENELUSURAN TAMBAHAN:\n";
    
    // Coba temukan produk dengan kategori yang mengandung kata 'makanan' atau 'food'
    $possibleKulinerCategories = Category::where('name', 'like', '%makanan%')
                                         ->orWhere('name', 'like', '%Makanan%')
                                         ->orWhere('name', 'like', '%food%')
                                         ->orWhere('name', 'like', '%Food%')
                                         ->orWhere('name', 'like', '%kuliner%')
                                         ->orWhere('name', 'like', '%Kuliner%')
                                         ->get();
    
    echo "Ditemukan " . $possibleKulinerCategories->count() . " kategori yang mungkin berkaitan dengan kuliner:\n";
    foreach ($possibleKulinerCategories as $cat) {
        echo "  - Kategori ID {$cat->id}: '{$cat->name}'\n";
        
        $prodCount = Product::where('category_id', $cat->id)->count();
        $activeProdCount = Product::where('category_id', $cat->id)->where('status', 'active')->count();
        
        echo "    Produk total: {$prodCount}, Produk aktif: {$activeProdCount}\n";
        
        if ($activeProdCount > 0) {
            $activeProds = Product::where('category_id', $cat->id)->where('status', 'active')->limit(3)->get();
            foreach ($activeProds as $prod) {
                echo "    - ID {$prod->id}: {$prod->name} (Rp {$prod->price})\n";
            }
        }
    }
}

echo "\n6. KESIMPULAN:\n";
if ($kulinerFound && $productsWithKuliner->count() > 0) {
    echo "✓ Kategori kuliner ditemukan dan produk-produk aktif ditemukan di dalamnya\n";
} else {
    echo "✗ Kategori kuliner tidak ditemukan ATAU tidak ada produk aktif di dalamnya\n";
    
    // Jika tidak ada produk dalam kategori kuliner, coba lihat apakah produk dengan nama tertentu ada
    $testProducts = Product::where('name', 'like', '%test%')->orWhere('name', 'like', '%produk%')->get();
    echo "Ditemukan {$testProducts->count()} produk dengan kata kunci 'test' atau 'produk':\n";
    foreach ($testProducts as $product) {
        $categoryName = $product->category ? $product->category->name : 'TIDAK ADA';
        echo "  - Produk ID: {$product->id}, Nama: '{$product->name}', Kategori: '{$categoryName}' (ID: {$product->category_id}), Status: {$product->status}\n";
    }
}
echo "\nProses diagnosa selesai.\n";