<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek apakah tabel carts sudah ada
        if (Schema::hasTable('carts')) {
            // Jika tabel sudah ada, hanya tambahkan kolom jika belum ada
            if (!Schema::hasColumn('carts', 'product_variant_id')) {
                Schema::table('carts', function (Blueprint $table) {
                    $table->unsignedBigInteger('product_variant_id')->nullable()->after('product_id');
                    $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
                });
            }

            // Cek dan atur unique constraint
            $this->updateUniqueConstraint();
        } else {
            // Jika tabel tidak ada, buat tabel carts dengan struktur lengkap
            Schema::create('carts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->unsignedBigInteger('product_variant_id')->nullable();
                $table->integer('quantity')->default(1);
                $table->timestamps();
                
                $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
                $table->unique(['user_id', 'product_id', 'product_variant_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('carts')) {
            Schema::table('carts', function (Blueprint $table) {
                // Hapus foreign key terlebih dahulu
                try {
                    $table->dropForeign(['product_variant_id']);
                } catch (\Exception $e) {
                    // Jika foreign key tidak ada, lanjutkan
                }
                
                // Hapus unique constraint
                try {
                    $table->dropUnique(['user_id', 'product_id', 'product_variant_id']);
                } catch (\Exception $e) {
                    // Jika constraint tidak ada, lanjutkan
                }
                
                // Tambahkan kembali constraint lama jika tidak ada
                try {
                    $table->unique(['user_id', 'product_id']);
                } catch (\Exception $e) {
                    // Jika constraint sudah ada, abaikan
                }
                
                // Hapus kolom
                $table->dropColumn('product_variant_id');
            });
        }
    }
    
    /**
     * Fungsi bantu untuk mengatur unique constraint dengan aman
     */
    private function updateUniqueConstraint(): void 
    {
        // Dapatkan struktur tabel untuk mengecek constraint yang ada
        $connection = DB::getPDO();
        
        // Cek constraint apa yang ada di tabel
        $existingConstraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'carts' 
            AND CONSTRAINT_TYPE = 'UNIQUE'
        ", [DB::getDatabaseName()]);
        
        // Hapus constraint lama jika ada
        foreach ($existingConstraints as $constraint) {
            $constraintName = $constraint->CONSTRAINT_NAME;
            if ($constraintName === 'carts_user_id_product_id_unique') {
                try {
                    Schema::table('carts', function (Blueprint $table) {
                        $table->dropUnique(['user_id', 'product_id']);
                    });
                } catch (\Exception $e) {
                    // Jika gagal dihapus, lanjutkan
                }
            } elseif ($constraintName === 'carts_user_id_product_id_product_variant_id_unique') {
                // Hapus constraint yang mungkin sudah ada sebelumnya
                try {
                    Schema::table('carts', function (Blueprint $table) {
                        $table->dropUnique(['user_id', 'product_id', 'product_variant_id']);
                    });
                } catch (\Exception $e) {
                    // Jika gagal dihapus, lanjutkan
                }
            }
        }
        
        // Tambahkan constraint baru
        try {
            Schema::table('carts', function (Blueprint $table) {
                $table->unique(['user_id', 'product_id', 'product_variant_id']);
            });
        } catch (\Exception $e) {
            // Jika constraint sudah ada, abaikan
        }
    }
};