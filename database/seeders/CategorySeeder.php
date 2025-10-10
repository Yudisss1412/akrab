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
        // Membuat kategori dummy
        Category::create([
            'name' => 'Fashion',
            'slug' => 'fashion',
            'description' => 'Berbagai produk fashion terbaru',
            'status' => 'active',
        ]);

        Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
            'description' => 'Berbagai produk elektronik',
            'status' => 'active',
        ]);

        Category::create([
            'name' => 'Aksesoris',
            'slug' => 'aksesoris',
            'description' => 'Aneka aksesoris pelengkap',
            'status' => 'active',
        ]);

        Category::create([
            'name' => 'Kulit',
            'slug' => 'kulit',
            'description' => 'Produk-produk berbahan kulit asli',
            'status' => 'active',
        ]);
    }
}
