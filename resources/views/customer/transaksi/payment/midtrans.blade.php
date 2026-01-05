@extends('layouts.app')

@section('title', 'Pembayaran Midtrans')

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
        <h1>Pembayaran Midtrans</h1>
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
          <!-- Informasi Pembayaran -->
          <section class="payment-info">
            <h2>Detail Pembayaran</h2>

            <div class="payment-details">
              <div class="detail-card">
                <div class="detail-item">
                  <span class="label">Jumlah Pembayaran</span>
                  <span class="value">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>

                <div class="detail-item">
                  <span class="label">Nomor Pesanan</span>
                  <span class="value">{{ $order->order_number }}</span>
                </div>

                <div class="detail-item">
                  <span class="label">Status Pembayaran</span>
                  <span class="value">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                </div>
              </div>
            </div>
          </section>

          <!-- Pembayaran Midtrans -->
          <section class="midtrans-payment-section">
            <h3>Metode Pembayaran</h3>
            <div class="payment-methods-grid">
              <div class="payment-method" data-method="credit_card">
                <div class="method-icon">
                  <i class="fas fa-credit-card"></i>
                </div>
                <div class="method-name">Kartu Kredit</div>
              </div>
              
              <div class="payment-method" data-method="gopay">
                <div class="method-icon">
                  <i class="fas fa-wallet"></i>
                </div>
                <div class="method-name">GoPay</div>
              </div>
              
              <div class="payment-method" data-method="shopeepay">
                <div class="method-icon">
                  <i class="fas fa-mobile-alt"></i>
                </div>
                <div class="method-name">ShopeePay</div>
              </div>
              
              <div class="payment-method" data-method="ovo">
                <div class="method-icon">
                  <i class="fas fa-phone"></i>
                </div>
                <div class="method-name">OVO</div>
              </div>
              
              <div class="payment-method" data-method="danacita">
                <div class="method-icon">
                  <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="method-name">Dana Cita</div>
              </div>
              
              <div class="payment-method" data-method="bank_transfer">
                <div class="method-icon">
                  <i class="fas fa-university"></i>
                </div>
                <div class="method-name">Transfer Bank</div>
              </div>
            </div>
          </section>

          <!-- Tombol Bayar -->
          <section class="payment-action">
            <button class="btn btn-primary btn-pay" id="pay-button">
              <i class="fas fa-shopping-cart"></i> Bayar Sekarang
            </button>
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

            <div class="payment-security">
              <i class="fas fa-shield-alt"></i>
              <span>Dilindungi oleh Midtrans</span>
            </div>
          </section>
        </div>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <!-- Midtrans Snap Script -->
  <script src="https://app.sandbox.midtrans.com/snap/snap.js" 
          data-client-key="{{ config('midtrans.client_key') }}"></script>
  
  <script>
    document.getElementById('pay-button').addEventListener('click', function() {
      // Ambil snap token dari server
      fetch('{{ route("payment.process") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          order_number: '{{ $order->order_number }}',
          payment_method: 'midtrans'
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Buka Snap popup
          snap.pay(data.snap_token, {
            onSuccess: function(result) {
              /* handle success */
              console.log(result);
              showNotification('success', 'Pembayaran berhasil! Pesanan akan segera diproses.');
              setTimeout(() => {
                window.location.href = '{{ route("cust.welcome") }}';
              }, 2000);
            },
            onPending: function(result) {
              /* handle pending */
              console.log(result);
              showNotification('info', 'Pembayaran sedang diproses. Kami akan mengirimkan notifikasi setelah pembayaran diverifikasi.');
              setTimeout(() => {
                window.location.href = '{{ route("cust.welcome") }}';
              }, 2000);
            },
            onError: function(result) {
              /* handle error */
              console.log(result);
              showNotification('error', 'Pembayaran gagal. Silakan coba lagi.');
            },
            onClose: function() {
              /* handle close */
              showNotification('warning', 'Pembayaran dibatalkan. Silakan lanjutkan pembayaran kapan saja.');
            }
          });
        } else {
          showNotification('error', data.message || 'Terjadi kesalahan saat memproses pembayaran');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
      });
    });

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
          <div class="notification-icon">${type === 'success' ? '✓' : type === 'error' ? '✕' : '⚠'}</div>
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

  <link rel="stylesheet" href="{{ asset('css/customer/transaksi/payment/midtrans.css') }}">
@endpush