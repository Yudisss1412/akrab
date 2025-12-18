<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Hanya jalankan seeder kategori dan subkategori karena sekarang mengelola semuanya
        $this->call([
            RoleSeeder::class,
            CategorySubcategorySeeder::class, // Ini sekarang mengatur kategori dan subkategori
            UserSeeder::class,
            ProductSeeder::class,
            ProductVariantSeeder::class,
            OrderSeeder::class,
            SellerOrderSeeder::class,
            ReviewsTableSeeder::class,
        ]);

        // Jalankan seeder review untuk penjual setelah semua data dasar dibuat
        $this->call([
            SellerReviewSeeder::class,
        ]);

        // Jalankan seeder untuk data dummy dinamis
        $this->call([
            DynamicReviewSeeder::class,
        ]);

        // Jalankan seeder untuk data dummy order
        $this->call([
            DummyOrdersSeeder::class,
        ]);

        // Jalankan seeder untuk data dummy riwayat penjualan
        $this->call([
            DummySalesHistorySeeder::class,
        ]);

        // Jalankan seeder untuk ulasan dummy
        $this->call([
            ReviewSeeder::class,
        ]);

        // Jalankan seeder khusus untuk produk dan rating penjual
        $this->call([
            ProductRatingSeeder::class,
            SellerProductReviewSeeder::class,
        ]);

        // Cek data
        $this->checkData();
    }

    private function checkData()
    {
        $seller = \App\Models\Seller::first();
        if ($seller) {
            echo "Seller count: " . \App\Models\Seller::count() . "\n";
            echo "Product count: " . \App\Models\Product::count() . "\n";
            echo "Review count: " . \App\Models\Review::count() . "\n";
            echo "First seller id: " . $seller->id . "\n";
            echo "First seller products count: " . $seller->products()->count() . "\n";
            echo "First seller user id: " . $seller->user_id . "\n";

            // Cek produk milik seller ini
            $products = $seller->products;
            echo "Product IDs for this seller: ";
            foreach ($products as $product) {
                echo $product->id . " ";
            }
            echo "\n";

            // Cek review untuk produk ini
            $productIds = $products->pluck('id')->toArray();
            $reviews = \App\Models\Review::whereIn('product_id', $productIds)->get();
            echo "Reviews count for this seller's products: " . $reviews->count() . "\n";

            foreach ($reviews as $review) {
                echo "Review ID: " . $review->id . ", Product ID: " . $review->product_id . ", Rating: " . $review->rating . "\n";
            }
        } else {
            echo "No seller found\n";
        }
    }
}
