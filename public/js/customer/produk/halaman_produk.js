'use strict';

/* ---------- STATE ---------- */
let currentPage = 1;
const pageSize = 8;

/* Rekomendasi: pagination 4/halaman */
let rekomPage = 1;
const rekomPageSize = 4;
let rekomPool = [];

/* ---------- SVG rating ---------- */
// Menggunakan viewBox tanpa width/height agar bisa diatur melalui CSS
const STAR_FULL = `<svg viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF600"/></svg>`;
const STAR_HALF = `<svg viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M29.6691 32.9982L28.0534 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535V29.2285L29.6691 32.9982ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF700"/></svg>`;
const STAR_EMPTY = `<svg viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M17.3316 32.9493L23.5003 29.2285L29.6691 32.9982L28.0535 25.9482L33.4878 21.1993L26.3399 20.6118L23.5003 13.9535L20.6607 20.5628L13.5128 21.1993L18.9472 25.9482L17.3316 32.9493ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF600"/></svg>`;

/* ---------- DOM ---------- */
const grid = document.getElementById('produk-grid');
const pagin = document.getElementById('produk-pagination');
const inputSearch = document.getElementById('navbar-search'); // dari partial navbar
const selectKategori = document.getElementById('filter-kategori');
const selectSubkategori = document.getElementById('filter-subkategori');
const inputHargaMin = document.getElementById('filter-harga-min');
const inputHargaMax = document.getElementById('filter-harga-max');
const selectRating = document.getElementById('filter-rating');
const selectUrutkan = document.getElementById('filter-urutkan');

const rekomGrid = document.getElementById('rekom-grid');
const rekomPagin = document.getElementById('rekom-pagination');

/* ---------- ROUTE & API ENDPOINTS ---------- */
const API_PRODUCTS = '/api/products/filter';  // API endpoint untuk mengambil semua produk
const API_SEARCH = '/api/products/search';  // Endpoint untuk pencarian produk (jika ada)
const API_CATEGORY = '/api/products/filter';  // Endpoint untuk produk berdasarkan kategori (menggunakan parameter kategori)
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
  if (frac >= 0.5) half = 1;
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
  if (!img) return;
  img.onerror = null; // Prevent infinite loop if placeholder also fails

  // Create simple SVG as fallback
  const svgString = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400"><rect width="100%" height="100%" fill="#eef6f4"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="22" fill="#7aa29b">Gambar tidak tersedia</text></svg>';

  img.src = 'data:image/svg+xml;utf8,' + encodeURIComponent(svgString);
}

/* ---------- API CALLS FOR DYNAMIC SEARCH/FILTER ---------- */
async function searchProducts(search = '', category = 'all', subcategory = '', minPrice = '', maxPrice = '', rating = '', sort = '') {
  try {
    let url = API_PRODUCTS; // Use '/api/products/filter' for all cases
    const params = new URLSearchParams();

    // Add search parameter if provided
    if (search) {
      params.append('search', search);
    }

    // Add category parameter if provided
    if (category && category !== 'all') {
      params.append('kategori', category);
    }

    // Add subcategory parameter if provided
    if (subcategory) {
      params.append('subkategori', subcategory);
    }

    // Add min price parameter if provided
    if (minPrice) {
      params.append('min_price', minPrice);
    }

    // Add max price parameter if provided
    if (maxPrice) {
      params.append('max_price', maxPrice);
    }

    // Add rating parameter if provided
    if (rating) {
      params.append('rating', rating);
    }

    // Add sort parameter if provided
    if (sort) {
      params.append('sort', sort);
    }

    // Add cache busting parameter
    params.append('_cb', Date.now());

    url += '?' + params.toString();

    // Get CSRF token from meta tag, fallback to empty if not found
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Cache-Control': 'no-cache, no-store, must-revalidate',
        'Pragma': 'no-cache',
        'Expires': '0',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken || '',
        'Accept': 'application/json'
      }
    });

    // Check if response is OK before attempting to parse JSON
    if (!response.ok) {
      if (response.status === 500) {
        console.error('Server error when requesting products from:', url);
        throw new Error(`Server error: ${response.status}`);
      } else if (response.status === 404) {
        console.error('API endpoint not found:', url);
        throw new Error(`API endpoint not found: ${response.status}`);
      } else {
        console.error('HTTP error when requesting products:', response.status, url);
        throw new Error(`HTTP error: ${response.status}`);
      }
    }

    // Check if the response is actually JSON before parsing
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      console.error('Response is not JSON:', await response.text());
      throw new Error('Response is not JSON');
    }

    const result = await response.json();

    // Format the data to match what the render function expects
    const formattedProducts = Array.isArray(result) ?
      result.map(product => ({
        id: product.id,
        nama: product.nama || product.name || 'Produk Tanpa Nama',
        kategori: product.kategori || product.category?.name || 'Umum',
        subkategori: product.subkategori || product.subcategory?.name || 'Umum',
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
    return [];
  }
}

/* ---------- LOAD ALL PRODUCTS FROM API ---------- */
async function loadAllProducts(category = 'all', subcategory = '', minPrice = '', maxPrice = '', rating = '', sort = '') {
  try {
    let url = API_PRODUCTS;
    const params = new URLSearchParams();
    if (category && category !== 'all') {
      params.append('kategori', category);
    }

    // Add subcategory parameter if provided
    if (subcategory) {
      params.append('subkategori', subcategory);
    }

    // Add min price parameter if provided
    if (minPrice) {
      params.append('min_price', minPrice);
    }

    // Add max price parameter if provided
    if (maxPrice) {
      params.append('max_price', maxPrice);
    }

    // Add rating parameter if provided
    if (rating) {
      params.append('rating', rating);
    }

    // Add sort parameter if provided
    if (sort) {
      params.append('sort', sort);
    }

    // Add cache busting parameter
    params.append('_cb', Date.now());

    url += '?' + params.toString();

    // Get CSRF token from meta tag, fallback to empty if not found
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Cache-Control': 'no-cache, no-store, must-revalidate',
        'Pragma': 'no-cache',
        'Expires': '0',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken || '',
        'Accept': 'application/json'
      }
    });

    // Check if response is OK before attempting to parse JSON
    if (!response.ok) {
      if (response.status === 500) {
        console.error('Server error when requesting products from:', url);
        throw new Error(`Server error: ${response.status}`);
      } else if (response.status === 404) {
        console.error('API endpoint not found:', url);
        throw new Error(`API endpoint not found: ${response.status}`);
      } else {
        console.error('HTTP error when requesting products:', response.status, url);
        throw new Error(`HTTP error: ${response.status}`);
      }
    }

    // Check if the response is actually JSON before parsing
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      console.error('Response is not JSON:', await response.text());
      throw new Error('Response is not JSON');
    }

    const result = await response.json();

    // Format the data to match what the render function expects
    const formattedProducts = Array.isArray(result) ?
      result.map(product => ({
        id: product.id,
        nama: product.nama || product.name || 'Produk Tanpa Nama',
        kategori: product.kategori || product.category?.name || 'Umum',
        subkategori: product.subkategori || product.subcategory?.name || 'Umum',
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
    return [];
  }
}

/* ---------- LOAD POPULAR PRODUCTS FROM API ---------- */
async function loadPopularProducts() {
  try {
    // Use the popular API endpoint with cache busting
    const url = new URL('/api/products/popular', window.location.origin);
    url.searchParams.append('_cb', Date.now());

    // Get CSRF token from meta tag, fallback to empty if not found
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Cache-Control': 'no-cache, no-store, must-revalidate',
        'Pragma': 'no-cache',
        'Expires': '0',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken || '',
        'Accept': 'application/json'
      }
    });

    // Check if response is OK before attempting to parse JSON
    if (!response.ok) {
      if (response.status === 500) {
        console.error('Server error when requesting popular products from:', url);
        throw new Error(`Server error: ${response.status}`);
      } else if (response.status === 404) {
        console.error('API endpoint not found:', url);
        throw new Error(`API endpoint not found: ${response.status}`);
      } else {
        console.error('HTTP error when requesting popular products:', response.status, url);
        throw new Error(`HTTP error: ${response.status}`);
      }
    }

    // Check if the response is actually JSON before parsing
    const contentType = response.headers.get('content-type');
    if (!contentType || !contentType.includes('application/json')) {
      console.error('Response is not JSON:', await response.text());
      throw new Error('Response is not JSON');
    }

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
    return [];
  }
}




/* ---------- RENDER FUNCTIONS ---------- */
function renderList(products = []) {
  if (!grid || !pagin) return;

  // Calculate total pages based on current products
  const totalPage = Math.max(1, Math.ceil(products.length / pageSize));

  // Adjust current page if it exceeds total pages
  if (currentPage > totalPage) currentPage = Math.max(1, totalPage);

  const start = (currentPage - 1) * pageSize;
  const view = products.slice(start, start + pageSize);

  grid.innerHTML = view.map(p => {
    const href = `${DETAIL_BASE}${encodeURIComponent(p.id)}`;
    const tokoHref = `/toko/${p.toko || p.seller?.name || p.seller_id || 'toko-tidak-ditemukan'}`; // Assuming there's a toko page
    const imageSrc = p.gambar || 'src/placeholder.png';
    const altText = (p.nama || 'Produk Tidak Bernama').toString().replace(/[&<>"']/g, function(match) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
      }[match];
    });
    return `
      <div class="produk-card" data-product-id="${p.id}">
        <img src="${imageSrc}" alt="${altText}"
             onerror="handleImageError(this)"
             loading="lazy">
        <div class="produk-card-info">
          <div class="produk-card-content">
            <h3 class="produk-card-name">${p.nama}</h3>
            <div class="produk-card-sub">${p.subkategori || p.kategori}</div>
            <div class="produk-card-price">${p.harga}</div>
            <div class="produk-card-toko">
              <a href="${tokoHref}" class="toko-link" data-seller-name="${p.toko || p.seller?.name || 'Toko Umum'}">${p.toko || p.seller?.name || 'Toko Umum'}</a>
            </div>
            <div class="produk-card-stars" aria-label="Rating ${p.rating} dari 5">${starsHTML(p.rating)}</div>
          </div>
        </div>
        <div class="produk-card-actions">
          <a class="btn-lihat" data-product-id="${p.id}" href="${href}">Lihat Detail</a>
          <button class="btn-add" data-product-id="${p.id}" data-name="${p.nama}" type="button">+ Keranjang</button>
        </div>
      </div>`;
  }).join('');

  renderPagination(totalPage);
}

// Render pagination buttons
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

function renderRekomendasi(products = [], maxItems = 8) {
  if (!rekomGrid) return;

  // Pagination variables for rekomendasi
  const rekomPageSize = 4; // Sesuai dengan definisi di awal
  const totalRekomPages = Math.max(1, Math.ceil(products.length / rekomPageSize));
  const start = (rekomPage - 1) * rekomPageSize;
  const view = products.slice(start, start + rekomPageSize);

  rekomGrid.innerHTML = view.map(p => {
    const href = `${DETAIL_BASE}${encodeURIComponent(p.id)}`;
    const tokoName = p.toko || p.seller?.name || 'Toko Umum';
    const tokoHref = `/toko/${encodeURIComponent(tokoName)}`;
    const imageSrc = p.gambar || 'src/placeholder.png';
    const altText = p.nama.replace(/"/g, '&quot;').replace(/'/g, '&apos;');
    return `
      <div class="produk-card" data-product-id="${p.id}">
        <img src="${imageSrc}" alt="${altText}"
             onerror="handleImageError(this)"
             loading="lazy">
        <div class="produk-card-info">
          <div class="produk-card-content">
            <h3 class="produk-card-name">${p.nama}</h3>
            <div class="produk-card-sub">${p.subkategori || p.kategori}</div>
            <div class="produk-card-price">${p.harga}</div>
            <div class="produk-card-toko">
              <a href="${tokoHref}" class="toko-link" data-seller-name="${p.toko || p.seller?.name || 'Toko Umum'}">${p.toko || p.seller?.name || 'Toko Umum'}</a>
            </div>
            <div class="produk-card-stars" aria-label="Rating ${p.rating} dari 5">${starsHTML(p.rating)}</div>
          </div>
        </div>
        <div class="produk-card-actions">
          <a class="btn-lihat" data-product-id="${p.id}" href="${href}">Lihat Detail</a>
          <button class="btn-add" data-product-id="${p.id}" data-name="${p.nama}" type="button">+ Keranjang</button>
        </div>
      </div>`;
  }).join('');

  // Render pagination for rekomendasi
  renderRekomendasiPagination(totalRekomPages);
}

// Fungsi untuk merender pagination rekomendasi
function renderRekomendasiPagination(totalPage) {
  if (!rekomPagin) return;

  const btn = (label, page, disabled = false, extra = '', aria = '') =>
    `<button class="page-btn ${extra}" data-page="${page}" ${disabled ? 'disabled' : ''} ${aria}>${label}</button>`;

  const maxBtn = 5;
  let start = Math.max(1, rekomPage - Math.floor(maxBtn / 2));
  let end = start + maxBtn - 1;
  if (end > totalPage) { end = totalPage; start = Math.max(1, end - maxBtn + 1); }

  let html = '';
  html += btn('«', 1, rekomPage === 1, '', 'aria-label="Halaman pertama"');
  html += btn('‹', rekomPage - 1, rekomPage === 1, '', 'aria-label="Sebelumnya"');
  for (let i = start; i <= end; i++) {
    const active = i === rekomPage;
    html += btn(i, i, false, active ? 'active' : '', active ? 'aria-current="page"' : '');
  }
  html += btn('›', rekomPage + 1, rekomPage === totalPage, '', 'aria-label="Berikutnya"');
  html += btn('»', totalPage, rekomPage === totalPage, '', 'aria-label="Halaman terakhir"');

  rekomPagin.innerHTML = html;
}

function renderProdukPopuler(products = [], maxItems = 8) {
  const populerGrid = document.getElementById('populer-grid');
  if (!populerGrid) return;

  // Pagination variables for populer
  const populerPageSize = 4; // Use same page size as rekomendasi
  const totalPopulerPages = Math.max(1, Math.ceil(products.length / populerPageSize));
  const currentPage = typeof window.populerPage !== 'undefined' ? window.populerPage : 1;
  const start = (currentPage - 1) * populerPageSize;
  const view = products.slice(start, start + populerPageSize);

  populerGrid.innerHTML = view.map(p => {
    const href = `${DETAIL_BASE}${encodeURIComponent(p.id)}`;
    const tokoHref = `/toko/${p.toko || p.seller?.name || p.seller_id || 'toko-tidak-ditemukan'}`;
    const imageSrc = p.gambar || 'src/placeholder.png';
    const altText = p.nama.replace(/"/g, '&quot;').replace(/'/g, '&apos;');
    return `
      <div class="produk-card" data-product-id="${p.id}">
        <img src="${imageSrc}" alt="${altText}"
             onerror="handleImageError(this)"
             loading="lazy">
        <div class="produk-card-info">
          <div class="produk-card-content">
            <h3 class="produk-card-name">${p.nama}</h3>
            <div class="produk-card-sub">${p.subkategori || p.kategori}</div>
            <div class="produk-card-price">${p.harga}</div>
            <div class="produk-card-toko">
              <a href="${tokoHref}" class="toko-link" data-seller-name="${p.toko || p.seller?.name || 'Toko Umum'}">${p.toko || p.seller?.name || 'Toko Umum'}</a>
            </div>
            <div class="produk-card-stars" aria-label="Rating ${p.rating} dari 5">${starsHTML(p.rating)}</div>
          </div>
        </div>
        <div class="produk-card-actions">
          <a class="btn-lihat" data-product-id="${p.id}" href="${href}">Lihat Detail</a>
          <button class="btn-add" data-product-id="${p.id}" data-name="${p.nama}" type="button">+ Keranjang</button>
        </div>
      </div>`;
  }).join('');

  // Render pagination for populer if populer pagination element exists
  const populerPagination = document.getElementById('populer-pagination');
  if (populerPagination) {
    renderPopulerPagination(totalPopulerPages, populerPagination);
  }
}

// Fungsi untuk merender pagination populer
function renderPopulerPagination(totalPage, populerPaginElement) {
  if (!populerPaginElement) return;

  // Use populerPage variable - create it if it doesn't exist
  if (typeof populerPage === 'undefined') {
    window.populerPage = 1; // Use global variable
  }

  const btn = (label, page, disabled = false, extra = '', aria = '') =>
    `<button class="page-btn ${extra}" data-page="${page}" ${disabled ? 'disabled' : ''} ${aria}>${label}</button>`;

  const maxBtn = 5;
  let start = Math.max(1, window.populerPage - Math.floor(maxBtn / 2));
  let end = start + maxBtn - 1;
  if (end > totalPage) { end = totalPage; start = Math.max(1, end - maxBtn + 1); }

  let html = '';
  html += btn('«', 1, window.populerPage === 1, '', 'aria-label="Halaman pertama"');
  html += btn('‹', window.populerPage - 1, window.populerPage === 1, '', 'aria-label="Sebelumnya"');
  for (let i = start; i <= end; i++) {
    const active = i === window.populerPage;
    html += btn(i, i, false, active ? 'active' : '', active ? 'aria-current="page"' : '');
  }
  html += btn('›', window.populerPage + 1, window.populerPage === totalPage, '', 'aria-label="Berikutnya"');
  html += btn('»', totalPage, window.populerPage === totalPage, '', 'aria-label="Halaman terakhir"');

  populerPaginElement.innerHTML = html;
}


/* ---------- MAIN FUNCTION WITH DYNAMIC CONTENT ---------- */
let currentSearch = '';
let currentCategory = 'all';
let currentSubcategory = '';
let currentMinPrice = '';
let currentMaxPrice = '';
let currentRating = '';
let currentSort = 'popular';
let allProductsCache = []; // Cache untuk menyimpan semua produk saat ini

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

  // Initialize subcategory filter from URL
  const subcategoryParam = urlParams.get('subkategori');
  if (subcategoryParam) {
    const subcategorySelect = document.getElementById('filter-subkategori');
    if (subcategorySelect) {
      subcategorySelect.value = subcategoryParam;
      currentSubcategory = subcategoryParam;
    }
  }

  // Initialize min price filter from URL
  const minPriceParam = urlParams.get('min_price');
  if (minPriceParam) {
    const minPriceInput = document.getElementById('filter-harga-min');
    if (minPriceInput) {
      minPriceInput.value = minPriceParam;
      currentMinPrice = minPriceParam;
    }
  }

  // Initialize max price filter from URL
  const maxPriceParam = urlParams.get('max_price');
  if (maxPriceParam) {
    const maxPriceInput = document.getElementById('filter-harga-max');
    if (maxPriceInput) {
      maxPriceInput.value = maxPriceParam;
      currentMaxPrice = maxPriceParam;
    }
  }

  // Initialize rating filter from URL
  const ratingParam = urlParams.get('rating');
  if (ratingParam) {
    const ratingSelect = document.getElementById('filter-rating');
    if (ratingSelect) {
      ratingSelect.value = ratingParam;
      currentRating = ratingParam;
    }
  }

  // Initialize sort filter from URL
  const sortParam = urlParams.get('sort');
  if (sortParam) {
    const sortSelect = document.getElementById('filter-urutkan');
    if (sortSelect) {
      sortSelect.value = sortParam;
      currentSort = sortParam;
    }
  }
}

async function updateContent() {
  let products = [];

  // If search or filter is applied, use search API
  if (currentSearch || currentCategory !== 'all' || currentSubcategory || currentMinPrice || currentMaxPrice || currentRating || currentSort !== 'popular') {
    products = await searchProducts(currentSearch, currentCategory, currentSubcategory, currentMinPrice, currentMaxPrice, currentRating, currentSort);
  } else {
    // Otherwise, load all products from API to ensure we have the latest
    products = await loadAllProducts(currentCategory, currentSubcategory, currentMinPrice, currentMaxPrice, currentRating, currentSort);
  }

  // Cache products for efficient pagination
  allProductsCache = products;

  renderList(products);

  // For recommendations, use a subset of products from current results
  if (products.length > 0) {
    const recommendations = products.slice(0, 8).sort(() => Math.random() - 0.5);
    renderRekomendasi(recommendations);
    // Cache recommendations for pagination
    rekomPool = recommendations;
  } else {
    // If no products from current search/filter, load popular products for recommendations
    loadPopularProducts().then(recommendationProducts => {
      renderRekomendasi(recommendationProducts.slice(0, 8));
      // Cache recommendations for pagination
      rekomPool = recommendationProducts;
    });
  }

  // Load and render popular products
  loadPopularProducts().then(popularProducts => {
    renderProdukPopuler(popularProducts);
    // Cache popular products for pagination (if populer-pagination is added)
    window.popularPool = popularProducts;
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

  // Populate subcategories based on selected category
  populateSubcategories(currentCategory);

  updateContent();
});

// Function to populate subcategories based on selected category
async function populateSubcategories(category) {
  if (!selectSubkategori || !category || category === 'all') {
    // If no category is selected or 'all' is selected, reset subcategories
    selectSubkategori.innerHTML = '<option value="">Semua</option>';
    selectSubkategori.disabled = false;
    currentSubcategory = '';
    return;
  }

  // Map the category name to match the URL format expected by the API
  const categoryNameMap = {
    'Kuliner': 'kuliner',
    'Fashion': 'fashion',
    'Kerajinan Tangan': 'kerajinan',
    'Produk Berkebun': 'berkebun',
    'Produk Kesehatan': 'kesehatan',
    'Mainan': 'mainan',
    'Hampers': 'hampers',
  };

  // Check if the category matches a known category name and map it to URL format
  let apiCategory = category.toLowerCase();

  // Look for a match in the reverse mapping
  for (const [dbCategory, urlCategory] of Object.entries(categoryNameMap)) {
    if (dbCategory.toLowerCase() === category.toLowerCase()) {
      apiCategory = urlCategory;
      break;
    }
  }

  try {
    // Call the new API endpoint that we created to get subcategories by category name
    const response = await fetch(`/api/subcategories/category/${encodeURIComponent(apiCategory)}`);

    if (!response.ok) {
      selectSubkategori.innerHTML = '<option value="">Semua</option>';
      selectSubkategori.disabled = false;
      return;
    }

    const data = await response.json();

    if (!data.success) {
      selectSubkategori.innerHTML = '<option value="">Semua</option>';
      selectSubkategori.disabled = false;
      return;
    }

    // Clear existing options
    selectSubkategori.innerHTML = '<option value="">Semua</option>';

    // Add new options from subcategories
    if (data.subcategories && data.subcategories.length > 0) {
      data.subcategories.forEach(subcategory => {
        const option = document.createElement('option');
        // Use the slug of the subcategory name as the value
        const slug = subcategory.name.toLowerCase().replace(/\s+/g, '-');
        option.value = slug;
        option.textContent = subcategory.name;
        selectSubkategori.appendChild(option);
      });
    }

    selectSubkategori.disabled = false;
  } catch (error) {
    // On error, show an empty option but don't disable the field
    // This allows users to still use the filter if there's no subcategory data
    selectSubkategori.innerHTML = '<option value="">Semua</option>';
    selectSubkategori.disabled = false;
    console.error('Error fetching subcategories:', error);
  }
}

// Event listener for subcategory filter
selectSubkategori?.addEventListener('change', () => {
  currentSubcategory = selectSubkategori.value;
  currentPage = 1;

  // Update URL to reflect subcategory selection
  const url = new URL(window.location);
  if (currentSubcategory) {
    url.searchParams.set('subkategori', currentSubcategory);
  } else {
    url.searchParams.delete('subkategori');
  }
  window.history.replaceState({}, '', url);

  updateContent();
});

// Event listener for min price filter
inputHargaMin?.addEventListener('input', () => {
  currentMinPrice = inputHargaMin.value;
  currentPage = 1;

  // Update URL to reflect min price selection
  const url = new URL(window.location);
  if (currentMinPrice) {
    url.searchParams.set('min_price', currentMinPrice);
  } else {
    url.searchParams.delete('min_price');
  }
  window.history.replaceState({}, '', url);

  updateContent();
});

// Event listener for max price filter
inputHargaMax?.addEventListener('input', () => {
  currentMaxPrice = inputHargaMax.value;
  currentPage = 1;

  // Update URL to reflect max price selection
  const url = new URL(window.location);
  if (currentMaxPrice) {
    url.searchParams.set('max_price', currentMaxPrice);
  } else {
    url.searchParams.delete('max_price');
  }
  window.history.replaceState({}, '', url);

  updateContent();
});

// Event listener for rating filter
selectRating?.addEventListener('change', () => {
  currentRating = selectRating.value;
  currentPage = 1;

  // Update URL to reflect rating selection
  const url = new URL(window.location);
  if (currentRating) {
    url.searchParams.set('rating', currentRating);
  } else {
    url.searchParams.delete('rating');
  }
  window.history.replaceState({}, '', url);

  updateContent();
});

// Event listener for sort filter
selectUrutkan?.addEventListener('change', () => {
  currentSort = selectUrutkan.value;
  currentPage = 1;

  // Update URL to reflect sort selection
  const url = new URL(window.location);
  if (currentSort) {
    url.searchParams.set('sort', currentSort);
  } else {
    url.searchParams.delete('sort');
  }
  window.history.replaceState({}, '', url);

  updateContent();
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

// Pagination event listener for main products
pagin?.addEventListener('click', e => {
  const target = e.target.closest('.page-btn');
  if (!target || target.disabled) return;

  const page = parseInt(target.dataset.page);
  if (page && page !== currentPage) {
    currentPage = page;
    // Re-render products with current page
    if (allProductsCache && allProductsCache.length > 0) {
      renderList(allProductsCache);
    } else {
      updateContent(); // Re-fetch if cache is not available
    }
  }
});

// Pagination event listener for rekomendasi
rekomPagin?.addEventListener('click', e => {
  const target = e.target.closest('.page-btn');
  if (!target || target.disabled) return;

  const page = parseInt(target.dataset.page);
  if (page && page !== rekomPage) {
    rekomPage = page;
    // Re-render rekomendasi products with current page
    // We need to have the full rekom pool to paginate, so we'll need to store it
    if (rekomPool && rekomPool.length > 0) {
      renderRekomendasi(rekomPool);
    } else {
      // If no pool, we need to fetch again
      loadPopularProducts().then(recommendationProducts => {
        rekomPool = recommendationProducts;
        renderRekomendasi(rekomPool);
      });
    }
  }
});

// Pagination event listener for populer (if pagination element exists)
const populerPagination = document.getElementById('populer-pagination');
populerPagination?.addEventListener('click', e => {
  const target = e.target.closest('.page-btn');
  if (!target || target.disabled) return;

  const page = parseInt(target.dataset.page);
  if (page && page !== (window.populerPage || 1)) {
    window.populerPage = page;
    // Re-render populer products with current page
    loadPopularProducts().then(popularProducts => {
      // We need to have the full popular pool to paginate
      window.popularPool = popularProducts;
      renderProdukPopuler(popularProducts);
    });
  }
});

/* ---------- INIT WITH LIVE DATA ---------- */
document.addEventListener('DOMContentLoaded', () => {
  // Initialize filters based on URL parameters
  initFiltersFromUrl();

  // Muat produk terbaru dari API saat halaman dimuat
  updateContent();
});