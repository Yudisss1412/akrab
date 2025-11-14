<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Hanya jalankan seeder kategori dan subkategori karena sekarang mengelola semuanya
        $this->call([
            RoleSeeder::class,
            CategorySubcategorySeeder::class, // Ini sekarang mengatur kategori dan subkategori
            UserSeeder::class,
            ProductSeeder::class,
            ProductVariantSeeder::class,
            OrderSeeder::class,
            SellerOrderSeeder::class,
            ReviewsTableSeeder::class,
        ]);
    }
}
