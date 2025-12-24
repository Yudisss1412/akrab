<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Buat Snap Token untuk pembayaran
     */
    public function getSnapToken($order, $items)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->shipping_address->phone ?? $order->user->phone,
            ],
            'item_details' => $this->formatItems($items),
            'enabled_payments' => ['gopay', 'shopeepay', 'ovo', 'danacita', 'permata_va', 'bca_va', 'bni_va', 'bri_va', 'echannel', 'credit_card'],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            \Log::error('Midtrans Snap Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Format item untuk Midtrans
     */
    private function formatItems($items)
    {
        $formattedItems = [];
        foreach ($items as $item) {
            $product = $item['product'] ?? null;
            $productVariant = $item['product_variant'] ?? null;

            if (!$product) {
                continue; // Skip item jika product tidak ditemukan
            }

            // Gunakan unit_price dari order item
            $unitPrice = $item['unit_price'] ?? $item['product']->price;
            if ($productVariant && !isset($item['unit_price'])) {
                $unitPrice += $productVariant->additional_price;
            }

            $formattedItems[] = [
                'id' => $product->id . ($productVariant ? '-' . $productVariant->id : ''),
                'price' => $unitPrice,
                'quantity' => $item['quantity'],
                'name' => $product->name . ($productVariant ? ' (' . $productVariant->name . ')' : ''),
            ];
        }
        return $formattedItems;
    }

    /**
     * Validasi transaksi dari Midtrans
     */
    public function validateTransaction($orderId)
    {
        try {
            $status = Transaction::status($orderId);
            return $status;
        } catch (\Exception $e) {
            \Log::error('Midtrans Validation Error: ' . $e->getMessage());
            return null;
        }
    }
}