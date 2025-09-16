// ====== Data (sementara; ganti dari backend bila perlu) ======
const WISHLIST = [
  { id:1, title:'Xbox Series S With Series X', price:1008000,  img:'https://picsum.photos/seed/xbox/640/400',  shop:'Toko Elektronik Jaya',     date:'24 Agu 2025', liked:true,  url:'/produk/1' },
  { id:2, title:'iPhone 15 128GB',             price:8670000,  img:'https://picsum.photos/seed/iphone/640/400', shop:'iStore Official',        date:'21 Agu 2025', liked:true,  url:'/produk/2' },
  { id:3, title:'PlayStation 5 Pro',            price:14120000, img:'https://picsum.photos/seed/ps5/640/400',     shop:'Game Center Nusantara',  date:'18 Agu 2025', liked:true,  url:'/produk/3' },
  { id:4, title:'Galaxy Buds3 (Silver)',        price:2142600,  img:'https://picsum.photos/seed/buds/640/400',    shop:'Samsung Official Store', date:'14 Agu 2025', liked:true,  url:'/produk/4' },
  { id:5, title:'Nike Kobe 6 “Grinch”',         price:32000000, img:'https://picsum.photos/seed/kobe/640/400',    shop:'Sneakerland Bandung',    date:'12 Agu 2025', liked:true,  url:'/produk/5' },
];

// ====== Utils ======
const $  = (s,root=document)=>root.querySelector(s);
const $$ = (s,root=document)=>[...root.querySelectorAll(s)];
const fmtIDR = n => (Number(n)||0).toLocaleString('id-ID');

// ====== Elements ======
const grid     = $('#wishlistGrid');
const filterEl = $('#wlFilter');
const emptyBox = $('#wlEmpty');
const toolbar  = document.querySelector('.wl-toolbar');

// ====== State ======
let STATE = Array.isArray(window.__WISHLIST__) ? window.__WISHLIST__.slice() : WISHLIST.slice();

// ====== Templates ======
function cardTemplate(item){
  const onStyle  = item.liked ? '' : 'display:none';
  const offStyle = item.liked ? 'display:none' : '';
  return `
  <article class="wl-card" role="listitem" data-id="${item.id}">
    <header class="card__head">
      <div class="user__name">${item.shop}</div>
      <time class="card__time">${item.date}</time>
    </header>

    <div class="card__body">
      <a class="product" href="${item.url}" aria-label="Lihat produk ${item.title}">
        <div class="product__thumb">
          <img src="${item.img}" alt="${item.title}">
        </div>
        <div class="product__meta">
          <div class="product__title">${item.title}</div>
          <div class="product__shop">${item.shop}</div>
        </div>
      </a>
    </div>

    <footer class="card__foot">
      <div class="wl-price">Rp ${fmtIDR(item.price)}</div>
      <button class="wl-like" aria-pressed="true" title="Hapus dari wishlist">
        <svg class="svg-off" style="${offStyle}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 47 47"><path d="M23.6962 36.3271L23.5003 36.5229L23.2849 36.3271C13.9828 27.8867 7.83366 22.3054 7.83366 16.6458C7.83366 12.7292 10.7712 9.79167 14.6878 9.79167C17.7037 9.79167 20.6412 11.75 21.6791 14.4133H25.3216C26.3595 11.75 29.297 9.79167 32.3128 9.79167C36.2295 9.79167 39.167 12.7292 39.167 16.6458C39.167 22.3054 33.0178 27.8867 23.6962 36.3271ZM32.3128 5.875C28.9053 5.875 25.6349 7.46125 23.5003 9.94833C21.3657 7.46125 18.0953 5.875 14.6878 5.875C8.65616 5.875 3.91699 10.5946 3.91699 16.6458C3.91699 24.0287 10.5753 30.08 20.6607 39.2254L23.5003 41.8104L26.3399 39.2254C36.4253 30.08 43.0837 24.0287 43.0837 16.6458C43.0837 10.5946 38.3445 5.875 32.3128 5.875Z" fill="#F24822"/></svg>
        <svg class="svg-on"  style="${onStyle}"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 47 47"><path d="M23.5003 41.8104L20.6607 39.2254C10.5753 30.08 3.91699 24.0287 3.91699 16.6458C3.91699 10.5946 8.65616 5.875 14.6878 5.875C18.0953 5.875 21.3657 7.46125 23.5003 9.94833C25.6349 7.46125 28.9053 5.875 32.3128 5.875C38.3445 5.875 43.0837 10.5946 43.0837 16.6458C43.0837 24.0287 36.4253 30.08 26.3399 39.2254L23.5003 41.8104Z" fill="#F24822"/></svg>
      </button>
    </footer>
  </article>`;
}

// ====== Sorting & empty-state ======
function applySort(items, key){
  const arr = items.slice();
  if (key === 'termurah') arr.sort((a,b)=>a.price - b.price);
  else if (key === 'termahal') arr.sort((a,b)=>b.price - a.price);
  return arr; // "terbaru": biarkan urutan data server
}
function updateEmptyState(){
  const hasItems = STATE.length > 0;
  if (emptyBox) emptyBox.hidden = hasItems;                // tampil hanya saat 0
  if (toolbar)  toolbar.style.visibility = hasItems ? 'visible' : 'hidden';
}

// ====== Render ======
function render(){
  if (!grid) return;
  const sorted = applySort(STATE, filterEl?.value || 'terbaru');
  grid.innerHTML = sorted.map(cardTemplate).join('');
  updateEmptyState();
}

// ====== Events ======
document.addEventListener('click', (e)=>{
  const btn = e.target.closest('.wl-like');
  if (!btn) return;
  const art = btn.closest('.wl-card');
  const id  = Number(art?.dataset.id);
  const idx = STATE.findIndex(it => it.id === id);
  if (idx !== -1) {
    STATE.splice(idx, 1); // remove dari wishlist
    render();
  }
});
filterEl?.addEventListener('change', render);

// ====== Init ======
render();
