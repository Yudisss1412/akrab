@extends('layouts.app')

@section('title', 'Keranjang Belanja — AKRAB')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/keranjang.css') }}" />
@endpush

@section('content')
  <main class="cart-page shell">
    <div class="container">
      <!-- Header Halaman -->
      <div class="page-header">
        <h1 class="page-title">Keranjang Belanja</h1>
        <a href="{{ route('kategori.kuliner') }}" class="continue-shopping-link">
          ← Lanjut Belanja
        </a>
      </div>
      
      <!-- Layout Dua Kolom -->
      <div class="cart-layout">
        <!-- Kolom Kiri: Daftar Produk -->
        <div class="cart-products">
          <!-- Kartu Produk -->
          <div class="products-card">
            <!-- Tabel Produk -->
            <table class="cart-table">
              <thead>
                <tr>
                  <th class="check-col">
                    <input type="checkbox" id="selectAllTop" class="item-check">
                  </th>
                  <th>Produk</th>
                  <th class="price-col">Harga Satuan</th>
                  <th class="qty-col">Jumlah</th>
                  <th class="subtotal-col">Subtotal</th>
                  <th class="action-col">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <!-- Produk 1 -->
                <tr>
                  <td class="check-col" data-label="Pilih">
                    <div class="check">
                      <input type="checkbox" class="item-check" checked>
                    </div>
                  </td>
                  <td class="product-col" data-label="Produk">
                    <div class="product-thumb">
                      <img src="{{ asset('src/CangkirKeramik1.png') }}" alt="Cangkir Keramik">
                    </div>
                    <div class="product-info">
                      <h3 class="product-name">Cangkir Keramik</h3>
                      <div class="product-sku">SKU: CK-250ml</div>
                    </div>
                  </td>
                  <td class="price-col" data-label="Harga">Rp 45.000</td>
                  <td class="qty-col" data-label="Jumlah">
                    <button class="qty-btn minus">-</button>
                    <input type="number" class="qty-input" value="1" min="1" max="99">
                    <button class="qty-btn plus">+</button>
                  </td>
                  <td class="subtotal-col" data-label="Subtotal">Rp 45.000</td>
                  <td class="action-col" data-label="Aksi">
                    <button class="delete-btn">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                
                <!-- Produk 2 -->
                <tr>
                  <td class="check-col" data-label="Pilih">
                    <div class="check">
                      <input type="checkbox" class="item-check" checked>
                    </div>
                  </td>
                  <td class="product-col" data-label="Produk">
                    <div class="product-thumb">
                      <img src="{{ asset('src/PiringKayu.png') }}" alt="Piring Kayu">
                    </div>
                    <div class="product-info">
                      <h3 class="product-name">Piring Kayu</h3>
                      <div class="product-sku">SKU: PK-18cm</div>
                    </div>
                  </td>
                  <td class="price-col" data-label="Harga">Rp 75.000</td>
                  <td class="qty-col" data-label="Jumlah">
                    <button class="qty-btn minus">-</button>
                    <input type="number" class="qty-input" value="1" min="1" max="99">
                    <button class="qty-btn plus">+</button>
                  </td>
                  <td class="subtotal-col" data-label="Subtotal">Rp 75.000</td>
                  <td class="action-col" data-label="Aksi">
                    <button class="delete-btn">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Kolom Kanan: Ringkasan Belanja -->
        <div class="cart-summary">
          <div class="summary-card">
            <h2>Ringkasan Belanja</h2>
            
            <div class="summary-details">
              <div class="summary-row">
                <span>Subtotal (<span id="subtotal-count">2</span> produk)</span>
                <span id="cart-subtotal">Rp 120.000</span>
              </div>
              <div class="summary-row">
                <span>Diskon</span>
                <span>Rp 0</span>
              </div>
              <div class="summary-row">
                <span>Estimasi Ongkos Kirim</span>
                <span>Gratis</span>
              </div>
            </div>
            
            <div class="summary-total">
              <span>Total</span>
              <span id="cartTotal">Rp 120.000</span>
            </div>
            
            <button class="btn-checkout">
              Lanjut ke Pembayaran
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <script src="{{ asset('js/customer/keranjang.js') }}"></script>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection