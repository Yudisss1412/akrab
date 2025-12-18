<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ambil produk-produk yang ada untuk membuat ulasan
        $productIds = DB::table('products')->pluck('id')->toArray();

        // Ambil user yang akan memberikan ulasan
        $userIds = DB::table('users')->pluck('id')->toArray();

        // Ambil order yang ada untuk relasi dengan ulasan
        $orderIds = DB::table('orders')->pluck('id')->toArray();

        if(empty($productIds) || empty($userIds) || empty($orderIds)) {
            $this->command->info('Tidak cukup produk, user, atau order untuk membuat ulasan. Silakan buat data terlebih dahulu.');
            return;
        }

        $reviews = [];

        // Buat 15 ulasan dummy (5 bagus, 5 netral, 5 jelek)
        for($i = 0; $i < 15; $i++) {
            $rating = 0; // Default
            $comment = '';

            // Tentukan rating dan komentar berdasarkan kategori
            if($i < 5) {
                // Ulasan bagus (rating 4-5)
                $rating = rand(4, 5);
                $commentsBagus = [
                    'Produknya sangat bagus dan berkualitas tinggi. Saya sangat puas dengan pembelian ini.',
                    'Kualitasnya luar biasa! Pasti akan beli lagi di masa depan.',
                    'Sangat merekomendasikan produk ini. Pengemasan juga rapi dan pengiriman cepat.',
                    'Produk yang saya terima melebihi ekspektasi. Warna dan bentuknya sesuai gambar.',
                    'Saya sangat terkesan dengan kualitas produk ini. Harganya pun terjangkau.'
                ];
                $comment = $commentsBagus[array_rand($commentsBagus)];
            } elseif($i < 10) {
                // Ulasan netral (rating 3)
                $rating = 3;
                $commentsNetral = [
                    'Produknya biasa saja, sesuai dengan harga. Tidak terlalu buruk juga tidak terlalu bagus.',
                    'Lumayan lah untuk harga segini. Ada beberapa hal kecil yang bisa diperbaiki.',
                    'Produknya oke, tapi sedikit kecewa dengan kualitasnya. Masih bisa diterima.',
                    'Sesuai ekspektasi, tidak lebih dan tidak kurang. Pengiriman juga standar.',
                    'Biasa aja, enggak ada yang spesial, tapi tidak jelek juga.'
                ];
                $comment = $commentsNetral[array_rand($commentsNetral)];
            } else {
                // Ulasan jelek (rating 1-2)
                $rating = rand(1, 2);
                $commentsJelek = [
                    'Produknya tidak sesuai dengan deskripsi. Tidak sesuai harapan saya.',
                    'Kualitasnya buruk sekali, bahannya terasa murahan dan cepat rusak.',
                    'Saya kecewa dengan pembelian ini. Barangnya rusak saat sampai di tangan saya.',
                    'Harga mahal tapi kualitasnya tidak sebanding. Tidak akan beli lagi.',
                    'Produknya cacat dan tidak sesuai gambar. Saya minta pengembalian uang.'
                ];
                $comment = $commentsJelek[array_rand($commentsJelek)];
            }

            $reviews[] = [
                'user_id' => $userIds[array_rand($userIds)],
                'product_id' => $productIds[array_rand($productIds)],
                'order_id' => $orderIds[array_rand($orderIds)],
                'rating' => $rating,
                'review_text' => $comment,
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 24))->subMinutes(rand(0, 60)),
                'updated_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 24))->subMinutes(rand(0, 60)),
                'reply' => null, // Belum ada balasan
                'status' => 'approved' // Status default untuk ulasan yang dibuat
            ];
        }

        // Insert semua ulasan ke database
        DB::table('reviews')->insert($reviews);

        $this->command->info('Berhasil membuat 15 ulasan dummy: 5 bagus (rating 4-5), 5 netral (rating 3), 5 jelek (rating 1-2)');
    }
}