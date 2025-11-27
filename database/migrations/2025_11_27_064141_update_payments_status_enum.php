<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Dapatkan enum values saat ini dari tabel payments
        $result = DB::select("
            SELECT COLUMN_TYPE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME = 'payments'
            AND COLUMN_NAME = 'payment_status'
            AND TABLE_SCHEMA = DATABASE()
        ");

        if ($result) {
            $enumStr = $result[0]->COLUMN_TYPE;
            preg_match_all("/'([^']+)'/", $enumStr, $matches);
            $enums = $matches[1];

            // Tambahkan nilai baru jika belum ada
            if (!in_array('pending_verification', $enums)) {
                $enums[] = 'pending_verification';
            }

            // Buat string enum baru
            $newEnums = "'" . implode("','", $enums) . "'";

            // Update field dengan enum baru
            DB::statement("ALTER TABLE payments MODIFY payment_status ENUM({$newEnums})");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum default yang aman
        DB::statement("ALTER TABLE payments MODIFY payment_status ENUM('pending', 'success', 'failed', 'cancelled', 'expired', 'settlement')");
    }
};