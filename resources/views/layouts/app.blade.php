<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UMKM AKRAB')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Embedded Header & Footer CSS -->
    <style>
    /* ========================================
       AKRAB — Shared Header & Footer Styles
       ======================================== */

    :root{
      --primary-color: #a8d5c9;
      --primary-color-dark: #006E5C;
      --secondary-color: #FFFFFF;
      --background-color: #f0fdfa;
      --dark-text-color: #333;
      --border-color: #e9ecef;

      /* Footer capsule sizing */
      --soc-icon: 18px;     /* ukuran ikon */
      --soc-gap: 12px;      /* jarak antar ikon */
      --soc-hitbox: 28px;   /* tap target per ikon */
      --soc-height: 36px;  /* tinggi kapsul */
    }

    /* ========== Header / Navbar ========== */
    .header{
      background: var(--secondary-color);
      border-bottom: 1px solid var(--border-color);
      height: 78px;
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 2.5rem;
      z-index: 1001;
      position: sticky; top: 0;
    }
    .header-left .logo{ height: 54px; width: auto; display: block; }
    .header-center{ flex: 1; display: flex; justify-content: center; }
    .header-right{ display: flex; gap: 1.2rem; align-items: center; }

    /* Icons di navbar */
    header:not(.compact) .search-bar{
      background: #fff;
      border: 1.5px solid var(--border-color);
      border-radius: 50px;
      padding: 0.7rem 1.2rem 0.7rem 2.8rem;
      min-width: 330px; max-width: 460px; width: 100%;
      position: relative; display: flex; align-items: center;
    }
    header:not(.compact) .search-bar input{
      border: none; background: transparent; outline: none;
      font-size: 1.1rem; width: 100%; color: var(--dark-text-color);
    }
    header:not(.compact) .search-icon{
      position: absolute; left: 1.05rem; top: 50%; transform: translateY(-50%);
      width: 22px; height: 22px; color: #aaa; display: block; line-height: 0;
    }
    .profile-ico, .cart-ico{ width: 27px; height: 27px; display:inline-flex; align-items:center; justify-content:center; }
    .profile-ico svg{ width: 27px; height: 27px; display:block; stroke: var(--primary-color-dark); fill: none; }
    .cart-ico svg{ width: 27px; height: 27px; display:block; fill: var(--primary-color-dark); stroke: none; }

    /* ========== Footer ========== */
    .footer{
      background-color: var(--secondary-color);
      color: var(--primary-color-dark);
      border-top: 1px solid var(--border-color);
      width: 100%; position: static; z-index: 100;
      font-size: 12px;
    }
    .footer .footer__inner.footer-3col{
      width: 100%; max-width: none; margin: 0;
      padding: 1rem 2rem;
      display: grid;
      grid-template-columns: minmax(0,1fr) auto minmax(0,1fr);
      align-items: center; gap: 1rem;
    }
    .footer-left{ justify-self: start; display:inline-flex; align-items:center; gap: 1rem; }
    .footer-center{ justify-self: center; text-align:center; }
    .footer-right{ justify-self: end; display:flex; align-items:center; }

    /* Link kebijakan */
    .footer-privacy a{
      color: var(--primary-color-dark);
      text-decoration: none; font-size: 12px;
    }
    .footer-privacy a:hover{ text-decoration: underline; }

    /* Kapsul sosmed */
    .social-icons-background{
      display: inline-flex; align-items: center; justify-content: center;
      gap: var(--soc-gap); height: var(--soc-height);
      min-width: calc(var(--soc-icon) * 6 + var(--soc-gap) * 3); /* cukup utk 4 ikon */
      padding: 0 10px; border-radius: 9999px;
      background: var(--primary-color-dark);
      color: #fff; line-height: 0;
    }
    .social-icon{
      width: var(--soc-hitbox); height: var(--soc-hitbox);
      display: inline-flex; align-items: center; justify-content: center;
      border-radius: 50%; line-height: 0;
    }
    .social-icon :is(svg,img,i){
      width: var(--soc-icon) !important; height: var(--soc-icon) !important;
      display: block !important; vertical-align: middle; object-fit: contain;
      fill: currentColor; stroke: none;
    }

    /* Guard svg footer */
    .footer svg{ max-width: none !important; transform: none !important; }

    /* ========== Responsive ========== */
    @media (max-width: 768px){
      .header{ padding: 0 1.5rem; }
      header:not(.compact) .search-bar{ min-width: 200px; }
    }

    @media (max-width: 480px){
      .header{ padding: 0 1.5rem; }
    }

    /* Footer stack rapi di mobile */
    @media (max-width: 640px){
      .footer .footer__inner.footer-3col{
        grid-template-columns: 1fr;
        row-gap: .75rem; text-align: center;
      }
      .footer-left, .footer-center, .footer-right{ justify-self: center; }
    }

    /* ============================================================
       FOOTER — LOCKED LAYOUT (© kiri • sosmed center • Privasi kanan)
       ============================================================ */
    @media (min-width:768px){
      footer.footer{ position:relative !important; min-height:64px !important; }

      /* pastikan inner bukan anchor */
      footer.footer .footer__inner{ position:static !important; }

      /* KIRI: © */
      footer.footer .footer-left{
        position:absolute !important;
        left:2rem !important;
        top:50% !important;
        transform:translateY(-50%) !important;
        margin:0 !important;
        white-space:nowrap !important;
        display:flex !important; align-items:center !important;
      }

      /* TENGAH: kapsul sosmed */
      footer.footer .footer-center,
      footer.footer .social-icons-background{
        position:absolute !important;
        left:50% !important;
        top:50% !important;
        transform:translate(-50%,-50%) !important;
        z-index:2 !important;
        margin:0 !important;
        text-align:center !important;
      }

      /* KANAN: kebijakan privasi */
      footer.footer .footer-right,
      footer.footer .footer-privacy{
        position:absolute !important;
        right:2rem !important;
        top:50% !important;
        transform:translateY(-50%) !important;
        margin:0 !important;
        text-align:right !important;
        white-space:nowrap !important;
        display:flex !important; align-items:center !important;
      }
    }
    </style>
    
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/order-detail.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    @stack('styles')
    
    <!-- CSS khusus untuk komponen penjual -->
    @if(request()->routeIs('penjual.*'))
        <link rel="stylesheet" href="{{ asset('css/penjual/components.css') }}">
    @endif
</head>
<body>
    <div class="main-layout">
        <!-- Header -->
        @yield('header')
        
        <!-- Main Content -->
        <main class="content">
            @yield('content')
        </main>
        
        <!-- Footer -->
        @yield('footer')
    </div>

    <script src="{{ asset('js/customer/helpers/csrfHelper.js') }}"></script>
    @stack('scripts')
    <script src="{{ asset('js/customer/script.js') }}"></script>

    <script>
      // Function to update cart count in header
      async function updateCartCount() {
        try {
          const response = await fetch('/api/cart/count', {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          });

          const data = await response.json();

          if (data.success) {
            const cartBadge = document.querySelector('.cart-badge');
            const cartCountElement = cartBadge ? cartBadge.querySelector('.cart-count') : null;

            if (data.count > 0) {
              // If cart count element exists, update it; otherwise create it
              if (cartCountElement) {
                cartCountElement.textContent = data.count;
              } else {
                // Create cart count element if it doesn't exist
                const newCartCount = document.createElement('span');
                newCartCount.className = 'cart-count';
                newCartCount.textContent = data.count;
                if (cartBadge) {
                  cartBadge.appendChild(newCartCount);
                }
              }
            } else {
              // Remove cart count if count is 0
              if (cartCountElement) {
                cartCountElement.remove();
              }
            }
          }
        } catch (error) {
          console.error('Error fetching cart count:', error);
          // Tidak menampilkan error ke pengguna karena ini hanya fungsi update count
        }
      }

      // Function to save cart to localStorage
      function saveCartToLocalStorage() {
        // Ambil semua item keranjang dari halaman (jika ada tombol/tombol keranjang yang bisa diidentifikasi)
        // Kita akan menyimpan dalam format JSON sederhana
        const cartItems = [];

        // Coba cari elemen keranjang di halaman
        const cartElements = document.querySelectorAll('[data-cart-item]');
        cartElements.forEach(element => {
          const productId = element.getAttribute('data-product-id');
          const productVariantId = element.getAttribute('data-variant-id');
          const quantity = element.getAttribute('data-quantity') || 1;

          if (productId) {
            // Cek apakah item sudah ada di array untuk menghindari duplikasi
            const existingItem = cartItems.find(item =>
              item.product_id == productId && item.product_variant_id == productVariantId
            );

            if (existingItem) {
              existingItem.quantity = parseInt(existingItem.quantity) + parseInt(quantity);
            } else {
              cartItems.push({
                product_id: productId,
                product_variant_id: productVariantId || null,
                quantity: parseInt(quantity)
              });
            }
          }
        });

        // Juga cari item dari elemen lain yang mungkin memiliki informasi keranjang
        const cartItemContainers = document.querySelectorAll('.keranjang-item, .cart-item, [class*="keranjang"] [class*="item"]');
        cartItemContainers.forEach(container => {
          const productId = container.querySelector('[data-product-id]')?.getAttribute('data-product-id') ||
                           container.getAttribute('data-product-id');
          const quantityEl = container.querySelector('.quantity, .qty, [class*="quantity"]') ||
                            container.querySelector('[data-quantity]');
          const variantId = container.querySelector('[data-variant-id]')?.getAttribute('data-variant-id') ||
                           container.getAttribute('data-variant-id');

          if (productId) {
            const quantity = quantityEl ?
              (quantityEl.value || quantityEl.textContent || quantityEl.getAttribute('data-quantity') || 1) : 1;

            const existingItem = cartItems.find(item =>
              item.product_id == productId && item.product_variant_id == variantId
            );

            if (existingItem) {
              existingItem.quantity = parseInt(existingItem.quantity) + parseInt(quantity);
            } else {
              cartItems.push({
                product_id: productId,
                product_variant_id: variantId || null,
                quantity: parseInt(quantity) || 1
              });
            }
          }
        });

        // Simpan ke localStorage
        if (cartItems.length > 0) {
          localStorage.setItem('cart_items', JSON.stringify(cartItems));
        }
      }

      // Function to load cart from localStorage and sync with server
      async function loadCartFromLocalStorage() {
        const cartData = localStorage.getItem('cart_items');

        if (cartData) {
          try {
            const items = JSON.parse(cartData);

            // Kirim data ke server untuk disimpan ke session
            if (items.length > 0) {
              const response = await fetch('/api/cart/sync', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ cart_data: cartData })
              });

              const result = await response.json();

              if (result.success) {
                // Update UI jika diperlukan
                updateCartCount();

                // Hapus data dari localStorage setelah disinkronkan
                // Tapi simpan sebagai backup sementara
                if (result.merged) {
                  localStorage.removeItem('cart_items');
                }
              }
            }
          } catch (error) {
            console.error('Error syncing cart from localStorage:', error);
          }
        }
      }

      // Initialize cart count on page load
      document.addEventListener('DOMContentLoaded', function() {
        // Load cart from localStorage and sync with server
        loadCartFromLocalStorage();
        updateCartCount();
      });

      // Save cart to localStorage before page unload
      window.addEventListener('beforeunload', function() {
        saveCartToLocalStorage();
      });

      // Save cart to localStorage on specific events (like adding to cart)
      document.addEventListener('click', function(e) {
        // Jika ada tombol yang berkaitan dengan keranjang, simpan ke localStorage
        if (e.target.closest('.add-to-cart, .btn-tambah-keranjang, [data-add-to-cart]')) {
          setTimeout(() => {
            saveCartToLocalStorage();
          }, 500); // Delay untuk memastikan DOM sudah diperbarui
        }
      });

      // Expose the functions globally so they can be called from other scripts
      window.updateCartCount = updateCartCount;
      window.saveCartToLocalStorage = saveCartToLocalStorage;
      window.loadCartFromLocalStorage = loadCartFromLocalStorage;
    </script>
</body>
</html>