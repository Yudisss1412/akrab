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

    .notification-info {
      border-left-color: #17a2b8;
      background-color: #f8fdff;
      color: #0c5460;
    }

    .notification-warning {
      border-left-color: #ffc107;
      background-color: #fffdf8;
      color: #856404;
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

    .notification-info .notification-icon {
      background-color: #d1ecf1;
      color: #17a2b8;
    }

    .notification-warning .notification-icon {
      background-color: #fff3cd;
      color: #856404;
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

    .payment-methods-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
      gap: 1rem;
      margin: 1rem 0;
    }

    .payment-method {
      border: 2px solid #e9ecef;
      border-radius: 8px;
      padding: 1rem;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .payment-method:hover {
      border-color: #006E5C;
      background-color: #f0fdfa;
    }

    .payment-method.selected {
      border-color: #006E5C;
      background-color: #e8f4f1;
    }

    .method-icon {
      font-size: 2rem;
      margin-bottom: 0.5rem;
      color: #006E5C;
    }

    .btn-pay {
      width: 100%;
      padding: 1rem;
      font-size: 1.1rem;
      font-weight: bold;
    }
  </style>
@endpush