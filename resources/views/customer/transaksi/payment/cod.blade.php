@extends('layouts.app')

@section('title', 'Konfirmasi Pesanan - COD')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/transaksi/pembayaran.css') }}"/>
@endpush

@section('content')
  <main class="payment-confirmation-page">
    <div class="container">
      <div class="page-header">
        <h1>Konfirmasi Pesanan</h1>
        <div class="progress-steps">
          <div class="step completed">
            <span class="step-number">1</span>
            <span class="step-label">Alamat</span>
          </div>
          <div class="step completed">
            <span class="step-number">2</span>
            <span class="step-label">Pengiriman</span>
          </div>
          <div class="step active">
            <span class="step-number">3</span>
            <span class="step-label">Pembayaran</span>
          </div>
        </div>
      </div>

      <div class="payment-confirmation-content">
        <div class="main-content">
          <!-- Informasi Pengiriman -->
          <section class="shipping-info">
            <h2>Pengiriman</h2>
            
            <div class="shipping-details">
              <div class="detail-card">
                <div class="detail-item">
                  <span class="label">Alamat Pengiriman</span>
                  <div class="address-info">
                    <span class="recipient">{{ $order->shipping_address->recipient_name ?? 'Tidak tersedia' }}</span>
                    <span class="phone">{{ $order->shipping_address->phone ?? 'Tidak tersedia' }}</span>
                    <span class="full-address">{{ $order->shipping_address->full_address ?? 'Tidak tersedia' }}</span>
                  </div>
                </div>
                
                <div class="detail-item">
                  <span class="label">Metode Pengiriman</span>
                  <span class="value">
                    @if($order->shipping_courier == 'reguler')
                      Reguler (3-5 hari kerja)
                    @elseif($order->shipping_courier == 'express')
                      Express (1-2 hari kerja)
                    @elseif($order->shipping_courier == 'same_day')
                      Same Day (Hari yang sama)
                    @else
                      {{ ucfirst($order->shipping_courier ?? 'Tidak tersedia') }}
                    @endif
                  </span>
                </div>
                
                <div class="detail-item">
                  <span class="label">Estimasi Pengiriman</span>
                  <span class="value">
                    @if($order->shipping_courier == 'same_day')
                      Hari ini
                    @elseif($order->shipping_courier == 'express')
                      1-2 hari kerja
                    @elseif($order->shipping_courier == 'reguler')
                      3-5 hari kerja
                    @else
                      3-5 hari kerja
                    @endif
                  </span>
                </div>
              </div>
            </div>
          </section>

          <!-- Informasi Pembayaran -->
          <section class="payment-info">
            <h2>Cash on Delivery (COD)</h2>
            
            <div class="payment-details">
              <div class="detail-card">
                <div class="detail-item">
                  <span class="label">Jumlah Pembayaran</span>
                  <span class="value">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                
                <div class="detail-item">
                  <span class="label">Metode Pembayaran</span>
                  <span class="value">Cash on Delivery (Bayar saat barang diterima)</span>
                </div>
                
                <div class="detail-item">
                  <span class="label">Pembayaran</span>
                  <span class="value">Dilakukan saat barang diterima oleh penerima</span>
                </div>
              </div>
            </div>
          </section>

          <!-- Petunjuk COD -->
          <section class="instructions-section">
            <h3>Petunjuk COD</h3>
            <ul class="instructions-list">
              <li>Barang akan dikirimkan sesuai dengan estimasi waktu pengiriman</li>
              <li>Pembayaran dilakukan saat barang diterima oleh penerima</li>
              <li>Bersiapkan uang tunai sesuai dengan total pembayaran</li>
              <li>Pastikan barang dalam kondisi baik sebelum melakukan pembayaran</li>
              <li>Apabila terdapat kerusakan pada barang, Anda berhak menolak penerimaan</li>
            </ul>
          </section>
        </div>

        <div class="sidebar">
          <!-- Ringkasan Pembayaran -->
          <section class="payment-summary">
            <h2>Ringkasan Pembayaran</h2>
            <div class="summary-details">
              <div class="summary-row">
                <span>Subtotal ({{ $order->items->sum('quantity') }} produk)</span>
                <span>Rp{{ number_format($order->sub_total, 0, ',', '.') }}</span>
              </div>
              <div class="summary-row">
                <span>Ongkos Kirim</span>
                <span>Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
              </div>
              <div class="summary-row">
                <span>Asuransi Pengiriman</span>
                <span>Rp{{ number_format($order->insurance_cost, 0, ',', '.') }}</span>
              </div>
              <div class="summary-row discount">
                <span>Diskon</span>
                <span>-Rp{{ number_format($order->discount ?? 0, 0, ',', '.') }}</span>
              </div>
              <div class="summary-divider"></div>
              <div class="summary-row total">
                <span>Total</span>
                <span class="total-amount">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
              </div>
            </div>

            <button class="btn btn-primary btn-confirm-order" id="confirmOrderBtn" onclick="confirmOrder()">
              Konfirmasi Pesanan
            </button>
          </section>
        </div>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <script>
    // Fungsi untuk konfirmasi pesanan COD
    function confirmOrder() {
      const confirmBtn = document.getElementById('confirmOrderBtn');
      confirmBtn.disabled = true;
      confirmBtn.innerHTML = 'Memproses...';

      // Siapkan form data untuk proses pembayaran COD
      const formData = new FormData();
      formData.append('order_number', '{{ $order->order_number }}');
      formData.append('payment_method', 'cod');
      formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

      // Proses konfirmasi pesanan COD
      fetch('{{ route("payment.process") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          // Tampilkan notifikasi sukses
          showNotification('success', 'Pesanan berhasil dikonfirmasi! Barang akan segera diproses. Pembayaran akan dilakukan saat barang diterima.');
          // Alihkan ke dashboard pembeli seperti yang diminta
          setTimeout(() => {
            window.location.href = '{{ route("cust.welcome") }}';
          }, 2000); // Tunggu 2 detik sebelum redirect agar notifikasi bisa terbaca
        } else {
          showNotification('error', data.message || 'Terjadi kesalahan saat memproses pesanan.');
          confirmBtn.disabled = false;
          confirmBtn.innerHTML = 'Konfirmasi Pesanan';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi atau hubungi admin jika masalah terus berlanjut.');
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = 'Konfirmasi Pesanan';
      });
    }

    // Fungsi untuk upload bukti pembayaran (untuk bank transfer dan e-wallet)
    function handleProofUpload(input) {
      if (input.files && input.files[0]) {
        const file = input.files[0];

        // Validasi tipe file
        if (!file.type.match('image.*')) {
          showNotification('error', 'Silakan pilih file gambar (JPG, PNG, JPEG)');
          return;
        }

        // Validasi ukuran file (maksimal 5MB)
        if (file.size > 5 * 1024 * 1024) {
          showNotification('error', 'Ukuran file terlalu besar. Maksimal 5MB');
          return;
        }

        // Tampilkan loading state
        const uploadBtn = document.getElementById('uploadProofBtn');
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = 'Mengunggah...';

        // Siapkan form data
        const formData = new FormData();
        formData.append('order_number', '{{ $order->order_number }}');
        formData.append('proof_image', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Upload bukti pembayaran
        fetch('{{ route("payment.upload-proof") }}', {
          method: 'POST',
          body: formData
        })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          if (data.success) {
            showNotification('success', 'Bukti pembayaran berhasil diunggah. Pesanan Anda akan diproses setelah verifikasi.');
            setTimeout(() => {
              window.location.href = '{{ route("order.invoice", ["order" => $order->order_number]) }}';
            }, 1500); // Tunggu 1.5 detik sebelum redirect
          } else {
            showNotification('error', data.message || 'Terjadi kesalahan saat mengunggah bukti pembayaran.');
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = 'Saya Telah Bayar';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showNotification('error', 'Terjadi kesalahan saat mengunggah bukti pembayaran. Silakan coba lagi.');
          uploadBtn.disabled = false;
          uploadBtn.innerHTML = 'Saya Telah Bayar';
        });
      }
    }

    // Fungsi untuk menampilkan notifikasi
    function showNotification(type, message) {
      // Hapus notifikasi sebelumnya jika ada
      const existingNotification = document.getElementById('notification-toast');
      if (existingNotification) {
        existingNotification.remove();
      }

      // Buat elemen notifikasi
      const notification = document.createElement('div');
      notification.id = 'notification-toast';
      notification.className = `notification-toast notification-${type}`;
      notification.innerHTML = `
        <div class="notification-content">
          <div class="notification-icon">${type === 'success' ? '✓' : '⚠'}</div>
          <div class="notification-message">${message}</div>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">&times;</button>
      `;

      // Tambahkan ke body
      document.body.appendChild(notification);

      // Tampilkan efek
      setTimeout(() => {
        notification.classList.add('show');
      }, 10);

      // Otomatis sembunyikan setelah 5 detik
      setTimeout(() => {
        if (document.contains(notification)) { // Pastikan elemen masih ada
          notification.classList.remove('show');
          setTimeout(() => {
            if (document.contains(notification)) {
              notification.remove();
            }
          }, 300);
        }
      }, 5000);
    }
  </script>

  <style>
    .notification-toast {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) translateY(-20px);
      z-index: 9999;
      min-width: 300px;
      max-width: 400px;
      padding: 16px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      display: flex;
      align-items: center;
      justify-content: space-between;
      opacity: 0;
      transition: all 0.3s ease;
      background: white;
      border-left: 4px solid;
    }

    .notification-toast.show {
      opacity: 1;
      transform: translate(-50%, -50%) translateY(0);
    }

    .notification-success {
      border-left-color: #28a745;
      background-color: #f8fff9;
      color: #155724;
    }

    .notification-error {
      border-left-color: #dc3545;
      background-color: #fff8f8;
      color: #721c24;
    }

    .notification-content {
      display: flex;
      align-items: center;
      flex: 1;
    }

    .notification-icon {
      font-size: 20px;
      font-weight: bold;
      margin-right: 12px;
      min-width: 24px;
      text-align: center;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
    }

    .notification-success .notification-icon {
      background-color: #d4edda;
      color: #28a745;
    }

    .notification-error .notification-icon {
      background-color: #f8d7da;
      color: #dc3545;
    }

    .notification-message {
      flex: 1;
      font-size: 14px;
      line-height: 1.4;
    }

    .notification-close {
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #6c757d;
      padding: 0;
      margin-left: 12px;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .notification-close:hover {
      color: #000;
    }
  </style>
@endpush