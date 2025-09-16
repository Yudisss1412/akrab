"use strict";

/* ======================================
   DATA & KONSTANTA
====================================== */
const CATEGORIES = ["Pakaian", "Aksesoris", "Elektronik", "Lainnya"];

let allProducts = Array.from({ length: 20 }, (_, i) => ({
  id: i + 1,
  name: "Kaos Polos",
  price: 50000,
  category: "Pakaian",
  stock: "10/100",
  photo: null               // <-- field foto untuk thumbnail tabel
}));

// koleksi yang sedang ditampilkan (hasil filter)
let products = [...allProducts];

// pagination state
let page = 1;
const perPage = 10;

/* ======================================
   DOM HELPERS
====================================== */
const $  = (sel) => document.querySelector(sel);

// Hooks utama
const tbody       = $("#tbodyProducts");
const searchInput = $("#searchInput");
const btnAdd      = $("#btnAdd");
const pageHint    = $("#pageHint");
const btnPrev     = $("#prevPage");
const btnNext     = $("#nextPage");

// Modal Produk
const modal       = $("#modal");
const productForm = $("#productForm");
const fName       = $("#fName");
const fPrice      = $("#fPrice");
const fCategory   = $("#fCategory");
const fDesc       = $("#fDesc");
const fStockCur   = $("#fStockCur");
const fStockMax   = $("#fStockMax");
const btnCancel   = $("#btnCancel");
const btnClose    = $("#btnClose");

// Upload Foto
const fileInput   = $("#fPhoto");
const photoDrop   = $("#photoDrop");

// state edit & foto saat ini (DataURL)
let editId = null;
let currentPhotoDataURL = null;

/* ======================================
   UTIL
====================================== */
function fmtIDR(n) {
  return n.toLocaleString("id-ID", { style: "currency", currency: "IDR" });
}
function parseStock(s = "0/0") {
  const [cur, max] = String(s).split("/").map((v) => parseInt(v, 10) || 0);
  return { cur, max };
}
function activeQuery() {
  return (searchInput?.value || "").toLowerCase().trim();
}
function applyFilter() {
  const q = activeQuery();
  products = allProducts.filter((p) =>
    `${p.name} ${p.category}`.toLowerCase().includes(q)
  );
}
function clampPage() {
  const maxPage = Math.max(1, Math.ceil(products.length / perPage));
  if (page > maxPage) page = maxPage;
  if (page < 1) page = 1;
  return maxPage;
}
function setPagerState() {
  const maxPage = Math.max(1, Math.ceil(products.length / perPage));
  if (btnPrev) btnPrev.disabled = page <= 1;
  if (btnNext) btnNext.disabled = page >= maxPage;
}
function placeholderPhotoSVG(){
  return `
    <svg viewBox="0 0 104 104" aria-hidden="true">
      <path d="M21.6667 91C19.2833 91 17.2438 90.1521 15.548 88.4563C13.8522 86.7606 13.0029 84.7196 13 82.3333V21.6667C13 19.2833 13.8493 17.2438 15.548 15.548C17.2467 13.8522 19.2862 13.0029 21.6667 13H82.3333C84.7167 13 86.7577 13.8493 88.4563 15.548C90.155 17.2467 91.0029 19.2862 91 21.6667V82.3333C91 84.7167 90.1521 86.7577 88.4563 88.4563C86.7606 90.155 84.7196 91.0029 82.3333 91H21.6667ZM21.6667 82.3333H82.3333V21.6667H21.6667V82.3333ZM26 73.6667H78L61.75 52L48.75 69.3333L39 56.3333L26 73.6667Z" fill="black"/>
    </svg>
  `;
}

/* ======================================
   ICONS
====================================== */
function iconEdit() {
  return `
  <svg viewBox="0 0 40 40" aria-hidden="true">
    <path d="M31.6371 3.58981L27.4071 0.931063C26.8944 0.609391 26.2749 0.504535 25.6849 0.639556C25.0948 0.774578 24.5826 1.13842 24.2608 1.65106L22.5921 4.30606L30.6871 9.38731L32.3546 6.73481C33.0246 5.66606 32.7046 4.25981 31.6371 3.58981ZM8.19332 27.2323L16.2858 32.3148L29.4746 11.3173L21.3796 6.23356L8.19207 27.2336L8.19332 27.2323ZM6.95582 33.6898L6.77832 38.4648L11.0033 36.2298L14.9283 34.1573L7.11832 29.2498L6.95582 33.6898Z" fill="black"/>
  </svg>`;
}
function iconTrash() {
  return `
  <svg viewBox="0 0 40 40" aria-hidden="true">
    <path fill="none"
      d="M6.66699 11.6667H33.3337M16.667 18.3333V28.3333M23.3337 18.3333V28.3333M8.33366 11.6667L10.0003 31.6667C10.0003 32.5507 10.3515 33.3986 10.9766 34.0237C11.6018 34.6488 12.4496 35 13.3337 35H26.667C27.551 35 28.3989 34.6488 29.024 34.0237C29.6491 33.3986 30.0003 32.5507 30.0003 31.6667L31.667 11.6667M15.0003 11.6667V6.66667C15.0003 6.22464 15.1759 5.80072 15.4885 5.48816C15.801 5.17559 16.225 5 16.667 5H23.3337C23.7757 5 24.1996 5.17559 24.5122 5.48816C24.8247 5.80072 25.0003 6.22464 25.0003 6.66667V11.6667"
      stroke="#FF0505" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>`;
}

/* ======================================
   RENDER TABEL
====================================== */
function render() {
  if (!tbody) return;

  const start = (page - 1) * perPage;
  const data = products.slice(start, start + perPage);

  tbody.innerHTML = data.map(p => `
    <tr>
      <td class="cell-name"><a href="#">${p.name}</a></td>
      <td>${fmtIDR(p.price)}</td>
      <td><span class="cat-pill">${p.category}</span></td>
      <td class="td-stock"><span class="stock-text">${p.stock}</span></td>

      <!-- FOTO thumbnail -->
      <td class="td-photo">
        <div class="thumb">
          ${p.photo ? `<img src="${p.photo}" alt="Foto ${p.name}">` : placeholderPhotoSVG()}
        </div>
      </td>

      <!-- AKSI (kanan sendiri) -->
      <td class="td-actions">
        <button class="btn-icon btn-edit" data-id="${p.id}" title="Edit">${iconEdit()}</button>
        <button class="btn-icon btn-del" data-id="${p.id}" title="Hapus">${iconTrash()}</button>
      </td>
    </tr>
  `).join('');

  const total = products.length;
  const end = Math.min(start + perPage, total);
  if (pageHint) pageHint.textContent = `Menampilkan ${start + 1}-${end} dari ${total} item`;
  setPagerState();
}

/* ======================================
   SEARCH (live filter)
====================================== */
searchInput?.addEventListener("input", () => {
  applyFilter();
  page = 1;
  clampPage();
  render();
});
// (opsional) tekan ESC di kolom cari untuk hapus query
searchInput?.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    searchInput.value = "";
    applyFilter();
    page = 1;
    clampPage();
    render();
  }
});

/* ======================================
   MODAL PRODUK
====================================== */
function openModal(edit = null, opts = { focusStock: false }) {
  editId = edit?.id ?? null;
  $("#modalTitle").textContent = editId ? "Edit Produk" : "Tambah Produk";

  fName.value     = edit?.name     ?? "";
  fPrice.value    = edit?.price    ?? "";
  fCategory.value = edit?.category ?? "Pakaian";
  fDesc.value     = edit?.desc     ?? "";

  const { cur, max } = parseStock(edit?.stock ?? "0/0");
  if (fStockCur) fStockCur.value = cur;
  if (fStockMax) fStockMax.value = max;

  // set preview foto dari data (jika ada)
  currentPhotoDataURL = edit?.photo ?? null;
  if (photoDrop) {
    if (currentPhotoDataURL) {
      photoDrop.classList.add("has-preview");
      photoDrop.style.backgroundImage = `url('${currentPhotoDataURL}')`;
    } else {
      photoDrop.classList.remove("has-preview", "is-drag");
      photoDrop.style.backgroundImage = "";
    }
  }
  if (fileInput) fileInput.value = "";

  modal?.classList.remove("hidden");
  document.body.classList.add("is-modal-open");

  if (opts.focusStock && fStockCur) {
    fStockCur.focus();
    fStockCur.scrollIntoView({ block: "center", behavior: "smooth" });
  }
}
function closeModal() {
  modal?.classList.add("hidden");
  document.body.classList.remove("is-modal-open");
}

btnAdd?.addEventListener("click", () => openModal());
btnCancel?.addEventListener("click", closeModal);
btnClose?.addEventListener("click", closeModal);

// ESC untuk menutup modal
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && !modal?.classList.contains("hidden")) {
    closeModal();
  }
});

/* Submit form */
productForm?.addEventListener("submit", (e) => {
  e.preventDefault();

  const name = fName.value.trim();
  const price = Number(fPrice.value || 0);
  const category = fCategory.value;

  const cur = Math.max(0, parseInt(fStockCur?.value || "0", 10) || 0);
  const max = Math.max(cur, parseInt(fStockMax?.value || "0", 10) || 0);
  const stock = `${cur}/${max}`;

  // ambil foto sebelumnya kalau edit & user tidak memilih foto baru
  const previous = editId ? allProducts.find(p => p.id === editId) : null;

  const payload = {
    id: editId ?? (allProducts.length ? Math.max(...allProducts.map((p) => p.id)) + 1 : 1),
    name,
    price,
    category,
    stock,
    photo: currentPhotoDataURL ?? previous?.photo ?? null   // <-- simpan DataURL ke data
  };

  if (editId) {
    allProducts = allProducts.map((p) => (p.id === editId ? { ...p, ...payload } : p));
  } else {
    allProducts = [payload, ...allProducts];
  }

  applyFilter();
  clampPage();
  closeModal();
  render();
});

/* ======================================
   TABEL: ACTIONS
====================================== */
tbody?.addEventListener("click", (e) => {
  const btnEdit = e.target.closest(".btn-edit");
  const btnDel  = e.target.closest(".btn-del");

  if (btnEdit) {
    const id = Number(btnEdit.dataset.id);
    const prod = allProducts.find(p => p.id === id);
    if (prod) openModal(prod);
    return;
  }

  if (btnDel) {
    const id = Number(btnDel.dataset.id);
    if (confirm("Hapus produk ini?")) {
      allProducts = allProducts.filter(p => p.id !== id);
      applyFilter();
      clampPage();
      render();
    }
  }
});

/* ======================================
   PAGER
====================================== */
btnPrev?.addEventListener("click", () => {
  if (page > 1) {
    page--;
    render();
  }
});
btnNext?.addEventListener("click", () => {
  const maxPage = Math.max(1, Math.ceil(products.length / perPage));
  if (page < maxPage) {
    page++;
    render();
  }
});

/* ======================================
   UPLOAD FOTO (preview + drag & drop)
   - simpan sebagai DataURL (persisten)
====================================== */
function readFileAsDataURL(file, cb){
  const reader = new FileReader();
  reader.onload = () => cb(reader.result);
  reader.readAsDataURL(file);
}

if (fileInput && photoDrop) {
  fileInput.addEventListener("change", (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    readFileAsDataURL(file, (dataURL) => {
      currentPhotoDataURL = dataURL;
      photoDrop.style.backgroundImage = `url('${dataURL}')`;
      photoDrop.classList.add("has-preview");
    });
  });

  ["dragenter", "dragover"].forEach((ev) =>
    photoDrop.addEventListener(ev, (e) => {
      e.preventDefault();
      photoDrop.classList.add("is-drag");
    })
  );
  ["dragleave", "drop"].forEach((ev) =>
    photoDrop.addEventListener(ev, (e) => {
      e.preventDefault();
      photoDrop.classList.remove("is-drag");
    })
  );

  photoDrop.addEventListener("drop", (e) => {
    const file = e.dataTransfer?.files?.[0];
    if (!file) return;

    // set ke input supaya konsisten dengan form
    const dt = new DataTransfer();
    dt.items.add(file);
    fileInput.files = dt.files;

    readFileAsDataURL(file, (dataURL) => {
      currentPhotoDataURL = dataURL;
      photoDrop.style.backgroundImage = `url('${dataURL}')`;
      photoDrop.classList.add("has-preview");
    });
  });
}

/* ======================================
   INIT
====================================== */
applyFilter();
clampPage();
render();
