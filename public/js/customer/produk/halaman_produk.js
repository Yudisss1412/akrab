'use strict';

/* ---------- STATE ---------- */
let currentPage = 1;
const pageSize = 8;

/* Rekomendasi: pagination 4/halaman */
let rekomPage = 1;
const rekomPageSize = 4;
let rekomPool = [];

/* ---------- SVG rating ---------- */
const STAR_FULL = `<svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF600"/></svg>`;
const STAR_HALF = `<svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M29.6691 32.9982L28.0534 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535V29.2285L29.6691 32.9982ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF700"/></svg>`;
const STAR_EMPTY = `<svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M17.3316 32.9493L23.5003 29.2285L29.6691 32.9982L28.0535 25.9482L33.4878 21.1993L26.3399 20.6118L23.5003 13.9535L20.6607 20.5628L13.5128 21.1993L18.9472 25.9482L17.3316 32.9493ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF600"/></svg>`;

/* ---------- DOM ---------- */
const grid = document.getElementById('produk-grid');
const pagin = document.getElementById('produk-pagination');
const inputSearch = document.getElementById('navbar-search'); // dari partial navbar
const selectKategori = document.getElementById('filter-kategori');

const rekomGrid = document.getElementById('rekom-grid');
const rekomPagin = document.getElementById('rekom-pagination');

/* ---------- ROUTE & API ENDPOINTS ---------- */
const API_PRODUCTS = '/produk';  // API endpoint baru untuk mengambil semua produk
const API_SEARCH = '/produk/search';
const API_CATEGORY = '/produk/kategori';
const DETAIL_BASE = '/produk_detail/';
const CART_ADD = '/cart/add';

/* ---------- HELPERS ---------- */
const debounce = (fn, wait = 200) => {
  let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
};

function starsHTML(num) {
  const r = Math.max(0, Math.min(5, parseFloat(num) || 0));
  let full = Math.floor(r), half = 0;
  const frac = r - full;
  if (frac >= 0.75) full += 1; else if (frac >= 0.25) half = 1;
  const empty = Math.max(0, 5 - full - half);
  return `${STAR_FULL.repeat(full)}${half ? STAR_HALF : ''}${STAR_EMPTY.repeat(empty)}`;
}

function formatPrice(price) {
  // If price is already formatted with 'Rp', return as is
  if (typeof price === 'string' && price.startsWith('Rp')) {
    return price;
  }
  
  // Convert to number if it's a string
  const numericPrice = typeof price === 'string' ? parseFloat(price) : price;
  
  // Format as Rupiah using Indonesian locale for currency
  // This will format as Rp X.XXX,XX with 2 decimal places
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 2,  // Tampilkan selalu 2 angka desimal
    maximumFractionDigits: 2   // Batasi hanya 2 angka desimal
  }).format(numericPrice);
}

/* ---------- IMAGE ERROR HANDLER ---------- */
function handleImageError(img) {
  img.onerror = null; // Prevent infinite loop if placeholder also fails
  
  // Create simple SVG as fallback
  const svgString = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400"><rect width="100%" height="100%" fill="#eef6f4"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="22" fill="#7aa29b">Gambar tidak tersedia</text></svg>';
  
  img.src = 'data:image/svg+xml;utf8,' + encodeURIComponent(svgString);
}

/* ---------- API CALLS FOR DYNAMIC SEARCH/FILTER ---------- */
async function searchProducts(search = '', category = 'all') {
  try {
    let url = search ? API_SEARCH : API_PRODUCTS;
    const params = new URLSearchParams();
    
    if (search) {
      params.append('q', search);
    } else {
      // For main products API, allow category filtering
      if (category && category !== 'all') params.append('kategori', category);
    }
    
    if (!search && category && category !== 'all') {
      url = API_PRODUCTS;  // Use main products API with category filter
      params.set('kategori', category);
    }
    
    url += '?' + params.toString();
    
    const response = await fetch(url);
    const result = await response.json();
    
    // Format the data to match what the render function expects
    const formattedProducts = Array.isArray(result) ? 
      result.map(product => ({
        id: product.id,
        nama: product.nama || product.name || 'Produk Tanpa Nama',
        kategori: product.kategori || product.category?.name || 'Umum',
        harga: product.harga ? formatPrice(product.harga) : formatPrice(product.price || 0),
        gambar: product.gambar || product.image || product.main_image || 'src/placeholder.png',
        rating: product.rating || product.average_rating || product.averageRating || 0,
        toko: product.toko || product.seller?.name || 'Toko Umum',
        deskripsi: product.deskripsi || product.description || ''
      })) : 
      [];
    
    return formattedProducts;
  } catch (error) {
    console.error('Error searching products:', error);
    showNotification('Gagal memuat produk', 'error');
    return [];
  }
}

/* ---------- LOAD ALL PRODUCTS FROM API ---------- */
async function loadAllProducts(category = 'all') {
  try {
    let url = API_PRODUCTS;
    if (category && category !== 'all') {
      const params = new URLSearchParams();
      params.append('kategori', category);
      url += '?' + params.toString();
    }
    
    const response = await fetch(url);
    const result = await response.json();
    
    // Format the data to match what the render function expects
    const formattedProducts = Array.isArray(result) ? 
      result.map(product => ({
        id: product.id,
        nama: product.nama || product.name || 'Produk Tanpa Nama',
        kategori: product.kategori || product.category?.name || 'Umum',
        harga: product.harga ? formatPrice(product.harga) : formatPrice(product.price || 0),
        gambar: product.gambar || product.image || product.main_image || 'src/placeholder.png',
        rating: product.rating || product.average_rating || product.averageRating || 0,
        toko: product.toko || product.seller?.name || 'Toko Umum',
        deskripsi: product.deskripsi || product.description || ''
      })) : 
      [];
    
    return formattedProducts;
  } catch (error) {
    console.error('Error loading products:', error);
    showNotification('Gagal memuat produk', 'error');
    return [];
  }
}

/* ---------- LOAD POPULAR PRODUCTS FROM API ---------- */
async function loadPopularProducts() {
  try {
    // Use the popular API endpoint
    const response = await fetch('/api/products/popular');
    const result = await response.json();
    
    // Format the data to match what the render function expects
    const formattedProducts = Array.isArray(result) ? 
      result.map(product => ({
        id: product.id,
        nama: product.name || product.nama || 'Produk Tanpa Nama',
        kategori: product.kategori || product.category?.name || 'Umum',
        harga: product.price || product.harga ? formatPrice(product.price || product.harga) : formatPrice(0),
        gambar: product.image || product.gambar || product.main_image || 'src/placeholder.png',
        rating: product.average_rating || product.rating || product.averageRating || 0,
        toko: product.toko || product.seller?.name || 'Toko Umum',
        deskripsi: product.description || product.deskripsi || ''
      })) : 
      [];
    
    return formattedProducts;
  } catch (error) {
    console.error('Error loading popular products:', error);
    // showNotification('Gagal memuat produk populer', 'error');
    return [];
  }
}



/* ---------- RENDER FUNCTIONS ---------- */
function renderList(products = []) {
  if (!grid || !pagin) return;

  // Calculate total pages based on a reasonable assumption
  // In a real app, API should return total count
  const totalPage = Math.max(1, Math.ceil(products.length / pageSize));
  if (currentPage > totalPage) currentPage = totalPage;

  const start = (currentPage - 1) * pageSize;
  const view = products.slice(start, start + pageSize);

  grid.innerHTML = view.map(p => {
    const href = `${DETAIL_BASE}${encodeURIComponent(p.id)}`;
    const imageSrc = p.gambar || 'src/placeholder.png';
    const altText = p.nama.replace(/"/g, '&quot;').replace(/'/g, '&apos;');
    return `
      <div class="produk-card" data-product-id="${p.id}">
        <img src="${imageSrc}" alt="${altText}"
             onerror="handleImageError(this)"
             loading="lazy">
        <div class="produk-card-info">
          <h3 class="produk-card-name">${p.nama}</h3>
          <div class="produk-card-sub">${p.kategori}</div>
          <div class="produk-card-price">${p.harga}</div>
          <div class="produk-card-toko">${p.toko}</div>
          <div class="produk-card-stars" aria-label="Rating ${p.rating} dari 5">${starsHTML(p.rating)}</div>
          <div class="produk-rating-angka">${p.rating}</div>
        </div>
        <div class="produk-card-actions">
          <a class="btn-lihat" data-product-id="${p.id}" href="${href}">Lihat Detail</a>
          <button class="btn-add" data-product-id="${p.id}" data-name="${p.nama}" type="button">+ Keranjang</button>
        </div>
      </div>`;
  }).join('');

  renderPagination(totalPage);
}

function renderRekomendasi(products = [], maxItems = 8) {
  if (!rekomGrid) return;

  // Limit to maxItems for rekomendasi section
  const view = products.slice(0, maxItems);

  rekomGrid.innerHTML = view.map(p => {
    const href = `${DETAIL_BASE}${encodeURIComponent(p.id)}`;
    const imageSrc = p.gambar || 'src/placeholder.png';
    const altText = p.nama.replace(/"/g, '&quot;').replace(/'/g, '&apos;');
    return `
      <div class="produk-card" data-product-id="${p.id}">
        <img src="${imageSrc}" alt="${altText}"
             onerror="handleImageError(this)"
             loading="lazy">
        <div class="produk-card-info">
          <h3 class="produk-card-name">${p.nama}</h3>
          <div class="produk-card-sub">${p.kategori}</div>
          <div class="produk-card-price">${p.harga}</div>
          <div class="produk-card-toko">${p.toko}</div>
          <div class="produk-card-stars" aria-label="Rating ${p.rating} dari 5">${starsHTML(p.rating)}</div>
          <div class="produk-rating-angka">${p.rating}</div>
        </div>
        <div class="produk-card-actions">
          <a class="btn-lihat" data-product-id="${p.id}" href="${href}">Lihat Detail</a>
          <button class="btn-add" data-product-id="${p.id}" data-name="${p.nama}" type="button">+ Keranjang</button>
        </div>
      </div>`;
  }).join('');
}

function renderProdukPopuler(products = [], maxItems = 8) {
  const populerGrid = document.getElementById('populer-grid');
  if (!populerGrid) return;

  // Limit to maxItems for populer section
  const view = products.slice(0, maxItems);

  populerGrid.innerHTML = view.map(p => {
    const href = `${DETAIL_BASE}${encodeURIComponent(p.id)}`;
    const imageSrc = p.gambar || 'src/placeholder.png';
    const altText = p.nama.replace(/"/g, '&quot;').replace(/'/g, '&apos;');
    return `
      <div class="produk-card" data-product-id="${p.id}">
        <img src="${imageSrc}" alt="${altText}"
             onerror="handleImageError(this)"
             loading="lazy">
        <div class="produk-card-info">
          <h3 class="produk-card-name">${p.nama}</h3>
          <div class="produk-card-sub">${p.kategori}</div>
          <div class="produk-card-price">${p.harga}</div>
          <div class="produk-card-toko">${p.toko}</div>
          <div class="produk-card-stars" aria-label="Rating ${p.rating} dari 5">${starsHTML(p.rating)}</div>
          <div class="produk-rating-angka">${p.rating}</div>
        </div>
        <div class="produk-card-actions">
          <a class="btn-lihat" data-product-id="${p.id}" href="${href}">Lihat Detail</a>
          <button class="btn-add" data-product-id="${p.id}" data-name="${p.nama}" type="button">+ Keranjang</button>
        </div>
      </div>`;
  }).join('');
}

function renderPagination(totalPage) {
  if (!pagin) return;

  const btn = (label, page, disabled = false, extra = '', aria = '') =>
    `<button class="page-btn ${extra}" data-page="${page}" ${disabled ? 'disabled' : ''} ${aria}>${label}</button>`;

  const maxBtn = 5;
  let start = Math.max(1, currentPage - Math.floor(maxBtn / 2));
  let end = start + maxBtn - 1;
  if (end > totalPage) { end = totalPage; start = Math.max(1, end - maxBtn + 1); }

  let html = '';
  html += btn('«', 1, currentPage === 1, '', 'aria-label="Halaman pertama"');
  html += btn('‹', currentPage - 1, currentPage === 1, '', 'aria-label="Sebelumnya"');
  for (let i = start; i <= end; i++) {
    const active = i === currentPage;
    html += btn(i, i, false, active ? 'active' : '', active ? 'aria-current="page"' : '');
  }
  html += btn('›', currentPage + 1, currentPage === totalPage, '', 'aria-label="Berikutnya"');
  html += btn('»', totalPage, currentPage === totalPage, '', 'aria-label="Halaman terakhir"');

  pagin.innerHTML = html;
}

/* ---------- MAIN FUNCTION WITH DYNAMIC CONTENT ---------- */
let currentSearch = '';
let currentCategory = 'all';

// Initialize filters from URL parameters
function initFiltersFromUrl() {
  const urlParams = new URLSearchParams(window.location.search);
  
  // Initialize category filter from URL
  const categoryParam = urlParams.get('kategori');
  if (categoryParam) {
    const categorySelect = document.getElementById('filter-kategori');
    if (categorySelect) {
      // Check if the category exists in the dropdown options
      const foundOption = Array.from(categorySelect.options).some(option => option.value === categoryParam);
      if (foundOption) {
        categorySelect.value = categoryParam;
        currentCategory = categoryParam;
      } else {
        // If category doesn't exist in the dropdown, reset to 'all'
        categorySelect.value = 'all';
        currentCategory = 'all';
      }
    }
  }
}

async function updateContent() {
  let products = [];
  
  // If search or filter is applied, use search API
  if (currentSearch || currentCategory !== 'all') {
    products = await searchProducts(currentSearch, currentCategory);
  } else {
    // Otherwise, load all products from API to ensure we have the latest
    products = await loadAllProducts(currentCategory);
  }
  
  renderList(products);
  
  // For recommendations, use a subset of products
  const recommendations = products.length > 0 ? 
    products.slice(0, 8).sort(() => Math.random() - 0.5) : 
    [];
  renderRekomendasi(recommendations);
  
  // Load and render popular products
  loadPopularProducts().then(popularProducts => {
    renderProdukPopuler(popularProducts);
  });
}

/* ---------- EVENTS ---------- */
inputSearch?.addEventListener('input', debounce(() => {
  currentSearch = (inputSearch?.value || '').trim();
  currentPage = 1;
  updateContent();
}, 500));

selectKategori?.addEventListener('change', () => {
  currentCategory = selectKategori.value;
  currentPage = 1;
  
  // Update URL to reflect category selection
  const url = new URL(window.location);
  if (currentCategory && currentCategory !== 'all') {
    url.searchParams.set('kategori', currentCategory);
  } else {
    url.searchParams.delete('kategori');
  }
  window.history.replaceState({}, '', url);
  
  updateContent();
});

pagin?.addEventListener('click', e => {
  const t = e.target.closest('.page-btn');
  if (!t || t.disabled) return;
  const to = +t.dataset.page;
  if (to >= 1) {
    currentPage = to;
    updateContent(); // Re-render with current products
  }
});

/* tombol di grid utama */
grid?.addEventListener('click', e => {
  const add = e.target.closest('.btn-add');
  if (add) {
    const productId = add.dataset.productId;
    const productName = add.dataset.name;
    addToCart(productId, productName);
    return;
  }
});

/* tombol di grid rekomendasi */
rekomGrid?.addEventListener('click', e => {
  const add = e.target.closest('.btn-add');
  const detail = e.target.closest('.btn-lihat');
  if (add) {
    const productId = add.dataset.productId;
    const productName = add.dataset.name;
    addToCart(productId, productName);
    return;
  }
  if (detail) { /* biarkan <a> navigate */ return; }
});

/* tombol di grid populer */
const populerGrid = document.getElementById('populer-grid');
populerGrid?.addEventListener('click', e => {
  const add = e.target.closest('.btn-add');
  const detail = e.target.closest('.btn-lihat');
  if (add) {
    const productId = add.dataset.productId;
    const productName = add.dataset.name;
    addToCart(productId, productName);
    return;
  }
  if (detail) { /* biarkan <a> navigate */ return; }
});

/* ---------- CART & NOTIFICATION FUNCTIONS ---------- */
async function addToCart(productId, productName = 'Produk') {
  // Ambil CSRF token
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
  if (!productId) {
    showNotification('Produk tidak ditemukan', 'error');
    return;
  }
  
  try {
    const response = await fetch(CART_ADD, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({ 
        product_id: productId,
        quantity: 1 // Default quantity
      })
    });

    const result = await response.json();

    if (result.success) {
      showNotification(result.message || `${productName} berhasil ditambahkan ke keranjang`, 'success');
    } else {
      showNotification(result.message || 'Gagal menambahkan ke keranjang', 'error');
    }
  } catch (error) {
    console.error('Error adding to cart:', error);
    showNotification('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
  }
}

function showNotification(message, type = 'info') {
  // Buat elemen notifikasi di tengah layar
  const notification = document.createElement('div');
  
  // Gaya untuk notifikasi di tengah layar
  Object.assign(notification.style, {
    position: 'fixed',
    top: '50%',
    left: '50%',
    transform: 'translate(-50%, -50%)',
    padding: '16px 24px',
    borderRadius: '8px',
    color: 'white',
    backgroundColor: type === 'error' ? '#ef4444' : type === 'success' ? '#10b981' : '#3b82f6',
    zIndex: '9999',
    fontWeight: '600',
    boxShadow: '0 4px 6px rgba(0,0,0,0.1)',
    fontSize: '14px',
    maxWidth: '400px',
    wordWrap: 'break-word',
    opacity: '0',
    transition: 'opacity 0.3s ease-in-out'
  });
  
  notification.textContent = message;
  
  // Tambahkan ke body
  document.body.appendChild(notification);
  
  // Tampilkan dengan efek fade-in
  setTimeout(() => {
    notification.style.opacity = '1';
  }, 10);
  
  // Hapus notifikasi setelah 3 detik
  setTimeout(() => {
    notification.style.opacity = '0';
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 300);
  }, 3000);
}

/* tombol refresh produk */
// Tidak ada tombol refresh karena sudah dihapus dari UI

/* ---------- INIT WITH LIVE DATA ---------- */
document.addEventListener('DOMContentLoaded', () => {
  // Initialize filters based on URL parameters
  initFiltersFromUrl();
  
  // Muat produk terbaru dari API saat halaman dimuat
  updateContent();
});