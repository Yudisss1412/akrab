@extends('layouts.app')

@section('title', 'Konfirmasi Pembayaran - E-wallet')

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
        <h1>Konfirmasi Pembayaran</h1>
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
            <h2>Pembayaran E-wallet</h2>
            
            <div class="payment-details">
              <div class="detail-card">
                <div class="detail-item">
                  <span class="label">Jumlah Pembayaran</span>
                  <span class="value">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                
                @php
                  $seller = $order->items->first() ? ($order->items->first()->product ? $order->items->first()->product->seller : null) : null;
                @endphp
                <div class="detail-item">
                  <span class="label">Ke Nomor</span>
                  <div class="wallet-info">
                    <span class="wallet-number">{{ $seller ? $seller->phone : 'Tidak tersedia' }}</span>
                    <small class="text-muted">a.n. {{ $seller ? ($seller->account_holder_name ?: $seller->store_name) : 'Nama Tidak Tersedia' }}</small>
                  </div>
                </div>
                
                <div class="detail-item">
                  <span class="label">Batas Waktu Pembayaran</span>
                  <span class="value">{{ \Carbon\Carbon::now()->addHours(24)->format('d M Y H:i') }}</span>
                </div>
              </div>
            </div>
          </section>

          <!-- QR Code -->
          <section class="qr-section">
            <h3>Kode QR Pembayaran</h3>
            <div class="qr-container">
              <div class="qr-placeholder">
                <svg width="200" height="200" viewBox="0 0 200 200">
                  <rect x="10" y="10" width="180" height="180" fill="#f0f0f0"/>
                  <rect x="20" y="20" width="20" height="20" fill="#000"/>
                  <rect x="160" y="20" width="20" height="20" fill="#000"/>
                  <rect x="20" y="160" width="20" height="20" fill="#000"/>
                  <rect x="40" y="40" width="10" height="10" fill="#000"/>
                  <rect x="60" y="40" width="10" height="10" fill="#000"/>
                  <rect x="80" y="40" width="10" height="10" fill="#000"/>
                  <rect x="100" y="40" width="10" height="10" fill="#000"/>
                  <rect x="120" y="40" width="10" height="10" fill="#000"/>
                  <rect x="140" y="40" width="10" height="10" fill="#000"/>
                  <rect x="40" y="60" width="10" height="10" fill="#000"/>
                  <rect x="80" y="60" width="10" height="10" fill="#000"/>
                  <rect x="100" y="60" width="10" height="10" fill="#000"/>
                  <rect x="140" y="60" width="10" height="10" fill="#000"/>
                  <rect x="40" y="80" width="10" height="10" fill="#000"/>
                  <rect x="140" y="80" width="10" height="10" fill="#000"/>
                  <rect x="40" y="100" width="10" height="10" fill="#000"/>
                  <rect x="80" y="100" width="10" height="10" fill="#000"/>
                  <rect x="100" y="100" width="10" height="10" fill="#000"/>
                  <rect x="120" y="100" width="10" height="10" fill="#000"/>
                  <rect x="140" y="100" width="10" height="10" fill="#000"/>
                  <rect x="60" y="120" width="10" height="10" fill="#000"/>
                  <rect x="80" y="120" width="10" height="10" fill="#000"/>
                  <rect x="100" y="120" width="10" height="10" fill="#000"/>
                  <rect x="120" y="120" width="10" height="10" fill="#000"/>
                  <rect x="140" y="120" width="10" height="10" fill="#000"/>
                  <rect x="40" y="140" width="10" height="10" fill="#000"/>
                  <rect x="60" y="140" width="10" height="10" fill="#000"/>
                  <rect x="80" y="140" width="10" height="10" fill="#000"/>
                  <rect x="100" y="140" width="10" height="10" fill="#000"/>
                  <rect x="120" y="140" width="10" height="10" fill="#000"/>
                  <rect x="140" y="140" width="10" height="10" fill="#000"/>
                </svg>
                <p>Scan QR Code untuk membayar</p>
              </div>
            </div>
          </section>

          <!-- Instruksi Pembayaran -->
          <section class="instructions-section">
            <h3>Instruksi Pembayaran</h3>
            <ol class="instructions-list">
              <li>Buka aplikasi {{ $seller ? ($seller->bank_name ?: 'Dompet Digital') : 'Dompet Digital' }} di smartphone Anda</li>
              <li>Pilih menu "Transfer" atau "Bayar"</li>
              <li>Masukkan nomor HP penjual: <strong>{{ $seller ? $seller->phone : 'Tidak tersedia' }}</strong></li>
              <li>Atau scan QR Code yang tersedia di halaman ini</li>
              <li>Verifikasi informasi pembayaran (jumlah, nama penerima, dll)</li>
              <li>Konfirmasi pembayaran</li>
              <li>Tunggu notifikasi bahwa pembayaran berhasil</li>
            </ol>
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

            <button class="btn btn-primary btn-upload-proof" id="uploadProofBtn" onclick="document.getElementById('proofUpload').click()">
              Saya Telah Bayar
            </button>
            <input type="file" id="proofUpload" style="display: none;" accept="image/*" onchange="handleProofUpload(this)">
            <small class="text-muted d-block mt-2">Upload bukti pembayaran dari aplikasi e-wallet Anda (OVO, DANA, GoPay, dll.)</small>
          </section>
        </div>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <script>
    function handleProofUpload(input) {
      // Validasi bahwa file dipilih
      if (!input.files || !input.files[0]) {
        showNotification('error', 'Silakan pilih file untuk diunggah.');
        return;
      }

      const file = input.files[0];

      // Validasi bahwa file tidak kosong
      if (file.size === 0) {
        showNotification('error', 'File tidak boleh kosong.');
        return;
      }

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
      .then(async response => {
        if (!response.ok) {
          const responseText = await response.text();
          console.error('HTTP Error:', response.status, response.statusText);
          console.error('Response body:', responseText);
          throw new Error(`HTTP error! status: ${response.status}, message: ${responseText}`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          showNotification('success', 'Bukti pembayaran berhasil diunggah. Pesanan Anda akan diproses setelah verifikasi.');
          setTimeout(() => {
            window.location.href = '{{ route("cust.welcome") }}';
          }, 1500);
        } else {
          showNotification('error', data.message || 'Terjadi kesalahan saat mengunggah bukti pembayaran.');
          uploadBtn.disabled = false;
          uploadBtn.innerHTML = 'Saya Telah Bayar';
        }
      })
      .catch(error => {
        console.error('Error during fetch:', error);
        // Tampilkan pesan error yang lebih informatif
        let errorMessage = 'Terjadi kesalahan saat mengunggah bukti pembayaran.';
        if (error.message.includes('HTTP error!')) {
          errorMessage = `Server error: ${error.message}`;
        } else if (error.message.includes('NetworkError')) {
          errorMessage = 'Koneksi jaringan error. Periksa koneksi internet Anda.';
        }
        showNotification('error', errorMessage + ' Silakan coba lagi.');
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = 'Saya Telah Bayar';
      });
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