@extends('layouts.app')

@section('title', 'Checkout')

@section('header')
  @include('components.header_compact')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/transaksi/checkout.css') }}"/>
@endpush

@section('content')
  <main class="checkout-page">
    <div class="container">
      <div class="page-header">
        <h1>Checkout</h1>
        <div class="progress-steps">
          <div class="step active">
            <span class="step-number">1</span>
            <span class="step-label">Alamat</span>
          </div>
          <div class="step">
            <span class="step-number">2</span>
            <span class="step-label">Pengiriman</span>
          </div>
          <div class="step">
            <span class="step-number">3</span>
            <span class="step-label">Pembayaran</span>
          </div>
        </div>
      </div>

      <div class="checkout-content">
        <div class="main-content">
          <!-- Alamat Pengiriman -->
          <section class="shipping-address">
            <h2>Alamat Pengiriman</h2>
            <div class="address-list">
              <div class="address-card selected">
                <div class="address-header">
                  <h3>Rumah</h3>
                  <span class="badge primary">Utama</span>
                </div>
                <p class="address-detail">Andi Saputra<br>Jl. Anggrek No. 12, Bandung<br>0812-3456-7890</p>
                <div class="address-actions">
                  <button class="btn btn-secondary">Ubah</button>
                </div>
              </div>

              <div class="address-card">
                <div class="address-header">
                  <h3>Kantor</h3>
                </div>
                <p class="address-detail">PT. Maju Bersama<br>Jl. Melati No. 45, Jakarta<br>021-1234567</p>
                <div class="address-actions">
                  <button class="btn btn-secondary">Gunakan</button>
                  <button class="btn btn-ghost">Ubah</button>
                </div>
              </div>

              <button class="btn btn-add-address">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Alamat Baru
              </button>
            </div>
          </section>

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

            <div class="order-notes">
              <label for="orderNotes">Catatan untuk Penjual (Opsional)</label>
              <textarea id="orderNotes" placeholder="Contoh: Warna biru dong, terima kasih"></textarea>
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

            <button class="btn btn-primary btn-checkout">
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
      // Checkout button
      const checkoutBtn = document.querySelector('.btn-checkout');
      if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
          // Simulasi proses checkout
          alert('Pesanan berhasil dibuat! Mengarahkan ke halaman pembayaran...');
          // Redirect ke halaman pembayaran
          window.location.href = '#';
        });
      }

      // Address selection
      const addressCards = document.querySelectorAll('.address-card');
      addressCards.forEach(card => {
        card.addEventListener('click', function(e) {
          if (!e.target.closest('.btn')) {
            addressCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
          }
        });
      });
    });
  </script>
@endpush