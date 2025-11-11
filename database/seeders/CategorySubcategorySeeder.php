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
        // Hapus semua subkategori dan kategori yang lama
        Subcategory::truncate();
        // Kita akan membuat ulang kategori dengan struktur yang diinginkan
        Category::whereNotIn('name', ['admin', 'seller', 'buyer'])->delete(); // Jaga role tidak terhapus

        // Definisikan kategori dan subkategori yang sesuai permintaan
        $categoriesWithSubcategories = [
            [
                'category' => 'Kuliner',
                'subcategories' => [
                    'Makanan Berat',
                    'Camilan', 
                    'Minuman',
                    'Bumbu & Bahan Masak',
                    'Kue & Kering',
                    'Makanan Ringan',
                    'Produk Olahan Susu'
                ]
            ],
            [
                'category' => 'Fashion',
                'subcategories' => [
                    'Pakaian Pria',
                    'Pakaian Wanita', 
                    'Pakaian Anak',
                    'Aksesoris',
                    'Tas',
                    'Sepatu',
                    'Perhiasan'
                ]
            ],
            [
                'category' => 'Kerajinan Tangan',
                'subcategories' => [
                    'Kerajinan Logam',
                    'Kerajinan Kayu',
                    'Kerajinan Kertas', 
                    'Kerajinan Kain',
                    'Kerajinan Tanah Liat',
                    'Souvenir & Hadiah',
                    'Alat Tulis Kerajinan'
                ]
            ],
            [
                'category' => 'Produk Berkebun',
                'subcategories' => [
                    'Tanaman Hias',
                    'Tanaman Buah',
                    'Tanaman Sayur',
                    'Tanaman Obat', 
                    'Pupuk & Nutrisi Tanaman',
                    'Peralatan Berkebun',
                    'Pot & Media Tanam'
                ]
            ],
            [
                'category' => 'Produk Kesehatan',
                'subcategories' => [
                    'Vitamin & Suplemen',
                    'Obat Herbal',
                    'Alat Kesehatan',
                    'Produk Perawatan Diri',
                    'Produk Terapi',
                    'Produk Diet & Nutrisi', 
                    'Alat Bantu Kesehatan'
                ]
            ],
            [
                'category' => 'Mainan',
                'subcategories' => [
                    'Mainan Edukatif',
                    'Mainan Bayi',
                    'Mainan Anak',
                    'Mainan Outdoor',
                    'Boneka & Action Figure',
                    'Permainan Tradisional',
                    'Puzzle & Permainan Meja'
                ]
            ],
            [
                'category' => 'Hampers',
                'subcategories' => [
                    'Hampers Makanan',
                    'Hampers Minuman',
                    'Hampers Kecantikan',
                    'Hampers Fashion',
                    'Hampers Bayi',
                    'Hampers Kesehatan',
                    'Hampers Buah & Sayur',
                    'Hampers Hari Raya'
                ]
            ]
        ];

        foreach ($categoriesWithSubcategories as $data) {
            // Buat kategori
            $category = Category::create([
                'name' => $data['category'],
                'slug' => strtolower(str_replace(' ', '-', $data['category'])),
                'description' => 'Kategori ' . $data['category'] . ' dengan berbagai produk unggulan',
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