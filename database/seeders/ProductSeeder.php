<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\Seller;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada user dan seller sebelum membuat produk
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $seller = Seller::first();
        if (!$seller) {
            $seller = Seller::create([
                'user_id' => $user->id,
                'store_name' => 'Test Seller Store',
                'owner_name' => 'Test Seller',
                'email' => 'seller@example.com',
                'status' => 'aktif',
            ]);
        }

        // Buat produk dummy
        Product::create([
            'name' => 'Kaos Polos Premium',
            'description' => 'Kaos polos kualitas premium dengan bahan lembut dan nyaman dipakai',
            'price' => 85000,
            'stock' => 100,
            'weight' => 200,
            'image' => 'products/kaos-polos.jpg',
            'seller_id' => $seller->id,
            'status' => 'active',
        ]);

        Product::create([
            'name' => 'Celana Jeans Premium',
            'description' => 'Celana jeans model terbaru dengan kualitas tinggi',
            'price' => 150000,
            'stock' => 50,
            'weight' => 500,
            'image' => 'products/celana-jeans.jpg',
            'seller_id' => $seller->id,
            'status' => 'active',
        ]);

        Product::create([
            'name' => 'Topi Baseball Trendy',
            'description' => 'Topi baseball model terbaru yang trendy dan stylish',
            'price' => 65000,
            'stock' => 75,
            'weight' => 100,
            'image' => 'products/topi-baseball.jpg',
            'seller_id' => $seller->id,
            'status' => 'active',
        ]);

        Product::create([
            'name' => 'Sepatu Sneakers Casual',
            'description' => 'Sepatu sneakers casual yang nyaman untuk aktivitas sehari-hari',
            'price' => 220000,
            'stock' => 30,
            'weight' => 800,
            'image' => 'products/sepatu-sneakers.jpg',
            'seller_id' => $seller->id,
            'status' => 'active',
        ]);

        Product::create([
            'name' => 'Jam Tangan Digital',
            'description' => 'Jam tangan digital dengan fitur lengkap dan tampilan modern',
            'price' => 250000,
            'stock' => 25,
            'weight' => 150,
            'image' => 'products/jam-tangan.jpg',
            'seller_id' => $seller->id,
            'status' => 'active',
        ]);

        Product::create([
            'name' => 'Dompet Kulit Asli',
            'description' => 'Dompet berbahan kulit asli dengan desain elegan dan fungsional',
            'price' => 120000,
            'stock' => 40,
            'weight' => 120,
            'image' => 'products/dompet-kulit.jpg',
            'seller_id' => $seller->id,
            'status' => 'active',
        ]);
    }
}
