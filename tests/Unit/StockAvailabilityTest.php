<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StockAvailabilityTest extends TestCase
{
    private $productStocks = [
        1 => 5,  // Produk ID 1 punya stok 5
        2 => 10,   // Produk ID 2 punya stok 10
    ];

    private function checkStock($productId, $qty)
    {
        if ($qty <= 0) {
            return false;
        }

        if (!isset($this->productStocks[$productId])) {
            return false; // Produk tidak ditemukan
        }

        return $this->productStocks[$productId] >= $qty;
    }

    public function test_checks_stock_availability_successfully(): void
    {
        $result = $this->checkStock(1, 5);

        $this->assertTrue($result);
    }

    public function test_fails_when_requested_quantity_exceeds_stock(): void
    {
        $result = $this->checkStock(1, 10);

        $this->assertFalse($result);
    }

    public function test_fails_when_requested_quantity_equals_zero(): void
    {
        $result = $this->checkStock(1, 0);

        $this->assertFalse($result);
    }

    public function test_fails_when_product_does_not_exist(): void
    {
        $result = $this->checkStock(999, 5);

        $this->assertFalse($result);
    }

    public function test_fails_when_requested_quantity_is_negative(): void
    {
        $result = $this->checkStock(1, -5);

        $this->assertFalse($result);
    }
}