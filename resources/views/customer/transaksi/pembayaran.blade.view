@extends('layouts.app')

@section('title', 'Pembayaran')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/transaksi/pembayaran.css') }}"/>
@endpush

@section('content')
  <main class="pembayaran-page">
    <div class="container">
      <div class="page-header">
        <h1>Pembayaran</h1>
        <div class="progress-steps">
          <div class="step">
            <span class="step-number">1</span>
            <span class="step-label">Alamat</span>
          </div>
          <div class="step">
            <span class="step-number">2</span>
            <span class="step-label">Pengiriman</span>
          </div>
          <div class="step active">
            <span class="step-number">3</span>
            <span class="step-label">Pembayaran</span>
          </div>
        </div>
      </div>

      <div class="pembayaran-content">
        <div class="main-content">
          <!-- Ringkasan Pesanan -->
          <section class="order-summary">
            <h2>Ringkasan Pesanan</h2>
            <div class="order-items">
              <div class="order-item">
                <img src="https://picsum.photos/seed/cup/64/64" alt="Produk" class="item-image">
                <div class="item-details">
                  <h3 class="item-name">Hard Case Premium Hybrid Clear</h3>
                  <p class="item-variant">Jet Black · iPhone 13</p>
                  <p class="item-price">Rp12.000 × 1</p>
                </div>
                <div class="item-total">Rp12.000</div>
              </div>

              <div class="order-item">
                <img src="https://picsum.photos/seed/charger/64/64" alt="Produk" class="item-image">
                <div class="item-details">
                  <h3 class="item-name">Fast Charging Adapter 20W</h3>
                  <p class="item-variant">Putih</p>
                  <p class="item-price">Rp45.000 × 1</p>
                </div>
                <div class="item-total">Rp45.000</div>
              </div>
            </div>
          </section>

          <!-- Detail Pengiriman -->
          <section class="shipping-details">
            <h2>Detail Pengiriman</h2>
            <div class="shipping-info">
              <div class="info-row">
                <span class="label">Alamat Pengiriman</span>
                <span class="value">Andi Saputra, Jl. Anggrek No. 12, Bandung, 0812-3456-7890</span>
              </div>
              <div class="info-row">
                <span class="label">Metode Pengiriman</span>
                <span class="value">Reguler (3-5 hari kerja)</span>
              </div>
            </div>
          </section>

          <!-- Metode Pembayaran -->
          <section class="payment-methods">
            <h2>Metode Pembayaran</h2>
            <div class="payment-options">
              <div class="payment-option">
                <input type="radio" id="bankTransfer" name="paymentMethod" value="bank_transfer" checked>
                <label for="bankTransfer" class="option-content">
                  <div class="option-header">
                    <div class="option-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M3 7V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7Z" stroke="#006E5C" stroke-width="2"/>
                        <path d="M3 10H21" stroke="#006E5C" stroke-width="2"/>
                        <path d="M8 14H10" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                    </div>
                    <div class="option-text">
                      <h3>Transfer Bank</h3>
                      <p>Bayar via rekening bank</p>
                    </div>
                  </div>
                </label>
              </div>

              <div class="payment-option">
                <input type="radio" id="eWallet" name="paymentMethod" value="e_wallet">
                <label for="eWallet" class="option-content">
                  <div class="option-header">
                    <div class="option-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="9" stroke="#006E5C" stroke-width="2"/>
                        <path d="M12 8V12L15 14" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                    </div>
                    <div class="option-text">
                      <h3>Dompet Digital</h3>
                      <p>OVO, GoPay, DANA, dll.</p>
                    </div>
                  </div>
                </label>
              </div>

              <div class="payment-option">
                <input type="radio" id="cod" name="paymentMethod" value="cod">
                <label for="cod" class="option-content">
                  <div class="option-header">
                    <div class="option-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#006E5C" stroke-width="2"/>
                        <path d="M12 16V12" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                        <path d="M12 8H12.01" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                    </div>
                    <div class="option-text">
                      <h3>Cash on Delivery</h3>
                      <p>Bayar saat barang diterima</p>
                    </div>
                  </div>
                </label>
              </div>
            </div>
          </section>
        </div>

        <div class="sidebar">
          <!-- Ringkasan Pembayaran -->
          <section class="payment-summary">
            <h2>Ringkasan Pembayaran</h2>
            <div class="summary-details">
              <div class="summary-row">
                <span>Subtotal (2 produk)</span>
                <span>Rp57.000</span>
              </div>
              <div class="summary-row">
                <span>Ongkos Kirim</span>
                <span>Rp9.000</span>
              </div>
              <div class="summary-row">
                <span>Asuransi Pengiriman</span>
                <span>Rp1.500</span>
              </div>
              <div class="summary-row discount">
                <span>Diskon</span>
                <span>-Rp5.000</span>
              </div>
              <div class="summary-divider"></div>
              <div class="summary-row total">
                <span>Total</span>
                <span class="total-amount">Rp62.500</span>
              </div>
            </div>

            <button class="btn btn-primary btn-bayar-sekarang">
              Bayar Sekarang
            </button>
          </section>
        </div>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Payment method selection
      const paymentOptions = document.querySelectorAll('.payment-option');
      paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
          const radio = this.querySelector('input[type="radio"]');
          radio.checked = true;
        });
      });

      // Bayar sekarang button
      const payBtn = document.querySelector('.btn-bayar-sekarang');
      if (payBtn) {
        payBtn.addEventListener('click', function() {
          // Get selected payment method
          const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
          
          // Show confirmation based on payment method
          let message = '';
          if (selectedMethod === 'bank_transfer') {
            message = 'Pesanan berhasil dibuat! Silakan transfer ke rekening yang tersedia.';
          } else if (selectedMethod === 'e_wallet') {
            message = 'Pesanan berhasil dibuat! Silakan bayar melalui dompet digital Anda.';
          } else {
            message = 'Pesanan berhasil dibuat! Siapkan uang tunai saat kurir tiba.';
          }
          
          alert(message);
          // Redirect to invoice page
          window.location.href = '{{ route("invoice") }}';
        });
      }
    });
  </script>
@endpush