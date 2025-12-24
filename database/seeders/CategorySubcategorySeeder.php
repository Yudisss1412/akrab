<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;

class CategorySubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua subkategori dan kategori yang lama (kecuali role)
        Subcategory::truncate();
        Category::whereNotIn('name', ['admin', 'seller', 'buyer'])->delete(); // Jaga role tidak terhapus

        // Definisikan kategori dan subkategori yang lebih spesifik
        $categoriesWithSubcategories = [
            [
                'category' => [
                    'name' => 'Makanan & Minuman',
                    'slug' => 'makanan-minuman',
                    'description' => 'Temukan berbagai produk makanan dan minuman menarik dari UMKM lokal',
                ],
                'subcategories' => [
                    'Makanan Ringan',
                    'Makanan Berat',
                    'Minuman',
                    'Oleh-oleh Khas',
                    'Bahan Masakan',
                    'Bumbu Dapur',
                    'Camilan Sehat',
                    'Makanan Beku'
                ]
            ],
            [
                'category' => [
                    'name' => 'Pakaian & Aksesori',
                    'slug' => 'pakaian-aksesori',
                    'description' => 'Temukan berbagai produk fashion dan aksesori menarik dari UMKM lokal',
                ],
                'subcategories' => [
                    'Pakaian Pria',
                    'Pakaian Wanita',
                    'Pakaian Anak',
                    'Aksesoris',
                    'Sepatu',
                    'Tas',
                    'Perhiasan',
                    'Jam Tangan'
                ]
            ],
            [
                'category' => [
                    'name' => 'Kerajinan & Hobi',
                    'slug' => 'kerajinan-hobi',
                    'description' => 'Temukan berbagai produk kerajinan tangan unik dari UMKM lokal',
                ],
                'subcategories' => [
                    'Kerajinan Tangan',
                    'Kerajinan Kayu',
                    'Kerajinan Kain',
                    'Kerajinan Logam',
                    'Kerajinan Tanah Liat',
                    'Kerajinan Bambu',
                    'Kerajinan Anyaman',
                    'Alat Musik Tradisional'
                ]
            ],
            [
                'category' => [
                    'name' => 'Rumah & Taman',
                    'slug' => 'rumah-taman',
                    'description' => 'Temukan berbagai produk untuk rumah dan taman dari UMKM lokal',
                ],
                'subcategories' => [
                    'Perabotan Rumah',
                    'Dekorasi',
                    'Alat Dapur',
                    'Perawatan Rumah',
                    'Tanaman Hias',
                    'Peralatan Berkebun',
                    'Pupuk & Nutrisi',
                    'Pot & Wadah Tanaman'
                ]
            ],
            [
                'category' => [
                    'name' => 'Kesehatan & Kecantikan',
                    'slug' => 'kesehatan-kecantikan',
                    'description' => 'Temukan berbagai produk kesehatan dan kecantikan alami dari UMKM lokal',
                ],
                'subcategories' => [
                    'Perawatan Wajah',
                    'Perawatan Tubuh',
                    'Perawatan Rambut',
                    'Suplemen Kesehatan',
                    'Obat Herbal',
                    'Alat Kesehatan',
                    'Minyak Esensial',
                    'Perawatan Gigi & Mulut'
                ]
            ],
            [
                'category' => [
                    'name' => 'Mainan & Edukasi',
                    'slug' => 'mainan-edukasi',
                    'description' => 'Temukan berbagai produk mainan edukatif dari UMKM lokal',
                ],
                'subcategories' => [
                    'Mainan Anak',
                    'Alat Peraga Edukatif',
                    'Puzzle & Permainan Meja',
                    'Boneka & Action Figure',
                    'Mainan Musik',
                    'Mainan Tradisional',
                    'Buku & Alat Tulis',
                    'Alat Peraga Montessori'
                ]
            ],
            [
                'category' => [
                    'name' => 'Hadiah & Souvenir',
                    'slug' => 'hadiah-souvenir',
                    'description' => 'Temukan berbagai produk hadiah dan souvenir menarik dari UMKM lokal',
                ],
                'subcategories' => [
                    'Kado Ulang Tahun',
                    'Kado Pernikahan',
                    'Souvenir Lebaran',
                    'Souvenir Natal',
                    'Souvenir Unik',
                    'Kerajinan Lokal',
                    'Produk Khas Daerah',
                    'Hadiah Kecil'
                ]
            ],
            [
                'category' => [
                    'name' => 'Elektronik & Gadget',
                    'slug' => 'elektronik-gadget',
                    'description' => 'Berbagai produk elektronik dan gadget terbaru',
                ],
                'subcategories' => [
                    'Handphone & Aksesoris',
                    'Laptop & Aksesoris',
                    'Komputer & Perangkat',
                    'Audio & Video',
                    'Kamera & Aksesoris',
                    'Perangkat Jaringan',
                    'Aksesoris Gadget',
                    'Smart Home'
                ]
            ],
            [
                'category' => [
                    'name' => 'Otomotif',
                    'slug' => 'otomotif',
                    'description' => 'Aneka perlengkapan otomotif berkualitas',
                ],
                'subcategories' => [
                    'Spare Part Motor',
                    'Spare Part Mobil',
                    'Aksesoris Motor',
                    'Aksesoris Mobil',
                    'Perawatan Kendaraan',
                    'Oli & Pelumas',
                    'Ban & Velg',
                    'Audio Mobil'
                ]
            ],
            [
                'category' => [
                    'name' => 'Olahraga & Rekreasi',
                    'slug' => 'olahraga-rekreasi',
                    'description' => 'Produk-produk untuk olahraga dan rekreasi',
                ],
                'subcategories' => [
                    'Perlengkapan Olahraga',
                    'Sepatu Olahraga',
                    'Pakaian Olahraga',
                    'Alat Fitness',
                    'Perlengkapan Renang',
                    'Perlengkapan Camping',
                    'Perlengkapan Sepeda',
                    'Mainan Air'
                ]
            ]
        ];

        foreach ($categoriesWithSubcategories as $data) {
            // Buat kategori
            $category = Category::create([
                'name' => $data['category']['name'],
                'slug' => $data['category']['slug'],
                'description' => $data['category']['description'],
                'status' => 'active'
            ]);

            // Buat subkategori untuk kategori ini
            foreach ($data['subcategories'] as $subcategoryName) {
                Subcategory::create([
                    'name' => $subcategoryName,
                    'category_id' => $category->id
                ]);
            }
        }
    }
}