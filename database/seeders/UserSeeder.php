<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Seller;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get role IDs
        $adminRole = Role::where('name', 'admin')->first();
        $sellerRole = Role::where('name', 'seller')->first();
        $buyerRole = Role::where('name', 'buyer')->first();

        // Create Admin Users
        $adminUsers = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@ecommerce.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $adminRole->id,
            ],
            [
                'name' => 'System Admin',
                'email' => 'system@admin.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $adminRole->id,
            ]
        ];

        foreach ($adminUsers as $adminUser) {
            User::create($adminUser);
        }

        // Create Seller Users and link them to the Sellers table
        $sellerUsers = [
            [
                'name' => 'Ahmad Santoso',
                'email' => 'ahmad@tokoanekeoti.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $sellerRole->id,
                'seller_data' => [
                    'store_name' => 'Toko Aneka Roti',
                    'owner_name' => 'Ahmad Santoso',
                    'email' => 'ahmad@tokoanekeoti.com',
                    'status' => 'aktif',
                    'join_date' => now()->subDays(30),
                    'active_products_count' => 15,
                    'total_sales' => 5000000.00,
                    'rating' => 4.5,
                ]
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@warungprima.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $sellerRole->id,
                'seller_data' => [
                    'store_name' => 'Warung Makan Prima',
                    'owner_name' => 'Siti Nurhaliza',
                    'email' => 'siti@warungprima.com',
                    'status' => 'aktif',
                    'join_date' => now()->subDays(45),
                    'active_products_count' => 22,
                    'total_sales' => 8500000.00,
                    'rating' => 4.7,
                ]
            ],
            [
                'name' => 'Budi Prasetyo',
                'email' => 'budi@fashionmodis.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $sellerRole->id,
                'seller_data' => [
                    'store_name' => 'Toko Fashion Modis',
                    'owner_name' => 'Budi Prasetyo',
                    'email' => 'budi@fashionmodis.com',
                    'status' => 'ditangguhkan',
                    'join_date' => now()->subDays(15),
                    'active_products_count' => 8,
                    'total_sales' => 2100000.00,
                    'rating' => 3.2,
                ]
            ],
            [
                'name' => 'Dewi Kartika',
                'email' => 'dewi@jayaabadi.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $sellerRole->id,
                'seller_data' => [
                    'store_name' => 'Minimarket Jaya Abadi',
                    'owner_name' => 'Dewi Kartika',
                    'email' => 'dewi@jayaabadi.com',
                    'status' => 'menunggu_verifikasi',
                    'join_date' => now()->subDays(5),
                    'active_products_count' => 0,
                    'total_sales' => 0.00,
                    'rating' => 0.0,
                ]
            ],
            [
                'name' => 'Rudi Firmansyah',
                'email' => 'rudi@elektromurah.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $sellerRole->id,
                'seller_data' => [
                    'store_name' => 'Toko Elektronik Murah',
                    'owner_name' => 'Rudi Firmansyah',
                    'email' => 'rudi@elektromurah.com',
                    'status' => 'baru',
                    'join_date' => now()->subDays(2),
                    'active_products_count' => 5,
                    'total_sales' => 1200000.00,
                    'rating' => 4.0,
                ]
            ]
        ];

        foreach ($sellerUsers as $sellerUser) {
            $userData = $sellerUser;
            unset($userData['seller_data']);
            
            $user = User::create($userData);
            
            // Create corresponding seller record
            $sellerData = $sellerUser['seller_data'];
            $sellerData['user_id'] = $user->id;
            Seller::create($sellerData);
        }

        // Create Buyer Users (regular usersrudi@elektromurah.com without seller records)
        $buyerUsers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $buyerRole->id,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $buyerRole->id,
            ],
            [
                'name' => 'Robert Johnson',
                'email' => 'robert@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $buyerRole->id,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $buyerRole->id,
            ],
            [
                'name' => 'Michael Wilson',
                'email' => 'michael@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $buyerRole->id,
            ]
        ];

        foreach ($buyerUsers as $buyerUser) {
            User::create($buyerUser);
        }
    }
}
