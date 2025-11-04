<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Hapus kolom subcategory dari tabel products
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('subcategory');
        });
    }

    public function down()
    {
        // Kembalikan kolom subcategory jika rollback
        Schema::table('products', function (Blueprint $table) {
            $table->string('subcategory')->nullable();
        });
    }
};