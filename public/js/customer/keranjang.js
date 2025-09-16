/* ===================== Cart JS (robust + state persist) ===================== */
(() => {
  // ===== Helpers =====
  const fmt = n => (Number(n) || 0).toLocaleString('id-ID');
  const $$  = sel => Array.from(document.querySelectorAll(sel));
  const $   = sel => document.querySelector(sel);

  const STORAGE_KEY = 'akr_cart_state_v1';

  const getRowId = row => row?.dataset.id?.toString().trim() || null;

  // harga satuan dari data-each (integer rupiah)
  function getEach(row){
    const el = row?.querySelector('[data-each]');
    const val = el ? Number(el.dataset.each) : 0;
    return Number.isFinite(val) ? val : 0;
  }

  // qty -> integer [1..999]
  function normQty(v){
    const n = Math.floor(Number(v) || 1);
    return Math.min(999, Math.max(1, n));
  }

  // ===== State (persist ke localStorage) =====
  function loadState(){
    try {
      const raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) return { qty:{}, selected:{} };
      const obj = JSON.parse(raw);
      return {
        qty: obj?.qty || {},
        selected: obj?.selected || {}
      };
    } catch {
      return { qty:{}, selected:{} };
    }
  }

  function saveState(state){
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    } catch {/* ignore */}
  }

  const state = loadState();

  // ===== Perhitungan =====
  function recalcItem(row){
    if (!row) return;
    const each = getEach(row);
    const qtyInput = row.querySelector('.qty-input');
    const qty = normQty(qtyInput?.value);
    if (qtyInput) qtyInput.value = qty;
    row.querySelectorAll('.line-total').forEach(el => el.textContent = fmt(each * qty));
  }

  function recalcSummary(){
    const rows = $$('.cart-item');
    let count = 0, subtotal = 0;
    rows.forEach(r=>{
      const cb  = r.querySelector('.item-check');
      if (cb && cb.checked){
        const each = getEach(r);
        const qty  = normQty(r.querySelector('.qty-input')?.value);
        count     += qty;
        subtotal  += each * qty;
      }
    });
    const countEl = $('#selectedCount');
    const subtotalEl = $('#subtotal');
    if (countEl) countEl.textContent = count;
    if (subtotalEl) subtotalEl.textContent = fmt(subtotal);
  }

  function setSelectAllState(){
    const items = $$('.cart-item .item-check');
    const allChecked = items.length > 0 && items.every(i => i.checked);
    const anyChecked = items.some(i => i.checked);
    ['selectAllTop','selectAllBottom'].forEach(id=>{
      const el = document.getElementById(id);
      if(!el) return;
      el.checked = allChecked;
      el.indeterminate = anyChecked && !allChecked;
    });
  }

  function syncSelectAll(){ setSelectAllState(); }

  // ===== Modal =====
  function openModal(id){
    const el = document.getElementById(id);
    if(!el) return;
    el.classList.add('is-open');
    el.setAttribute('aria-hidden', 'false');
    el.querySelector('.modal-ok')?.focus();
  }
  function closeModal(id){
    const el = document.getElementById(id);
    if(!el) return;
    el.classList.remove('is-open');
    el.setAttribute('aria-hidden', 'true');
  }
  
  // ===== Alert =====
  function showAlert(title, message, type = 'success', duration = 3000){
    // Cek apakah fungsi showAlert sudah didefinisikan di halaman (dari blade)
    if(typeof window.showAlert === 'function'){
      window.showAlert(title, message, type, duration);
    } else {
      // Fallback ke alert browser jika tidak ada
      alert(title + ': ' + message);
    }
  }

  // ===== Apply/Update State <-> DOM =====
  function applyStateToDOM(){
    $$('.cart-item').forEach(row=>{
      const id = getRowId(row);
      if (!id) return;
      // qty
      const savedQty = state.qty[id];
      const input = row.querySelector('.qty-input');
      if (input) {
        if (savedQty != null) input.value = normQty(savedQty);
        recalcItem(row);
      }
      // selected
      const savedSel = state.selected[id];
      const cb = row.querySelector('.item-check');
      if (cb) cb.checked = !!savedSel;
    });
    recalcSummary();
    setSelectAllState();
  }

  function updateStateFromRow(row){
    const id = getRowId(row);
    if (!id) return;
    const qty = normQty(row.querySelector('.qty-input')?.value);
    const checked = !!row.querySelector('.item-check')?.checked;
    state.qty[id] = qty;
    state.selected[id] = checked;
  }

  function removeFromState(row){
    const id = getRowId(row);
    if (!id) return;
    delete state.qty[id];
    delete state.selected[id];
  }

  // ===== Events =====
  document.addEventListener('click', e=>{
    const row = e.target.closest('.cart-item');

    // qty +
    if (row && (e.target.classList.contains('plus') || e.target.closest('.plus'))) {
      const input = row.querySelector('.qty-input');
      if (input) input.value = normQty(Number(input.value || 1) + 1);
      recalcItem(row); updateStateFromRow(row); saveState(state); recalcSummary(); setSelectAllState();
    }

    // qty -
    if (row && (e.target.classList.contains('minus') || e.target.closest('.minus'))) {
      const input = row.querySelector('.qty-input');
      if (input) input.value = normQty(Number(input.value || 1) - 1);
      recalcItem(row); updateStateFromRow(row); saveState(state); recalcSummary(); setSelectAllState();
    }

    // hapus item
    if (row && e.target.closest('.trash')) {
      removeFromState(row); saveState(state);
      row.remove(); recalcSummary(); setSelectAllState();
      showAlert('Berhasil', 'Produk berhasil dihapus dari keranjang!', 'success');
    }

    // select all (atas & bawah)
    if (e.target.id === 'selectAllTop' || e.target.id === 'selectAllBottom') {
      const checked = e.target.checked;
      $$('.item-check').forEach(c => { c.checked = checked; });
      // tulis ke state
      $$('.cart-item').forEach(updateStateFromRow);
      saveState(state);
      setSelectAllState();
      recalcSummary();
    }

    // hapus massal
    if (e.target.id === 'bulkDelete') {
      const selected = $('.cart-item .item-check:checked');
      if (!selected.length){ openModal('emptyModal'); return; }
      selected.forEach(cb => {
        const r = cb.closest('.cart-item');
        removeFromState(r);
        r?.remove();
      });
      saveState(state);
      recalcSummary(); setSelectAllState();
      showAlert('Berhasil', 'Produk terpilih berhasil dihapus dari keranjang!', 'success');
    }

    // === Checkout ===
    if (e.target.id === 'checkout') {
      const anySelected = !!document.querySelector('.cart-item .item-check:checked');
      if (!anySelected) { 
        // openModal('emptyModal'); 
        showAlert('Peringatan', 'Anda belum memilih produk untuk checkout', 'warning');
        return; 
      }
      // gunakan data-href jika disediakan di blade; fallback ke /checkout
      const href = e.target.dataset?.href || "/checkout";
      showAlert('Proses Checkout', 'Mengarahkan ke halaman checkout...', 'success', 0); // 0 = tidak otomatis hilang
      setTimeout(() => {
        window.location.href = href;
      }, 1000);
    }

    // tutup modal: klik OK atau overlay
    // if (e.target.id === 'emptyOk' || (e.target.classList.contains('modal-overlay') && e.target.id === 'emptyModal')) {
    //   closeModal('emptyModal');
    // }
  });

  document.addEventListener('keydown', e=>{
    if (e.key === 'Escape') closeModal('emptyModal');
  });

  document.addEventListener('input', e=>{
    // qty input
    if (e.target.classList.contains('qty-input')) {
      const row = e.target.closest('.cart-item');
      e.target.value = normQty(e.target.value);
      recalcItem(row); updateStateFromRow(row); saveState(state); recalcSummary(); setSelectAllState();
    }
    // checkbox item
    if (e.target.classList.contains('item-check')) {
      const row = e.target.closest('.cart-item');
      updateStateFromRow(row); saveState(state);
      recalcSummary(); setSelectAllState();
    }
  });

  // ===== Init =====
  $$('.cart-item').forEach(recalcItem);
  applyStateToDOM(); // ini juga panggil recalc & sync

  // === Smart fixed cart bar: parkir di bawah item terakhir ===
  const cartBar   = document.getElementById('cartBar');
  const sentinel  = document.getElementById('endSentinel');
  const spacer    = document.getElementById('cartSpacer');

  if (cartBar && sentinel && spacer && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver(([entry]) => {
      if (entry.isIntersecting) {
        cartBar.classList.add('at-end');
        spacer.classList.add('is-end');   // hilangkan gap saat bar parkir
      } else {
        cartBar.classList.remove('at-end');
        spacer.classList.remove('is-end');
      }
    }, { root: null, threshold: 0, rootMargin: '0px 0px -1px 0px' });
    io.observe(sentinel);
  }
})();
