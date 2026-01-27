<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class PaymentMethodValidationTest extends TestCase
{
    private function validatePaymentMethod($method)
    {
        if (!is_string($method)) {
            return false;
        }

        $validMethods = [
            'credit_card',
            'debit_card',
            'bank_transfer',
            'cod',
            'ewallet'
        ];

        // Check if method contains spaces or special characters (except underscore)
        if (preg_match('/[^a-z0-9_]/', $method)) {
            return false;
        }

        return in_array(strtolower($method), $validMethods);
    }

    public function test_validates_valid_payment_method(): void
    {
        $validMethods = ['credit_card', 'debit_card', 'bank_transfer', 'cod', 'ewallet'];

        foreach ($validMethods as $method) {
            $result = $this->validatePaymentMethod($method);
            $this->assertTrue($result, "Method {$method} should be valid");
        }
    }

    public function test_rejects_invalid_payment_method(): void
    {
        $invalidMethods = ['bitcoin', 'paypal_fake', 'unknown_method', '', null];

        foreach ($invalidMethods as $method) {
            $result = $this->validatePaymentMethod($method);
            $this->assertFalse($result, "Method {$method} should be invalid");
        }
    }

    public function test_handles_case_sensitive_methods_correctly(): void
    {
        // Assuming the validation is case-sensitive
        $result = $this->validatePaymentMethod('CREDIT_CARD');
        $this->assertFalse($result, "Uppercase method should be invalid if validation is case-sensitive");

        $result = $this->validatePaymentMethod('credit_card');
        $this->assertTrue($result, "Lowercase method should be valid");
    }

    public function test_rejects_payment_method_with_spaces_or_special_characters(): void
    {
        // Jika metode pembayaran tidak boleh mengandung spasi atau karakter khusus
        $result = $this->validatePaymentMethod('credit card');
        $this->assertFalse($result, "Method with spaces should be invalid");

        $result = $this->validatePaymentMethod('credit@card');
        $this->assertFalse($result, "Method with special character should be invalid");
    }
}