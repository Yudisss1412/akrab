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