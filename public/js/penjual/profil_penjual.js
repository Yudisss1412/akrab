"use strict";

/* =========================================================
   Inisialisasi hanya jika section Produk ada
========================================================= */
(function () {
  const $  = (sel) => document.querySelector(sel);
  const on = (el, ev, fn, opts) => el && el.addEventListener(ev, fn, opts);

  const grid = $("#grid");
  if (!grid) {
    // Section produk tidak ada di halaman ini -> hentikan tanpa error.
    return;
  }

  /* =========================================================
     DUMMY DATA PRODUK
  ========================================================= */
  const CATEGORIES = ["Electronics","Accessories","Home","Travel","Toys"];
  const IMGS = [
    "https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?q=80&w=600&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1512496015851-a90fb38ba796?q=80&w=600&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=600&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=600&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1546868871-7041f2a55e12?q=80&w=600&auto=format&fit=crop",
  ];

  const PLACEHOLDER_SVG =
    "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='200'%3E%3Crect width='100%25' height='100%25' fill='%23f3f4f6'/%3E%3Ctext x='50%25' y='50%25' fill='%239ca3af' dominant-baseline='middle' text-anchor='middle' font-family='Arial' font-size='14'%3ENo Image%3C/text%3E%3C/svg%3E";

  let all = Array.from({length:23}, (_,i)=>({
    id:i+1,
    name:[
      "Canon Camera X-7800","Apple 15 Pro Max","MINISO Upgraded Lamp",
      "Universal Travel Adapter","Power Point Clicker","Pentagon 55 Cms Luggage",
      "USB-C Hub 9-in-1","Noise Cancelling Headset","Smart Home Plug","Mini Projector"
    ][i%10],
    img: IMGS[i%IMGS.length],
    price: [399,2400,11.49,14.59,200,49.99,29.99,89.99,15.99,679][i%10],
    sold:  [5,3,9,5,3,15,42,8,6,5][i%10],
    category: CATEGORIES[i% CATEGORIES.length],
    tag: ["Item","Service","Dynamic","File"][i%4]
  }));

  /* =========================================================
     STATE
  ========================================================= */
  let filtered = [...all];
  let page = 1;
  const perPage = 8;

  /* =========================================================
     HELPERS
  ========================================================= */
  const fmtUSD = new Intl.NumberFormat("en-US",{style:"currency",currency:"USD"});
  function formatUSD(n){ return fmtUSD.format(n).replace(".00",""); }

  function debounce(fn, ms=200){
    let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args), ms); };
  }
  const unique = (arr) => [...new Set(arr)];
  const prefersReduced = () =>
    window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function clampPage(){
    const max = Math.max(1, Math.ceil(filtered.length / perPage));
    if (page > max) page = max;
    if (page < 1) page = 1;
    return max;
  }

  /* =========================================================
     HOOKS
  ========================================================= */
  const filterCategory = $("#filterCategory");
  const searchInput    = $("#searchInput");
  const productCount   = $("#productCount");
  const prevBtn        = $("#prevPage");
  const nextBtn        = $("#nextPage");
  const pageNums       = $("#pageNums");

  /* =========================================================
     INIT KATEGORI
  ========================================================= */
  (function initCategoryOptions(){
    if(!filterCategory) return;
    const existing = Array.from(filterCategory.querySelectorAll("option")).map(o=>o.value);
    const sources  = unique([...CATEGORIES, ...all.map(p=>p.category)]);
    sources
      .filter(c=>!existing.includes(c))
      .forEach(c=>{
        const opt=document.createElement("option");
        opt.value=c; opt.textContent=c;
        filterCategory.appendChild(opt);
      });
  })();

  /* =========================================================
     FILTER + RENDER
  ========================================================= */
  function applyFilter(){
    const q   = (searchInput?.value||"").toLowerCase().trim();
    const cat = filterCategory?.value || "all";
    filtered = all.filter(p=>{
      const okCat = cat==="all" ? true : p.category===cat;
      const okQ   = (`${p.name} ${p.category}`).toLowerCase().includes(q);
      return okCat && okQ;
    });
    page = 1;
    render(true);
  }

  function render(scrollTop=false){
    const start = (page-1)*perPage;
    const list  = filtered.slice(start, start+perPage);

    grid.innerHTML = list.map(p=>{
      const name = String(p.name||"");
      const img  = String(p.img||"");
      return `
        <article class="pcard" data-id="${p.id}">
          <div class="pthumb">
            <img src="${img}" alt="${name}" loading="lazy">
          </div>
          <div class="ptitle" title="${name}">${name}</div>
          <div class="pmeta">
            <span class="tag">🏷️ ${p.category}</span>
            <span class="tag">🏷️ ${p.tag}</span>
          </div>
          <div class="pmeta">
            <span class="pprice">${formatUSD(p.price)}</span>
            <span class="tag">🔁 ${p.sold}</span>
            <span class="pdots" title="More" role="button" tabindex="0">⋯</span>
          </div>
        </article>
      `;
    }).join("");

    const maxPage = clampPage();
    if(productCount) productCount.textContent = filtered.length;
    if(prevBtn) prevBtn.disabled = page<=1;
    if(nextBtn) nextBtn.disabled = page>=maxPage;

    if(pageNums){
      pageNums.innerHTML = Array.from({length:maxPage}, (_,i)=>{
        const n=i+1, act = n===page ? "is-active" : "";
        const aria = n===page ? ` aria-current="page"` : "";
        return `<span class="num ${act}" data-n="${n}" role="button" tabindex="0"${aria}>${n}</span>`;
      }).join("");
    }

    if(scrollTop && !prefersReduced()){
      grid.scrollIntoView({block:"start", behavior:"smooth"});
    }
  }

  /* =========================================================
     IMAGE FALLBACK (tanpa onerror inline)
  ========================================================= */
  on(grid, "error", (e)=>{
    const t = e.target;
    if (t && t.tagName === "IMG") {
      t.src = PLACEHOLDER_SVG;
    }
  }, true); // capture

  /* =========================================================
     EVENTS
  ========================================================= */
  on(filterCategory, "change", applyFilter);

  if(searchInput){
    const doFilter = debounce(applyFilter, 200);
    on(searchInput, "input", doFilter);
    on(searchInput, "keydown", (e)=>{
      if(e.key==="Escape"){
        searchInput.value = "";
        applyFilter();
      }
    });
  }

  on(prevBtn, "click", ()=>{
    if(page>1){ page--; render(true); }
  });
  on(nextBtn, "click", ()=>{
    const maxPage = clampPage();
    if(page<maxPage){ page++; render(true); }
  });
  on(pageNums, "click", (e)=>{
    const el = e.target.closest(".num"); if(!el) return;
    const n = Number(el.dataset.n);
    if(Number.isFinite(n)){ page=n; render(true); }
  });
  on(pageNums, "keydown", (e)=>{
    const el = e.target.closest(".num"); if(!el) return;
    if(e.key==="Enter" || e.key===" "){
      e.preventDefault();
      const n = Number(el.dataset.n);
      if(Number.isFinite(n)){ page=n; render(true); }
    }
  });

  /* =========================================================
     GO!
  ========================================================= */
  applyFilter();
})();

// ===============================
// Detail & Print (Riwayat Penjualan)
// ===============================
(function () {
  const $ = (s) => document.querySelector(s);
  const on = (el, ev, fn) => el && el.addEventListener(ev, fn);
  const ordersViewport = document.getElementById("ordersViewport");
  if (!ordersViewport) return;

  // Buat container modal sekali saja
  let modal = document.getElementById("detailModal");
  if (!modal) {
    modal = document.createElement("div");
    modal.id = "detailModal";
    modal.className = "modal hidden";
    modal.innerHTML = `
      <div class="modal-backdrop" data-close="1"></div>
      <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="detailTitle">
        <button class="modal-close" title="Tutup" data-close="1">×</button>
        <h3 id="detailTitle" class="modal-title">Detail Transaksi</h3>
        <p class="modal-sub" id="detailSub"></p>

        <div id="detailBody"></div>
        <div class="modal-actions">
          <button class="btn btn-ghost" data-close="1">Tutup</button>
          <button class="btn btn-primary" id="btnPrintDetail">Cetak</button>
        </div>
      </div>`;
    document.body.appendChild(modal);

    // Tutup modal
    modal.addEventListener("click", (e) => {
      if (e.target.dataset.close) hideModal();
    });
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && !modal.classList.contains("hidden")) hideModal();
    });
    function hideModal(){ modal.classList.add("hidden"); document.body.style.overflow = ""; }
    window.__hideTxnModal = hideModal; // optional
  }

  function showModal(orderEl) {
    // Ambil data dasar dari card yang diklik (dummy extraction)
    const shop = orderEl.querySelector(".shop-name")?.textContent?.trim() || "-";
    const meta = orderEl.querySelector(".order-meta")?.textContent?.trim() || "";
    const total= orderEl.querySelector(".total-price")?.textContent?.trim() || "-";
    const name = orderEl.querySelector(".item-name")?.textContent?.trim() || "-";
    const qty  = orderEl.querySelector(".item-qty")?.textContent?.trim() || "-";
    const img  = orderEl.querySelector(".item-img")?.getAttribute("src") || "";

    // Ambil data pesanan dari card (dengan asumsi data disimpan di dataset atau diambil dari sumber lain)
    // Kita perlu mengambil data pesanan dari sumber yang menyediakan informasi timeline
    const orderId = orderEl.dataset.orderId;

    // Ambil data pesanan dari daftar pesanan yang dimuat sebelumnya
    // Kita perlu menyimpan data pesanan di variabel global atau mengambilnya dari sumber lain
    const orderData = window.recentOrdersData ? window.recentOrdersData.find(order => order.order_number == orderId) : null;

    // Header
    const sub = `No. Invoice: ${orderData ? orderData.order_number : 'INV-001'} • ${meta}`;
    modal.querySelector("#detailSub").textContent = sub;

    // Format timeline
    let timelineHtml = '';
    if (orderData && orderData.timeline && orderData.timeline.length > 0) {
        const timelineItems = orderData.timeline.map(log => `${log.status_display} (${log.timestamp})`).join(' → ');
        timelineHtml = `<dd>${timelineItems}</dd>`;
    } else {
        timelineHtml = '<dd>Timeline tidak tersedia</dd>';
    }

    // Body (isi sesuai spesifikasi kamu)
    modal.querySelector("#detailBody").innerHTML = `
      <div class="dl" style="margin-bottom:12px">
        <dt>Status</dt><dd><span class="badge">${orderData ? orderData.status_display : 'Selesai'}</span></dd>
        <dt>Pembeli</dt><dd>${orderData ? orderData.customer_name : 'Adi Saputra'} • 0812-3456-7890</dd>
        <dt>Pengiriman</dt><dd>Jl. Melati No. 12, Banyuwangi • JNE REG • Resi: ${orderData ? orderData.tracking_number : 'JNEXXXXX'} (tracking)</dd>
        <dt>Pembayaran</dt><dd>Transfer VA • TRX123456 • ${orderData ? orderData.created_at : '02 Sep 2025 10:05'} • Lunas</dd>
      </div>

      <table class="table-mini" aria-label="Item">
        <tr>
          <td>
            <div style="display:flex;align-items:center">
              <img class="thumb" src="${img}" alt="">
              <div>
                <div style="font-weight:600">${name}</div>
                <div style="color:#bdbdbd;font-size:12px">Varian: -</div>
              </div>
            </div>
          </td>
          <td>${qty}</td>
          <td style="text-align:right">${total.replace("Total:","").trim()}</td>
        </tr>
      </table>

      <div class="dl" style="margin-top:10px">
        <dt>Subtotal</dt><dd>Rp ${parseInt(orderData ? orderData.subtotal : 1250000).toLocaleString('id-ID')}</dd>
        <dt>Diskon</dt><dd>-</dd>
        <dt>Ongkir</dt><dd>Rp 0</dd>
        <dt>Pajak</dt><dd>Rp 0</dd>
        <dt><strong>Grand Total</strong></dt><dd><strong>${total.replace("Total:","").trim()}</strong></dd>
      </div>

      <div class="dl" style="margin-top:12px">
        <dt>Timeline</dt>
        ${timelineHtml}
      </div>
    `;

    modal.classList.remove("hidden");
    document.body.style.overflow = "hidden";
  }

  // Delegasi klik untuk tombol Detail & Cetak
  on(ordersViewport, "click", (e) => {
    const detailBtn = e.target.closest(".btn-detail");
    const printBtn  = e.target.closest(".btn-print");
    if (detailBtn) {
      e.preventDefault();
      const card = detailBtn.closest(".order-card");
      if (card) showModal(card);
    }
    if (printBtn) {
      e.preventDefault();
      // versi singkat: print satu kartu apa adanya
      const card = printBtn.closest(".order-card");
      if (card) {
        const w = window.open("", "_blank");
        w.document.write(`<title>Cetak</title><body>${card.outerHTML}</body>`);
        w.document.close(); w.focus(); w.print();
      }
    }
  });
})();

(function(){
  const root = document.querySelector('.reviews-viewport');
  if(!root) return;

  const MAX = 500;
  const templates = [
    "Terima kasih atas ulasannya 🙏",
    "Maaf atas ketidaknyamanannya, kami tindak lanjuti ya.",
    "Pesan Anda sangat membantu perbaikan layanan kami."
  ];

  function composerHTML(){
    const chips = templates.map(t=>`<button type="button" class="reply-chip" data-t="${t.replace(/"/g,'&quot;')}">${t}</button>`).join('');
    return `
      <div class="reply-composer">
        <div class="reply-composer__top">
          <div class="reply-composer__chips">${chips}</div>
          <div class="reply-counter"><span class="cnt">0</span>/${MAX}</div>
        </div>
        <textarea placeholder="Tulis balasan untuk pembeli… (maks ${MAX} karakter)"></textarea>
        <div class="reply-composer__bar">
          <div class="reply-composer__opts">
            <label><input type="checkbox" class="opt-public" checked> Tampilkan publik</label>
            <label><input type="checkbox" class="opt-notify" checked> Kirim notifikasi ke pembeli</label>
          </div>
          <div class="reply-actions">
            <button class="btn btn-ghost btn-sm btn-cancel">Batal</button>
            <button class="btn btn-primary btn-sm btn-send" disabled>Kirim</button>
          </div>
        </div>
      </div>`;
  }

  function ensureComposer(card){
    let cmp = card.querySelector('.reply-composer');
    if(cmp) return cmp;
    const body = card.querySelector('.rev-body') || card;
    body.insertAdjacentHTML('afterend', composerHTML());
    cmp = card.querySelector('.reply-composer');

    const ta = cmp.querySelector('textarea');
    const cnt = cmp.querySelector('.cnt');
    const btnSend = cmp.querySelector('.btn-send');

    function update(){
      const len = ta.value.trim().length;
      cnt.textContent = len;
      btnSend.disabled = (len < 3 || len > MAX);
    }
    ta.addEventListener('input', update);
    cmp.querySelectorAll('.reply-chip').forEach(ch=>{
      ch.addEventListener('click', ()=>{
        const t = ch.dataset.t || '';
        ta.value = (ta.value ? (ta.value.trim()+' ') : '') + t;
        ta.dispatchEvent(new Event('input'));
        ta.focus();
      });
    });
    cmp.querySelector('.btn-cancel').addEventListener('click', ()=> cmp.remove());
    cmp.querySelector('.btn-send').addEventListener('click', ()=>{
      const text = ta.value.trim();
      if(!text) return;
      // tampilkan balasan (dummy)
      const now = new Date();
      const stamp = now.toLocaleString('id-ID', { dateStyle:'medium', timeStyle:'short' });
      cmp.insertAdjacentHTML('afterend', `
        <div class="seller-reply">
          <div class="seller-reply__meta">Balasan penjual • ${stamp}</div>
          <div class="seller-reply__text">${text.replace(/\n/g,'<br>')}</div>
        </div>
      `);
      cmp.remove();
    });

    // init counter
    update();
    return cmp;
  }

  root.addEventListener('click', (e)=>{
    const btn = e.target.closest('.btn-reply');
    if(!btn) return;
    const card = btn.closest('.review-card');
    if(!card) return;
    // toggle: kalau ada -> fokus; kalau belum -> buat
    const cmp = ensureComposer(card);
    const ta = cmp.querySelector('textarea');
    ta.focus({ preventScroll:false });
  });
})();

// ===============================
// Riwayat Ulasan Dinamis
// ===============================
(function(){
  const reviewsSection = document.querySelector('.reviews-section');
  if (!reviewsSection) return;
  
  const reviewsViewport = reviewsSection.querySelector('.reviews-viewport');
  if (!reviewsViewport) return;
  
  // Fungsi untuk memuat ulasan dari API
  async function loadRecentReviews() {
    try {
      const response = await fetch('/penjual/reviews/recent');
      const data = await response.json();
      
      if (data.success && data.reviews.length > 0) {
        // Bersihkan konten sebelumnya kecuali tombol "Lihat Semua"
        const viewAllBtn = reviewsViewport.querySelector('.view-all-btn');
        reviewsViewport.innerHTML = '';
        
        // Tambahkan ulasan
        data.reviews.forEach(review => {
          const reviewElement = createReviewElement(review);
          reviewsViewport.appendChild(reviewElement);
        });
        
        // Tambahkan kembali tombol "Lihat Semua"
        if (viewAllBtn) {
          reviewsViewport.appendChild(viewAllBtn);
        }
      }
    } catch (error) {
      console.error('Error loading recent reviews:', error);
    }
  }
  
  // Fungsi untuk membuat elemen ulasan
  function createReviewElement(review) {
    const article = document.createElement('article');
    article.className = 'review-card';
    
    // Rating stars
    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
      const starClass = i <= review.rating ? 'star filled' : 'star';
      starsHtml += `<svg class="${starClass}" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1l2.6 5.3L18 7l-4 3.9L15 18l-5-2.6L5 18l1-7.1L2 7l5.4-.7L10 1z"/></svg>`;
    }
    
    article.innerHTML = `
      <div class="rev-top" style="padding:.8rem 1rem;">
        <div><strong>${review.user_name}</strong> • <time datetime="${review.created_at}">${review.created_at}</time></div>
        <div class="rev-stars" aria-label="${review.rating} dari 5 bintang">
          ${starsHtml}
        </div>
      </div>
      <div class="rev-body" style="padding:0 1rem 1rem;">
        <div class="rev-text">${(review.review_text || 'Tidak ada komentar').replace(/\(\d+\)/g, '')}</div>
        ${review.reply ? `
          <div class="rev-reply" style="margin-top: 0.75rem; padding: 0.75rem; background: #f0fdfa; border-left: 3px solid var(--ak-primary); border-radius: 0 var(--ak-radius) var(--ak-radius) 0;">
            <strong>Balasan:</strong>
            <p style="margin: 0.25rem 0 0;">${review.reply}</p>
          </div>
        ` : ''}
      </div>
    `;
    
    return article;
  }
  
  // Muat ulasan saat halaman dimuat
  document.addEventListener('DOMContentLoaded', loadRecentReviews);
})();

// ===============================
// Riwayat Penjualan Dinamis
// ===============================
(function(){
  const ordersSection = document.querySelector('.orders-section');
  if (!ordersSection) return;
  
  const ordersViewport = document.getElementById('ordersViewport');
  if (!ordersViewport) return;
  
  // Fungsi untuk memuat pesanan dari API
  async function loadRecentOrders() {
    try {
      const response = await fetch('/penjual/pesanan/recent');
      const data = await response.json();

      if (data.success && data.orders.length > 0) {
        // Simpan data pesanan ke variabel global
        window.recentOrdersData = data.orders;

        // Bersihkan konten sebelumnya
        ordersViewport.innerHTML = '';

        // Tambahkan pesanan
        data.orders.forEach(order => {
          const orderElement = createOrderElement(order);
          ordersViewport.appendChild(orderElement);
        });
      }
    } catch (error) {
      console.error('Error loading recent orders:', error);
    }
  }
  
  // Fungsi untuk membuat elemen pesanan
  function createOrderElement(order) {
    const article = document.createElement('article');
    article.className = 'order-card';
    article.setAttribute('data-order-id', order.order_number);

    // Status mapping untuk tampilan
    const statusLabels = {
      'pending_payment': 'Menunggu Pembayaran',
      'processing': 'Diproses',
      'shipping': 'Dikirim',
      'completed': 'Selesai',
      'cancelled': 'Dibatalkan'
    };

    const statusLabel = statusLabels[order.status] || order.status;

    article.innerHTML = `
      <div class="order-head">
        <div class="shop-name">${order.customer_name}</div>
        <div class="order-meta">
          <span>Tanggal:
            <time datetime="${order.created_at}">${order.created_at}</time>
          </span>
          • <span>Status: <strong>${statusLabel}</strong></span>
        </div>
      </div>

      <div class="order-items">
        <div class="order-item">
          <img class="item-img" src="${order.product_image}" alt="${order.product_name}">
          <div class="item-body">
            <div class="item-row">
              <div class="item-name">${order.product_name}</div>
              <div class="item-qty">Qty ${order.quantity}</div>
            </div>
            <div class="item-desc scrollable">
              Pesanan #${order.order_number}
            </div>
            <div class="item-subtotal">Rp ${parseInt(order.subtotal).toLocaleString('id-ID')}</div>
          </div>
        </div>
      </div>

      <div class="order-actions">
        <div class="total-price" aria-label="Total">Total: Rp ${parseInt(order.total_amount).toLocaleString('id-ID')}</div>
        <div class="row-actions">
          <a href="/penjual/pesanan/${order.id}" class="btn btn-ghost btn-detail">Detail</a>
          <a href="/invoice/${order.id}" class="btn btn-primary">Cetak</a>
        </div>
      </div>
    `;

    return article;
  }
  
  // Variabel global untuk menyimpan data pesanan
  window.recentOrdersData = [];

  // Muat pesanan saat halaman dimuat
  document.addEventListener('DOMContentLoaded', loadRecentOrders);
})();
