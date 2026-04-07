<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            // Cek dulu, kalau kolom BELUM ADA, baru dibikin.
            // Kalau udah ada (dari hasil import SQL), ya lewatin aja.
            if (!Schema::hasColumn('sellers', 'bank_name')) {
                $table->string('bank_name')->nullable();
            }
            if (!Schema::hasColumn('sellers', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable();
            }
            if (!Schema::hasColumn('sellers', 'account_holder_name')) {
                $table->string('account_holder_name')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_account_number', 'account_holder_name']);
        });
    }
};
