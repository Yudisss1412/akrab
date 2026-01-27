<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class CheckoutValidationTest extends TestCase
{
    private function validateCheckoutData($data)
    {
        // Validasi cart tidak kosong
        if (empty($data['items'])) {
            return [
                'success' => false,
                'message' => 'Cart cannot be empty'
            ];
        }

        // Validasi quantity > 0
        foreach ($data['items'] as $item) {
            if ($item['quantity'] <= 0) {
                return [
                    'success' => false,
                    'message' => 'Quantity must be greater than 0'
                ];
            }
        }

        // Validasi alamat, penerima, dan no HP
        if (empty($data['address']) || empty($data['recipient']) || empty($data['phone'])) {
            return [
                'success' => false,
                'message' => 'Address, recipient, and phone number are required'
            ];
        }

        // Jika semua validasi lolos
        return [
            'success' => true,
            'message' => 'Checkout data is valid'
        ];
    }

    public function test_validates_non_empty_cart(): void
    {
        $cartData = [
            'items' => [],
            'address' => 'Jl. Test No. 123',
            'recipient' => 'John Doe',
            'phone' => '081234567890'
        ];

        $result = $this->validateCheckoutData($cartData);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('empty', $result['message']);
    }

    public function test_validates_positive_quantity(): void
    {
        $cartData = [
            'items' => [
                ['product_id' => 1, 'quantity' => 0],
            ],
            'address' => 'Jl. Test No. 123',
            'recipient' => 'John Doe',
            'phone' => '081234567890'
        ];

        $result = $this->validateCheckoutData($cartData);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('greater than 0', $result['message']);
    }

    public function test_validates_valid_address_recipient_and_phone(): void
    {
        $cartData = [
            'items' => [
                ['product_id' => 1, 'quantity' => 2],
            ],
            'address' => '',
            'recipient' => '',
            'phone' => ''
        ];

        $result = $this->validateCheckoutData($cartData);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('required', $result['message']);
    }

    public function test_passes_validation_with_valid_checkout_data(): void
    {
        $cartData = [
            'items' => [
                ['product_id' => 1, 'quantity' => 2],
            ],
            'address' => 'Jl. Test No. 123',
            'recipient' => 'John Doe',
            'phone' => '081234567890'
        ];

        $result = $this->validateCheckoutData($cartData);

        $this->assertTrue($result['success']);
    }
}