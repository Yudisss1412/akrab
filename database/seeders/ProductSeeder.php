<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Category;
use App\Models\ProductVariant;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure categories exist before creating products
        $categories = Category::pluck('id')->toArray();
        if (empty($categories)) {
            $this->command->info('No categories found, calling CategorySeeder...');
            $this->call(CategorySeeder::class);
            $categories = Category::pluck('id')->toArray();
        }

        // Ensure sellers exist before creating products
        $sellers = Seller::all();
        if ($sellers->count() === 0) {
            $this->command->info('No sellers found, calling UserSeeder to create sellers...');
            $this->call(UserSeeder::class);
            $sellers = Seller::all();
        }

        // Prepare products data with dynamic category and seller assignments
        $productsData = [
            [
                'name' => 'Kaos Polos Premium',
                'description' => 'Kaos polos kualitas premium dengan bahan lembut dan nyaman dipakai',
                'price' => 85000,
                'stock' => 100,
                'weight' => 200,
                'image' => 'products/kaos-polos.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Celana Jeans Premium',
                'description' => 'Celana jeans model terbaru dengan kualitas tinggi',
                'price' => 150000,
                'stock' => 50,
                'weight' => 500,
                'image' => 'products/celana-jeans.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Topi Baseball Trendy',
                'description' => 'Topi baseball model terbaru yang trendy dan stylish',
                'price' => 65000,
                'stock' => 75,
                'weight' => 100,
                'image' => 'products/topi-baseball.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Sepatu Sneakers Casual',
                'description' => 'Sepatu sneakers casual yang nyaman untuk aktivitas sehari-hari',
                'price' => 220000,
                'stock' => 30,
                'weight' => 800,
                'image' => 'products/sepatu-sneakers.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Jam Tangan Digital',
                'description' => 'Jam tangan digital dengan fitur lengkap dan tampilan modern',
                'price' => 250000,
                'stock' => 25,
                'weight' => 150,
                'image' => 'products/jam-tangan.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Dompet Kulit Asli',
                'description' => 'Dompet berbahan kulit asli dengan desain elegan dan fungsional',
                'price' => 120000,
                'stock' => 40,
                'weight' => 120,
                'image' => 'products/dompet-kulit.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Kemeja Formal',
                'description' => 'Kemeja formal untuk kebutuhan kerja dan acara resmi',
                'price' => 135000,
                'stock' => 60,
                'weight' => 300,
                'image' => 'products/kemeja-formal.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Hoodie Hangat',
                'description' => 'Hoodie tebal dan hangat untuk cuaca dingin',
                'price' => 180000,
                'stock' => 45,
                'weight' => 400,
                'image' => 'products/hoodie-hangat.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Kacamata Hitam',
                'description' => 'Kacamata hitam berkualitas dengan perlindungan UV maksimal',
                'price' => 95000,
                'stock' => 80,
                'weight' => 50,
                'image' => 'products/kacamata-hitam.jpg',
                'status' => 'active',
            ],
            [
                'name' => 'Tas Ransel',
                'description' => 'Tas ransel multifungsi untuk kegiatan sehari-hari',
                'price' => 150000,
                'stock' => 35,
                'weight' => 600,
                'image' => 'products/tas-ransel.jpg',
                'status' => 'active',
            ],
        ];

        // Distribute products among different sellers and categories
        foreach ($productsData as $index => $productData) {
            $seller = $sellers->get($index % $sellers->count());
            $category = $categories[$index % count($categories)];
            
            $productData['seller_id'] = $seller->id;
            $productData['category_id'] = $category;
            
            Product::create($productData);
        }
        
        // Call ProductVariantSeeder after creating products
        $this->call(ProductVariantSeeder::class);
    }
}
