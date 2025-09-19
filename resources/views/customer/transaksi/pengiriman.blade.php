@extends('layouts.app')

@section('title', 'Pengiriman')

@section('header')
  @include('components.header_compact')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/transaksi/pengiriman.css') }}"/>
@endpush

@section('content')
  <main class="pengiriman-page">
    <div class="container">
      <div class="page-header">
        <h1>Pengiriman</h1>
        <div class="progress-steps">
          <div class="step">
            <span class="step-number">1</span>
            <span class="step-label">Alamat</span>
          </div>
          <div class="step active">
            <span class="step-number">2</span>
            <span class="step-label">Pengiriman</span>
          </div>
          <div class="step">
            <span class="step-number">3</span>
            <span class="step-label">Pembayaran</span>
          </div>
        </div>
      </div>

      <div class="pengiriman-content">
        <div class="main-content">
          <!-- Alamat Pengiriman -->
          <section class="shipping-address-summary">
            <h2>Alamat Pengiriman</h2>
            <div class="address-card selected">
              <div class="address-header">
                <h3>Rumah</h3>
                <span class="badge primary">Utama</span>
              </div>
              <p class="address-detail">Andi Saputra<br>Jl. Anggrek No. 12, Bandung<br>0812-3456-7890</p>
            </div>
          </section>

          <!-- Metode Pengiriman -->
          <section class="shipping-methods">
            <h2>Metode Pengiriman</h2>
            <div class="shipping-options">
              <div class="shipping-option selected">
                <div class="option-header">
                  <div class="option-info">
                    <h3>Reguler</h3>
                    <p>3-5 hari kerja</p>
                  </div>
                  <div class="option-price">Rp9.000</div>
                </div>
                <p class="option-description">Layanan pengiriman reguler dengan asuransi barang.</p>
              </div>

              <div class="shipping-option">
                <div class="option-header">
                  <div class="option-info">
                    <h3>Kilat</h3>
                    <p>1-2 hari kerja</p>
                  </div>
                  <div class="option-price">Rp15.000</div>
                </div>
                <p class="option-description">Layanan pengiriman cepat dengan prioritas pengemasan.</p>
              </div>

              <div class="shipping-option">
                <div class="option-header">
                  <div class="option-info">
                    <h3>Same Day</h3>
                    <p>Hari ini juga</p>
                  </div>
                  <div class="option-price">Rp25.000</div>
                </div>
                <p class="option-description">Pengiriman dalam hari yang sama dengan kurir khusus.</p>
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

            <button class="btn btn-primary btn-lanjut-pembayaran">
              Lanjut ke Pembayaran
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
      // Shipping option selection
      const shippingOptions = document.querySelectorAll('.shipping-option');
      shippingOptions.forEach(option => {
        option.addEventListener('click', function() {
          shippingOptions.forEach(opt => opt.classList.remove('selected'));
          this.classList.add('selected');
        });
      });

      // Lanjut ke pembayaran button
      const paymentBtn = document.querySelector('.btn-lanjut-pembayaran');
      if (paymentBtn) {
        paymentBtn.addEventListener('click', function() {
          // Redirect to payment page
          window.location.href = '{{ route("cust.pembayaran") }}';
        });
      }
    });
  </script>
@endpush