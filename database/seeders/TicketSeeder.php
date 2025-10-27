<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\User; // Asumsikan user sudah ada di database

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada user terlebih dahulu
        $users = User::all();
        if ($users->count() === 0) {
            $this->command->info('Tidak ada user ditemukan. Silakan buat user terlebih dahulu sebelum menjalankan seeder ini.');
            return;
        }

        // Ambil user pertama sebagai pembuat tiket (jika tidak ada user khusus untuk contoh)
        $user = $users->first();

        // Membuat beberapa tiket bantuan asli
        Ticket::create([
            'user_id' => $user->id,
            'subject' => 'Kesulitan dalam proses checkout',
            'message' => 'Saya mengalami kesulitan saat melakukan checkout. Tombol pembayaran tidak merespons setelah saya klik.',
            'category' => 'technical',
            'priority' => 'high',
            'status' => 'open',
        ]);

        Ticket::create([
            'user_id' => $user->id,
            'subject' => 'Pertanyaan tentang metode pembayaran',
            'message' => 'Apakah platform mendukung pembayaran melalui transfer bank? Saya tidak menemukan opsi tersebut di halaman pembayaran.',
            'category' => 'billing',
            'priority' => 'medium',
            'status' => 'in_progress',
        ]);

        Ticket::create([
            'user_id' => $user->id,
            'subject' => 'Akun saya terblokir',
            'message' => 'Akun saya tiba-tiba logout dan tidak bisa login kembali. Mohon bantuan untuk mengakses akun saya kembali.',
            'category' => 'account',
            'priority' => 'urgent',
            'status' => 'resolved',
            'resolution_notes' => 'Issue akun terblokir diselesaikan setelah reset password',
            'resolved_at' => now()->subDays(2),
        ]);

        Ticket::create([
            'user_id' => $user->id,
            'subject' => 'Kualitas produk tidak sesuai deskripsi',
            'message' => 'Produk yang saya terima tidak sesuai dengan deskripsi di halaman produk. Warna berbeda dari yang ditampilkan.',
            'category' => 'product',
            'priority' => 'high',
            'status' => 'in_progress',
        ]);

        $this->command->info('Berhasil menambahkan 4 tiket bantuan asli ke database.');
    }
}
