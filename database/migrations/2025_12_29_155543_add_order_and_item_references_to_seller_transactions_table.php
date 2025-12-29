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
        Schema::table('seller_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->after('seller_id');
            $table->unsignedBigInteger('order_item_id')->nullable()->after('order_id');
            $table->unsignedBigInteger('withdrawal_request_id')->nullable()->after('order_item_id');

            // Add indexes for performance
            $table->index('order_id');
            $table->index('order_item_id');
            $table->index('withdrawal_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_transactions', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['order_item_id']);
            $table->dropIndex(['withdrawal_request_id']);

            $table->dropColumn(['order_id', 'order_item_id', 'withdrawal_request_id']);
        });
    }
};
