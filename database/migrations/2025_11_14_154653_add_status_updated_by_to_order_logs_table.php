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
        Schema::table('order_logs', function (Blueprint $table) {
            $table->string('status')->nullable();  // Menyimpan status pesanan saat log dibuat
            $table->string('updated_by')->nullable();  // Menyimpan siapa yang memperbarui status (seller, customer, system)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_logs', function (Blueprint $table) {
            $table->dropColumn(['status', 'updated_by']);
        });
    }
};
