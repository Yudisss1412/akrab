<?php
// Script debug untuk memeriksa kategori dan produk

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;

// Cek apakah koneksi ke database berhasil
try {
    DB::connection()->getPdo();
    echo "Koneksi ke database berhasil\n";
} catch (\Exception $e) {
    echo "Koneksi ke database gagal: " . $e->getMessage() . "\n";
    exit;
}

// Cek semua kategori yang ada
echo "\n======= DAFTAR SEMUA KATEGORI =======\n";
$allCategories = Category::all();
foreach ($allCategories as $cat) {
    echo "ID: {$cat->id}, Nama: '{$cat->name}', Slug: '{$cat->slug}'\n";
}

// Cek apakah kategori Kuliner ada
echo "\n======= PENCARIAN KATEGORI 'KULINER' =======\n";
$kulinerCategory1 = Category::where('name', 'Kuliner')->first();
if ($kulinerCategory1) {
    echo "Ditemukan kategori Kuliner: ID {$kulinerCategory1->id}, Nama: '{$kulinerCategory1->name}'\n";
} else {
    echo "Tidak ditemukan kategori dengan nama 'Kuliner'\n";
}

$kulinerCategory2 = Category::where('name', 'Kuliner')->first();
if ($kulinerCategory2) {
    echo "Ditemukan kategori Kuliner: ID {$kulinerCategory2->id}, Nama: '{$kulinerCategory2->name}'\n";
} else {
    echo "Tidak ditemukan kategori dengan nama 'Kuliner'\n";
}

$kulinerCategory3 = Category::where('name', 'KULINER')->first();
if ($kulinerCategory3) {
    echo "Ditemukan kategori KULINER: ID {$kulinerCategory3->id}, Nama: '{$kulinerCategory3->name}'\n";
} else {
    echo "Tidak ditemukan kategori dengan nama 'KULINER'\n";
}

$kulinerCategory4 = Category::where('name', 'kuliner')->first();
if ($kulinerCategory4) {
    echo "Ditemukan kategori kuliner: ID {$kulinerCategory4->id}, Nama: '{$kulinerCategory4->name}'\n";
} else {
    echo "Tidak ditemukan kategori dengan nama 'kuliner'\n";
}

$kulinerCategory5 = Category::where('slug', 'kuliner')->first();
if ($kulinerCategory5) {
    echo "Ditemukan kategori dengan slug 'kuliner': ID {$kulinerCategory5->id}, Nama: '{$kulinerCategory5->name}'\n";
} else {
    echo "Tidak ditemukan kategori dengan slug 'kuliner'\n";
}

// Cek produk-produk yang terkait dengan kategori Kuliner
echo "\n======= JUMLAH PRODUK PER KATEGORI =======\n";
foreach ($allCategories as $cat) {
    $productCount = Product::where('category_id', $cat->id)
                           ->where('status', 'active')
                           ->count();
    echo "Kategori '{$cat->name}' (ID {$cat->id}): {$productCount} produk aktif\n";
}

// Cek produk dengan nama tertentu untuk melihat kategorinya
echo "\n======= PRODUK DENGAN NAMA BERKAITAN DENGAN 'PRODUK TEST' =======\n";
$testProducts = Product::where('name', 'like', '%test%')->get();
foreach ($testProducts as $product) {
    $categoryName = $product->category ? $product->category->name : 'N/A';
    echo "Produk ID {$product->id}: '{$product->name}' - Kategori ID: {$product->category_id}, Nama Kategori: {$categoryName}, Status: {$product->status}\n";
}

// Cek produk dengan kategori_id NULL atau tidak valid
echo "\n======= PRODUK DENGAN KATEGORI TIDAK VALID =======\n";
$invalidCategoryProducts = Product::whereNull('category_id')
                                  ->orWhereDoesntHave('category')
                                  ->limit(10) // Ambil hanya 10 untuk contoh
                                  ->get();
foreach ($invalidCategoryProducts as $product) {
    $categoryName = $product->category ? $product->category->name : 'TIDAK ADA KATEGORI';
    echo "Produk ID {$product->id}: '{$product->name}' - Category ID: {$product->category_id}, Relasi Kategori: {$categoryName}, Status: {$product->status}\n";
}

echo "\nPemeriksaan selesai.\n";