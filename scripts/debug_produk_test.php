<?php
// Script untuk debug produk "produk test 1" secara spesifik

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;

echo "=== DEBUG PRODUK 'produk test 1' ===\n";

// Cari produk "produk test 1"
$testProducts = Product::where('name', 'like', '%produk test 1%')->get();

if ($testProducts->isEmpty()) {
    echo "Produk dengan nama 'produk test 1' tidak ditemukan\n";
    
    // Cari produk dengan kata "test" untuk menemukan apa pun yang mirip
    $testProducts = Product::where('name', 'like', '%test%')->get();
    echo "Mencari produk dengan kata 'test' sebagai gantinya...\n";
}

echo "Ditemukan " . $testProducts->count() . " produk:\n";

foreach ($testProducts as $product) {
    echo "\n- ID: {$product->id}\n";
    echo "  Nama: {$product->name}\n";
    echo "  Deskripsi: {$product->description}\n";
    echo "  Harga: {$product->price}\n";
    echo "  Status: {$product->status}\n";
    echo "  Category ID: {$product->category_id}\n";
    echo "  Subcategory ID: {$product->subcategory_id}\n";
    echo "  Subcategory (string): {$product->subcategory}\n";
    
    // Cek kategori
    $category = null;
    if ($product->category_id) {
        $category = Category::find($product->category_id);
        if ($category) {
            echo "  Kategori (dari relasi): {$category->name}\n";
        } else {
            echo "  Kategori (dari relasi): TIDAK DITEMUKAN\n";
        }
    } else {
        echo "  Kategori (dari relasi): TIDAK ADA (category_id NULL)\n";
    }
    
    // Cek subkategori
    $subcategory = null;
    if ($product->subcategory_id) {
        $subcategory = Subcategory::find($product->subcategory_id);
        if ($subcategory) {
            echo "  Subkategori (dari relasi): {$subcategory->name}\n";
        } else {
            echo "  Subkategori (dari relasi): TIDAK DITEMUKAN\n";
        }
    } else {
        echo "  Subkategori (dari relasi): TIDAK ADA (subcategory_id NULL)\n";
    }
    
    // Cek apakah produk ini seharusnya berada di kategori Kuliner
    $kulinerCategory = Category::where('name', 'Kuliner')->first();
    if ($kulinerCategory) {
        if ($product->category_id == $kulinerCategory->id) {
            echo "  ✓ Produk ini SEHARUSNYA muncul di kategori Kuliner\n";
        } else {
            echo "  ✗ Produk ini TIDAK ADA di kategori Kuliner (ID kategori: {$product->category_id}, Nama kategori seharusnya: {$kulinerCategory->name})\n";
        }
    } else {
        // Coba dengan variasi nama kategori Kuliner
        $kategoriVariasi = Category::where('name', 'like', '%kuliner%')
                                   ->orWhere('name', 'like', '%makanan%')
                                   ->orWhere('name', 'like', '%Kuliner%')
                                   ->first();
        
        if ($kategoriVariasi) {
            echo "  Ditemukan kategori yang mirip dengan 'Kuliner': {$kategoriVariasi->name} (ID: {$kategoriVariasi->id})\n";
            if ($product->category_id == $kategoriVariasi->id) {
                echo "  ✓ Produk ini seharusnya muncul di kategori {$kategoriVariasi->name}\n";
            } else {
                echo "  ✗ Produk ini tidak ada di kategori {$kategoriVariasi->name}\n";
            }
        } else {
            echo "  Tidak ditemukan kategori yang mirip dengan 'Kuliner'\n";
        }
    }
}

// Info tambahan: Temukan semua produk yang terkait dengan kategori Kuliner
echo "\n=== INFO TAMBAHAN ===\n";
$allCategories = Category::all();
echo "Jumlah total kategori: " . $allCategories->count() . "\n";
foreach ($allCategories as $cat) {
    echo "- Kategori ID {$cat->id}: {$cat->name}\n";
}

$kulinerCat = Category::where('name', 'Kuliner')->first();
if ($kulinerCat) {
    $produkInKuliner = Product::where('category_id', $kulinerCat->id)->where('status', 'active')->get();
    echo "\nProduk dalam kategori Kuliner (aktif): {$produkInKuliner->count()}\n";
    foreach ($produkInKuliner as $prod) {
        echo "  - ID {$prod->id}: {$prod->name}\n";
    }
} else {
    echo "\nTidak ditemukan kategori Kuliner di database\n";
}

echo "\nDebug selesai.\n";