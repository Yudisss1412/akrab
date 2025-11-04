<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subcategory;

class SubcategorySeeder extends Seeder
{
    public function run()
    {
        // Hapus semua subkategori lama
        \App\Models\Subcategory::query()->delete();

        $categories = [
            ['name' => 'Kuliner', 'subcategories' => [
                'Makanan Berat', 'Camilan', 'Minuman', 'Bumbu & Bahan Masak', 
                'Kue & Kering', 'Makanan Ringan', 'Produk Olahan Susu'
            ]],
            ['name' => 'Fashion', 'subcategories' => [
                'Pakaian Pria', 'Pakaian Wanita', 'Pakaian Anak', 'Aksesoris', 
                'Tas', 'Sepatu', 'Perhiasan'
            ]],
            ['name' => 'Kerajinan Tangan', 'subcategories' => [
                'Kerajinan Logam', 'Kerajinan Kayu', 'Kerajinan Kertas', 
                'Kerajinan Kain', 'Kerajinan Tanah Liat', 'Souvenir & Hadiah', 
                'Alat Tulis Kerajinan'
            ]],
            ['name' => 'Berkebun', 'subcategories' => [
                'Tanaman Hias', 'Tanaman Buah', 'Tanaman Sayur', 'Tanaman Obat', 
                'Pupuk & Nutrisi Tanaman', 'Peralatan Berkebun', 'Pot & Media Tanam'
            ]],
            ['name' => 'Kesehatan', 'subcategories' => [
                'Vitamin & Suplemen', 'Obat Herbal', 'Alat Kesehatan', 
                'Produk Perawatan Diri', 'Produk Terapi', 'Produk Diet & Nutrisi', 
                'Alat Bantu Kesehatan'
            ]],
            ['name' => 'Mainan', 'subcategories' => [
                'Mainan Edukatif', 'Mainan Bayi', 'Mainan Anak', 'Mainan Outdoor', 
                'Boneka & Action Figure', 'Permainan Tradisional', 'Puzzle & Permainan Meja'
            ]],
            ['name' => 'Hampers', 'subcategories' => [
                'Hampers Makanan', 'Hampers Minuman', 'Hampers Kecantikan', 
                'Hampers Fashion', 'Hampers Bayi', 'Hampers Kesehatan', 
                'Hampers Buah & Sayur', 'Hampers Hari Raya'
            ]]
        ];

        foreach ($categories as $catData) {
            $category = \App\Models\Category::where('name', $catData['name'])->first();
            if ($category) {
                foreach ($catData['subcategories'] as $subName) {
                    Subcategory::create([
                        'name' => $subName,
                        'category_id' => $category->id
                    ]);
                }
            }
        }
    }
}