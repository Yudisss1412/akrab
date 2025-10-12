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
  </style>
@endpush

@section('content')
  <main class="cart-page shell">
    <!-- Alert Modal -->
    <div id="alertOverlay" class="alert-overlay">
      <div class="alert-container">
        <div class="alert-header">
          <span class="alert-icon">✅</span>
          <h3 id="alertTitle">Berhasil</h3>
          <button id="alertClose" class="alert-close">&times;</button>
        </div>
        <div class="alert-body">
          <p id="alertMessage">Item berhasil ditambahkan ke keranjang!</p>
        </div>
        <div class="alert-actions">
          <button id="alertAction" class="btn-primary">Lihat Keranjang</button>
        </div>
      </div>
    </div>

    <!-- Cart Content -->
    <div class="container">
      <h1>Keranjang Belanja</h1>
      
      <!-- Cart Items -->
      <div class="cart-items">
        <!-- Product 1 -->
        <div class="cart-item">
          <div class="item-image">
            <img src="{{ asset('src/CangkirKeramik1.png') }}" alt="Cangkir Keramik">
          </div>
          
          <div class="item-details">
            <div class="item-info">
              <h3>Cangkir Keramik</h3>
              <p class="item-sku">SKU: CK-250ml</p>
              <div class="item-price">Rp 45.000</div>
            </div>
            
            <div class="item-actions">
              <div class="quantity-control">
                <button class="qty-btn" onclick="changeQty(0, -1)">-</button>
                <input type="number" class="qty-input" id="qty-0" value="1" min="1" onchange="updatePrice(0)">
                <button class="qty-btn" onclick="changeQty(0, 1)">+</button>
              </div>
              
              <div class="item-total" id="total-0">Rp 45.000</div>
            </div>
            
            <div class="item-remove">
              <button class="remove-btn" onclick="removeItem(0)">×</button>
            </div>
          </div>
        </div>
        
        <!-- Product 2 -->
        <div class="cart-item">
          <div class="item-image">
            <img src="{{ asset('src/PiringKayu.png') }}" alt="Piring Kayu">
          </div>
          
          <div class="item-details">
            <div class="item-info">
              <h3>Piring Kayu</h3>
              <p class="item-sku">SKU: PK-18cm</p>
              <div class="item-price">Rp 75.000</div>
            </div>
            
            <div class="item-actions">
              <div class="quantity-control">
                <button class="qty-btn" onclick="changeQty(1, -1)">-</button>
                <input type="number" class="qty-input" id="qty-1" value="1" min="1" onchange="updatePrice(1)">
                <button class="qty-btn" onclick="changeQty(1, 1)">+</button>
              </div>
              
              <div class="item-total" id="total-1">Rp 75.000</div>
            </div>
            
            <div class="item-remove">
              <button class="remove-btn" onclick="removeItem(1)">×</button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Checkout Bar -->
      <div class="cart-bar">
        <div class="cart-bar__inner container">
          <div class="summary">
            <div class="summary-label">Total:</div>
            <div class="summary-value" id="cartTotal">Rp 120.000</div>
          </div>
          
          <div class="actions">
            <a href="{{ route('kategori.kuliner') }}" class="btn-outline">Lanjut Belanja</a>
            <a href="{{ route('transaksi.checkout') }}" class="btn-primary">Checkout</a>
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <script>
    // Quantity control functions
    function changeQty(index, change) {
      const input = document.getElementById(`qty-${index}`);
      let newQty = parseInt(input.value) + change;
      
      if (newQty < 1) newQty = 1;
      if (newQty > 99) newQty = 99;
      
      input.value = newQty;
      updatePrice(index);
    }
    
    function updatePrice(index) {
      const qty = parseInt(document.getElementById(`qty-${index}`).value);
      const price = [45000, 75000][index]; // Harga produk
      const total = qty * price;
      
      document.getElementById(`total-${index}`).textContent = 
        'Rp ' + total.toLocaleString('id-ID');
      
      updateCartTotal();
    }
    
    function updateCartTotal() {
      const totals = [
        parseInt(document.getElementById('total-0').textContent.replace(/[^\d]/g, '')),
        parseInt(document.getElementById('total-1').textContent.replace(/[^\d]/g, ''))
      ];
      
      const cartTotal = totals[0] + totals[1];
      document.getElementById('cartTotal').textContent = 
        'Rp ' + cartTotal.toLocaleString('id-ID');
    }
    
    function removeItem(index) {
      const item = document.querySelectorAll('.cart-item')[index];
      item.style.opacity = '0';
      item.style.transform = 'translateX(-100%)';
      
      setTimeout(() => {
        item.remove();
        updateCartTotal();
      }, 300);
    }
    
    // Alert functions
    let alertTimeout;
    
    function showAlert(title, message, type = 'success') {
      const alertOverlay = document.getElementById('alertOverlay');
      const alertTitle = document.getElementById('alertTitle');
      const alertMessage = document.getElementById('alertMessage');
      const alertAction = document.getElementById('alertAction');
      
      // Update content
      alertTitle.textContent = title;
      alertMessage.textContent = message;
      
      // Add appropriate class based on type
      alertOverlay.classList.remove('alert-success', 'alert-error', 'alert-warning');
      alertOverlay.classList.add(`alert-${type}`);
      
      // Show alert
      alertOverlay.style.display = 'flex';
      setTimeout(() => {
        alertOverlay.classList.add('show');
      }, 10);
      
      // Auto hide after 5 seconds
      if (alertTimeout) clearTimeout(alertTimeout);
      alertTimeout = setTimeout(hideAlert, 5000);
    }
    
    function hideAlert() {
      const alertOverlay = document.getElementById('alertOverlay');
      alertOverlay.classList.remove('show');
      
      setTimeout(() => {
        alertOverlay.style.display = 'none';
      }, 300);
      
      if (alertTimeout) {
        clearTimeout(alertTimeout);
        alertTimeout = null;
      }
    }
    
    // Event listeners
    document.getElementById('alertClose').addEventListener('click', hideAlert);
    document.getElementById('alertOverlay').addEventListener('click', function(e) {
      if (e.target === this) {
        hideAlert();
      }
    });
    
    // Example: Show alert on page load (can be removed)
    // document.addEventListener('DOMContentLoaded', function() {
    //   showAlert('Selamat datang', 'Produk berhasil ditambahkan ke keranjang!', 'success');
    // });
  </script>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection