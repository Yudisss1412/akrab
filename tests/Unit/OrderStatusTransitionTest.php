<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class OrderStatusTransitionTest extends TestCase
{
    private function canTransition($fromStatus, $toStatus)
    {
        $validTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered'],
            'delivered' => ['refunded'],
            'cancelled' => ['refunded'],
        ];

        if (!isset($validTransitions[$fromStatus])) {
            return false;
        }

        return in_array($toStatus, $validTransitions[$fromStatus]);
    }

    public function test_allows_valid_status_transitions(): void
    {
        // Transisi yang valid: pending -> processing -> shipped -> delivered
        $this->assertTrue($this->canTransition('pending', 'processing'));
        $this->assertTrue($this->canTransition('processing', 'shipped'));
        $this->assertTrue($this->canTransition('shipped', 'delivered'));
    }

    public function test_denies_invalid_status_transitions(): void
    {
        // Transisi yang tidak valid: pending -> delivered langsung
        $this->assertFalse($this->canTransition('pending', 'delivered'));

        // Transisi yang tidak valid: delivered -> processing
        $this->assertFalse($this->canTransition('delivered', 'processing'));

        // Transisi yang tidak valid: shipped -> pending
        $this->assertFalse($this->canTransition('shipped', 'pending'));
    }

    public function test_allows_cancellation_from_pending_or_processing(): void
    {
        // Pembatalan hanya diizinkan dari status pending atau processing
        $this->assertTrue($this->canTransition('pending', 'cancelled'));
        $this->assertTrue($this->canTransition('processing', 'cancelled'));
    }

    public function test_denies_cancellation_from_shipped_or_delivered(): void
    {
        // Pembatalan tidak diizinkan dari status shipped atau delivered
        $this->assertFalse($this->canTransition('shipped', 'cancelled'));
        $this->assertFalse($this->canTransition('delivered', 'cancelled'));
    }

    public function test_handles_refund_transitions_properly(): void
    {
        // Pengembalian uang hanya dari delivered atau cancelled
        $this->assertTrue($this->canTransition('delivered', 'refunded'));
        $this->assertTrue($this->canTransition('cancelled', 'refunded'));
    }
}