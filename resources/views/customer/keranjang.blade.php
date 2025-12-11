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
        <a href="{{ url()->previous() ?: route('cust.welcome') }}" class="continue-shopping-link">
          ← Lanjut Belanja
        </a>
      </div>
      
      <!-- Layout Dua Kolom -->
      <div class="cart-layout">
        <!-- Kolom Kiri: Daftar Produk -->
        <div class="cart-products">
          <!-- Kartu Produk -->
          <div class="products-card">
            <!-- Tabel Produk - Desktop Version -->
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
                </tr>
              </thead>
              <tbody>
                @forelse($cartItems as $item)
                <tr data-item-id="{{ $item['id'] ?? $item->id }}">
                  <td class="check-col" data-label="Pilih">
                    <div class="check">
                      <input type="checkbox" class="item-check" checked data-item-id="{{ $item['id'] ?? $item->id }}">
                    </div>
                  </td>
                  <td class="product-col" data-label="Produk">
                    <div class="product-thumb-container">
                      <div class="product-thumb">
                        <img src="{{ ($item['product'] ?? $item->product)->main_image ? asset('storage/' . ($item['product'] ?? $item->product)->main_image) : asset('src/placeholder.png') }}" alt="{{ ($item['product'] ?? $item->product)->name }}">
                      </div>
                      <button class="delete-btn-float" data-item-id="{{ $item['id'] ?? $item->id }}">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                    <div class="product-info">
                      <h3 class="product-name">{{ ($item['product'] ?? $item->product)->name }}</h3>
                      <div class="product-sku">SKU: {{ ($item['product'] ?? $item->product)->sku ?? 'N/A' }}</div>
                      @if($item['product_variant'] ?? $item->productVariant ?? null)
                      <div class="product-variant">Varian: {{ ($item['product_variant'] ?? $item->productVariant)->name }}</div>
                      @endif
                    </div>
                  </td>
                  <td class="price-col" data-label="Harga">
                    @php
                      $basePrice = ($item['product'] ?? $item->product)->price;
                      $variantPrice = ($item['product_variant'] ?? $item->productVariant ?? null) ? ($item['product_variant'] ?? $item->productVariant)->additional_price : 0;
                      $totalPrice = $basePrice + $variantPrice;
                    @endphp
                    Rp {{ number_format($totalPrice, 0, ',', '.') }}
                  </td>
                  <td class="qty-col" data-label="Jumlah">
                    <button class="qty-btn minus" data-item-id="{{ $item['id'] ?? $item->id }}">-</button>
                    <input type="number" class="qty-input" value="{{ $item['quantity'] ?? $item->quantity }}" min="0" max="99" data-item-id="{{ $item['id'] ?? $item->id }}">
                    <button class="qty-btn plus" data-item-id="{{ $item['id'] ?? $item->id }}">+</button>
                  </td>
                  <td class="subtotal-col" data-label="Subtotal">Rp {{ number_format($totalPrice * ($item['quantity'] ?? $item->quantity), 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center">
                    <p>Keranjang Anda kosong. <a href="{{ url()->previous() ?: route('cust.welcome') }}">Lanjutkan belanja</a></p>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>

            <!-- Mobile Product Cards - Mobile Version -->
            <div class="mobile-products-list">
              @forelse($cartItems as $item)
              <div class="product-item-card" data-item-id="{{ $item['id'] ?? $item->id }}">
                <div class="product-item-thumb-container">
                  <div class="product-item-thumb">
                    <img src="{{ ($item['product'] ?? $item->product)->main_image ? asset('storage/' . ($item['product'] ?? $item->product)->main_image) : asset('src/placeholder.png') }}" alt="{{ ($item['product'] ?? $item->product)->name }}">
                  </div>
                  <button class="delete-btn-mobile" data-item-id="{{ $item['id'] ?? $item->id }}">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
                <div class="product-item-details">
                  <h3 class="product-item-name">{{ ($item['product'] ?? $item->product)->name }}</h3>
                  <div class="product-item-price">
                    @php
                      $basePrice = ($item['product'] ?? $item->product)->price;
                      $variantPrice = ($item['product_variant'] ?? $item->productVariant ?? null) ? ($item['product_variant'] ?? $item->productVariant)->additional_price : 0;
                      $itemPrice = $basePrice + $variantPrice;
                    @endphp
                    Rp {{ number_format($itemPrice, 0, ',', '.') }}
                  </div>
                  <div class="product-item-qty">
                    <div class="qty-controls">
                      <button class="qty-btn minus" data-item-id="{{ $item['id'] ?? $item->id }}">-</button>
                      <input type="number" class="qty-input" value="{{ $item['quantity'] ?? $item->quantity }}" min="0" max="99" data-item-id="{{ $item['id'] ?? $item->id }}">
                      <button class="qty-btn plus" data-item-id="{{ $item['id'] ?? $item->id }}">+</button>
                    </div>
                  </div>
                </div>
              </div>
              @empty
              <div class="text-center">
                <p>Keranjang Anda kosong. <a href="{{ url()->previous() ?: route('cust.welcome') }}">Lanjutkan belanja</a></p>
              </div>
              @endforelse
            </div>
          </div>
        </div>

        <!-- Kolom Kanan: Ringkasan Belanja -->
        <div class="cart-summary">
          <div class="summary-card">
            <h2>Ringkasan Belanja</h2>

            <div class="summary-details-container">
              <div class="summary-details">
                <div class="summary-row">
                  <span>Subtotal (<span id="subtotal-count">{{ $cartItems->count() }}</span> produk)</span>
                  <span id="cart-subtotal">Rp {{ number_format($cartSubtotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                  <span>Diskon</span>
                  <span>- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                  <span>Total Berat</span>
                  <span>{{ number_format($totalWeight, 2, ',', '.') }} kg</span>
                </div>
              </div>

              <div class="summary-total">
                <span>Total</span>
                <span id="cartTotal">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
              </div>
            </div>

            <a href="{{ route('checkout') }}" class="btn-checkout">
              Lanjut ke Pembayaran
            </a>
          </div>
        </div>
      </div>

      <!-- Sticky Checkout Bar for Mobile -->
      <div class="sticky-checkout-bar">
        <div class="checkout-total">Total: Rp <span id="mobile-cartTotal">{{ number_format($cartTotal, 0, ',', '.') }}</span></div>
        <a href="{{ route('checkout') }}" class="checkout-btn-mobile">
          Checkout
        </a>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <script src="{{ asset('js/customer/keranjang.js') }}?v=22"></script>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection