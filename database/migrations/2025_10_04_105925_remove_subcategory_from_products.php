<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Hapus kolom subcategory dari tabel products jika kolom tersebut ada
        if (Schema::hasColumn('products', 'subcategory')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('subcategory');
            });
        }
    }

    public function down()
    {
        // Kembalikan kolom subcategory jika rollback (hanya jika tidak ada)
        if (!Schema::hasColumn('products', 'subcategory')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('subcategory')->nullable();
            });
        }
    }
};