<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk membuat tabel pengguna dan tabel terkait
 *
 * Migration ini membuat tabel 'users' yang menyimpan informasi pengguna
 * dalam sistem e-commerce AKRAB, serta tabel tambahan untuk manajemen
 * sesi dan reset password.
 */
return new class extends Migration
{
    /**
     * Menjalankan migrasi - membuat tabel pengguna dan tabel terkait
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();  // ID unik pengguna (auto-increment)
            $table->string('name');  // Nama lengkap pengguna
            $table->string('email')->unique();  // Alamat email pengguna (unik)
            $table->timestamp('email_verified_at')->nullable();  // Waktu email diverifikasi (opsional)
            $table->string('password');  // Kata sandi pengguna (di-hash)
            $table->rememberToken();  // Token untuk fitur "remember me"
            $table->timestamps();  // Timestamp created_at dan updated_at
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();  // Email yang meminta reset password (primary key)
            $table->string('token');  // Token untuk reset password
            $table->timestamp('created_at')->nullable();  // Waktu token dibuat (opsional)
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();  // ID sesi (primary key)
            $table->foreignId('user_id')->nullable()->index();  // ID pengguna yang terkait dengan sesi (opsional, dengan indeks)
            $table->string('ip_address', 45)->nullable();  // Alamat IP pengguna (opsional)
            $table->text('user_agent')->nullable();  // User agent browser (opsional)
            $table->longText('payload');  // Data sesi (dalam bentuk terenkripsi)
            $table->integer('last_activity')->index();  // Waktu aktivitas terakhir (dengan indeks)
        });
    }

    /**
     * Mengembalikan migrasi - menghapus tabel pengguna dan tabel terkait
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
