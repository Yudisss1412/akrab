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
                'description' => 'Kaos polos kualitas premium dengan bahan lembut dan nyaman dipakai sehari-hari. Terbuat dari 100% katun pilihan yang breathable dan tidak panas saat dikenakan.',
                'price' => 85000,
                'stock' => 100,
                'weight' => 200,
                'image' => 'products/kaos-polos.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => '100% Katun',
                    'Jenis Kelamin' => 'Unisex',
                    'Ukuran' => 'S, M, L, XL',
                    'Panduan Perawatan' => 'Cuci dengan suhu maksimal 30°C',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Katun',
                'size' => 'S-M-L-XL',
                'color' => 'Putih, Hitam, Abu-abu, Navy',
                'brand' => 'CottonStyle',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak ada',
                'min_order' => 1,
                'ready_stock' => 100,
                'features' => [
                    'Bahan lembut dan breathable',
                    'Desain simple dan versatile',
                    'Tersedia dalam berbagai warna',
                    'Tahan lama dan tidak mudah melar'
                ]
            ],
            [
                'name' => 'Celana Jeans Premium',
                'description' => 'Celana jeans model terbaru dengan kualitas tinggi dari bahan denim premium yang kuat dan nyaman dikenakan.',
                'price' => 150000,
                'stock' => 50,
                'weight' => 500,
                'image' => 'products/celana-jeans.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Denim Premium',
                    'Jenis Kelamin' => 'Pria',
                    'Ukuran' => '28, 30, 32, 34, 36',
                    'Kantong' => '5 kantong',
                    'Kancing' => 'Kancing logam premium',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Denim',
                'size' => '28-30-32-34-36',
                'color' => 'Dark Blue, Light Blue, Black',
                'brand' => 'DenimStyle',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi jahitan 30 hari',
                'min_order' => 1,
                'ready_stock' => 50,
                'features' => [
                    'Bahan denim premium yang tahan lama',
                    'Desain modern dan trendy',
                    'Kantong multifungsi',
                    'Tidak mudah kusut'
                ]
            ],
            [
                'name' => 'Topi Baseball Trendy',
                'description' => 'Topi baseball model terbaru yang trendy dan stylish untuk pelengkap gaya sehari-hari. Cocok untuk berbagai aktivitas outdoor.',
                'price' => 65000,
                'stock' => 75,
                'weight' => 100,
                'image' => 'products/topi-baseball.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Katun Drill',
                    'Ukuran' => 'Adjustable (55-60cm)',
                    'Jenis' => 'Snapback',
                    'Kualitas' => 'Premium',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Katun Drill',
                'size' => 'Adjustable',
                'color' => 'Hitam, Putih, Merah',
                'brand' => 'CapStyle',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi produk 7 hari',
                'min_order' => 1,
                'ready_stock' => 75,
                'features' => [
                    'Desain trendy dan modern',
                    'Adjustable strap untuk kenyamanan',
                    'Kualitas bahan premium',
                    'Cocok untuk berbagai usia'
                ]
            ],
            [
                'name' => 'Sepatu Sneakers Casual',
                'description' => 'Sepatu sneakers casual yang nyaman untuk aktivitas sehari-hari. Didesain dengan teknologi sol EVA untuk kenyamanan optimal.',
                'price' => 220000,
                'stock' => 30,
                'weight' => 800,
                'image' => 'products/sepatu-sneakers.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Kulit Sintetis + Kanvas',
                    'Ukuran' => '39-44 (Laki-laki), 36-40 (Perempuan)',
                    'Sol' => 'Sol EVA Anti Slip',
                    'Jenis' => 'Lace-up',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Kulit Sintetis + Kanvas',
                'size' => '39-44',
                'color' => 'Putih, Hitam, Abu-abu',
                'brand' => 'SneakersPro',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi sol 3 bulan',
                'min_order' => 1,
                'ready_stock' => 30,
                'features' => [
                    'Teknologi sol EVA untuk kenyamanan',
                    'Desain casual namun elegan',
                    'Sirkulasi udara yang baik',
                    'Tahan air ringan'
                ]
            ],
            [
                'name' => 'Jam Tangan Digital',
                'description' => 'Jam tangan digital dengan fitur lengkap dan tampilan modern. Tahan air hingga kedalaman 50 meter.',
                'price' => 250000,
                'stock' => 25,
                'weight' => 150,
                'image' => 'products/jam-tangan.jpg',
                'status' => 'active',
                'specifications' => [
                    'Tipe' => 'Digital',
                    'Bahan Tali' => 'Silikon Premium',
                    'Bahan Kaca' => 'Mineral Crystal',
                    'Tahan Air' => '50 meter',
                    'Fungsi' => 'Waktu, stopwatch, alarm',
                    'Negara Asal' => 'China'
                ],
                'material' => 'Silikon + Stainless Steel',
                'size' => 'Universal',
                'color' => 'Hitam, Putih, Biru',
                'brand' => 'TimePro',
                'origin' => 'China',
                'warranty' => 'Garansi 1 tahun',
                'min_order' => 1,
                'ready_stock' => 25,
                'features' => [
                    'Tahan air hingga 50m',
                    'Display digital jelas',
                    'Tali silikon nyaman',
                    'Baterai tahan lama'
                ]
            ],
            [
                'name' => 'Dompet Kulit Asli',
                'description' => 'Dompet berbahan kulit asli dengan desain elegan dan fungsional. Terdiri dari banyak slot kartu dan kompartemen uang.',
                'price' => 120000,
                'stock' => 40,
                'weight' => 120,
                'image' => 'products/dompet-kulit.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Kulit Sapi Asli',
                    'Dimensi' => '12 x 9 x 2 cm',
                    'Slot Kartu' => '8 slot',
                    'Kompartemen Uang' => '2 kompartemen',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Kulit Sapi Asli',
                'size' => '12 x 9 x 2 cm',
                'color' => 'Coklat, Hitam',
                'brand' => 'LeatherCraft',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi bahan 6 bulan',
                'min_order' => 1,
                'ready_stock' => 40,
                'features' => [
                    'Bahan kulit asli premium',
                    'Banyak slot kartu',
                    'Desain elegan',
                    'Tahan lama dan tidak mudah rusak'
                ]
            ],
            [
                'name' => 'Kemeja Formal',
                'description' => 'Kemeja formal untuk kebutuhan kerja dan acara resmi. Terbuat dari bahan berkualitas tinggi yang nyaman dan tidak mudah kusut.',
                'price' => 135000,
                'stock' => 60,
                'weight' => 300,
                'image' => 'products/kemeja-formal.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Katun Oxford',
                    'Jenis Kelamin' => 'Pria',
                    'Ukuran' => 'S, M, L, XL, XXL',
                    'Lengan' => 'Panjang',
                    'Kerah' => 'Klasik',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Katun Oxford',
                'size' => 'S-XXL',
                'color' => 'Putih, Biru, Abu-abu',
                'brand' => 'OfficeWear',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak ada',
                'min_order' => 1,
                'ready_stock' => 60,
                'features' => [
                    'Bahan tidak mudah kusut',
                    'Kenyamanan optimal',
                    'Desain formal modern',
                    'Tersedia dalam banyak warna'
                ]
            ],
            [
                'name' => 'Hoodie Hangat',
                'description' => 'Hoodie tebal dan hangat untuk cuaca dingin. Didesain dengan kantong besar dan tali serut untuk kenyamanan maksimal.',
                'price' => 180000,
                'stock' => 45,
                'weight' => 400,
                'image' => 'products/hoodie-hangat.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Fleece Premium',
                    'Jenis Kelamin' => 'Unisex',
                    'Ukuran' => 'S, M, L, XL',
                    'Tutup Kepala' => 'Hoodie dengan tali serut',
                    'Kantong' => 'Kantong besar depan',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Fleece',
                'size' => 'S-XL',
                'color' => 'Hitam, Abu-abu, Merah, Biru',
                'brand' => 'WarmStyle',
                'origin' => 'Indonesia',
                'warranty' => 'Tidak ada',
                'min_order' => 1,
                'ready_stock' => 45,
                'features' => [
                    'Bahan fleece tebal dan hangat',
                    'Desain trendy dan nyaman',
                    'Kantong besar multifungsi',
                    'Tali serut untuk adjustability'
                ]
            ],
            [
                'name' => 'Kacamata Hitam',
                'description' => 'Kacamata hitam berkualitas dengan perlindungan UV maksimal. Desain yang stylish dan nyaman dipakai dalam berbagai aktivitas.',
                'price' => 95000,
                'stock' => 80,
                'weight' => 50,
                'image' => 'products/kacamata-hitam.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan Frame' => 'Plastik Kualitas Tinggi',
                    'Bahan Lensa' => 'Polarized',
                    'Perlindungan UV' => '100%',
                    'Model' => 'Aviator',
                    'Negara Asal' => 'China'
                ],
                'material' => 'Plastik + Lensa Polarized',
                'size' => 'Standar Dewasa',
                'color' => 'Hitam, Gold, Silver',
                'brand' => 'SunVision',
                'origin' => 'China',
                'warranty' => 'Garansi produk 30 hari',
                'min_order' => 1,
                'ready_stock' => 80,
                'features' => [
                    'Perlindungan UV 100%',
                    'Lensa polarized anti silau',
                    'Desain modis dan trendy',
                    'Ringan dan nyaman dipakai'
                ]
            ],
            [
                'name' => 'Tas Ransel',
                'description' => 'Tas ransel multifungsi untuk kegiatan sehari-hari. Kompartemen luas dengan banyak kantong untuk organisasi maksimal.',
                'price' => 150000,
                'stock' => 35,
                'weight' => 600,
                'image' => 'products/tas-ransel.jpg',
                'status' => 'active',
                'specifications' => [
                    'Bahan' => 'Polycarbonate + Nylon',
                    'Dimensi' => '30 x 20 x 45 cm',
                    'Kapasitas' => '20 Liter',
                    'Kompartemen' => 'Utama, laptop, kecil',
                    'Tali' => 'Adjustable',
                    'Negara Asal' => 'Indonesia'
                ],
                'material' => 'Polycarbonate + Nylon',
                'size' => '30 x 20 x 45 cm',
                'color' => 'Hitam, Biru, Abu-abu',
                'brand' => 'BackPackPro',
                'origin' => 'Indonesia',
                'warranty' => 'Garansi produk 6 bulan',
                'min_order' => 1,
                'ready_stock' => 35,
                'features' => [
                    'Bahan tahan air',
                    'Banyak kompartemen',
                    'Tali bahu ergonomis',
                    'Kapasitas besar untuk kebutuhan harian'
                ]
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
