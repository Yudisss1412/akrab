<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StockDeductionTest extends TestCase
{
    private $productStocks = [
        1 => 5,  // Produk ID 1 punya stok 5
        2 => 10,   // Produk ID 2 punya stok 10
    ];

    private function resetProductStocks()
    {
        $this->productStocks = [
            1 => 5,  // Produk ID 1 punya stok 5
            2 => 10,   // Produk ID 2 punya stok 10
        ];
    }

    private function deductStock($productId, $qty)
    {
        if ($qty <= 0) {
            return false;
        }

        if (!isset($this->productStocks[$productId])) {
            return false; // Produk tidak ditemukan
        }

        if ($this->productStocks[$productId] < $qty) {
            return false; // Stok tidak mencukupi
        }

        $this->productStocks[$productId] -= $qty; // Kurangi stok
        return true;
    }

    public function test_deducts_stock_correctly(): void
    {
        // Reset stok produk 1 ke 5 sebelum test
        $this->resetProductStocks();

        $result = $this->deductStock(1, 3);

        $this->assertTrue($result);
    }

    public function test_fails_to_deduct_when_requested_quantity_exceeds_available_stock(): void
    {
        // Reset stok produk 1 ke 5 sebelum test
        $this->resetProductStocks();

        $result = $this->deductStock(1, 10);

        $this->assertFalse($result);
    }

    public function test_fails_to_deduct_when_product_does_not_exist(): void
    {
        // Reset stok produk 1 ke 5 sebelum test
        $this->resetProductStocks();

        $result = $this->deductStock(999, 5);

        $this->assertFalse($result);
    }

    public function test_fails_to_deduct_when_requested_quantity_is_zero(): void
    {
        // Reset stok produk 1 ke 5 sebelum test
        $this->resetProductStocks();

        $result = $this->deductStock(1, 0);

        $this->assertFalse($result);
    }

    public function test_fails_to_deduct_when_requested_quantity_is_negative(): void
    {
        // Reset stok produk 1 ke 5 sebelum test
        $this->resetProductStocks();

        $result = $this->deductStock(1, -5);

        $this->assertFalse($result);
    }
}