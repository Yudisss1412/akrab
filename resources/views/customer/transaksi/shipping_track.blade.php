<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Track Pesanan #{{ $orderData->order_number }} - AKRAB</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            padding-bottom: 60px;
        }

        .tracking-header {
            background: var(--ak-white);
            padding: 1.5rem;
            border-radius: var(--ak-radius);
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--ak-border);
        }

        .tracking-card {
            background: var(--ak-white);
            border-radius: var(--ak-radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--ak-border);
        }

        .tracking-timeline {
            position: relative;
            padding-left: 2rem;
        }

        .tracking-timeline::before {
            content: '';
            position: absolute;
            left: 9px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--ak-primary-light);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 5px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--ak-white);
            border: 2px solid var(--ak-primary);
        }

        .timeline-item.completed::before {
            background: var(--ak-primary);
            border-color: var(--ak-primary);
        }

        .timeline-item.current::before {
            background: var(--ak-white);
            border: 3px solid var(--ak-primary);
            width: 20px;
            height: 20px;
        }

        .timeline-item.completed .timeline-status {
            color: var(--ak-primary);
            font-weight: 600;
        }

        .timeline-item.current .timeline-status {
            color: var(--ak-primary);
            font-weight: 700;
        }

        .order-summary {
            background: var(--ak-white);
            border-radius: var(--ak-radius);
            padding: 1rem;
            border: 1px solid var(--ak-border);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-shipped {
            background: #e6f7ff;
            color: #1890ff;
            border: 1px solid #91d5ff;
        }

        .status-delivered {
            background: #f6ffed;
            color: #52c41a;
            border: 1px solid #b7eb8f;
        }

        .status-pending {
            background: #fffbe6;
            color: #faad14;
            border: 1px solid #ffe58f;
        }

        .btn-back {
            background: var(--ak-primary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--ak-radius);
        }

        .btn-back:hover {
            background: #005a4a;
            color: white;
        }

        .tracking-number {
            font-family: monospace;
            font-weight: 600;
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            display: inline-block;
        }

        /* Styling untuk notifikasi */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            transform: translateX(400px);
            transition: transform 0.3s ease-in-out;
            max-width: 350px;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            background: linear-gradient(135deg, #00c853, #009624);
        }

        .notification.error {
            background: linear-gradient(135deg, #ff5252, #e53935);
        }

        .notification.info {
            background: linear-gradient(135deg, #2979ff, #2962ff);
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <a href="{{ route('order.invoice', $orderData->order_number) }}" class="btn btn-back mb-3">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Detail Pesanan
                </a>

                <div class="tracking-header">
                    <h2 class="mb-3">Pelacakan Pengiriman</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Nomor Pesanan:</strong></p>
                            <p class="text-muted">{{ $orderData->order_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Status Pengiriman:</strong></p>
                            <span class="status-badge 
                                @if($orderData->status === 'delivered') status-delivered
                                @elseif($orderData->status === 'shipped') status-shipped
                                @else status-pending @endif">
                                @if($orderData->status === 'delivered') Diterima
                                @elseif($orderData->status === 'shipped') Dikirim
                                @else Dalam Proses @endif
                            </span>
                        </div>
                    </div>

                    @if($orderData->tracking_number)
                    <div class="mt-3">
                        <p class="mb-1"><strong>Nomor Resi:</strong></p>
                        <div class="tracking-number">{{ $orderData->tracking_number }}</div>
                    </div>
                    @endif
                </div>

                <div class="tracking-card">
                    <h3 class="mb-4">Riwayat Pengiriman</h3>
                    
                    @if(count($adjustedTimeline) > 0)
                    <div class="tracking-timeline">
                        @foreach($adjustedTimeline as $index => $event)
                        @php
                            $isCompleted = $index < count($adjustedTimeline) - 1;
                            $isCurrent = $index == count($adjustedTimeline) - 1;
                        @endphp
                        
                        <div class="timeline-item 
                            @if($isCompleted) completed 
                            @elseif($isCurrent) current 
                            @endif">
                            
                            <div class="timeline-status">
                                {{ $event['status'] }}
                                @if($isCurrent)
                                <span class="badge bg-primary ms-2">Sekarang</span>
                                @endif
                            </div>
                            
                            <div class="text-muted small mb-1">
                                <i class="bi bi-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($event['timestamp'])->format('d M Y H:i') }}
                            </div>
                            
                            <div class="text-muted small">
                                <i class="bi bi-geo-alt me-1"></i>
                                {{ $event['location'] }}
                            </div>
                            
                            <div class="mt-1">
                                <small class="text-muted">{{ $event['description'] }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted">Belum ada informasi pelacakan tersedia.</p>
                    @endif
                </div>

                @if($orderData->status === 'delivered' || $orderData->status === 'shipped')
                <div class="tracking-card">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        Pesanan ini sedang dalam pengiriman. Jika Anda belum menerima pesanan ini dalam waktu yang ditentukan, silakan laporkan:
                        <button type="button" class="btn btn-link p-0 ms-2 text-decoration-underline" onclick="reportUndeliveredOrder('{{ $orderData->order_number }}')">
                            Laporkan Barang Belum Diterima
                        </button>
                    </div>
                </div>
                @endif

                <div class="tracking-card order-summary">
                    <h4 class="mb-3">Ringkasan Pesanan</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Total Harga:</strong></p>
                            <p class="mb-0">Rp {{ number_format($orderData->total_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Ekspedisi:</strong></p>
                            <p class="mb-0">{{ $orderData->shipping_courier ?? 'Tidak Ditentukan' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showNotification(message, type = 'info') {
            // Hapus notifikasi sebelumnya jika ada
            const existingNotification = document.querySelector('.notification');
            if (existingNotification) {
                existingNotification.remove();
            }

            // Buat elemen notifikasi
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;

            // Tambahkan ke body
            document.body.appendChild(notification);

            // Tampilkan notifikasi
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);

            // Hapus notifikasi setelah 5 detik
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }

        function reportUndeliveredOrder(orderNumber) {
            if (confirm('Apakah Anda yakin ingin melaporkan bahwa pesanan ini belum diterima?')) {
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
                        showNotification('Laporan berhasil dikirim. Status pesanan telah diperbarui.', 'success');
                        setTimeout(() => {
                            location.reload(); // Refresh halaman untuk menampilkan status terbaru
                        }, 1500);
                    } else {
                        showNotification('Gagal mengirim laporan: ' + (data.message || 'Terjadi kesalahan'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan saat mengirim laporan', 'error');
                });
            }
        }
    </script>
</body>
</html>
