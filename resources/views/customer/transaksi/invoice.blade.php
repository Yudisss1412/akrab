<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number ?? 'N/A' }} - UMKM AKRAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
    :root {
      --ak-primary: #006E5C;
      --ak-primary-light: #a8d5c9;
      --ak-white: #FFFFFF;
      --ak-background: #f0fdfa;
      --ak-text: #1D232A;
      --ak-muted: #6b7280;
      --ak-border: #E5E7EB;
      --ak-radius: 12px;
      --ak-space: 16px;
    }
    
    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--ak-background);
        color: var(--ak-text);
        margin: 0;
        padding: 20px;
    }
    
    .invoice-container {
        max-width: 800px;
        margin: 0 auto;
        background: var(--ak-white);
        border-radius: var(--ak-radius);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .invoice-header {
        background: var(--ak-white);
        padding: 1.5rem;
        border-bottom: 1px solid var(--ak-border);
    }
    
    .invoice-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--ak-primary);
        margin: 0;
    }
    
    .invoice-number {
        font-size: 1rem;
        color: var(--ak-muted);
        margin-top: 0.25rem;
    }
    
    .invoice-content {
        padding: 2rem;
    }
    
    .section-title {
        font-weight: 600;
        color: var(--ak-primary);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--ak-border);
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        padding: 0.25rem 0;
    }
    
    .detail-label {
        color: var(--ak-muted);
        font-weight: 500;
    }
    
    .detail-value {
        font-weight: 500;
    }
    
    .parties {
        display: flex;
        gap: 2rem;
        margin: 1.5rem 0;
    }
    
    .party {
        flex: 1;
    }
    
    .party h3 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--ak-primary);
        margin-bottom: 0.75rem;
    }
    
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
    }
    
    .items-table th {
        text-align: left;
        padding: 0.75rem;
        border-bottom: 2px solid var(--ak-border);
        font-weight: 600;
        color: var(--ak-text);
    }
    
    .items-table td {
        padding: 0.75rem;
        border-bottom: 1px solid var(--ak-border);
        vertical-align: top;
    }
    
    .item-info {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .item-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        background: #f3f4f6;
    }
    
    .item-details h4 {
        margin: 0 0 0.25rem 0;
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    .item-meta {
        margin: 0;
        font-size: 0.8rem;
        color: var(--ak-muted);
    }
    
    .text-right {
        text-align: right;
    }
    
    .text-center {
        text-align: center;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
    }
    
    .summary-row.total {
        font-weight: 600;
        border-top: 1px solid var(--ak-border);
        margin-top: 0.5rem;
        padding-top: 0.75rem;
    }
    
    .summary-divider {
        height: 1px;
        background: var(--ak-border);
        margin: 0.75rem 0;
    }
    
    .status.paid {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 500;
        background: rgba(34, 197, 94, 0.1);
        color: #16a34a;
    }
    
    .status.unpaid {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 500;
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }
    
    .print-actions {
        padding: 1.5rem;
        background: var(--ak-white);
        border-top: 1px solid var(--ak-border);
        text-align: center;
    }
    
    .btn {
        border: 1px solid transparent;
        border-radius: var(--ak-radius);
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-outline {
        border: 1px solid var(--ak-primary);
        color: var(--ak-primary);
        background: transparent;
    }

    .btn-outline:hover {
        background: var(--ak-primary);
        color: white;
    }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="invoice-title">Invoice</h1>
                    <div class="invoice-number">#{{ $order->order_number ?? 'N/A' }}</div>
                </div>
                <div>
                    <img src="{{ asset('src/Logo_UMKM.png') }}" alt="AKRAB" class="logo" style="height: 60px;">
                </div>
            </div>
        </div>

        <div class="invoice-content">
            <section class="invoice-details">
                <h3 class="section-title">Detail Pesanan</h3>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Pemesanan:</span>
                    <span class="detail-value">{{ $order->created_at->format('d M Y, H:i') ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status Pembayaran:</span>
                    @php
                        $status = $order->paid_at ? 'Sudah Dibayar' : 'Belum Dibayar';
                        $statusClass = $order->paid_at ? 'paid' : 'unpaid';
                    @endphp
                    <span class="status {{ $statusClass }}">{{ $status }}</span>
                </div>
                @if($order->paid_at)
                <div class="detail-row">
                    <span class="detail-label">Tanggal Pembayaran:</span>
                    <span class="detail-value">{{ $order->paid_at->format('d M Y, H:i') }}</span>
                </div>
                @endif
            </section>

            <div class="parties">
                <div class="party">
                    <h3>Dikirim ke:</h3>
                    <p><strong>{{ $order->shipping_address->recipient_name ?? 'N/A' }}</strong><br>
                    {{ $order->shipping_address->full_address ?? 'N/A' }}<br>
                    {{ $order->shipping_address->phone ?? 'N/A' }}</p>
                </div>

                <div class="party">
                    <h3>Dikirim dari:</h3>
                    <p><strong>PENJUAL</strong><br>
                    Alamat Toko<br>
                    Nomor Telepon Toko</p>
                </div>
            </div>

            <section class="order-items">
                <h3 class="section-title">Detail Pesanan</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-right">Harga</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($order) && $order->items)
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="item-info">
                                        @if($item->product && $item->product->main_image)
                                            <img src="{{ asset('storage/' . $item->product->main_image) }}" alt="{{ $item->product->name }}" class="item-image">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                        <div class="item-details">
                                            <h4>{{ $item->product->name ?? 'N/A' }}</h4>
                                            @if($item->variant)
                                                <p class="item-meta">{{ $item->variant->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right">Rp {{ number_format($item->unit_price ?? 0, 2, ',', '.') }}</td>
                                <td class="text-center">{{ $item->quantity ?? 0 }}</td>
                                <td class="text-right">Rp {{ number_format($item->subtotal ?? 0, 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">Tidak ada item pesanan</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </section>

            <section class="payment-summary">
                <h3 class="section-title">Ringkasan Pembayaran</h3>
                <div class="summary-row">
                    <span>Subtotal Produk ({{ $order->items->count() ?? 0 }} produk)</span>
                    <span>Rp {{ number_format($order->sub_total ?? 0, 2, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>Ongkos Kirim</span>
                    <span>Rp {{ number_format($order->shipping_cost ?? 0, 2, ',', '.') }}</span>
                </div>
                @if($order->insurance_cost > 0)
                <div class="summary-row">
                    <span>Asuransi Pengiriman</span>
                    <span>Rp {{ number_format($order->insurance_cost, 2, ',', '.') }}</span>
                </div>
                @endif
                @if($order->discount > 0)
                <div class="summary-row">
                    <span>Diskon</span>
                    <span>-Rp {{ number_format($order->discount, 2, ',', '.') }}</span>
                </div>
                @endif
                <div class="summary-divider"></div>
                <div class="summary-row total">
                    <span>Total Pembayaran</span>
                    <span class="total-amount">Rp {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}</span>
                </div>
            </section>
        </div>
        
        <div class="print-actions">
            <button id="btnPrint" class="btn btn-outline" type="button" aria-label="Cetak invoice">
                <i class="bi bi-printer"></i> Cetak Invoice
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Print button
            const printBtn = document.getElementById('btnPrint');
            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    window.print();
                });
            }
        });
    </script>
</body>
</html>
