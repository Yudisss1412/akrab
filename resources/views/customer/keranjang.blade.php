@extends('layouts.app')

@section('title', 'Keranjang Belanja — AKRAB')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/keranjang.css') }}" />
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
      position: fixed !important;    /* <— jadikan fixed */
      bottom: 0 !important;          /* <— posisiin ke bawah */
      left: 0 !important;            /* <— nempel tepi kiri */
      right: 0 !important;           /* <— nempel tepi kanan */
      width: auto !important;        /* <— lebar otomatis */
      margin: 0 !important;          /* <— hilangkan margin */
      border-radius: 0 !important;   /* <— hilangkan border radius */
      z-index: 1000 !important;      /* <— z-index diatas yang lain */
      backdrop-filter: blur(10px); 
      -webkit-backdrop-filter: blur(10px);
    }
    
    .cart-bar .cart-bar__inner{
      max-width: none !important;    /* <— inner juga jangan dibatasi */
      width: 100% !important;
      margin: 0 !important;
      padding: 14px clamp(16px,4vw,32px) !important;
    }
    
    /* Layout dua kolom untuk keranjang */
    .cart-layout {
      display: grid;
      grid-template-columns: 65% 35%;
      gap: 24px;
      margin-top: 24px;
    }
    
    @media (max-width: 992px) {
      .cart-layout {
        grid-template-columns: 1fr;
      }
    }
    
    /* Memastikan tidak ada padding yang tidak perlu */
    .cart-products, .cart-summary {
      width: 100%;
    }
    
    /* Kartu ringkasan */
    .summary-card {
      background: #fff;
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: var(--gap-md);
      box-shadow: 0 2px 10px rgba(0,0,0,.04);
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }
    
    .summary-card h2 {
      margin: 0 0 0.5rem 0;
      color: var(--primary-color-dark);
      font-size: 1.25rem;
      font-weight: 700;
      text-align: center;
      padding-bottom: 0.75rem;
      border-bottom: 1px solid var(--border-color);
    }
    
    /* Header tabel produk */
    .table-head {
      display: grid;
      grid-template-columns: 24px 96px 1fr auto auto auto 40px;
      gap: 12px;
      align-items: center;
      color: #6b7280;
      padding: 16px 20px;
      margin-bottom: 0;
      border-bottom: 1px solid var(--stroke);
      font-weight: 600;
      background-color: var(--background-color); /* Memberikan latar belakang yang berbeda */
    }
    
    .th-check {
      width: 24px;
    }
    
    .th-thumb {
      width: 96px;
    }
    
    .th {
      font-weight: 600;
      color: #6b7280;
      font-size: 14px;
    }
    
    /* Item keranjang */
    .cart-item {
      display: grid;
      grid-template-columns: 24px 96px 1fr auto auto auto 40px;
      gap: 12px;
      align-items: center;
      padding: 16px 20px;
      border-bottom: 1px solid var(--stroke);
      transition: background-color 0.2s ease;
    }
    
    .cart-item:last-child {
      border-bottom: none;
    }
    
    .cart-item:hover {
      background-color: #f9fafb;
    }
    
    .check {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .item-check {
      width: 18px;
      height: 18px;
      border: 2px solid var(--stroke);
      border-radius: 4px;
      appearance: none;
      cursor: pointer;
      position: relative;
      transition: all 0.2s ease;
    }
    
    .item-check:checked {
      background-color: var(--primary-color-dark);
      border-color: var(--primary-color-dark);
    }
    
    .item-check:checked::after {
      content: '';
      position: absolute;
      left: 5px;
      top: 1px;
      width: 4px;
      height: 8px;
      border: solid white;
      border-width: 0 2px 2px 0;
      transform: rotate(45deg);
    }
    
    .item-check:hover {
      border-color: var(--primary-color-dark);
    }
    
    .thumb {
      width: 96px;
      height: 96px;
      object-fit: cover;
      border-radius: 12px;
      border: 1px solid var(--border-color);
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      transition: all 0.2s ease;
    }
    
    .thumb:hover {
      border-color: var(--primary-color-dark);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .thumb img {
      max-width: 100%;
      max-height: 100%;
      object-fit: cover;
      border-radius: 12px;
      transition: transform 0.2s ease;
    }
    
    .thumb:hover img {
      transform: scale(1.05);
    }
    
    .meta {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    
    .title {
      margin: 0;
      font-size: 16px;
      font-weight: 600;
      color: var(--text-dark);
      line-height: 1.3;
    }
    
    .variant {
      font-size: 13px;
      color: var(--text-muted);
    }
    
    .price-each {
      font-weight: 700;
      font-size: 15px;
      color: var(--text-dark);
      min-width: 80px;
      text-align: right;
    }
    
    /* Kontrol jumlah */
    .qty {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .qty-btn {
      width: 32px;
      height: 32px;
      border: 1px solid var(--stroke);
      background: #fff;
      border-radius: 8px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      font-weight: bold;
      color: var(--text-dark);
      transition: all 0.2s ease;
      user-select: none; /* Mencegah seleksi teks saat klik */
    }
    
    .qty-btn:hover {
      background-color: var(--background-color);
      border-color: var(--primary-color-dark);
    }
    
    .qty-btn:active {
      transform: scale(0.95);
    }
    
    .qty-input {
      width: 50px;
      height: 32px;
      text-align: center;
      border: 1px solid var(--stroke);
      border-radius: 8px;
      font-weight: 600;
      font-size: 14px;
      transition: border-color 0.2s ease;
    }
    
    .qty-input:focus {
      outline: none;
      border-color: var(--primary-color-dark);
      box-shadow: 0 0 0 2px rgba(0, 110, 92, 0.2);
    }
    
    .line-total {
      font-weight: 700;
      font-size: 15px;
      color: var(--primary-color-dark);
      min-width: 90px;
      text-align: right;
    }
    
    .trash {
      background: none;
      border: none;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ef4444;
      cursor: pointer;
      transition: all 0.2s ease;
      user-select: none;
    }
    
    .trash:hover {
      background-color: #fee2e2;
      color: #dc2626;
    }
    
    .trash:active {
      transform: scale(0.95);
    }
    
    /* Detail ringkasan belanja */
    .summary-details {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      padding: 0.5rem 0;
    }
    
    .summary-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 0;
      border-bottom: 1px dashed var(--border-color);
    }
    
    .summary-row:last-child {
      border-bottom: none;
    }
    
    .summary-row span:first-child {
      color: var(--text-muted);
      font-size: 0.95rem;
    }
    
    .summary-row span:last-child {
      font-weight: 600;
      color: var(--text-dark);
      font-size: 1rem;
    }
    
    /* Total ringkasan */
    .summary-total {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 0;
      border-top: 2px solid var(--border-color);
      margin-top: 0.5rem;
    }
    
    .summary-total span:first-child {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--text-dark);
    }
    
    .summary-total span:last-child {
      font-size: 1.25rem;
      font-weight: 800;
      color: var(--primary-color-dark);
    }
    
    /* Tombol checkout */
    .btn-block {
      display: block;
      width: 100%;
      text-align: center;
    }
    
    /* Link "Lanjut Belanja" di atas */
    .continue-shopping-top {
      margin-bottom: 16px;
    }
    
    .continue-shopping-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      color: var(--primary-color-dark);
      font-weight: 500;
      transition: color 0.2s ease;
    }
    
    .continue-shopping-link:hover {
      color: #004d40;
      text-decoration: underline;
    }
    
    /* Link "Lanjut Belanja" lama dihapus */
    
    /* Tombol checkout */
    .btn-checkout {
      width: 100%;
      padding: 1rem;
      border: none;
      border-radius: var(--radius-lg);
      background: linear-gradient(90deg, var(--primary-color-dark), #10b981);
      color: #fff;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 110, 92, 0.3);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .btn-checkout:hover {
      opacity: 0.95;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0, 110, 92, 0.4);
    }
    
    .btn-checkout:active {
      transform: translateY(0);
    }
    
    .btn-checkout:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(0, 110, 92, 0.5);
    }
    
    /* Responsif */
    @media (max-width: 768px) {
      .cart-table-header, .cart-row {
        grid-template-columns: 1fr;
        text-align: left;
      }
      
      .cart-col {
        justify-content: space-between;
        padding: 4px 0;
      }
      
      .cart-col.product {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
      }
      
      .product-item {
        flex-direction: row;
        width: 100%;
      }
      
      .cart-layout {
        gap: 16px;
      }
    }
  </style>
@endpush

@section('content')
  <main class="cart-page shell">
    <!-- Toast Notification -->
    <div id="toastNotification" class="toast-notification">
      <div class="toast-content">
        <div class="toast-icon">✓</div>
        <div class="toast-body">
          <h3 id="toastTitle" class="toast-header-text">Berhasil</h3>
          <p id="toastMessage" class="toast-message">Item berhasil ditambahkan ke keranjang!</p>
          <div class="toast-actions">
            <button id="toastAction" class="toast-btn toast-btn-primary">Lihat Keranjang</button>
          </div>
        </div>
        <button id="toastClose" class="toast-close">&times;</button>
      </div>
    </div>

    <!-- Cart Content -->
    <div class="container">
      <h1>Keranjang Belanja</h1>
      
      <!-- Continue Shopping Link -->
      <div class="continue-shopping-top">
        <a href="{{ route('kategori.kuliner') }}" class="continue-shopping-link">
          <i class="bi bi-arrow-left"></i> 
          Lanjut Belanja
        </a>
      </div>
      
      <!-- Two Column Layout -->
      <div class="cart-layout">
        <!-- Left Column: Product List -->
        <div class="cart-products">
          <div class="cart-products-card">
            <!-- Product Table Header -->
            <div class="table-head">
              <div class="th-check check">
                <input type="checkbox" id="selectAllTop">
              </div>
              <div class="th-thumb"></div>
              <div class="th"></div>
              <div class="th">Harga Satuan</div>
              <div class="th">Jumlah</div>
              <div class="th">Subtotal</div>
              <div class="th-trash"></div>
            </div>
            
            <!-- Product Items Table -->
            <div class="cart-items">
              <!-- Product 1 -->
              <div class="cart-item" data-id="1">
                <div class="check">
                  <input type="checkbox" class="item-check" checked>
                </div>
                <div class="thumb">
                  <img src="{{ asset('src/CangkirKeramik1.png') }}" alt="Cangkir Keramik">
                </div>
                <div class="meta">
                  <h3 class="title">Cangkir Keramik</h3>
                  <div class="variant">SKU: CK-250ml</div>
                </div>
                <div class="price-each" data-each="45000">Rp 45.000</div>
                <div class="qty">
                  <button class="qty-btn minus">-</button>
                  <input type="number" class="qty-input" value="1" min="1" max="99">
                  <button class="qty-btn plus">+</button>
                </div>
                <div class="line-total">Rp 45.000</div>
                <div class="trash">
                  <i class="bi bi-trash"></i>
                </div>
              </div>
              
              <!-- Product 2 -->
              <div class="cart-item" data-id="2">
                <div class="check">
                  <input type="checkbox" class="item-check" checked>
                </div>
                <div class="thumb">
                  <img src="{{ asset('src/PiringKayu.png') }}" alt="Piring Kayu">
                </div>
                <div class="meta">
                  <h3 class="title">Piring Kayu</h3>
                  <div class="variant">SKU: PK-18cm</div>
                </div>
                <div class="price-each" data-each="75000">Rp 75.000</div>
                <div class="qty">
                  <button class="qty-btn minus">-</button>
                  <input type="number" class="qty-input" value="1" min="1" max="99">
                  <button class="qty-btn plus">+</button>
                </div>
                <div class="line-total">Rp 75.000</div>
                <div class="trash">
                  <i class="bi bi-trash"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Right Column: Order Summary -->
        <div class="cart-summary">
          <div class="summary-card">
            <h2>Ringkasan Belanja</h2>
            
            <div class="summary-details">
              <div class="summary-row">
                <span>Subtotal (<span id="selectedCount">2</span> produk)</span>
                <span id="subtotal">Rp 120.000</span>
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
            
            <button id="checkout" class="btn-checkout">
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