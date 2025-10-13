// Bikin wishlist hanya tampil 1 item (sisanya scroll),
// dan selaraskan batas bawah Riwayat Pesanan dengan bawah card Wishlist.
(function alignOrdersToWishlistBottom(){
  const wishCard   = document.getElementById('wishlistCard');
  const wishVP     = document.getElementById('wishlistViewport');
  const ordersSec  = document.getElementById('ordersSection');
  const ordersHead = ordersSec ? ordersSec.querySelector('.orders-head') : null;
  const ordersVP   = document.getElementById('ordersViewport');

  function clamp(n, min){ return n < min ? min : n; }

  function recompute(){
    // 1) Wishlist: tampilkan hanya 1 item
    if (wishVP) {
      const firstItem = wishVP.querySelector('.wishlist-card');
      if (firstItem) {
        // tinggi nyata item pertama
        const h = firstItem.getBoundingClientRect().height;
        wishVP.style.maxHeight = `${h}px`;
      }
    }

    // 2) Riwayat Pesanan: sejajarkan batas bawah dengan bawah card Wishlist
    if (wishCard && ordersSec && ordersHead && ordersVP) {
      const wishBottom = wishCard.getBoundingClientRect().bottom;
      const ordersTop  = ordersSec.getBoundingClientRect().top;
      const headH      = ordersHead.getBoundingClientRect().height;

      const secStyle   = getComputedStyle(ordersSec);
      const padTop     = parseFloat(secStyle.paddingTop) || 0;
      const padBottom  = parseFloat(secStyle.paddingBottom) || 0;

      // total ruang vertikal yang tersedia untuk viewport di dalam ordersSection
      let available = wishBottom - ordersTop - headH - padTop - padBottom;

      // cegah nilai minus/terlalu kecil
      available = clamp(available, 160);

      ordersVP.style.maxHeight = `${available}px`;
    }
  }

  window.addEventListener('load', recompute);
  window.addEventListener('resize', recompute);
  // antisipasi font/layout async
  setTimeout(recompute, 60);
})();

// Demo handler "Beli Lagi"
document.addEventListener('click', (e)=>{
  const btn = e.target.closest('.buy-again');
  if(!btn) return;
  const card = btn.closest('.order-card');
  const id = card?.dataset?.orderId || '(tanpa nomor)';
  alert(`Pesanan ${id} dimasukkan lagi ke keranjang ✅`);
});

// Toggle tombol wishlist (love)
document.addEventListener('click', (e)=>{
  const btn = e.target.closest('.btn-wish');
  if(!btn) return;

  const offSvg = btn.querySelector('.svg-off');
  const onSvg  = btn.querySelector('.svg-on');
  const isOn   = btn.getAttribute('aria-pressed') === 'true';

  btn.setAttribute('aria-pressed', (!isOn).toString());
  if(isOn){
    if(offSvg) offSvg.style.display = '';
    if(onSvg)  onSvg.style.display  = 'none';
    btn.dataset.state = 'off';
  }else{
    if(offSvg) offSvg.style.display = 'none';
    if(onSvg)  onSvg.style.display  = '';
    btn.dataset.state = 'on';
  }
});

// === Clamp "Riwayat Ulasan" to show exactly one card height, scroll the rest ===
(function clampReviewsToOneCard(){
  function recompute(){
    var vp = document.querySelector('.reviews-viewport');
    if (!vp) return;
    var first = vp.querySelector('.review-card');
    if (!first) return;

    // Hitung tinggi card pertama (setelah gambar termuat)
    var h = first.getBoundingClientRect().height;
    if (h && h > 0) {
      vp.style.maxHeight = h + 'px';
      vp.style.overflowY = 'auto';
    }
  }

  // Recompute saat layout siap, gambar selesai, dan saat resize
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', recompute);
  } else {
    recompute();
  }
  window.addEventListener('load', recompute);
  window.addEventListener('resize', recompute);
})();

document.addEventListener('click', function(e){
  const link = e.target.closest('a.js-logout');
  if (!link) return;

  // (opsional) bersihkan jejak login di storage
  try {
    const keep = new Set(['akrab_wishlist']);
    Object.keys(localStorage).forEach(k=>{
      if (!keep.has(k) && /^(akrab_|token|access_token|refresh_token|user|remember)/i.test(k)) {
        localStorage.removeItem(k);
      }
    });
    sessionStorage.clear();
  } catch {}

  // Penting: JANGAN panggil e.preventDefault(),
  // biarkan browser lanjut ke link.href ({{ route('welcome') }})
});

// Tambahkan animasi halus saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
  const cards = document.querySelectorAll('.card, .wishlist-card, .order-card, .review-card');
  cards.forEach((card, index) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    
    setTimeout(() => {
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, 100 * index);
  });
});

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
        // Remove any existing click listeners first
        changePasswordBtn.removeEventListener('click', changePasswordHandler);
        // Add new click listener
        changePasswordBtn.addEventListener('click', changePasswordHandler);
      }
      
      if (closeChangePasswordModal && changePasswordModal) {
        closeChangePasswordModal.removeEventListener('click', closeChangePasswordHandler);
        closeChangePasswordModal.addEventListener('click', closeChangePasswordHandler);
      }
      
      if (cancelPasswordChange && changePasswordModal) {
        cancelPasswordChange.removeEventListener('click', closeChangePasswordHandler);
        cancelPasswordChange.addEventListener('click', closeChangePasswordHandler);
      }
      
      // Account deletion modal functionality
      const deleteAccountBtns = document.querySelectorAll('.settings-item.danger-zone .btn-danger');
      const deleteAccountBtnActual = deleteAccountBtns.length > 0 ? deleteAccountBtns[0] : null;
      
      const deleteAccountModal = document.getElementById('deleteAccountModal');
      const closeDeleteAccountModal = document.getElementById('closeDeleteAccountModal');
      const cancelDeleteAccount = document.getElementById('cancelDeleteAccount');
      const confirmDeleteAccount = document.getElementById('confirmDeleteAccount');
      
      if (deleteAccountBtnActual && deleteAccountModal) {
        deleteAccountBtnActual.removeEventListener('click', deleteAccountHandler);
        deleteAccountBtnActual.addEventListener('click', deleteAccountHandler);
      }
      
      if (closeDeleteAccountModal && deleteAccountModal) {
        closeDeleteAccountModal.removeEventListener('click', closeDeleteAccountHandler);
        closeDeleteAccountModal.addEventListener('click', closeDeleteAccountHandler);
      }
      
      if (cancelDeleteAccount && deleteAccountModal) {
        cancelDeleteAccount.removeEventListener('click', closeDeleteAccountHandler);
        cancelDeleteAccount.addEventListener('click', closeDeleteAccountHandler);
      }
      
      if (confirmDeleteAccount && deleteAccountModal) {
        confirmDeleteAccount.removeEventListener('click', confirmDeleteHandler);
        confirmDeleteAccount.addEventListener('click', confirmDeleteHandler);
      }
      
      // Password change form submission
      if (savePasswordChange && passwordChangeForm && changePasswordModal) {
        savePasswordChange.removeEventListener('click', savePasswordHandler);
        savePasswordChange.addEventListener('click', savePasswordHandler);
      }
    }
    
    // Define separate handler functions to avoid re-creation
    function changePasswordHandler() {
      const changePasswordModal = document.getElementById('changePasswordModal');
      if (changePasswordModal) {
        changePasswordModal.classList.add('show');
      }
    }
    
    function closeChangePasswordHandler() {
      const changePasswordModal = document.getElementById('changePasswordModal');
      if (changePasswordModal) {
        changePasswordModal.classList.remove('show');
      }
    }
    
    function deleteAccountHandler() {
      const deleteAccountModal = document.getElementById('deleteAccountModal');
      if (deleteAccountModal) {
        deleteAccountModal.classList.add('show');
      }
    }
    
    function closeDeleteAccountHandler() {
      const deleteAccountModal = document.getElementById('deleteAccountModal');
      if (deleteAccountModal) {
        deleteAccountModal.classList.remove('show');
      }
    }
    
    function confirmDeleteHandler() {
      // In a real application, this would trigger the account deletion process
      alert('Akun Anda akan dihapus. Fungsi ini akan diimplementasikan di backend.');
      const deleteAccountModal = document.getElementById('deleteAccountModal');
      if (deleteAccountModal) {
        deleteAccountModal.classList.remove('show');
      }
    }
    
    function savePasswordHandler() {
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
      const changePasswordModal = document.getElementById('changePasswordModal');
      if (changePasswordModal) {
        changePasswordModal.classList.remove('show');
      }
      
      // Reset form
      const passwordChangeForm = document.getElementById('passwordChangeForm');
      if (passwordChangeForm) {
        passwordChangeForm.reset();
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
    
    // Close modal when clicking outside of it using event delegation
    document.addEventListener('click', function(event) {
      const changePasswordModal = document.getElementById('changePasswordModal');
      const deleteAccountModal = document.getElementById('deleteAccountModal');
      
      // Only close if clicking on the backdrop (not on modal content)
      if (changePasswordModal && event.target === changePasswordModal) {
        changePasswordModal.classList.remove('show');
      }
      if (deleteAccountModal && event.target === deleteAccountModal) {
        deleteAccountModal.classList.remove('show');
      }
    });
  }
})();
