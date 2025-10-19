<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SellerProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada kategori
        if (Category::count() == 0) {
            $this->call(CategorySeeder::class);
        }
        
        // Ambil kategori
        $categories = Category::all();
        
        // Ambil beberapa user dengan role seller
        $sellers = User::whereHas('role', function($query) {
            $query->where('name', 'seller');
        })->take(3)->get();
        
        if ($sellers->isEmpty()) {
            echo "Tidak ada penjual ditemukan. Jalankan UserSeeder terlebih dahulu.\n";
            return;
        }
        
        // Buat produk contoh untuk setiap penjual
        foreach ($sellers as $seller) {
            for ($i = 1; $i <= 5; $i++) {
                $category = $categories->random();
                
                $product = Product::create([
                    'name' => 'Produk Contoh ' . $i . ' dari ' . $seller->name,
                    'description' => 'Ini adalah deskripsi produk contoh yang dibuat untuk demonstrasi sistem e-commerce. Produk ini memiliki kualitas tinggi dan harga terjangkau.',
                    'price' => rand(50000, 500000),
                    'stock' => rand(10, 100),
                    'weight' => rand(100, 2000),
                    'category_id' => $category->id,
                    'seller_id' => $seller->id,
                    'status' => 'active' // Gunakan nilai yang valid sesuai enum
                ]);
                
                // Tambahkan gambar contoh
                $product->images()->create([
                    'image_path' => 'products/sample-product.jpg',
                    'alt_text' => 'Gambar produk contoh ' . $i
                ]);
            }
        }
        
        echo "Seeder produk berhasil dijalankan. " . (Product::count()) . " produk telah dibuat.\n";
    }
}
