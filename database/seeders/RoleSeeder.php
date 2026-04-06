<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate([
            'name' => 'admin'
        ], [
            'display_name' => 'Administrator',
            'description' => 'System administrator with full access'
        ]);

        Role::firstOrCreate([
            'name' => 'seller'
        ], [
            'display_name' => 'Seller',
            'description' => 'Seller with access to manage products and orders'
        ]);

        Role::firstOrCreate([
            'name' => 'buyer'
        ], [
            'display_name' => 'Buyer',
            'description' => 'Regular buyer with access to browse and purchase products'
        ]);
    }
}
