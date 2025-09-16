@extends('layouts.customer')

@section('title', 'Keranjang Belanja — AKRAB')

{{-- NAVBAR: pakai versi compact tanpa searchbar --}}
@section('navbar')
  @include('partials.navbar_compact', ['cartCount' => $cartCount ?? 0])
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/keranjang.css') }}" />
  <style>
    .shell{
      max-width: none !important;   /* <— dari 1200px jadi tidak dibatasi */
      width: 100% !important;
      /* Pakai 3 nilai: top | right/left | bottom (spasi bawah 96px untuk aman dari fixed bar) */
      padding: 24px clamp(16px,4vw,32px) 96px !important;
      margin: 0 !important;
    }

    /* 2) Buka lebar konten cart & inner bar checkout agar full width juga */
    .cart-page .container,
    .cart-bar__inner.container{
      width: 100%;
      max-width: none;              /* <— hilangkan batas lebar */
      margin: 0;                    /* jangan center fixed width */
      padding-inline: clamp(16px,4vw,32px);
    }

    /* 3) Opsional: biar bar checkout benar-benar nempel tepi layar */
    .cart-bar{
      left: 0;
      right: 0;
      width: 100%;
    }

    /* 4) (Opsional) Kalau mau tetap ada batas di layar super lebar,
          ganti max-width:none di atas jadi:
          max-width: 1600px; margin-inline:auto;
       tapi sesuai permintaan: full lebar layar. */
  </style>
@endpush

@section('content')
  <!-- Modal: belum memilih produk -->
  <div id="emptyModal" class="modal-overlay" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="emptyTitle">
      <p id="emptyTitle" class="modal-title">Anda belum memilih produk untuk checkout</p>
      <div class="modal-actions">
        <button id="emptyOk" class="modal-ok" type="button">OK</button>
      </div>
    </div>
  </div>

  <!-- ===== MAIN (Cart) ===== -->
  <div class="cart-page">
    <div class="container">
      <div class="page-head">
        <h1 class="page-title">Keranjang Belanja</h1>
        <div class="bulk-actions">
          <label class="check">
            <input id="selectAllTop" type="checkbox" />
            <span>Pilih Semua</span>
          </label>
          <a id="bulkDelete" class="link">Hapus</a>
        </div>
      </div>

      <!-- Header kolom -->
      <div class="table-head">
        <div class="th th-check"></div>
        <div class="th th-thumb"></div>
        <div class="th th-produk">Produk</div>
        <div class="th th-harga">Harga Satuan</div>
        <div class="th th-qty">Kuantitas</div>
        <div class="th th-total">Total Harga</div>
        <div class="th th-aksi">Aksi</div>
      </div>

      <!-- Toko -->
      <div class="shop-row">
        <span class="shop-badge">Star+</span>
        <span class="shop-name">Caseiphone.Grosir</span>
      </div>

      <!-- ====== ITEMS (dummy) ====== -->

      <!-- Item 1 -->
      <article class="cart-item" data-id="1">
        <input class="item-check" type="checkbox" aria-label="pilih" />
        <img class="thumb" src="https://via.placeholder.com/96x96" alt="produk" />
        <div class="meta">
          <h3 class="title">Hard Case Premium Hybrid Clear</h3>
          <div class="variant">Variasi: <strong>Jet Black · iPhone 13</strong></div>
          <div class="price-each mobile-only">Rp<span data-each="12000">12.000</span></div>
        </div>
        <div class="price-each desktop-only">Rp<span data-each="12000">12.000</span></div>
        <div class="qty">
          <button class="qty-btn minus" type="button">–</button>
          <input class="qty-input" type="number" min="1" value="1" inputmode="numeric" />
          <button class="qty-btn plus" type="button">+</button>
        </div>
        <div class="total">Rp<span class="line-total">12.000</span></div>
        <button class="trash" type="button" aria-label="Hapus">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M9 6V4a2 2 0 012-2h2a2 2 0 012 2v2"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
        </button>
      </article>

      <!-- Item 2 -->
      <article class="cart-item" data-id="2">
        <input class="item-check" type="checkbox" aria-label="pilih" />
        <img class="thumb" src="https://via.placeholder.com/96x96?text=Case" alt="produk" />
        <div class="meta">
          <h3 class="title">Hard Case Clear Anti Shock</h3>
          <div class="variant">Variasi: <strong>Transparent · iPhone 13</strong></div>
          <div class="price-each mobile-only">Rp<span data-each="18000">18.000</span></div>
        </div>
        <div class="price-each desktop-only">Rp<span data-each="18000">18.000</span></div>
        <div class="qty">
          <button class="qty-btn minus" type="button">–</button>
          <input class="qty-input" type="number" min="1" value="2" inputmode="numeric" />
          <button class="qty-btn plus" type="button">+</button>
        </div>
        <div class="total">Rp<span class="line-total">36.000</span></div>
        <button class="trash" type="button" aria-label="Hapus">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M9 6V4a2 2 0 012-2h2a2 2 0 012 2v2"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
        </button>
      </article>

      <!-- Item 3 -->
      <article class="cart-item" data-id="3">
        <input class="item-check" type="checkbox" aria-label="pilih" />
        <img class="thumb" src="https://via.placeholder.com/96x96?text=Charger" alt="produk" />
        <div class="meta">
          <h3 class="title">Fast Charging Adapter 20W</h3>
          <div class="variant">Warna: <strong>Putih</strong></div>
          <div class="price-each mobile-only">Rp<span data-each="45000">45.000</span></div>
        </div>
        <div class="price-each desktop-only">Rp<span data-each="45000">45.000</span></div>
        <div class="qty">
          <button class="qty-btn minus" type="button">–</button>
          <input class="qty-input" type="number" min="1" value="1" />
          <button class="qty-btn plus" type="button">+</button>
        </div>
        <div class="total">Rp<span class="line-total">45.000</span></div>
        <button class="trash" type="button" aria-label="Hapus">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M9 6V4a2 2 0 012-2h2a2 2 0 012 2v2"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
        </button>
      </article>

      <!-- Item 4 -->
      <article class="cart-item" data-id="4">
        <input class="item-check" type="checkbox" aria-label="pilih" />
        <img class="thumb" src="https://via.placeholder.com/96x96?text=Earphone" alt="produk" />
        <div class="meta">
          <h3 class="title">Bluetooth Earphone TWS V5.1</h3>
        <div class="variant">Warna: <strong>Hitam</strong></div>
          <div class="price-each mobile-only">Rp<span data-each="99000">99.000</span></div>
        </div>
        <div class="price-each desktop-only">Rp<span data-each="99000">99.000</span></div>
        <div class="qty">
          <button class="qty-btn minus" type="button">–</button>
          <input class="qty-input" type="number" min="1" value="1" />
          <button class="qty-btn plus" type="button">+</button>
        </div>
        <div class="total">Rp<span class="line-total">99.000</span></div>
        <button class="trash" type="button" aria-label="Hapus">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M9 6V4a2 2 0 012-2h2a2 2 0 012 2v2"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
        </button>
      </article>

      <!-- Item 5 -->
      <article class="cart-item" data-id="5">
        <input class="item-check" type="checkbox" aria-label="pilih" />
        <img class="thumb" src="https://via.placeholder.com/96x96?text=Cable" alt="produk" />
        <div class="meta">
          <h3 class="title">USB-C to Lightning Cable 1m</h3>
          <div class="variant">Warna: <strong>Putih</strong></div>
          <div class="price-each mobile-only">Rp<span data-each="29000">29.000</span></div>
        </div>
        <div class="price-each desktop-only">Rp<span data-each="29000">29.000</span></div>
        <div class="qty">
          <button class="qty-btn minus" type="button">–</button>
          <input class="qty-input" type="number" min="1" value="3" />
          <button class="qty-btn plus" type="button">+</button>
        </div>
        <div class="total">Rp<span class="line-total">87.000</span></div>
        <button class="trash" type="button" aria-label="Hapus">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M9 6V4a2 2 0 012-2h2a2 2 0 012 2v2"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
        </button>
      </article>

      <!-- spacer agar tidak ketutup bar fixed -->
      <div id="cartSpacer" class="cart-bottom-spacer"></div>
    </div>
  </div>

  <!-- ===== FIXED BOTTOM BAR (Checkout) ===== -->
  <div id="cartBar" class="cart-bar">
    <div class="cart-bar__inner container">
      <label class="check cart-bar__select">
        <input id="selectAllBottom" type="checkbox" />
        <span>Pilih Semua</span>
      </label>
      <div class="cart-bar__total">
        <span>Total (<span id="selectedCount">0</span> produk):</span>
        <strong>Rp <span id="subtotal">0</span></strong>
      </div>
      <button id="checkout" class="btn-checkout" type="button">Checkout</button>
    </div>
  </div>

  <div id="endSentinel"></div>
@endsection

@push('scripts')
  <script defer src="{{ asset('js/keranjang.js') }}"></script>
@endpush
