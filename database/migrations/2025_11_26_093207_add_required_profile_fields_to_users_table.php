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
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan field-field yang dihapus oleh migrasi sebelumnya
            if (!Schema::hasColumn('users', 'province')) {
                $table->string('province')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('province');
            }

            if (!Schema::hasColumn('users', 'district')) {
                $table->string('district')->nullable()->after('city');
            }

            if (!Schema::hasColumn('users', 'ward')) {
                $table->string('ward')->nullable()->after('district');
            }

            if (!Schema::hasColumn('users', 'full_address')) {
                $table->text('full_address')->nullable()->after('ward');
            }

            // Tambahkan field bio yang digunakan di controller tetapi tidak ada di database
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('full_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['province', 'city', 'district', 'ward', 'full_address', 'bio']);
        });
    }
};
