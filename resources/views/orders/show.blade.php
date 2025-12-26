<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Pesanan #{{ $order->order_number }} - UMKM AKRAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/order-detail.css') }}">
    
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
    }
    
    .page-header {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
    }

    .page-header h1 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--ak-primary);
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

    .btn-primary {
      background: var(--ak-primary);
      color: white;
      border-color: var(--ak-primary);
    }

    .btn-primary:hover {
      background: #005a4a;
      border-color: #005a4a;
    }

    .card {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
      overflow: hidden;
    }

    .card-header {
      background: var(--ak-white);
      border-bottom: 1px solid var(--ak-border);
      padding: 1rem 1.25rem;
      font-weight: 600;
      color: var(--ak-primary);
      border-radius: var(--ak-radius) var(--ak-radius) 0 0 !important;
    }

    .card-body {
      padding: 1.5rem;
    }

    .status-badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 1rem;
      font-size: 0.75rem;
      font-weight: 500;
    }

    .status-processing {
      background: rgba(59, 130, 246, 0.1);
      color: #2563eb;
    }

    .status-shipping {
      background: rgba(139, 69, 193, 0.1);
      color: #8b45c1;
    }

    .status-completed {
      background: rgba(34, 197, 94, 0.1);
      color: #16a34a;
    }

    .status-cancelled {
      background: rgba(239, 68, 68, 0.1);
      color: #dc2626;
    }

    .table th {
      border-top: none;
      font-weight: 600;
      color: var(--ak-text);
      background-color: transparent;
    }

    .table td {
      vertical-align: middle;
      padding: 0.75rem 1rem;
    }

    .product-image {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 6px;
      background: #f3f4f6;
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      padding: 0.5rem 0;
      border-bottom: 1px solid var(--ak-border);
    }

    .summary-item:last-child {
      border-bottom: none;
      font-weight: 600;
    }

    .summary-label {
      color: var(--ak-muted);
    }

    .summary-value {
      font-weight: 500;
    }

    .info-section {
      margin-bottom: 1.5rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid var(--ak-border);
    }

    .info-section:last-child {
      margin-bottom: 0;
      padding-bottom: 0;
      border-bottom: none;
    }

    .info-title {
      font-weight: 600;
      color: var(--ak-primary);
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid var(--ak-border);
    }

    .info-row {
      display: flex;
      margin-bottom: 0.75rem;
    }

    .info-label {
      min-width: 150px;
      color: var(--ak-muted);
      font-weight: 500;
    }

    .info-value {
      flex: 1;
    }

    .seller-actions .btn {
      width: 100%;
      margin-bottom: 0.5rem;
    }

    .seller-actions .btn:last-child {
      margin-bottom: 0;
    }

    .timeline {
      position: relative;
      padding: 1rem 0;
    }

    .timeline-item {
      display: flex;
      margin-bottom: 1.5rem;
      align-items: flex-start;
    }

    .timeline-item:last-child {
      margin-bottom: 0;
    }

    .timeline-icon {
      width: 32px;
      height: 32px;
      background: var(--ak-primary-light);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      margin-right: 1rem;
      color: var(--ak-primary);
    }

    .timeline-content {
      flex: 1;
    }

    .timeline-content p {
      margin-bottom: 0.25rem;
    }

    .timeline-date {
      color: var(--ak-muted);
      font-size: 0.875rem;
    }

    .print-buttons {
      display: flex;
      gap: 0.5rem;
    }

    .print-buttons .btn {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Header Halaman -->
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('penjual.pesanan') }}" class="btn btn-outline me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h1>Detail Pesanan #{{ $order->order_number }}</h1>
                </div>
                <div class="print-buttons">
                    <button class="btn btn-outline-primary" onclick="window.print();">
                        <i class="bi bi-printer"></i> Cetak Invoice
                    </button>
                    <a href="{{ route('shipping.label', $order->order_number) }}" class="btn btn-outline">
                        <i class="bi bi-truck"></i> Cetak Label
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Kolom Utama (Kiri) - 7/12 (about 58.33%) -->
            <div class="col-lg-7">
                <!-- Kartu Rincian Produk -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Rincian Produk</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Gambar</th>
                                        <th>Nama Produk</th>
                                        <th>Jumlah</th>
                                        <th>Harga Satuan</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            @if($item->product->main_image)
                                            <img src="{{ asset('storage/' . $item->product->main_image) }}" alt="{{ $item->product->name }}" class="product-image">
                                            @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $item->product->name }}</div>
                                            @if($item->variant)
                                                <small class="text-muted">{{ $item->variant->name }}</small>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ $item->quantity }}</td>
                                        <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6 ms-auto">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="text-end">Subtotal Produk:</td>
                                        <td class="text-end">Rp {{ number_format($order->sub_total, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">Biaya Pengiriman:</td>
                                        <td class="text-end">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                    </tr>
                                    @if($order->insurance_cost > 0)
                                    <tr>
                                        <td class="text-end">Asuransi:</td>
                                        <td class="text-end">Rp {{ number_format($order->insurance_cost, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    @if($order->discount > 0)
                                    <tr>
                                        <td class="text-end">Diskon:</td>
                                        <td class="text-end text-danger">-Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="text-end fw-bold">Total Keseluruhan:</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kartu Informasi Pengiriman & Pembeli -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Pengiriman & Pembeli</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-section">
                            <div class="info-title">Pengiriman</div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="fw-semibold">Nama Penerima</div>
                                    <div>{{ $order->shipping_address->recipient_name }}</div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="fw-semibold">No. Telepon</div>
                                    <div>{{ $order->shipping_address->phone }}</div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="fw-semibold">Alamat</div>
                                    <div>{{ $order->shipping_address->full_address }}</div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="fw-semibold">Jasa Pengiriman</div>
                                    <div>{{ $order->shipping_courier }}</div>
                                </div>
                            </div>
                            @if($order->tracking_number)
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="fw-semibold">Nomor Resi</div>
                                    <div>
                                        @php
                                            $trackingUrl = '';
                                            $courier = strtolower($order->shipping_courier ?? '');

                                            // Mapping untuk beberapa ekspedisi umum di Indonesia
                                            switch ($courier) {
                                                case 'jne':
                                                    $trackingUrl = 'https://www.jne.co.id/id/tracking?waybill=' . $order->tracking_number;
                                                    break;
                                                case 'jnt':
                                                case 'j&t':
                                                case 'jnt express':
                                                    $trackingUrl = 'https://www.jet.co.id/track?waybill=' . $order->tracking_number;
                                                    break;
                                                case 'sicepat':
                                                    $trackingUrl = 'https://sicepat.com/track?awb=' . $order->tracking_number;
                                                    break;
                                                case 'tiki':
                                                    $trackingUrl = 'https://www.tiki.id/tracking?track=' . $order->tracking_number;
                                                    break;
                                                case 'pos':
                                                case 'pos indonesia':
                                                    $trackingUrl = 'https://www.posindonesia.co.id/id/track-and-trace?waybill=' . $order->tracking_number;
                                                    break;
                                                case 'anteraja':
                                                    $trackingUrl = 'https://anteraja.id/waybill?waybill=' . $order->tracking_number;
                                                    break;
                                                case 'wahana':
                                                    $trackingUrl = 'https://www.wahana.com/waybill?waybill=' . $order->tracking_number;
                                                    break;
                                                case 'ninja van':
                                                case 'ninjavan':
                                                    $trackingUrl = 'https://www.ninjavan.co/id/track?search=' . $order->tracking_number;
                                                    break;
                                                default:
                                                    // Jika kurir tidak dikenali, coba beberapa situs pelacakan umum
                                                    $trackingUrl = '#';
                                            }
                                        @endphp
                                        @if($trackingUrl !== '#')
                                            <a href="{{ $trackingUrl }}" target="_blank" class="text-decoration-none">
                                                <i class="bi bi-box-seam me-1"></i>{{ $order->tracking_number }}
                                                <i class="bi bi-box-arrow-up-right ms-1"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">{{ $order->tracking_number }}</span>
                                            <small class="d-block text-muted">Kurir tidak dikenali untuk pelacakan otomatis</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($order->status === 'delivered')
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Pesanan ini telah ditandai sebagai diterima. Jika Anda belum menerima pesanan ini, silakan laporkan:
                                        <button type="button" class="btn btn-link p-0 ms-2 text-decoration-underline" onclick="reportUndeliveredOrder('{{ $order->order_number }}')">
                                            Laporkan Barang Belum Diterima
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <button class="btn btn-outline btn-sm mt-2">
                                <i class="bi bi-clipboard"></i> Salin Alamat
                            </button>
                        </div>
                        
                        <div class="info-section">
                            <div class="info-title">Pembeli</div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="fw-semibold">Nama Pembeli</div>
                                    <div>{{ $order->user->name }}</div>
                                </div>
                            </div>
                            @if($order->notes)
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="fw-semibold">Catatan dari Pembeli</div>
                                    <div>{{ $order->notes }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar (Kanan) - 5/12 (about 41.67%) -->
            <div class="col-lg-5">
                <!-- Kartu Ringkasan & Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ringkasan & Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="summary-label">Status Pesanan</span>
                                @php
                                    $statusLabels = [
                                        'pending' => 'Perlu Diproses',
                                        'confirmed' => 'Telah Dikonfirmasi',
                                        'shipped' => 'Telah Dikirim',
                                        'delivered' => 'Telah Diterima',
                                        'cancelled' => 'Dibatalkan'
                                    ];
                                    
                                    $statusBadgeClasses = [
                                        'pending' => 'status-processing',
                                        'confirmed' => 'status-processing',
                                        'shipped' => 'status-shipping',
                                        'delivered' => 'status-completed',
                                        'cancelled' => 'status-cancelled'
                                    ];
                                    
                                    $statusLabel = $statusLabels[$order->status] ?? 'Status Tidak Dikenal';
                                    $statusBadgeClass = $statusBadgeClasses[$order->status] ?? 'status-cancelled';
                                @endphp
                                <span class="status-badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span>
                            </div>
                            
                            <div class="summary-item">
                                <span class="summary-label">Tanggal Dipesan</span>
                                <span class="summary-value">{{ $order->created_at->format('d M Y H:i') }}</span>
                            </div>
                            
                            @if($order->paid_at)
                            <div class="summary-item">
                                <span class="summary-label">Tanggal Dibayar</span>
                                <span class="summary-value">{{ $order->paid_at->format('d M Y H:i') }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="summary-item">
                            <span class="summary-label">Total Pembayaran</span>
                            <span class="summary-value fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Kartu Aksi Penjual -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Aksi Penjual</h5>
                    </div>
                    <div class="card-body">
                        @if($order->status === 'pending')
                        <button class="btn btn-outline w-100 mb-2" data-bs-toggle="modal" data-bs-target="#addTrackingModal">
                            Masukkan Nomor Resi
                        </button>
                        <button class="btn btn-primary w-100 mb-2">Konfirmasi Pembayaran</button>
                        <button class="btn btn-outline w-100">Batalkan Pesanan</button>
                        @elseif($order->status === 'confirmed')
                        <button class="btn btn-outline w-100 mb-2" data-bs-toggle="modal" data-bs-target="#addTrackingModal">
                            Masukkan Nomor Resi
                        </button>
                        <button class="btn btn-primary w-100">Konfirmasi Pesanan & Beri Tahu Pembeli</button>
                        @elseif($order->status === 'shipped')
                        <button class="btn btn-primary w-100 mb-2">Tandai Selesai</button>
                        <button class="btn btn-outline w-100">Perbarui Status & Beri Tahu Pembeli</button>
                        @elseif($order->status === 'delivered')
                        <button class="btn btn-outline w-100">Ucapkan Terima Kasih & Beri Tahu Pembeli</button>
                        @endif
                    </div>
                </div>

                <!-- Kartu Riwayat Pesanan -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Riwayat Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($order->logs->sortByDesc('created_at') as $log)
                            <div class="timeline-item d-flex">
                                <div class="timeline-icon">
                                    <i class="bi bi-circle"></i>
                                </div>
                                <div class="timeline-content">
                                    <p class="mb-0">{{ $log->description }}</p>
                                    <span class="timeline-date">{{ $log->created_at->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk masukkan nomor resi -->
    <div class="modal fade" id="addTrackingModal" tabindex="-1" style="z-index: 1050;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Masukkan Nomor Resi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="trackingForm">
                        <div class="mb-3">
                            <label for="trackingNumber" class="form-label">Nomor Resi</label>
                            <input type="text" class="form-control" id="trackingNumber" placeholder="Masukkan nomor resi">
                        </div>
                        <div class="mb-3">
                            <label for="courier" class="form-label">Jasa Pengiriman</label>
                            <select class="form-control" id="courier">
                                <option value="jne">JNE</option>
                                <option value="tiki">TIKI</option>
                                <option value="pos">POS Indonesia</option>
                                <option value="jnt">J&T</option>
                                <option value="sicepat">SiCepat</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function reportUndeliveredOrder(orderNumber) {
            if (confirm('Apakah Anda yakin ingin melaporkan bahwa pesanan ini belum diterima?')) {
                // Kirim permintaan ke server untuk mengubah status kembali ke 'shipped'
                fetch(`/api/orders/${orderNumber}/report-undelivered`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Laporan berhasil dikirim. Status pesanan telah diperbarui.');
                        location.reload(); // Refresh halaman untuk menampilkan status terbaru
                    } else {
                        alert('Gagal mengirim laporan: ' + (data.message || 'Terjadi kesalahan'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengirim laporan');
                });
            }
        }
    </script>
</body>
</html>