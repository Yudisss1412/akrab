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

  // qty -> integer [1..99]
  function normQty(v){
    const n = Math.floor(Number(v) || 1);
    return Math.min(99, Math.max(1, n));
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
    row.querySelectorAll('.line-total').forEach(el => el.textContent = 'Rp ' + fmt(each * qty));
  }

  function recalcSummary(){
    const rows = $('.cart-item');
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
    const cartTotalEl = $('#cartTotal');
    if (countEl) countEl.textContent = count;
    if (subtotalEl) subtotalEl.textContent = 'Rp ' + fmt(subtotal);
    if (cartTotalEl) cartTotalEl.textContent = 'Rp ' + fmt(subtotal);
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
  function showAlert(title, message, type = 'success', showCartButton = true){
    // Cek apakah fungsi showToast sudah didefinisikan di halaman (dari blade)
    if(typeof window.showToast === 'function'){
      window.showToast(title, message, type, showCartButton);
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

    // hapus item (menangani baik tombol trash maupun ikon di dalamnya)
    if (row && (e.target.closest('.trash') || e.target.classList.contains('bi-trash'))) {
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
  $('.cart-item').forEach(recalcItem);
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
  
  // === Rekalkulasi subtotal saat halaman dimuat ===
  setTimeout(() => {
    recalcSummary();
  }, 100);
  
  // === Update tampilan saat halaman dimuat ===
  document.addEventListener('DOMContentLoaded', function() {
    recalcSummary();
  });
  
  // === Tambahkan event listener untuk tombol checkout ===
  document.addEventListener('click', function(e) {
    // Checkout
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
  });
})();

// ====== Toast Notification Functions ======
let toastTimeout;

function showToast(title, message, type = 'success', showCartButton = true) {
  const toast = document.getElementById('toastNotification');
  const toastTitle = document.getElementById('toastTitle');
  const toastMessage = document.getElementById('toastMessage');
  const toastAction = document.getElementById('toastAction');
  
  // Update content
  toastTitle.textContent = title;
  toastMessage.textContent = message;
  
  // Show or hide the "Lihat Keranjang" button based on the parameter
  if (showCartButton) {
    toastAction.style.display = 'inline-block';
  } else {
    toastAction.style.display = 'none';
  }
  
  // Show toast
  toast.style.display = 'block';
  setTimeout(() => {
    toast.classList.add('show');
  }, 10);
  
  // Auto hide after 5 seconds
  if (toastTimeout) clearTimeout(toastTimeout);
  toastTimeout = setTimeout(hideToast, 5000);
}

function hideToast() {
  const toast = document.getElementById('toastNotification');
  toast.classList.remove('show');
  
  setTimeout(() => {
    toast.style.display = 'none';
  }, 300);
  
  if (toastTimeout) {
    clearTimeout(toastTimeout);
    toastTimeout = null;
  }
}

// Event listeners for toast notification
document.addEventListener('DOMContentLoaded', function() {
  const toastClose = document.getElementById('toastClose');
  const toastOverlay = document.getElementById('toastNotification');
  const toastAction = document.getElementById('toastAction');
  
  if (toastClose) {
    toastClose.addEventListener('click', hideToast);
  }
  
  if (toastOverlay) {
    toastOverlay.addEventListener('click', function(e) {
      if (e.target === this) {
        hideToast();
      }
    });
  }
  
  if (toastAction) {
    toastAction.addEventListener('click', function() {
      // Navigate to cart page
      window.location.href = "/keranjang"; // Ganti dengan route yang sesuai
    });
  }
  
  // Show toast notification on page load for demonstration
  // Uncomment the line below to show toast on page load
  // showToast('Berhasil', 'Item berhasil ditambahkan ke keranjang!', 'success');
});

/* ==================== AKHIR FILE ==================== */

// ====== JavaScript untuk Halaman Profil Pembeli ======
// Fungsi untuk menangani perpindahan konten dinamis dan modal
(function() {
  'use strict';
  
  // Pastikan DOM sudah siap
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeProfileFunctionality);
  } else {
    initializeProfileFunctionality();
  }
  
  function initializeProfileFunctionality() {
    // Cek apakah kita berada di halaman profil pembeli
    if (!document.querySelector('.profile-layout')) {
      return; // Keluar jika bukan halaman profil pembeli
    }
    
    // Get all navigation links and content sections
    const navItems = document.querySelectorAll('.profile-navigation .nav-item');
    const contentSections = document.querySelectorAll('.main-content section');
    
    // Function to initialize modal event listeners (can be called when needed)
    function initializeModalListeners() {
      // Password change modal functionality
      const changePasswordBtn = document.getElementById('changePasswordBtn');
      const changePasswordModal = document.getElementById('changePasswordModal');
      const closeChangePasswordModal = document.getElementById('closeChangePasswordModal');
      const cancelPasswordChange = document.getElementById('cancelPasswordChange');
      const savePasswordChange = document.getElementById('savePasswordChange');
      const passwordChangeForm = document.getElementById('passwordChangeForm');
      
      if (changePasswordBtn && changePasswordModal) {
        // Use a class-based approach to track if listener exists
        if (!changePasswordBtn.classList.contains('modal-listener-added')) {
          changePasswordBtn.addEventListener('click', function() {
            changePasswordModal.style.display = 'flex';
          });
          changePasswordBtn.classList.add('modal-listener-added');
        }
      }
      
      if (closeChangePasswordModal && changePasswordModal) {
        if (!closeChangePasswordModal.classList.contains('modal-listener-added')) {
          closeChangePasswordModal.addEventListener('click', function() {
            changePasswordModal.style.display = 'none';
          });
          closeChangePasswordModal.classList.add('modal-listener-added');
        }
      }
      
      if (cancelPasswordChange && changePasswordModal) {
        if (!cancelPasswordChange.classList.contains('modal-listener-added')) {
          cancelPasswordChange.addEventListener('click', function() {
            changePasswordModal.style.display = 'none';
          });
          cancelPasswordChange.classList.add('modal-listener-added');
        }
      }
      
      // Account deletion modal functionality
      const deleteAccountBtns = document.querySelectorAll('.settings-item.danger-zone .btn-danger');
      const deleteAccountBtnActual = deleteAccountBtns.length > 0 ? deleteAccountBtns[0] : null;
      
      const deleteAccountModal = document.getElementById('deleteAccountModal');
      const closeDeleteAccountModal = document.getElementById('closeDeleteAccountModal');
      const cancelDeleteAccount = document.getElementById('cancelDeleteAccount');
      const confirmDeleteAccount = document.getElementById('confirmDeleteAccount');
      
      if (deleteAccountBtnActual && deleteAccountModal) {
        if (!deleteAccountBtnActual.classList.contains('modal-listener-added')) {
          deleteAccountBtnActual.addEventListener('click', function() {
            deleteAccountModal.style.display = 'flex';
          });
          deleteAccountBtnActual.classList.add('modal-listener-added');
        }
      }
      
      if (closeDeleteAccountModal && deleteAccountModal) {
        if (!closeDeleteAccountModal.classList.contains('modal-listener-added')) {
          closeDeleteAccountModal.addEventListener('click', function() {
            deleteAccountModal.style.display = 'none';
          });
          closeDeleteAccountModal.classList.add('modal-listener-added');
        }
      }
      
      if (cancelDeleteAccount && deleteAccountModal) {
        if (!cancelDeleteAccount.classList.contains('modal-listener-added')) {
          cancelDeleteAccount.addEventListener('click', function() {
            deleteAccountModal.style.display = 'none';
          });
          cancelDeleteAccount.classList.add('modal-listener-added');
        }
      }
      
      if (confirmDeleteAccount && deleteAccountModal) {
        if (!confirmDeleteAccount.classList.contains('modal-listener-added')) {
          confirmDeleteAccount.addEventListener('click', function() {
            // In a real application, this would trigger the account deletion process
            alert('Akun Anda akan dihapus. Fungsi ini akan diimplementasikan di backend.');
            deleteAccountModal.style.display = 'none';
          });
          confirmDeleteAccount.classList.add('modal-listener-added');
        }
      }
      
      if (savePasswordChange && passwordChangeForm && changePasswordModal) {
        if (!savePasswordChange.classList.contains('modal-listener-added')) {
          savePasswordChange.addEventListener('click', function() {
            // Get form values
            const currentPassword = document.getElementById('currentPassword')?.value;
            const newPassword = document.getElementById('newPassword')?.value;
            const confirmNewPassword = document.getElementById('confirmNewPassword')?.value;
            
            // Basic validation
            if (!currentPassword || !newPassword || !confirmNewPassword) {
              alert('Harap isi semua kolom kata sandi.');
              return;
            }
            
            if (newPassword !== confirmNewPassword) {
              alert('Kata sandi baru dan konfirmasi kata sandi tidak cocok.');
              return;
            }
            
            // In a real application, this would send a request to the backend
            alert('Kata sandi telah berhasil diubah. Fungsi ini akan diimplementasikan di backend.');
            changePasswordModal.style.display = 'none';
            
            // Reset form
            passwordChangeForm.reset();
          });
          savePasswordChange.classList.add('modal-listener-added');
        }
      }
    }
    
    // Add click event listeners to navigation items
    navItems.forEach(item => {
      item.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Get the target content ID from data attribute
        const targetId = this.getAttribute('data-target');
        
        // Remove active class from all navigation items
        navItems.forEach(navItem => {
          navItem.classList.remove('active');
        });
        
        // Add active class to clicked navigation item
        this.classList.add('active');
        
        // Hide all content sections
        contentSections.forEach(section => {
          section.classList.remove('active-content');
          section.classList.add('hidden-content');
        });
        
        // Show the target content section
        const targetSection = document.getElementById(targetId);
        if (targetSection) {
          targetSection.classList.remove('hidden-content');
          targetSection.classList.add('active-content');
          
          // Initialize modal listeners when account settings is shown
          if (targetId === 'account-settings') {
            initializeModalListeners();
          }
        }
      });
    });
    
    // Set default active content (Riwayat Pesanan) on page load
    contentSections.forEach(section => {
      section.classList.remove('active-content');
      section.classList.add('hidden-content');
    });
    
    const defaultSection = document.getElementById('order-history');
    if (defaultSection) {
      defaultSection.classList.remove('hidden-content');
      defaultSection.classList.add('active-content');
    }
    
    // Initialize modal listeners for the default section if it's account settings
    if (defaultSection?.id === 'account-settings') {
      initializeModalListeners();
    }
    
    // Close modal when clicking outside of it (add once to avoid duplicates)
    if (!document.body.classList.contains('outside-modal-listener-added')) {
      document.addEventListener('click', function(event) {
        const changePasswordModal = document.getElementById('changePasswordModal');
        const deleteAccountModal = document.getElementById('deleteAccountModal');
        
        // Only close if clicking on the backdrop (not on modal content)
        if (changePasswordModal && event.target === changePasswordModal) {
          changePasswordModal.style.display = 'none';
        }
        if (deleteAccountModal && event.target === deleteAccountModal) {
          deleteAccountModal.style.display = 'none';
        }
      });
      document.body.classList.add('outside-modal-listener-added');
    }
  }
})();

// ===== Toast Notification Functions =====
let toastTimeout;

// Create toast notification element if it doesn't exist
function createToastElement() {
  // Check if toast already exists
  const existingToast = document.getElementById('toastNotification');
  if (existingToast) {
    return existingToast;
  }
  
  // Create toast element
  const toast = document.createElement('div');
  toast.id = 'toastNotification';
  toast.className = 'toast-notification';
  
  toast.innerHTML = `
    <div class="toast-content">
      <div class="toast-icon">✓</div>
      <div class="toast-body">
        <h3 id="toastTitle" class="toast-header-text">Berhasil</h3>
        <p id="toastMessage" class="toast-message">Item berhasil ditambahkan ke keranjang!</p>
        <div class="toast-actions">
          <button id="toastAction" class="toast-btn toast-btn-primary">Lihat Keranjang</button>
        </div>
      </div>
      <button id="toastClose" class="toast-close">&times;</button>
    </div>
  `;
  
  document.body.appendChild(toast);
  
  // Add event listeners
  document.getElementById('toastClose').addEventListener('click', hideToast);
  document.getElementById('toastNotification').addEventListener('click', function(e) {
    if (e.target === this) {
      hideToast();
    }
  });
  
  document.getElementById('toastAction').addEventListener('click', function() {
    // Navigate to cart page
    window.location.href = "/keranjang"; // Ganti dengan route yang sesuai
  });
  
  return toast;
}

function showToast(title, message, type = 'success', showCartButton = true) {
  // Create toast element if it doesn't exist
  const toast = createToastElement();
  const toastTitle = document.getElementById('toastTitle');
  const toastMessage = document.getElementById('toastMessage');
  const toastAction = document.getElementById('toastAction');
  
  // Update content
  toastTitle.textContent = title;
  toastMessage.textContent = message;
  
  // Show or hide the "Lihat Keranjang" button based on the parameter
  if (showCartButton) {
    toastAction.style.display = 'inline-block';
  } else {
    toastAction.style.display = 'none';
  }
  
  // Show toast with animation
  toast.style.display = 'block';
  setTimeout(() => {
    toast.classList.add('show');
  }, 10);
  
  // Auto hide after 5 seconds
  if (toastTimeout) clearTimeout(toastTimeout);
  toastTimeout = setTimeout(hideToast, 5000);
}

function hideToast() {
  const toast = document.getElementById('toastNotification');
  if (!toast) return;
  
  toast.classList.remove('show');
  
  setTimeout(() => {
    toast.style.display = 'none';
  }, 300);
  
  if (toastTimeout) {
    clearTimeout(toastTimeout);
    toastTimeout = null;
  }
}

// Event listeners for toast notification
document.addEventListener('DOMContentLoaded', function() {
  // Create toast element on page load
  createToastElement();
  
  // Show toast notification on page load for demonstration
  // Uncomment the line below to show toast on page load
  // showToast('Berhasil', 'Item berhasil ditambahkan ke keranjang!', 'success');
});

/* ==================== AKHIR FILE ==================== */

