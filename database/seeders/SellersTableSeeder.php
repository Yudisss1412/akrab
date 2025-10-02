<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seller;

class SellersTableSeeder extends Seeder
{
    public function run()
    {
        $sellers = [
            [
                'store_name' => 'Toko Aneka Roti',
                'owner_name' => 'Ahmad Santoso',
                'email' => 'ahmad@tokoanekeoti.com',
                'status' => 'aktif',
                'join_date' => now()->subDays(30),
                'active_products_count' => 15,
                'total_sales' => 5000000.00,
                'rating' => 4.5,
            ],
            [
                'store_name' => 'Warung Makan Prima',
                'owner_name' => 'Siti Nurhaliza',
                'email' => 'siti@warungprima.com',
                'status' => 'aktif',
                'join_date' => now()->subDays(45),
                'active_products_count' => 22,
                'total_sales' => 8500000.00,
                'rating' => 4.7,
            ],
            [
                'store_name' => 'Toko Fashion Modis',
                'owner_name' => 'Budi Prasetyo',
                'email' => 'budi@fashionmodis.com',
                'status' => 'ditangguhkan',
                'join_date' => now()->subDays(15),
                'active_products_count' => 8,
                'total_sales' => 2100000.00,
                'rating' => 3.2,
            ],
            [
                'store_name' => 'Minimarket Jaya Abadi',
                'owner_name' => 'Dewi Kartika',
                'email' => 'dewi@jayaabadi.com',
                'status' => 'menunggu_verifikasi',
                'join_date' => now()->subDays(5),
                'active_products_count' => 0,
                'total_sales' => 0.00,
                'rating' => 0.0,
            ],
            [
                'store_name' => 'Toko Elektronik Murah',
                'owner_name' => 'Rudi Firmansyah',
                'email' => 'rudi@elektromurah.com',
                'status' => 'baru',
                'join_date' => now()->subDays(2),
                'active_products_count' => 5,
                'total_sales' => 1200000.00,
                'rating' => 4.0,
            ],
        ];

        foreach ($sellers as $seller) {
            Seller::create($seller);
        }
    }
}