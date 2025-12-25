<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingTrackingController extends Controller
{
    public function show($order)
    {
        try {
            $orderData = Order::with(['shipping_address'])->where('order_number', $order)->firstOrFail();
            
            // Base timeline for shipping tracking
            $baseTimeline = [
                [
                    'status' => 'Pesanan Diterima',
                    'description' => 'Pesanan Anda telah diterima oleh penjual',
                    'timestamp' => $orderData->created_at->format('Y-m-d H:i:s'),
                    'location' => $orderData->shipping_address->city ?? 'Kota Asal',
                    'status_code' => 'accepted'
                ],
                [
                    'status' => 'Dikemas',
                    'description' => 'Pesanan sedang dikemas oleh penjual',
                    'timestamp' => $orderData->created_at->addHours(2)->format('Y-m-d H:i:s'),
                    'location' => $orderData->shipping_address->city ?? 'Kota Asal',
                    'status_code' => 'packed'
                ],
                [
                    'status' => 'Dikirim dari Kota Asal',
                    'description' => 'Pesanan telah dikirim dari kota asal',
                    'timestamp' => $orderData->created_at->addHours(4)->format('Y-m-d H:i:s'),
                    'location' => $orderData->shipping_address->city ?? 'Kota Asal',
                    'status_code' => 'shipped_origin'
                ],
                [
                    'status' => 'Dalam Perjalanan',
                    'description' => 'Pesanan sedang dalam perjalanan menuju kota tujuan',
                    'timestamp' => $orderData->created_at->addHours(24)->format('Y-m-d H:i:s'),
                    'location' => 'Dalam perjalanan',
                    'status_code' => 'in_transit'
                ],
                [
                    'status' => 'Sampai di Kota Tujuan',
                    'description' => 'Pesanan telah tiba di kota tujuan',
                    'timestamp' => $orderData->created_at->addHours(48)->format('Y-m-d H:i:s'),
                    'location' => $orderData->shipping_address->city ?? 'Kota Tujuan',
                    'status_code' => 'arrived_destination'
                ],
                [
                    'status' => 'Diantar ke Alamat',
                    'description' => 'Pesanan sedang diantar ke alamat tujuan',
                    'timestamp' => $orderData->created_at->addHours(72)->format('Y-m-d H:i:s'),
                    'location' => $orderData->shipping_address->full_address ?? 'Alamat Tujuan',
                    'status_code' => 'out_for_delivery'
                ],
                [
                    'status' => 'Diterima',
                    'description' => 'Pesanan telah diterima oleh penerima',
                    'timestamp' => $orderData->created_at->addHours(73)->format('Y-m-d H:i:s'),
                    'location' => $orderData->shipping_address->full_address ?? 'Alamat Tujuan',
                    'status_code' => 'delivered'
                ]
            ];

            // Adjust the timeline based on actual order status
            $adjustedTimeline = [];
            $maxIndexToShow = 0;

            // Determine how far the order has progressed based on its status
            switch ($orderData->status) {
                case 'pending':
                    $maxIndexToShow = 0;
                    break;
                case 'confirmed':
                    $maxIndexToShow = 2;
                    break;
                case 'shipped':
                    $maxIndexToShow = 5;
                    break;
                case 'delivered':
                    $maxIndexToShow = 6;
                    break;
                default:
                    $maxIndexToShow = 0;
            }

            // Show only steps up to the current status
            for ($i = 0; $i <= $maxIndexToShow && $i < count($baseTimeline); $i++) {
                $item = $baseTimeline[$i];
                // Adjust timestamp to be relative to order creation time
                $item['timestamp'] = $orderData->created_at->addHours($i * 5)->format('Y-m-d H:i:s');
                $adjustedTimeline[] = $item;
            }

            return view('customer.transaksi.shipping_track', compact('orderData', 'adjustedTimeline'));
        } catch (\Exception $e) {
            Log::error('Error in shipping tracking: ' . $e->getMessage() . ' in file: ' . $e->getFile() . ' on line: ' . $e->getLine());
            abort(404, 'Pesanan tidak ditemukan');
        }
    }
}