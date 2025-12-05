'use strict';

/* ---------- DOM ELEMENTS ---------- */
const produkGrid = document.getElementById('produk-grid');
const filterKategori = document.getElementById('filter-kategori');
const filterHarga = document.getElementById('filter-harga');
const urutkan = document.getElementById('urutkan');

/* ---------- API ENDPOINTS ---------- */
const API_PRODUCTS = '/api/products/filter';
const DETAIL_BASE = '/produk_detail/';

/* ---------- HELPERS ---------- */
function formatPrice(price) {
  // Convert to number if it's a string
  const numericPrice = typeof price === 'string' ? parseFloat(price) : price;

  // Format as Rupiah using Indonesian locale for currency
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(numericPrice);
}

function starsHTML(rating) {
  const fullStars = Math.floor(rating);
  const hasHalfStar = rating - fullStars >= 0.5;
  const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

  return '★'.repeat(fullStars) + 
         (hasHalfStar ? '★' : '') + // Half star represented by filled star for simplicity
         '☆'.repeat(emptyStars);
}

/* ---------- EVENT LISTENERS FOR FILTERS ---------- */
filterKategori?.addEventListener('change', function() {
  applyFilters();
});

filterHarga?.addEventListener('change', function() {
  applyFilters();
});

urutkan?.addEventListener('change', function() {
  applyFilters();
});

/* ---------- APPLY FILTERS ---------- */
function applyFilters() {
  // Get current filter values
  const kategori = filterKategori.value;
  const hargaRange = filterHarga.value;
  const sortBy = urutkan.value;

  // Simpan ke URL untuk persistensi
  const url = new URL(window.location);
  if (kategori && kategori !== 'all') {
    url.searchParams.set('kategori', kategori);
  } else {
    url.searchParams.delete('kategori');
  }
  
  if (hargaRange && hargaRange !== 'all') {
    url.searchParams.set('harga', hargaRange);
  } else {
    url.searchParams.delete('harga');
  }
  
  if (sortBy && sortBy !== 'terbaru') {
    url.searchParams.set('sort', sortBy);
  } else {
    url.searchParams.delete('sort');
  }
  
  // Tambahkan parameter toko untuk membatasi hasil ke toko tertentu
  const sellerId = document.querySelector('.toko-identity')?.dataset?.sellerId;
  if (sellerId) {
    url.searchParams.set('seller', sellerId);
  }

  // Update URL tanpa reload
  window.history.replaceState({}, '', url);

  // Load produk dengan filter baru
  loadFilteredProducts(kategori, hargaRange, sortBy);
}

/* ---------- LOAD FILTERED PRODUCTS ---------- */
async function loadFilteredProducts(kategori, hargaRange, sortBy) {
  try {
    let url = API_PRODUCTS;
    const params = new URLSearchParams();

    // Ambil ID toko dari elemen halaman
    const tokoNameElement = document.querySelector('.toko-name');
    if (tokoNameElement) {
      // Kita perlu mendapatkan ID toko dari rute atau data lain
      // Untuk saat ini kita akan menggunakan pendekatan lain
      params.append('seller_store_name', tokoNameElement.textContent.trim());
    }

    // Tambahkan filter kategori jika dipilih
    if (kategori && kategori !== 'all') {
      params.append('kategori', kategori);
    }

    // Tambahkan filter harga jika dipilih
    if (hargaRange && hargaRange !== 'all') {
      if (hargaRange === '0-50000') {
        params.append('min_price', '0');
        params.append('max_price', '50000');
      } else if (hargaRange === '50000-100000') {
        params.append('min_price', '50000');
        params.append('max_price', '100000');
      } else if (hargaRange === '100000-500000') {
        params.append('min_price', '100000');
        params.append('max_price', '500000');
      } else if (hargaRange === '500000+') {
        params.append('min_price', '500000');
      }
    }

    // Tambahkan parameter urutan
    if (sortBy) {
      let sortParam = 'newest'; // default
      switch (sortBy) {
        case 'harga-terendah':
          sortParam = 'price-low';
          break;
        case 'harga-tertinggi':
          sortParam = 'price-high';
          break;
        case 'rating-tertinggi':
          sortParam = 'highest-rated';
          break;
        case 'terbaru':
          sortParam = 'newest';
          break;
      }
      params.append('sort', sortParam);
    }

    // Tambahkan cache busting
    params.append('_cb', Date.now());

    url += '?' + params.toString();

    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Cache-Control': 'no-cache, no-store, must-revalidate',
        'Pragma': 'no-cache',
        'Expires': '0',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const result = await response.json();
    const products = Array.isArray(result) ? result : [];

    // Render produk hasil filter
    renderFilteredProducts(products);
  } catch (error) {
    console.error('Error loading filtered products:', error);
    // Tampilkan pesan error ke user
    produkGrid.innerHTML = '<div class="no-products-message"><p>Terjadi kesalahan saat memuat produk.</p></div>';
  }
}

/* ---------- RENDER FILTERED PRODUCTS ---------- */
function renderFilteredProducts(products) {
  if (!produkGrid) return;

  if (products.length === 0) {
    produkGrid.innerHTML = '<div class="no-products-message"><p>Tidak ada produk yang sesuai dengan filter.</p></div>';
    return;
  }

  produkGrid.innerHTML = products.map(product => {
    const imageSrc = product.image || product.gambar || product.main_image || 'src/placeholder_produk.png';
    const altText = (product.nama || product.name || 'Produk Tidak Bernama').toString()
                     .replace(/[&<>"']/g, function(match) {
                       return {
                         '&': '&amp;',
                         '<': '&lt;',
                         '>': '&gt;',
                         '"': '&quot;',
                         "'": '&#39;'
                       }[match];
                     });
    
    return `
      <div class="produk-card" data-product-id="${product.id}">
        <img src="${imageSrc}" alt="${altText}"
             class="produk-img"
             onerror="this.onerror=null; this.src='src/placeholder_produk.png';">
        <div class="produk-card-info">
          <div class="produk-card-content">
            <h3 class="produk-card-name">${product.nama || product.name || 'Nama Produk'}</h3>
            <div class="produk-card-sub">${product.subkategori || product.subcategory || product.kategori || product.category || 'Umum'}</div>
            <div class="produk-card-price">${formatPrice(product.harga || product.price || 0)}</div>
            <div class="produk-card-toko">
              <a href="${window.location.pathname}" class="toko-link" data-seller-name="${product.toko || product.seller || 'Toko Ini'}">${product.toko || product.seller || 'Toko Ini'}</a>
            </div>
            <div class="produk-card-stars" aria-label="Rating ${product.average_rating || product.rating || 0} dari 5">
              <span class="stars">${starsHTML(product.average_rating || product.rating || 0)}</span>
              <span class="rating-value">(${product.average_rating || product.rating || 0})</span>
            </div>
          </div>
        </div>
        <div class="produk-card-actions">
          <a class="btn-lihat lihat-detail-btn" 
             data-product-id="${product.id}"
             href="${DETAIL_BASE}${encodeURIComponent(product.id)}">Lihat Detail</a>
          <button class="btn-add"
                  data-product-id="${product.id}"
                  data-name="${product.nama || product.name || 'Produk'}"
                  type="button">+ Keranjang</button>
        </div>
      </div>
    `;
  }).join('');

  // Tambahkan event listeners untuk tombol add to cart
  attachCartEventListeners();
}

/* ---------- ADD TO CART FUNCTIONALITY ---------- */
function attachCartEventListeners() {
  const addButtons = document.querySelectorAll('.btn-add');
  addButtons.forEach(button => {
    // Hapus event listener lama jika ada
    const newButton = button.cloneNode(true);
    button.parentNode.replaceChild(newButton, button);
  });

  // Tambahkan event listener baru
  document.querySelectorAll('.btn-add').forEach(button => {
    button.addEventListener('click', function() {
      const productId = this.getAttribute('data-product-id');
      const productName = this.getAttribute('data-name');
      addToCart(productId, productName);
    });
  });
}

async function addToCart(productId, productName = 'Produk') {
  if (!productId) {
    showNotification('Produk tidak ditemukan', 'error');
    return;
  }

  // Ambil CSRF token dari meta tag
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  try {
    const response = await fetch('/cart/add', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        product_id: productId,
        quantity: 1
      })
    });

    const result = await response.json();

    if (response.ok && result.success) {
      showNotification(result.message || `${productName} berhasil ditambahkan ke keranjang`, 'success');
    } else {
      showNotification(result.message || 'Gagal menambahkan ke keranjang', 'error');
    }
  } catch (error) {
    console.error('Error adding to cart:', error);
    showNotification('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
  }
}

/* ---------- NOTIFICATION FUNCTION ---------- */
function showNotification(message, type = 'info') {
  // Hapus notifikasi sebelumnya jika ada
  const existingNotifications = document.querySelectorAll('.notification');
  existingNotifications.forEach(note => note.remove());

  // Buat elemen notifikasi
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.textContent = message;

  // Styling dasar untuk notifikasi
  Object.assign(notification.style, {
    position: 'fixed',
    top: '20px',
    right: '20px',
    padding: '12px 20px',
    borderRadius: '6px',
    color: '#fff',
    backgroundColor: type === 'error' ? '#dc3545' : type === 'success' ? '#28a745' : '#007bff',
    zIndex: '9999',
    boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
    fontSize: '14px',
    maxWidth: '400px',
    wordWrap: 'break-word'
  });

  // Tambahkan ke body
  document.body.appendChild(notification);

  // Hapus notifikasi setelah 3 detik
  setTimeout(() => {
    if (notification.parentNode) {
      notification.parentNode.removeChild(notification);
    }
  }, 3000);
}

/* ---------- INISIALISASI ---------- */
document.addEventListener('DOMContentLoaded', function() {
  // Cek apakah ada filter di URL dan terapkan
  const urlParams = new URLSearchParams(window.location.search);
  
  const kategoriParam = urlParams.get('kategori');
  if (kategoriParam && filterKategori) {
    filterKategori.value = kategoriParam;
  }
  
  const hargaParam = urlParams.get('harga');
  if (hargaParam && filterHarga) {
    filterHarga.value = hargaParam;
  }
  
  const sortParam = urlParams.get('sort');
  if (sortParam && urutkan) {
    // Konversi parameter sort ke nilai yang sesuai di dropdown
    let sortValue = 'terbaru';
    switch(sortParam) {
      case 'price-low':
        sortValue = 'harga-terendah';
        break;
      case 'price-high':
        sortValue = 'harga-tertinggi';
        break;
      case 'highest-rated':
        sortValue = 'rating-tertinggi';
        break;
    }
    urutkan.value = sortValue;
  }

  // Terapkan filter jika ada parameter di URL
  if (kategoriParam || hargaParam || sortParam) {
    applyFilters();
  }

  // Inisialisasi event listeners untuk add to cart
  attachCartEventListeners();
});

// Fungsi untuk menangani error gambar
function handleImageError(img) {
  if (!img) return;
  img.onerror = null; // Cegah infinite loop jika placeholder juga gagal

  // Gunakan SVG sebagai fallback
  const svgString = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>';
  img.src = 'data:image/svg+xml;utf8,' + encodeURIComponent(svgString);
}