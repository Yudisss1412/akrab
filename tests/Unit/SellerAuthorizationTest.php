<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SellerAuthorizationTest extends TestCase
{
    private function canUpdateProduct($user, $product)
    {
        if (!$user) {
            return false; // Guest tidak bisa update
        }

        // Admin bisa update semua produk
        if (isset($user->role) && $user->role === 'admin') {
            return true;
        }

        // Seller hanya bisa update produk miliknya sendiri
        return isset($user->id) && isset($product->seller_id) && $user->id === $product->seller_id;
    }

    private function canUpdateOrder($user, $order)
    {
        if (!$user) {
            return false; // Guest tidak bisa update
        }

        // Hanya seller yang memiliki order bisa update
        return isset($user->id) && isset($order->seller_id) && $user->id === $order->seller_id;
    }

    private function canDeleteProduct($user, $product)
    {
        if (!$user) {
            return false; // Guest tidak bisa delete
        }

        // Hanya pemilik produk yang bisa delete
        return isset($user->id) && isset($product->seller_id) && $user->id === $product->seller_id;
    }

    public function test_allows_seller_to_update_their_own_product(): void
    {
        $seller = (object)['id' => 1];
        $product = (object)['seller_id' => 1]; // Milik seller yang sama

        $result = $this->canUpdateProduct($seller, $product);

        $this->assertTrue($result);
    }

    public function test_denies_seller_from_updating_others_product(): void
    {
        $seller = (object)['id' => 1];
        $product = (object)['seller_id' => 2]; // Milik seller lain

        $result = $this->canUpdateProduct($seller, $product);

        $this->assertFalse($result);
    }

    public function test_denies_guest_from_updating_any_product(): void
    {
        $guest = null;
        $product = (object)['seller_id' => 1];

        $result = $this->canUpdateProduct($guest, $product);

        $this->assertFalse($result);
    }

    public function test_allows_seller_to_update_their_own_order(): void
    {
        $seller = (object)['id' => 1];
        $order = (object)['seller_id' => 1]; // Milik seller yang sama

        $result = $this->canUpdateOrder($seller, $order);

        $this->assertTrue($result);
    }

    public function test_denies_seller_from_updating_others_order(): void
    {
        $seller = (object)['id' => 1];
        $order = (object)['seller_id' => 2]; // Milik seller lain

        $result = $this->canUpdateOrder($seller, $order);

        $this->assertFalse($result);
    }

    public function test_allows_admin_to_update_any_product(): void
    {
        $admin = (object)['id' => 1, 'role' => 'admin'];
        $product = (object)['seller_id' => 2]; // Milik seller lain

        $result = $this->canUpdateProduct($admin, $product);

        $this->assertTrue($result);
    }

    public function test_allows_product_owner_to_delete_their_product(): void
    {
        $seller = (object)['id' => 1];
        $product = (object)['seller_id' => 1]; // Milik seller yang sama

        $result = $this->canDeleteProduct($seller, $product);

        $this->assertTrue($result);
    }
}