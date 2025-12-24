<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Category;

// Jalankan di konteks Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Ambil semua kategori
$categories = Category::all();

echo "Jumlah kategori sebelum: " . count($categories) . "\n";

// Nama kategori asli
$originalNames = ['Kuliner', 'Fashion', 'Kerajinan Tangan', 'Produk Berkebun', 'Produk Kesehatan', 'Mainan', 'Hampers', 'Elektronik', 'Aksesoris', 'Kulit'];

// Hapus kategori yang bukan termasuk kategori asli
foreach ($categories as $category) {
    if (!in_array($category->name, $originalNames)) {
        echo "Menghapus: " . $category->name . "\n";
        $category->delete();
    }
}

echo "Pembersihan selesai.\n";

// Tampilkan kategori yang tersisa
$remaining = Category::all();
echo "Jumlah kategori setelah: " . count($remaining) . "\n";
foreach ($remaining as $cat) {
    echo "- " . $cat->name . "\n";
}