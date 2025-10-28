<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WithdrawalRequest;
use App\Models\Seller; // Kita asumsikan penjual sudah ada di database
use Faker\Factory as Faker;

class WithdrawalRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Pastikan ada seller terlebih dahulu
        $sellers = Seller::all();
        if ($sellers->count() === 0) {
            $this->command->info('Tidak ada seller ditemukan. Silakan buat seller terlebih dahulu sebelum menjalankan seeder ini.');
            return;
        }

        // Membuat beberapa withdrawal request
        for ($i = 0; $i < 3; $i++) {
            $seller = $sellers->random();
            
            WithdrawalRequest::create([
                'seller_id' => $seller->id,
                'amount' => $faker->randomElement([500000, 1000000, 1500000, 2000000, 2500000, 3000000, 5000000]),
                'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                'payment_method' => $faker->randomElement(['bank_transfer', 'ewallet']),
                'bank_name' => $faker->randomElement(['BCA', 'Mandiri', 'BNI', 'BRI']),
                'account_number' => $faker->numerify('##########'),
                'account_name' => $faker->name(),
                'ewallet_number' => $faker->randomElement([null, '+6281234567890']),
                'rejection_reason' => $faker->randomElement([null, 'Dokumen tidak lengkap', 'Rekening tidak valid', 'Saldo tidak mencukupi']),
                'notes' => $faker->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $this->command->info('Berhasil menambahkan 3 withdrawal request ke database.');
    }
}
