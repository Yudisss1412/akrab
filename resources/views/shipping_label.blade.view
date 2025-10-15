<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Pengiriman - #{{ $order->order_number ?? 'N/A' }}</title>
    <style>
        /* Reset dan styling dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Tombol cetak */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #006E5C;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }

        .print-button:hover {
            background-color: #005a4a;
        }

        /* Styling untuk saat dicetak */
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            
            .print-button {
                display: none;
            }
            
            .label-container {
                border: 2px solid black !important;
                box-shadow: none !important;
                margin: 0 !important;
                width: 15cm !important;
                height: 10cm !important;
            }
        }

        /* Kontainer utama label */
        .label-container {
            width: 15cm;
            height: 10cm;
            background-color: white;
            border: 2px solid black;
            padding: 15px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        /* Garis pemisah */
        .divider {
            height: 1px;
            background-color: #ccc;
            margin: 10px 0;
        }

        /* Bagian header label */
        .section-header {
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
            color: #333;
        }

        /* Bagian penting - penerima */
        .recipient-name {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }

        /* Bagian detail pengiriman */
        .detail-item {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin: 3px 0;
        }

        .detail-label {
            font-weight: bold;
        }

        /* Bagian barcode */
        .barcode-area {
            width: 80px;
            height: 40px;
            border: 1px solid #000;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            text-align: center;
        }

        .barcode-label {
            font-size: 10px;
            text-align: center;
            margin-top: 5px;
        }

        /* Grid layout untuk bagian atas */
        .label-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Informasi alamat */
        .address-info {
            font-size: 12px;
            line-height: 1.4;
        }

        .large-text {
            font-size: 14px !important;
            font-weight: bold !important;
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">Cetak Halaman Ini</button>
    
    <div class="label-container">
        <!-- Bagian Informasi Pengirim (Dari) -->
        <div class="label-grid">
            <div class="sender-section">
                <div class="section-header">DARI / PENGIRIM:</div>
                <div class="address-info">
                    <div>Nama Toko: Toko UMKM</div>
                    <div>Nomor Telepon: -</div>
                </div>
            </div>

            <!-- Bagian Informasi Penerima (Kepada) -->
            <div class="recipient-section">
                <div class="section-header">KEPADA / PENERIMA:</div>
                <div>
                    <div class="recipient-name">{{ $order->shipping_address->recipient_name ?? 'N/A' }}</div>
                    <div class="address-info">
                        <div>{{ $order->shipping_address->phone ?? 'N/A' }}</div>
                        <div class="large-text">{{ $order->shipping_address->full_address ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Bagian Detail Pengiriman -->
        <div class="delivery-details">
            <div class="detail-item">
                <span class="detail-label">ID Pesanan:</span>
                <span>{{ $order->order_number ?? 'N/A' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Jasa Pengiriman:</span>
                <span>{{ $order->shipping_courier ?? 'N/A' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Berat Paket:</span>
                <span>- kg</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Isi Paket:</span>
                <span>
                    @if($order->items && $order->items->count() > 0)
                        {{ substr($order->items->first()->product->name ?? 'Item', 0, 20) }}
                        @if($order->items->count() > 1)
                            +{{ $order->items->count() - 1 }}
                        @endif
                    @else
                        Barang
                    @endif
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Pembayaran:</span>
                @if($order->total_amount)
                    <span>COD Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                @else
                    <span>Non-COD</span>
                @endif
            </div>
        </div>

        <!-- Bagian Barcode -->
        <div style="position: absolute; bottom: 15px; right: 15px;">
            <div class="barcode-area">
                <!-- Area untuk barcode -->
            </div>
            <div class="barcode-label">Barcode / Kode Booking</div>
        </div>
    </div>

    <script>
        // Fungsi tambahan jika diperlukan
        document.addEventListener('DOMContentLoaded', function() {
            // Fokus pada halaman untuk tampilan cetak yang lebih baik
        });
    </script>
</body>
</html>