<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class OrderTotalCalculationTest extends TestCase
{
    private function calculateTotal(array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            $price = (float) ($item['price'] ?? 0);
            $qty   = (int) ($item['quantity'] ?? 0);

            $total += $price * $qty;
        }

        return $total;
    }

    public function test_calculates_total_correctly_for_single_item(): void
    {
        $items = [
            ['price' => 10000, 'quantity' => 2],
        ];

        $this->assertEquals(20000, $this->calculateTotal($items));
    }

    public function test_calculates_total_correctly_for_multiple_items(): void
    {
        $items = [
            ['price' => 10000, 'quantity' => 2], // 20000
            ['price' => 5000,  'quantity' => 1], // 5000
        ];

        $this->assertEquals(25000, $this->calculateTotal($items));
    }

    public function test_returns_zero_for_empty_items(): void
    {
        $this->assertEquals(0, $this->calculateTotal([]));
    }

    public function test_handles_decimal_prices_correctly(): void
    {
        $items = [
            ['price' => 10000.50, 'quantity' => 2], // 20001.0
        ];

        $this->assertEquals(20001.0, $this->calculateTotal($items));
    }
}