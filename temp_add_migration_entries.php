<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Migration entries to add manually (excluding the problematic one)
$migrations_to_add = [
    '2025_11_27_032214_add_bank_account_to_sellers_table',
    '2025_11_27_033611_update_orders_status_enum_add_waiting_verification',
    '2025_11_27_063201_add_proof_image_to_payments_table',
    '2025_11_27_064141_update_payments_status_enum',
    '2025_11_27_064340_update_payments_status_enum_simple',
    '2025_12_05_143000_add_lat_lng_to_users_table',
    '2025_12_05_151812_add_profile_image_to_sellers_table',
    '2025_12_07_075148_create_ticket_replies_table',
    '2025_12_08_150206_create_admin_activity_logs_table', // This is the one we need
    '2025_12_09_154018_add_unique_constraint_to_sku_column_in_products_table'
];

foreach ($migrations_to_add as $migration) {
    $existing = DB::table('migrations')->where('migration', $migration)->first();
    
    if (!$existing) {
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => 1
        ]);
        echo "Added migration: $migration\n";
    } else {
        echo "Migration already exists: $migration\n";
    }
}

echo "Migration entries added successfully.\n";