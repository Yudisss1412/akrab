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

    // Header
    const sub = `No. Invoice: INV-001 • ${meta}`;
    modal.querySelector("#detailSub").textContent = sub;

    // Body (isi sesuai spesifikasi kamu)
    modal.querySelector("#detailBody").innerHTML = `
      <div class="dl" style="margin-bottom:12px">
        <dt>Status</dt><dd><span class="badge">Selesai</span></dd>
        <dt>Pembeli</dt><dd>Adi Saputra • 0812-3456-7890</dd>
        <dt>Pengiriman</dt><dd>Jl. Melati No. 12, Banyuwangi • JNE REG • Resi: JNEXXXXX (tracking)</dd>
        <dt>Pembayaran</dt><dd>Transfer VA • TRX123456 • 02 Sep 2025 10:05 • Lunas</dd>
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
        <dt>Subtotal</dt><dd>Rp 1.250.000</dd>
        <dt>Diskon</dt><dd>-</dd>
        <dt>Ongkir</dt><dd>Rp 0</dd>
        <dt>Pajak</dt><dd>Rp 0</dd>
        <dt><strong>Grand Total</strong></dt><dd><strong>${total.replace("Total:","").trim()}</strong></dd>
      </div>

      <div class="dl" style="margin-top:12px">
        <dt>Timeline</dt>
        <dd>Buat (09:55) → Bayar (10:05) → Diproses (10:20) → Dikirim (13:15) → Selesai (02 Sep 18:40)</dd>
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
