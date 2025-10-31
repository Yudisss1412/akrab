"use strict";

/* ============== Helpers ============== */
const $  = (s) => document.querySelector(s);
const $$ = (s) => Array.from(document.querySelectorAll(s));
const on = (el, ev, fn) => el && el.addEventListener(ev, fn);
const getVal = (sel, fallback = "") => {
  const el = $(sel);
  if (!el) return fallback;
  // number inputs -> angka
  if (el.type === "number") return Number(el.value || 0);
  return el.value ?? fallback;
};
const fmtIDR = (n) =>
  (Number(n) || 0).toLocaleString("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 2, maximumFractionDigits: 2 });

/* ============== DOM ============== */
const printCard = $("#printCard");
const btnPrint  = $("#btnPrint");

/* Deteksi mode: editor atau preview-only */
const editorMode =
  !!$("#itemsWrap") ||
  !!$(".editor") ||
  !!$("#s_name"); // ada input toko? berarti editor

/* =========================
   MODE PREVIEW-ONLY (no editor)
   ========================= */
if (!editorMode) {
  // Blade sudah merender HTML invoice di #printCard. Kita cuma butuh tombol cetak.
  on(btnPrint, "click", () => window.print());
  // Selesai lebih awal supaya gak eksekusi kode editor.
}

/* =========================
   MODE EDITOR (opsional)
   ========================= */
else {
  const itemsWrap = $("#itemsWrap");
  const btnAddRow = $("#btnAddRow");

  /* ---------- State ---------- */
  const state = {
    store: {
      name: getVal("#s_name", "Shoppy.gg"),
      contact: getVal("#s_contact", "+62 812-3456-7890 • support@shoppy.gg"),
      addr: getVal("#s_addr", "Jl. Melati No. 12, Banyuwangi"),
      logo: getVal("#s_logo", "/src/Logo_UMKM.png"),
    },
    meta: {
      no: getVal("#m_no", "INV-2025-0001"),
      time: getVal("#m_time") || new Date().toISOString().slice(0, 16),
      payStatus: getVal("#m_pay_status", "PAID"),     // PAID / UNPAID / PENDING
      orderStatus: getVal("#m_order_status", "Selesai"),
    },
    buyer: { name: getVal("#b_name", "Budi Santoso"), contact: getVal("#b_contact", "budi@mail.com • 0812-0000-0000") },
    ship:  {
      addr: getVal("#sh_addr", "Perum Mawar Blok A-12, Banyuwangi"),
      courier: getVal("#sh_courier", "JNE • Reguler"),
      awb: getVal("#sh_awb", "JNE1234567890"),
      track: getVal("#sh_track", "https://jne.co.id/tracking/JNE1234567890"),
    },
    payment: {
      method: getVal("#p_method", "Virtual Account"),
      txid: getVal("#p_txid", "TX123456789"),
      time: getVal("#p_time", ""),
      status: getVal("#p_status", "Berhasil"),
    },
    items: [
      {
        img: "https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=300&auto=format&fit=crop",
        name: "Mini Projector",
        varian: "Default",
        qty: 1,
        price: 1250000,
      },
    ],
    summary: {
      disc: getVal("#sum_disc", 0),
      ship: getVal("#sum_ship", 20000),
      taxP: getVal("#sum_tax", 0),
      note: getVal("#sum_note", "Terima kasih telah berbelanja."),
    },
  };

  /* ---------- Items editor rows ---------- */
  function rowTpl(i, it) {
    const safeImg = String(it.img || "");
    return `
    <div class="item-line" data-i="${i}">
      <img class="item-thumb" src="${safeImg}"
           alt=""
           onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22160%22 height=%22100%22%3E%3Crect width=%22100%25%22 height=%22100%25%22 fill=%22%23f3f4f6%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 fill=%22%239ca3af%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2212%22%3ENo Image%3C/text%3E%3C/svg%3E';">

      <div class="col item-url">
        <input class="i_img" type="text" value="${safeImg}" placeholder="URL thumbnail">
      </div>

      <div class="col item-name">
        <input class="i_name" type="text" value="${it.name || ""}" placeholder="Nama item">
      </div>

      <div class="col item-variant">
        <input class="i_var" type="text" value="${it.varian || ""}" placeholder="Varian">
      </div>

      <div class="col item-qty">
        <input class="i_qty" type="number" value="${it.qty}" min="1">
      </div>

      <div class="col item-price">
        <input class="i_price" type="number" value="${it.price}" min="0">
      </div>

      <button class="item-remove" title="Hapus">×</button>
    </div>`;
  }

  function renderRows() {
    if (!itemsWrap) return;
    itemsWrap.innerHTML = state.items.map((it, i) => rowTpl(i, it)).join("");
  }

  function bindRowEvents() {
    if (!itemsWrap) return;

    // input change
    itemsWrap.oninput = (e) => {
      const row = e.target.closest(".item-line, .item-row"); // support lama
      if (!row) return;
      const i = Number(row.dataset.i);
      if (!Number.isFinite(i)) return;

      if (e.target.classList.contains("i_img")) {
        state.items[i].img = e.target.value;
        const imgEl = row.querySelector(".item-thumb");
        if (imgEl && imgEl.src !== e.target.value) imgEl.src = e.target.value;
      }
      if (e.target.classList.contains("i_name"))  state.items[i].name   = e.target.value;
      if (e.target.classList.contains("i_var"))   state.items[i].varian = e.target.value;
      if (e.target.classList.contains("i_qty"))   state.items[i].qty    = Math.max(1, Number(e.target.value || 1));
      if (e.target.classList.contains("i_price")) state.items[i].price  = Math.max(0, Number(e.target.value || 0));
      renderPreview();
    };

    // remove
    itemsWrap.onclick = (e) => {
      const row = e.target.closest(".item-line, .item-row");
      if (!row) return;
      if (e.target.classList.contains("item-remove")) {
        const i = Number(row.dataset.i);
        if (!Number.isFinite(i)) return;
        state.items.splice(i, 1);
        renderRows(); bindRowEvents(); renderPreview();
      }
    };
  }

  /* ---------- Form listeners ---------- */
  [
    ["#s_name", "store", "name"],
    ["#s_contact", "store", "contact"],
    ["#s_addr", "store", "addr"],
    ["#s_logo", "store", "logo"],

    ["#m_no", "meta", "no"],
    ["#m_time", "meta", "time"],
    ["#m_pay_status", "meta", "payStatus"],
    ["#m_order_status", "meta", "orderStatus"],

    ["#b_name", "buyer", "name"],
    ["#b_contact", "buyer", "contact"],

    ["#sh_addr", "ship", "addr"],
    ["#sh_courier", "ship", "courier"],
    ["#sh_awb", "ship", "awb"],
    ["#sh_track", "ship", "track"],

    ["#p_method", "payment", "method"],
    ["#p_txid", "payment", "txid"],
    ["#p_time", "payment", "time"],
    ["#p_status", "payment", "status"],

    ["#sum_disc", "summary", "disc"],
    ["#sum_ship", "summary", "ship"],
    ["#sum_tax", "summary", "taxP"],
    ["#sum_note", "summary", "note"],
  ].forEach(([sel, group, key]) => {
    const el = $(sel);
    if (!el) return;
    const handler = () => {
      state[group][key] = el.type === "number" ? Number(el.value || 0) : el.value;
      renderPreview();
    };
    on(el, "input", handler);
    on(el, "change", handler);
  });

  /* ---------- Preview renderer ---------- */
  function calcTotals() {
    const subtotal = state.items.reduce((s, it) => s + it.qty * it.price, 0);
    const disc = Number(state.summary.disc || 0);
    const ship = Number(state.summary.ship || 0);
    const taxP = Number(state.summary.taxP || 0);
    const tax  = Math.max(0, (subtotal - disc) * (taxP / 100));
    const grand = Math.max(0, subtotal - disc + ship + tax);
    return { subtotal, disc, ship, tax, grand };
  }

  function previewHTML() {
    const { store, meta, buyer, ship, payment, items, summary } = state;
    const totals = calcTotals();
    const payBadge = meta.payStatus.toUpperCase() === "PAID" ? "paid" : "unpaid";

    return `
    <div class="inv-head">
      <div class="inv-store">
        <img src="${store.logo}" alt="logo">
        <div>
          <div style="font-weight:800">${store.name}</div>
          <div style="color:#6b7280; font-size:13px">${store.addr}</div>
          <div style="color:#6b7280; font-size:13px">${store.contact}</div>
        </div>
      </div>
      <div class="inv-meta">
        <div style="font-size:12px; color:#6b7280">Invoice</div>
        <div style="font-weight:800">${meta.no}</div>
        <div class="badge ${payBadge}">Bayar: ${meta.payStatus}</div>
        <div class="badge">${meta.orderStatus}</div>
        <div style="font-size:12px; color:#6b7280">${String(meta.time).replace('T',' ')}</div>
      </div>
    </div>

    <div class="inv-cols">
      <div class="box">
        <h4>Pembeli</h4>
        <div style="font-weight:600">${buyer.name}</div>
        <div style="color:#6b7280">${buyer.contact}</div>
      </div>
      <div class="box">
        <h4>Pengiriman</h4>
        <div>${ship.addr}</div>
        <div style="color:#6b7280">Kurir: ${ship.courier}</div>
        <div style="color:#6b7280">Resi: ${ship.awb} • <a href="${ship.track}" target="_blank" rel="noopener">Tracking</a></div>
      </div>
    </div>

    <div class="inv-cols">
      <div class="box">
        <h4>Pembayaran</h4>
        <div>Metode: <strong>${payment.method}</strong></div>
        <div style="color:#6b7280">ID Transaksi: ${payment.txid || "-"}</div>
        <div style="color:#6b7280">Waktu Bayar: ${payment.time ? String(payment.time).replace('T',' ') : "-"}</div>
        <div class="badge" style="margin-top:6px">${payment.status}</div>
      </div>
      <div class="box">
        <h4>Catatan</h4>
        <div class="note">${summary.note || "—"}</div>
      </div>
    </div>

    <table class="items-table" aria-label="Item">
      <thead>
        <tr>
          <th>Item</th><th>Varian</th><th>Qty</th><th>Harga</th><th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        ${items.map(it => `
          <tr>
            <td>
              <div style="display:flex; align-items:center; gap:8px; min-width:0">
                <img class="thumb" src="${it.img}" alt=""
                     onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22%3E%3Crect width=%22100%25%22 height=%22100%25%22 fill=%22%23f3f4f6%22/%3E%3C/svg%3E';">
                <div>${it.name}</div>
              </div>
            </td>
            <td>${it.varian || "—"}</td>
            <td>${it.qty}</td>
            <td>${fmtIDR(it.price)}</td>
            <td>${fmtIDR(it.qty * it.price)}</td>
          </tr>
        `).join("")}
      </tbody>
    </table>

    <div class="summary">
      <div></div>
      <div class="totals">
        <div class="row"><span>Subtotal</span><strong>${fmtIDR(totals.subtotal)}</strong></div>
        <div class="row"><span>Diskon/Voucher</span><strong>- ${fmtIDR(totals.disc)}</strong></div>
        <div class="row"><span>Ongkir</span><strong>${fmtIDR(totals.ship)}</strong></div>
        <div class="row"><span>Pajak</span><strong>${fmtIDR(totals.tax)}</strong></div>
        <div class="row grand"><span>Grand Total</span><strong>${fmtIDR(totals.grand)}</strong></div>
      </div>
    </div>`;
  }

  function renderPreview() {
    if (!printCard) return;
    printCard.innerHTML = previewHTML();
  }

  /* ---------- Add item row ---------- */
  on(btnAddRow, "click", () => {
    state.items.push({ img: "", name: "Produk Baru", varian: "", qty: 1, price: 0 });
    renderRows(); bindRowEvents(); renderPreview();
  });

  /* ---------- Print ---------- */
  on(btnPrint, "click", () => window.print());

  /* ---------- Boot ---------- */
  (function init() {
    // set default datetime now untuk meta/pembayaran jika kosong
    const mTime = $("#m_time");
    const pTime = $("#p_time");
    if (mTime && !mTime.value) mTime.value = state.meta.time;
    if (pTime && !pTime.value) pTime.value = "";

    renderRows(); bindRowEvents();
    renderPreview();
  })();
}

/* NOTE:
   - Di halaman invoice preview-only (Blade sudah render), script di atas
     hanya memasang handler tombol Cetak, tanpa menyentuh #printCard.
   - Jika nanti kamu aktifkan kembali editor (punya .editor / #itemsWrap / input-input),
     mode editor otomatis aktif dan live preview berjalan normal.
*/
