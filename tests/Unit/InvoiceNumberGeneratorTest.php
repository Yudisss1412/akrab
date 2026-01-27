<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Carbon\Carbon;

class InvoiceNumberGeneratorTest extends TestCase
{
    private function generateInvoiceNumber()
    {
        $date = Carbon::now()->format('Ymd');
        $randomNumber = str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);

        return "INV-{$date}-{$randomNumber}";
    }

    public function test_generates_invoice_number_with_correct_format(): void
    {
        // Mock waktu saat ini
        Carbon::setTestNow(Carbon::create(2023, 10, 15, 14, 30, 0));

        $invoiceNumber = $this->generateInvoiceNumber();

        // Format yang diharapkan: INV-YYYYMMDD-XXXXX
        $expectedPattern = '/^INV-20231015-\d{5}$/';

        $this->assertMatchesRegularExpression($expectedPattern, $invoiceNumber);
        $this->assertEquals(18, strlen($invoiceNumber)); // INV- + 8 digits date + - + 5 digits = 3+1+8+1+5 = 18 chars
    }

    public function test_generates_invoice_number_with_current_date(): void
    {
        // Mock waktu saat ini
        Carbon::setTestNow(Carbon::create(2023, 10, 15, 14, 30, 0));

        $currentDate = Carbon::now()->format('Ymd');

        $invoiceNumber = $this->generateInvoiceNumber();

        $datePart = substr($invoiceNumber, 4, 8); // Ambil bagian tanggal dari INV-YYYYMMDD-XXXXX

        $this->assertEquals($currentDate, $datePart);
    }

    public function test_has_consistent_format_across_different_calls(): void
    {
        // Mock waktu saat ini
        Carbon::setTestNow(Carbon::create(2023, 10, 15, 14, 30, 0));

        $firstInvoice = $this->generateInvoiceNumber();
        $secondInvoice = $this->generateInvoiceNumber();

        $pattern = '/^INV-\d{8}-\d{5}$/';

        $this->assertMatchesRegularExpression($pattern, $firstInvoice);
        $this->assertMatchesRegularExpression($pattern, $secondInvoice);
    }
}