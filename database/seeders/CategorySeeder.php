<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat kategori-kategori yang digunakan dalam rute website
        $categories = [
            [
                'name' => 'Kuliner',
                'slug' => 'kuliner',
                'description' => 'Temukan berbagai produk kuliner menarik dari UMKM lokal',
                'status' => 'active',
            ],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'description' => 'Temukan berbagai produk fashion menarik dari UMKM lokal',
                'status' => 'active',
            ],
            [
                'name' => 'Kerajinan Tangan',
                'slug' => 'kerajinan-tangan',
                'description' => 'Temukan berbagai produk kerajinan tangan unik dari UMKM lokal',
                'status' => 'active',
            ],
            [
                'name' => 'Produk Berkebun',
                'slug' => 'produk-berkebun',
                'description' => 'Temukan berbagai produk berkebun alami dari UMKM lokal',
                'status' => 'active',
            ],
            [
                'name' => 'Produk Kesehatan',
                'slug' => 'produk-kesehatan',
                'description' => 'Temukan berbagai produk kesehatan alami dari UMKM lokal',
                'status' => 'active',
            ],
            [
                'name' => 'Mainan',
                'slug' => 'mainan',
                'description' => 'Temukan berbagai produk mainan edukatif dari UMKM lokal',
                'status' => 'active',
            ],
            [
                'name' => 'Hampers',
                'slug' => 'hampers',
                'description' => 'Temukan berbagai produk hampers menarik dari UMKM lokal',
                'status' => 'active',
            ],
            [
                'name' => 'Elektronik',
                'slug' => 'elektronik',
                'description' => 'Berbagai produk elektronik',
                'status' => 'active',
            ],
            [
                'name' => 'Aksesoris',
                'slug' => 'aksesoris',
                'description' => 'Aneka aksesoris pelengkap',
                'status' => 'active',
            ],
            [
                'name' => 'Kulit',
                'slug' => 'kulit',
                'description' => 'Produk-produk berbahan kulit asli',
                'status' => 'active',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']], // kondisi pencarian
                $category // data untuk dibuat jika tidak ditemukan
            );
        }
    }
}
