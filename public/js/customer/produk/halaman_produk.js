'use strict';

/* ---------- DATA DUMMY (gambar & fallback) ---------- */
const IMG_DEFAULT = 'src/product_1.png';
const IMG_PLACEHOLDER =
  'data:image/svg+xml;utf8,' +
  encodeURIComponent(
    `<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400">
       <rect width="100%" height="100%" fill="#eef6f4"/>
       <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
             font-family="Arial, Helvetica, sans-serif" font-size="22" fill="#7aa29b">
         Gambar tidak tersedia
       </text>
     </svg>`
  );

const daftarProduk = [
  {nama:"Madu Hutan 250ml", kategori:"Minuman", harga:"Rp 62.000", gambar:IMG_DEFAULT, rating:"4.4"},
  {nama:"Teh Bunga Telang", kategori:"Minuman", harga:"Rp 28.000", gambar:IMG_DEFAULT, rating:"4.2"},
  {nama:"Keripik Pisang Cokelat", kategori:"Camilan", harga:"Rp 19.000", gambar:IMG_DEFAULT, rating:"3.7"},
  {nama:"Tas Anyaman Pandan", kategori:"Kerajinan", harga:"Rp 95.000", gambar:IMG_DEFAULT, rating:"4.6"},
  {nama:"Kopi Liberika 250gr", kategori:"Kopi", harga:"Rp 45.000", gambar:IMG_DEFAULT, rating:"4.6"},
  {nama:"Sabun Herbal Zaitun", kategori:"Sabun", harga:"Rp 25.000", gambar:IMG_DEFAULT, rating:"4.7"},
  {nama:"Keranjang Rotan Handmade", kategori:"Kerajinan", harga:"Rp 120.000", gambar:IMG_DEFAULT, rating:"4.5"},
  {nama:"Brownies Panggang", kategori:"Kue", harga:"Rp 38.000", gambar:IMG_DEFAULT, rating:"4.8"},
  {nama:"Dodol Durian", kategori:"Camilan", harga:"Rp 30.000", gambar:IMG_DEFAULT, rating:"4.1"},
  {nama:"Sirup Jahe Merah", kategori:"Minuman", harga:"Rp 27.000", gambar:IMG_DEFAULT, rating:"3.9"},
  {nama:"Sambal Bawang Rumahan", kategori:"Lainnya", harga:"Rp 18.000", gambar:IMG_DEFAULT, rating:"4.5"},
  {nama:"Kerupuk Ikan Tenggiri", kategori:"Camilan", harga:"Rp 22.000", gambar:IMG_DEFAULT, rating:"4.0"},
  {nama:"Toples Rotan Mini", kategori:"Kerajinan", harga:"Rp 35.000", gambar:IMG_DEFAULT, rating:"4.2"},
  {nama:"Sabun Kopi Scrub", kategori:"Sabun", harga:"Rp 29.000", gambar:IMG_DEFAULT, rating:"4.7"},
  {nama:"Kopi Robusta 200gr", kategori:"Kopi", harga:"Rp 39.000", gambar:IMG_DEFAULT, rating:"4.4"},
  {nama:"Kue Sagu Keju", kategori:"Kue", harga:"Rp 33.000", gambar:IMG_DEFAULT, rating:"4.6"},
  {nama:"Keripik Singkong Balado", kategori:"Camilan", harga:"Rp 16.000", gambar:IMG_DEFAULT, rating:"3.5"},
  {nama:"Teh Rosella", kategori:"Minuman", harga:"Rp 24.000", gambar:IMG_DEFAULT, rating:"4.3"},
  {nama:"Anyaman Bambu Serbaguna", kategori:"Kerajinan", harga:"Rp 80.000", gambar:IMG_DEFAULT, rating:"4.5"},
  {nama:"Kopi Arabika 250gr", kategori:"Kopi", harga:"Rp 58.000", gambar:IMG_DEFAULT, rating:"4.7"},
  {nama:"Sabun Susu Kambing", kategori:"Sabun", harga:"Rp 26.000", gambar:IMG_DEFAULT, rating:"4.5"},
  {nama:"Rengginang Udang", kategori:"Camilan", harga:"Rp 21.000", gambar:IMG_DEFAULT, rating:"4.2"},
  {nama:"Cookies Cokelat Chip", kategori:"Kue", harga:"Rp 34.000", gambar:IMG_DEFAULT, rating:"4.6"},
  {nama:"Batik Tulis Mini Frame", kategori:"Kerajinan", harga:"Rp 150.000", gambar:IMG_DEFAULT, rating:"4.8"},
];

/* ---------- STATE ---------- */
let currentPage = 1;
const pageSize  = 8;

/* Rekomendasi: pagination 4/halaman */
let rekomPage = 1;
const rekomPageSize = 4;
let rekomPool = [];

/* ---------- SVG rating ---------- */
const STAR_FULL  = `<svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF600"/></svg>`;
const STAR_HALF  = `<svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M29.6691 32.9982L28.0534 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535V29.2285L29.6691 32.9982ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF700"/></svg>`;
const STAR_EMPTY = `<svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M17.3316 32.9493L23.5003 29.2285L29.6691 32.9982L28.0535 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535L20.6607 20.5628L13.5128 21.1993L18.9472 25.9482L17.3316 32.9493ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF600"/></svg>`;

/* ---------- DOM ---------- */
const grid           = document.getElementById('produk-grid');
const pagin          = document.getElementById('produk-pagination');
const inputSearch    = document.getElementById('navbar-search'); // dari partial navbar
const selectKategori = document.getElementById('filter-kategori');

const rekomGrid      = document.getElementById('rekom-grid');
const rekomPagin     = document.getElementById('rekom-pagination');

/* ---------- ROUTE DETAIL ---------- */
const DETAIL_BASE = '/produk_detail?nama=';

/* ---------- HELPERS ---------- */
const debounce = (fn, wait=200) => {
  let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
};

function starsHTML(num){
  const r = Math.max(0, Math.min(5, parseFloat(num)||0));
  let full = Math.floor(r), half = 0;
  const frac = r - full;
  if (frac >= 0.75) full += 1; else if (frac >= 0.25) half = 1;
  const empty = Math.max(0, 5 - full - half);
  return `${STAR_FULL.repeat(full)}${half ? STAR_HALF : ''}${STAR_EMPTY.repeat(empty)}`;
}

function filterData(){
  let data = daftarProduk.slice();
  const kw = (inputSearch?.value || '').trim().toLowerCase();
  if (kw) data = data.filter(p => p.nama.toLowerCase().includes(kw) || p.kategori.toLowerCase().includes(kw));
  if (selectKategori && selectKategori.value !== 'all') data = data.filter(p => p.kategori === selectKategori.value);
  return data;
}

function shuffle(a){
  const arr = a.slice();
  for (let i=arr.length-1;i>0;i--){
    const j = Math.floor(Math.random()*(i+1));
    [arr[i],arr[j]]=[arr[j],arr[i]];
  }
  return arr;
}

let lastFilterKey = '';
function currentFilterKey(){
  return `${selectKategori ? selectKategori.value : 'all'}|${(inputSearch?.value||'').trim().toLowerCase()}`;
}

/* ---------- RENDER LIST (utama) ---------- */
function renderList(){
  if (!grid || !pagin) return;

  const filtered = filterData();
  const totalPage = Math.max(1, Math.ceil(filtered.length / pageSize));
  if (currentPage > totalPage) currentPage = totalPage;

  const start = (currentPage - 1) * pageSize;
  const view  = filtered.slice(start, start + pageSize);

  grid.innerHTML = view.map(p=>{
    const href = `${DETAIL_BASE}${encodeURIComponent(p.nama)}`;
    return `
      <div class="produk-card">
        <img src="${p.gambar}" alt="${p.nama}"
             onerror="this.onerror=null;this.src='${IMG_PLACEHOLDER}'">
        <div class="produk-card-info">
          <h3 class="produk-card-name">${p.nama}</h3>
          <div class="produk-card-sub">${p.kategori}</div>
          <div class="produk-card-price">${p.harga}</div>
          <div class="produk-card-stars" aria-label="Rating ${p.rating} dari 5">${starsHTML(p.rating)}</div>
        </div>
        <div class="produk-card-actions">
          <a class="btn-lihat" data-name="${p.nama}" href="${href}">Lihat Detail</a>
          <button class="btn-add" data-name="${p.nama}" type="button">+ Keranjang</button>
        </div>
      </div>`;
  }).join('');

  renderPagination(totalPage);

  // refresh rekom-pool kalau filter berubah
  const k = currentFilterKey();
  if (k !== lastFilterKey){
    lastFilterKey = k;
    const base = filtered.length ? filtered : daftarProduk;
    rekomPool = shuffle(base);
    rekomPage = 1;
  }
  renderRekomendasi();
}

/* ---------- PAGINATION (utama) ---------- */
function renderPagination(totalPage){
  if (!pagin) return;

  const btn = (label, page, disabled=false, extra='', aria='') =>
    `<button class="page-btn ${extra}" data-page="${page}" ${disabled?'disabled':''} ${aria}>${label}</button>`;

  const maxBtn = 5;
  let start = Math.max(1, currentPage - Math.floor(maxBtn/2));
  let end   = start + maxBtn - 1;
  if (end > totalPage){ end = totalPage; start = Math.max(1, end - maxBtn + 1); }

  let html = '';
  html += btn('«', 1, currentPage===1, '', 'aria-label="Halaman pertama"');
  html += btn('‹', currentPage-1, currentPage===1, '', 'aria-label="Sebelumnya"');
  for (let i=start;i<=end;i++) {
    const active = i===currentPage;
    html += btn(i, i, false, active?'active':'', active?'aria-current="page"':'');
  }
  html += btn('›', currentPage+1, currentPage===totalPage, '', 'aria-label="Berikutnya"');
  html += btn('»', totalPage, currentPage===totalPage, '', 'aria-label="Halaman terakhir"');

  pagin.innerHTML = html;
}

/* ---------- RENDER REKOMENDASI ---------- */
function renderRekomendasi(){
  if (!rekomGrid) return;

  const total = Math.max(1, Math.ceil(rekomPool.length / rekomPageSize));
  if (rekomPage > total) rekomPage = total;

  const start = (rekomPage - 1) * rekomPageSize;
  const view  = rekomPool.slice(start, start + rekomPageSize);

  rekomGrid.innerHTML = view.map(p=>{
    const href = `${DETAIL_BASE}${encodeURIComponent(p.nama)}`;
    return `
      <div class="rek-card">
        <img class="rek-img" src="${p.gambar}" alt="${p.nama}"
             onerror="this.onerror=null;this.src='${IMG_PLACEHOLDER}'">
        <div class="rek-body">
          <div class="rek-name">${p.nama}</div>
          <div class="rek-cat">${p.kategori}</div>
          <div class="rek-price">${p.harga}</div>
          <div class="produk-card-stars" aria-label="Rating ${p.rating} dari 5">${starsHTML(p.rating)}</div>
          <div class="rek-actions">
            <a class="rek-btn primary" data-name="${p.nama}" href="${href}">Lihat Detail</a>
            <button class="rek-btn ghost" data-name="${p.nama}" type="button">+ Keranjang</button>
          </div>
        </div>
      </div>`;
  }).join('');

  renderRekomPagination(total);
}

/* ---------- Pagination rekomendasi ---------- */
function renderRekomPagination(totalPage){
  if (!rekomPagin) return;

  const btn = (label, page, disabled=false, extra='', aria='') =>
    `<button class="page-btn ${extra}" data-rpage="${page}" ${disabled?'disabled':''} ${aria}>${label}</button>`;

  const maxBtn = 5;
  let start = Math.max(1, rekomPage - Math.floor(maxBtn/2));
  let end   = start + maxBtn - 1;
  if (end > totalPage){ end = totalPage; start = Math.max(1, end - maxBtn + 1); }

  let html = '';
  html += btn('«', 1, rekomPage===1, '', 'aria-label="Halaman pertama rekomendasi"');
  html += btn('‹', rekomPage-1, rekomPage===1, '', 'aria-label="Sebelumnya"');
  for (let i=start;i<=end;i++) {
    const active = i===rekomPage;
    html += btn(i, i, false, active?'active':'', active?'aria-current="page"':'');
  }
  html += btn('›', rekomPage+1, rekomPage===totalPage, '', 'aria-label="Berikutnya"');
  html += btn('»', totalPage, rekomPage===totalPage, '', 'aria-label="Halaman terakhir rekomendasi"');

  rekomPagin.innerHTML = html;
}

/* ---------- EVENTS ---------- */
inputSearch?.addEventListener('input', debounce(()=>{
  currentPage = 1; renderList();
}, 200));

selectKategori?.addEventListener('change', ()=>{
  currentPage = 1; renderList();
});

pagin?.addEventListener('click', e=>{
  const t = e.target.closest('.page-btn');
  if (!t || t.disabled) return;
  const to = +t.dataset.page;
  if (to>=1){ currentPage = to; renderList(); }
});

rekomPagin?.addEventListener('click', e=>{
  const t = e.target.closest('.page-btn');
  if (!t || t.disabled) return;
  const to = +t.dataset.rpage;
  if (to>=1){ rekomPage = to; renderRekomendasi(); }
});

/* tombol di grid utama */
grid?.addEventListener('click', e=>{
  const add  = e.target.closest('.btn-add');
  if (add){ alert(`${add.dataset.name} ditambahkan ke keranjang!`); return; }
});

/* tombol di grid rekomendasi */
rekomGrid?.addEventListener('click', e=>{
  const add  = e.target.closest('.rek-btn.ghost');
  const detail = e.target.closest('.rek-btn.primary');
  if (add){ alert(`${add.dataset.name} ditambahkan ke keranjang!`); return; }
  if (detail){ /* biarkan <a> navigate */ return; }
});

/* ---------- INIT ---------- */
lastFilterKey = currentFilterKey();
rekomPool = shuffle(filterData().length ? filterData() : daftarProduk);
renderList();