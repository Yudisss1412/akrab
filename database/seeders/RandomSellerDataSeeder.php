<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RandomSellerDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dapatkan role seller
        $sellerRole = Role::where('name', 'seller')->first();
        
        if (!$sellerRole) {
            $this->command->error('Role "seller" tidak ditemukan. Silakan buat role seller terlebih dahulu.');
            return;
        }

        // Ambil semua pengguna dengan role seller
        $sellers = User::where('role_id', $sellerRole->id)->get();

        // Data acak untuk diisi
        $phoneNumbers = [
            '081234567890',
            '085712345678',
            '082145678901',
            '081356789012',
            '087812345678',
            '089534567890',
            '081298765432',
            '085612345678',
            '082245678901',
            '081387654321'
        ];

        $shopDescriptions = [
            'Toko online terpercaya menyediakan berbagai kebutuhan rumah tangga dengan harga terjangkau.',
            'Menyediakan produk-produk berkualitas dengan harga bersaing dan layanan terbaik.',
            'Toko yang menjual berbagai macam produk original dengan garansi resmi.',
            'Berbagai produk elektronik dan aksesori tersedia di toko kami dengan harga terbaik.',
            'Menyediakan fashion terbaru dengan kualitas premium dan harga bersaing.',
            'Toko online yang menjual produk-produk original dari brand ternama.',
            'Berbagai produk kecantikan dan perawatan kulit tersedia di sini.',
            'Toko yang fokus pada produk-produk kebutuhan sehari-hari dengan kualitas terbaik.',
            'Menyediakan perlengkapan bayi dan anak dengan harga terjangkau.',
            'Toko online terpercaya dengan pengiriman cepat dan aman.'
        ];

        $bankNames = [
            'Bank Central Asia (BCA)',
            'Bank Mandiri',
            'Bank Rakyat Indonesia (BRI)',
            'Bank Negara Indonesia (BNI)',
            'Bank Permata',
            'Bank Danamon',
            'Bank CIMB Niaga',
            'Bank Panin',
            'Bank OCBC NISP',
            'Bank UOB'
        ];

        $bankAccountNumbers = [
            '1234567890',
            '0987654321',
            '1122334455',
            '5566778899',
            '9988776655',
            '4433221100',
            '2345678901',
            '3456789012',
            '4567890123',
            '5678901234'
        ];

        $bankAccountNames = [
            'PT. Toko Berkah Jaya',
            'CV. Sentosa Abadi',
            'UD. Makmur Sejahtera',
            'Toko Online Sukses',
            'CV. Jaya Sentosa',
            'PT. Berkah Abadi',
            'UD. Sumber Rejeki',
            'Toko Modern Jaya',
            'CV. Cemerlang Mandiri',
            'PT. Sejahtera Sentosa'
        ];

        $counter = 0;
        foreach ($sellers as $seller) {
            // Ambil data acak
            $randomPhone = $phoneNumbers[array_rand($phoneNumbers)];
            $randomDescription = $shopDescriptions[array_rand($shopDescriptions)];
            $randomBankName = $bankNames[array_rand($bankNames)];
            $randomBankAccountNumber = $bankAccountNumbers[array_rand($bankAccountNumbers)];
            $randomBankAccountName = $bankAccountNames[array_rand($bankAccountNames)];

            // Update data penjual
            $seller->update([
                'phone' => $randomPhone,
                'shop_description' => $randomDescription,
                'bank_name' => $randomBankName,
                'bank_account_number' => $randomBankAccountNumber,
                'bank_account_name' => $randomBankAccountName,
            ]);

            $counter++;
            $this->command->info("Data toko untuk penjual {$seller->name} telah diperbarui.");
        }

        $this->command->info("Berhasil memperbarui data toko untuk {$counter} penjual.");
    }
}