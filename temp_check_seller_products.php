<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Seller;
use App\Models\Product;

// Cari user dengan nama Rudi Firmansyah
$user = User::where('name', 'Rudi Firmansyah')->first();

if ($user) {
    echo "Ditemukan user: " . $user->name . " (ID: " . $user->id . ")\n";
    
    // Cek apakah user ini adalah seller
    $seller = Seller::where('user_id', $user->id)->first();
    
    if ($seller) {
        echo "User ini adalah seller (ID: " . $seller->id . ")\n";
        
        // Ambil produk-produk dari seller ini
        $products = Product::where('seller_id', $seller->id)->get();
        
        echo "Jumlah produk milik seller: " . $products->count() . "\n";
        
        if ($products->count() > 0) {
            echo "Daftar produk:\n";
            foreach ($products as $product) {
                echo "- " . $product->name . " (ID: " . $product->id . ", Harga: Rp " . number_format($product->price, 0, ',', '.') . ", Stok: " . $product->stock . ")\n";
            }
        } else {
            echo "Seller ini tidak memiliki produk.\n";
        }
    } else {
        echo "User ini tidak terdaftar sebagai seller.\n";
    }
} else {
    echo "Tidak ditemukan user dengan nama 'Rudi Firmansyah'.\n";
    
    // Coba cari nama yang mirip
    $similarUsers = User::where('name', 'like', '%Rudi%')->get();
    
    if ($similarUsers->count() > 0) {
        echo "Ditemukan user dengan nama mirip:\n";
        foreach ($similarUsers as $user) {
            echo "- " . $user->name . " (ID: " . $user->id . ")\n";
        }
    }
}