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
  return 'Rp ' + price.toLocaleString('id-ID');
}

/* ---------- API CALLS FOR DYNAMIC SEARCH/FILTER ---------- */
async function searchProducts(search = '', category = 'all') {
  try {
    let url = API_SEARCH;
    const params = new URLSearchParams();
    
    if (search) params.append('q', search);
    if (category && category !== 'all') params.append('kategori', category);
    
    url += '?' + params.toString();
    
    const response = await fetch(url);
    const result = await response.json();
    
    // Format the data to match what the render function expects
    const formattedProducts = Array.isArray(result) ? 
      result.map(product => ({
        id: product.id,
        nama: product.name,
        kategori: product.category?.name || product.kategori || 'Umum',
        harga: formatPrice(product.price),
        gambar: product.image ? product.image : (product.gambar || 'src/placeholder.png'),
        rating: product.average_rating || product.rating || 0,
        toko: product.seller?.name || product.toko || 'Toko Umum',
        deskripsi: product.description || ''
      })) : 
      [];
    
    return formattedProducts;
  } catch (error) {
    console.error('Error searching products:', error);
    showNotification('Gagal memuat produk', 'error');
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
    return `
      <div class="produk-card" data-product-id="${p.id}">
        <img src="${p.gambar}" alt="${p.nama}"
             onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,' + encodeURIComponent(
               '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"600\" height=\"400\"><rect width=\"100%\" height=\"100%\" fill=\"#eef6f4\"/><text x=\"50%\" y=\"50%\" dominant-baseline=\"middle\" text-anchor=\"middle\" font-family=\"Arial, Helvetica, sans-serif\" font-size=\"22\" fill=\"#7aa29b\">Gambar tidak tersedia</text></svg>'
             )">
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
    return `
      <div class="rek-card" data-product-id="${p.id}">
        <img class="rek-img" src="${p.gambar}" alt="${p.nama}"
             onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,' + encodeURIComponent(
               '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"600\" height=\"400\"><rect width=\"100%\" height=\"100%\" fill=\"#eef6f4\"/><text x=\"50%\" y=\"50%\" dominant-baseline=\"middle\" text-anchor=\"middle\" font-family=\"Arial, Helvetica, sans-serif\" font-size=\"22\" fill=\"#7aa29b\">Gambar tidak tersedia</text></svg>'
             )">
        <div class="rek-body">
          <div class="rek-name">${p.nama}</div>
          <div class="rek-cat">${p.kategori}</div>
          <div class="rek-price">${p.harga}</div>
          <div class="rek-toko">${p.toko}</div>
          <div class="produk-card-stars" aria-label="Rating ${p.rating} dari 5">${starsHTML(p.rating)}</div>
          <div class="rek-rating-angka">${p.rating}</div>
          <div class="rek-actions">
            <a class="rek-btn primary" data-product-id="${p.id}" href="${href}">Lihat Detail</a>
            <button class="rek-btn ghost" data-product-id="${p.id}" data-name="${p.nama}" type="button">+ Keranjang</button>
          </div>
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
let allProducts = window.initialProducts || []; // Data dari server bisa ditempatkan di window

async function updateContent() {
  let products = allProducts;
  
  // Filter based on search and category if they exist
  if (currentSearch || currentCategory !== 'all') {
    products = await searchProducts(currentSearch, currentCategory);
  }
  
  renderList(products);
  
  // For recommendations, show a subset of products or popular products
  const recommendations = products.length > 0 ? 
    products.slice(0, 8).sort(() => Math.random() - 0.5) : 
    window.popularProducts || [];
  renderRekomendasi(recommendations);
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
  const add = e.target.closest('.rek-btn.ghost');
  const detail = e.target.closest('.rek-btn.primary');
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
  // Hapus notifikasi yang sudah ada
  const existingNotifications = document.querySelectorAll('.notification');
  existingNotifications.forEach(notification => notification.remove());
  
  // Buat elemen notifikasi
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  
  // Gaya dasar untuk notifikasi
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

/* ---------- INIT WITH SERVER DATA ---------- */
document.addEventListener('DOMContentLoaded', () => {
  // Ambil data produk dari server (akan diisi oleh blade template nanti)
  // Jika tidak ada data dari server, kita bisa membuat fungsi untuk mengambil data pertama kali
  updateContent();
});