<?php

namespace Database\Seeders;

use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil admin user untuk membuat log sample
        $admin = User::where('role_id', 1)->first(); // Asumsikan role_id 1 adalah admin

        if ($admin) {
            AdminActivityLog::create([
                'user_id' => $admin->id,
                'activity' => 'Menyetujui penarikan dana',
                'description' => 'Admin menyetujui penarikan dana untuk Toko Aneka Roti',
                'status' => 'success',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ]);

            AdminActivityLog::create([
                'user_id' => $admin->id,
                'activity' => 'Menangguhkan akun pembeli',
                'description' => 'Admin menangguhkan akun pembeli atas nama Jono Joni',
                'status' => 'success',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ]);

            AdminActivityLog::create([
                'user_id' => $admin->id,
                'activity' => 'Mengubah pengaturan komisi',
                'description' => 'Admin mengubah pengaturan komisi platform',
                'status' => 'success',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ]);

            AdminActivityLog::create([
                'user_id' => $admin->id,
                'activity' => 'Menghapus produk',
                'description' => 'Admin menghapus produk yang melanggar ketentuan',
                'status' => 'success',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ]);

            AdminActivityLog::create([
                'user_id' => $admin->id,
                'activity' => 'Mengaktifkan kembali produk',
                'description' => 'Admin mengaktifkan kembali produk yang sebelumnya ditangguhkan',
                'status' => 'success',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ]);
        }
    }
}
