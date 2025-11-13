<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WithdrawalRequest;
use App\Models\Seller;
use Faker\Factory as Faker;

class ProperWithdrawalRequestSeeder extends Seeder
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

        // Membuat beberapa withdrawal request dengan struktur database yang benar
        for ($i = 0; $i < 15; $i++) {
            $seller = $sellers->random();

            // Generate random amount
            $amount = $faker->randomElement([500000, 1000000, 1500000, 2000000, 2500000, 3000000, 5000000, 7500000, 10000000]);
            
            // Generate status
            $status = $faker->randomElement(['pending', 'processing', 'completed', 'rejected']);
            
            // Generate bank account info
            $banks = ['BCA', 'Mandiri', 'BNI', 'BRI', 'BTN', 'CIMB', 'Danamon'];
            $bank = $banks[array_rand($banks)];
            $accountNumber = str_pad($faker->randomNumber(7), 7, '0', STR_PAD_LEFT);
            $bankAccount = $bank . ' - ' . $accountNumber;
            
            // Generate notes if rejected
            $notes = $status === 'rejected' ? $faker->randomElement([
                'Dokumen tidak lengkap', 
                'Rekening tidak valid', 
                'Informasi tidak sesuai', 
                'Verifikasi gagal'
            ]) : null;

            WithdrawalRequest::create([
                'seller_id' => $seller->id,
                'amount' => $amount,
                'status' => $status,
                'request_date' => now()->subDays(rand(0, 30))->subHours(rand(0, 24)),
                'processed_date' => $status === 'completed' ? now()->subDays(rand(0, 10))->subHours(rand(0, 24)) : null,
                'bank_account' => $bankAccount,
                'notes' => $notes,
            ]);
        }

        $this->command->info('Berhasil menambahkan 15 withdrawal request ke database.');
    }
}