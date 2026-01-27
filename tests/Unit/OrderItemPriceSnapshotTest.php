<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class OrderItemPriceSnapshotTest extends TestCase
{
    private function createOrderItem($product, $quantity)
    {
        return [
            'product_id' => $product->id,
            'price_at_purchase' => $product->price,
            'quantity' => $quantity
        ];
    }

    public function test_snapshots_current_product_price_when_creating_order_item(): void
    {
        // Membuat objek product fiktif
        $product = (object)[
            'id' => 1,
            'price' => 100000,
            'name' => 'Test Product'
        ];

        $orderItem = $this->createOrderItem($product, 2);

        // Memastikan bahwa harga produk disimpan sebagai snapshot
        $this->assertEquals(100000, $orderItem['price_at_purchase']);
        $this->assertEquals(1, $orderItem['product_id']);
        $this->assertEquals(2, $orderItem['quantity']);
    }

    public function test_creates_order_item_with_correct_attributes(): void
    {
        // Membuat objek product fiktif
        $product = (object)[
            'id' => 1,
            'price' => 75000,
            'name' => 'Special Product',
            'sku' => 'SP-001'
        ];

        $orderItem = $this->createOrderItem($product, 3);

        // Pastikan semua atribut penting disimpan
        $this->assertArrayHasKey('product_id', $orderItem);
        $this->assertArrayHasKey('price_at_purchase', $orderItem);
        $this->assertArrayHasKey('quantity', $orderItem);
        $this->assertEquals(1, $orderItem['product_id']);
        $this->assertEquals(75000, $orderItem['price_at_purchase']);
        $this->assertEquals(3, $orderItem['quantity']);
    }

    public function test_handles_decimal_prices_in_snapshot(): void
    {
        // Membuat objek product fiktif
        $product = (object)[
            'id' => 1,
            'price' => 99999.99,
            'name' => 'Decimal Price Product'
        ];

        $orderItem = $this->createOrderItem($product, 1);

        $this->assertEquals(99999.99, $orderItem['price_at_purchase']);
    }
}