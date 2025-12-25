<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SellerAddressDescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar kecamatan, desa, dan dusun asli di Banyuwangi
        $kecamatans = [
            'Gambiran', 'Srono', 'Genteng', 'Sempu', 'Banyuwangi', 'Bangorejo', 'Purwoharjo',
            'Tegaldlimo', 'Muncar', 'Cluring', 'Glenmore', 'Kalibaru', 'Singojuruh', 'Rogojampi',
            'Kabat', 'Glagah', 'Wongsorejo', 'Songgon', 'Sumberbaru', 'Tegalsari', 'Licin'
        ];

        $desas = [
            'Gambiran', 'Srono', 'Genteng', 'Sempu', 'Benculuk', 'Bangorejo', 'Purwoharjo',
            'Tegaldlimo', 'Muncar', 'Cluring', 'Glenmore', 'Kalibaru', 'Singojuruh', 'Grajagan',
            'Kabat', 'Glagah', 'Wongsorejo', 'Songgon', 'Sumberbaru', 'Tegalsari', 'Licin',
            'Kedungrejo', 'Kedungasri', 'Kedungwungu', 'Kedungpring', 'Kedunggepok'
        ];

        $dusuns = [
            'Karanganyar', 'Karangharjo', 'Karangrejo', 'Karangmulyo', 'Karangsono',
            'Panderejo', 'Pandean', 'Pandansari', 'Pandanwangi', 'Pandantoyo',
            'Sumberanyar', 'Sumberarum', 'Sumberberas', 'Sumberbulu', 'Sumberdodol',
            'Sumbergondo', 'Sumberjati', 'Sumberjaya', 'Sumberkerto', 'Sumbermujur',
            'Sumberrejo', 'Sumbersewu', 'Sumberwringin', 'Sumberwono', 'Sumberwaru'
        ];

        // Daftar nomor HP dan bank
        $banks = [
            'Bank BRI', 'Bank BNI', 'Bank Mandiri', 'Bank BCA', 'Bank BTN', 'Bank CIMB',
            'Bank Danamon', 'Bank Permata', 'Bank Mega', 'Bank Panin', 'Bank OCBC NISP',
            'Bank UOB', 'Bank Commonwealth', 'Bank Sinarmas', 'Bank Maybank'
        ];

        $sellers = \App\Models\Seller::with('user')->get();

        foreach ($sellers as $index => $seller) {
            if ($seller->user) {
                // Ambil nama kecamatan, desa, dan dusun dari daftar asli
                $kecamatan = $kecamatans[$index % count($kecamatans)];
                $desa = $desas[$index % count($desas)];
                $dusun = $dusuns[$index % count($dusuns)];

                // Buat data alamat dalam urutan: Kabupaten Banyuwangi, Kecamatan, Desa, Dusun, Nama Jalan
                $jalan = 'Jl. ' . $seller->store_name . ' No. ' . rand(1, 100);

                // Gabungkan semua bagian alamat
                $fullAddress = "Kecamatan {$kecamatan}, Desa {$desa}, Dusun {$dusun}, {$jalan}";

                // Buat nomor HP acak
                $phone = '08' . rand(1000000000, 9999999999);

                // Update data user dengan alamat lengkap (hanya kolom yang sudah ada di database)
                $seller->user->update([
                    'address' => 'Kabupaten Banyuwangi, ' . $fullAddress,
                    'full_address' => 'Kabupaten Banyuwangi, ' . $fullAddress,
                    'district' => $seller->user->district ?: $kecamatan,
                    'ward' => $seller->user->ward ?: $desa,
                    'city' => $seller->user->city ?: 'Banyuwangi',
                    'phone' => $seller->user->phone ?: $phone
                ]);

                // Update deskripsi toko jika kosong (gunakan shop_description di tabel user)
                if (empty($seller->user->shop_description)) {
                    $descriptions = [
                        "Toko {$seller->store_name} menyediakan berbagai produk berkualitas untuk memenuhi kebutuhan Anda.",
                        "Selamat datang di Toko {$seller->store_name}, tempat belanja terpercaya Anda.",
                        "Toko {$seller->store_name} - Menyediakan produk-produk terbaik dengan harga bersaing.",
                        "Kami dari Toko {$seller->store_name} siap melayani Anda dengan sepenuh hati.",
                        "Toko {$seller->store_name} - Pilihan tepat untuk kebutuhan Anda."
                    ];

                    $seller->user->update([
                        'shop_description' => $descriptions[array_rand($descriptions)]
                    ]);
                }

                // Update informasi bank untuk penjual
                $seller->update([
                    'bank_name' => $seller->bank_name ?: $banks[$index % count($banks)],
                    'bank_account_number' => $seller->bank_account_number ?: '12345' . rand(1000, 9999),
                    'account_holder_name' => $seller->account_holder_name ?: $seller->owner_name
                ]);

                echo "Berhasil mengisi data untuk toko: {$seller->store_name}\n";
            }
        }
    }
}
