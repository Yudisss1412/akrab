<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Wishlist;
use App\Models\User;
use App\Models\Product;

class WishlistsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user dan product untuk membuat wishlist
        $users = User::all();
        $products = Product::all();
        
        foreach ($users as $user) {
            // Ambil beberapa produk untuk dimasukkan ke wishlist
            $productSubset = $products->random(rand(2, 5));
            
            foreach ($productSubset as $product) {
                // Cek apakah produk sudah ada di wishlist user ini
                $existingWishlist = Wishlist::where('user_id', $user->id)
                                           ->where('product_id', $product->id)
                                           ->first();
                
                if (!$existingWishlist) {
                    // Tambahkan produk ke wishlist
                    Wishlist::create([
                        'user_id' => $user->id,
                        'product_id' => $product->id
                    ]);
                }
            }
        }
    }
}
