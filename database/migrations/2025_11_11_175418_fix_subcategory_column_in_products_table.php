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
        Schema::table('products', function (Blueprint $table) {
            // Drop foreign key constraint first if it exists
            if (Schema::hasColumn('products', 'subcategory_id')) {
                $table->dropForeign(['subcategory_id']);
            }
            
            // Remove the string subcategory column if it exists
            if (Schema::hasColumn('products', 'subcategory')) {
                $table->dropColumn('subcategory');
            }
            
            // Add the subcategory_id foreign key column
            if (!Schema::hasColumn('products', 'subcategory_id')) {
                $table->unsignedBigInteger('subcategory_id')->nullable();
                $table->foreign('subcategory_id')->references('id')->on('subcategories')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['subcategory_id']);
            
            // Drop the subcategory_id column
            $table->dropColumn('subcategory_id');
            
            // Add back the string subcategory column
            $table->string('subcategory')->nullable();
        });
    }
};
