'use strict';

document.addEventListener('DOMContentLoaded', () => {
  // ===== Dummy data 17 item agar footer "Menampilkan 1–11 dari 17 item" pas
  const CATEGORIES = Array.from({ length: 17 }, (_, i) => ({ id: i + 1, name: 'Makanan' }));

  let filtered = [...CATEGORIES];
  let page = 1;
  const perPage = 11;

  const $ = (s) => document.querySelector(s);

  // DOM els
  const listEl      = $("#categoryList");
  const pageHint    = $("#pageHint");
  const btnPrev     = $("#prevPage");
  const btnNext     = $("#nextPage");
  const btnAdd      = $("#btnAddCategory");
  const searchInput = $("#searchCategory");

  // Modal els
  const modal       = $("#modalOverlay");
  const modalInput  = $("#modalInput");
  const btnSave     = $("#btnSaveModal");
  const btnCancel   = $("#btnCancelModal");

  let editingId = null; // null => tambah; number => edit

  // Icons (SVG)
  const iconEdit = () => `
  <svg viewBox="0 0 40 40" width="20" height="20" aria-hidden="true">
    <path d="M31.6371 3.58981L27.4071 0.931063C26.8944 0.609391 26.2749 0.504535 25.6849 0.639556C25.0948 0.774578 24.5826 1.13842 24.2608 1.65106L22.5921 4.30606L30.6871 9.38731L32.3546 6.73481C33.0246 5.66606 32.7046 4.25981 31.6371 3.58981ZM8.19332 27.2323L16.2858 32.3148L29.4746 11.3173L21.3796 6.23356L8.19207 27.2336L8.19332 27.2323ZM6.95582 33.6898L6.77832 38.4648L11.0033 36.2298L14.9283 34.1573L7.11832 29.2498L6.95582 33.6898Z" fill="black"/>
  </svg>`;

  const iconTrash = () => `
  <svg class="icon-trash" viewBox="0 0 40 40" width="20" height="20" aria-hidden="true">
    <path fill="none" d="M6.667 11.667H33.333M16.667 18.333V28.333M23.333 18.333V28.333M8.333 11.667 10 31.667c0 .884.351 1.732.976 2.357A3.333 3.333 0 0 0 13.333 35H26.667a3.333 3.333 0 0 0 3.333-3.333L31.667 11.667M15 11.667V6.667c0-.442.176-.866.488-1.179A1.667 1.667 0 0 1 16.667 4H23.333c.442 0 .866.176 1.179.488.312.312.488.736.488 1.179v5" stroke="#FF0505" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>`;

  // Render list + footer
  function render() {
    if (!listEl || !pageHint || !btnPrev || !btnNext) return;

    const start = (page - 1) * perPage;
    const data  = filtered.slice(start, start + perPage);

    listEl.innerHTML = data.map(item => `
      <div class="category-row" data-id="${item.id}">
        <div class="category-name">${escapeHtml(item.name)}</div>
        <div class="row-actions">
          <button class="icon-btn btn-edit" title="Edit">${iconEdit()}</button>
          <button class="icon-btn btn-delete" title="Hapus">${iconTrash()}</button>
        </div>
      </div>
    `).join("");

    // Footer info
    const total = filtered.length;
    const end   = Math.min(start + perPage, total);
    pageHint.textContent = total ? `Menampilkan ${start + 1}–${end} dari ${total} item` : 'Tidak ada data';

    // Pagination state
    const maxP = Math.max(1, Math.ceil(total / perPage));
    btnPrev.disabled = page <= 1;
    btnNext.disabled = page >= maxP;
  }

  function clampPage() {
    const maxP = Math.max(1, Math.ceil(filtered.length / perPage));
    if (page < 1) page = 1;
    if (page > maxP) page = maxP;
    return maxP;
  }

  // Simple escape
  function escapeHtml(s = '') {
    return s.replace(/[&<>"']/g, (m) => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;' }[m]));
  }

  // --- Modal helpers
  function openModal(defaultValue = '', id = null) {
    if (!modal || !modalInput) return;
    editingId = id; // null add
    modalInput.value = defaultValue;
    modal.classList.add('show');
    modalInput.focus();
  }
  function closeModal() {
    if (!modal) return;
    modal.classList.remove('show');
    editingId = null;
  }

  // --- Events
  searchInput?.addEventListener('input', () => {
    const q = (searchInput.value || '').toLowerCase().trim();
    filtered = CATEGORIES.filter((c) => c.name.toLowerCase().includes(q));
    page = 1; render();
  });

  btnPrev?.addEventListener('click', () => { if (page > 1) { page--; render(); } });
  btnNext?.addEventListener('click', () => {
    const maxP = clampPage();
    if (page < maxP) { page++; render(); }
  });

  btnAdd?.addEventListener('click', () => openModal('', null));
  btnCancel?.addEventListener('click', closeModal);
  modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

  // Enter = Simpan, Esc = Batal
  modalInput?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); btnSave?.click(); }
    if (e.key === 'Escape') { e.preventDefault(); closeModal(); }
  });

  btnSave?.addEventListener('click', () => {
    const name = (modalInput?.value || '').trim() || 'Tanpa Nama';
    if (editingId == null) {
      // tambah
      const last = CATEGORIES[CATEGORIES.length - 1];
      const newId = (last?.id || 0) + 1;
      CATEGORIES.push({ id: newId, name });
    } else {
      // edit
      const idx = CATEGORIES.findIndex((c) => c.id === editingId);
      if (idx !== -1) CATEGORIES[idx].name = name;
    }
    // refresh filter sesuai query sekarang
    const q = (searchInput?.value || '').toLowerCase().trim();
    filtered = CATEGORIES.filter((c) => c.name.toLowerCase().includes(q));
    clampPage(); render(); closeModal();
  });

  // Delegasi edit/hapus
  listEl?.addEventListener('click', (e) => {
    const row = e.target.closest('.category-row');
    if (!row) return;
    const id = Number(row.dataset.id);

    if (e.target.closest('.btn-edit')) {
      const item = CATEGORIES.find((c) => c.id === id);
      openModal(item?.name || '', id);
      return;
    }
    if (e.target.closest('.btn-delete')) {
      if (confirm('Hapus kategori ini?')) {
        const idx = CATEGORIES.findIndex((c) => c.id === id);
        if (idx !== -1) CATEGORIES.splice(idx, 1);
        const q = (searchInput?.value || '').toLowerCase().trim();
        filtered = CATEGORIES.filter((c) => c.name.toLowerCase().includes(q));
        clampPage(); render();
      }
    }
  });

  // Init
  render();
});
