@extends('layouts.app')

@section('title', 'Profil Pembeli — Wishlist & Riwayat Pesanan')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/profil/profil_pembeli.css') }}">
@endpush

@section('content')
  @php
    // FRONTEND-ONLY URL (tanpa route/backend):
    $u1 = url('/produk/101/' . \Illuminate\Support\Str::slug('Cangkir Keramik 250ml'));
    $u2 = url('/produk/102/' . \Illuminate\Support\Str::slug('Piring Kayu 18cm'));

    // CATATAN:
    // Kalau nanti backend siap & punya named route:
    // $u1 = route('produk.detail', ['id'=>101, 'slug'=>\Illuminate\Support\Str::slug('Cangkir Keramik 250ml')]);
    // $u2 = route('produk.detail', ['id'=>102, 'slug'=>\Illuminate\Support\Str::slug('Piring Kayu 18cm')]);
  @endphp

  <div class="profile-layout">
    <!-- LEFT COLUMN (Sidebar Profil & Navigasi) -->
    <div class="sidebar-profile">
      <!-- PROFILE SECTION -->
      <div class="profile-card card">
        <div class="profile-header">
          <div class="profile-avatar">
            <img src="{{ asset('src/profil_pembeli.png') }}" alt="Profil Pembeli">
          </div>
          <div class="profile-info">
            <h2>{{ auth()->user() ? auth()->user() ? auth()->user()->name : "Guest" : 'Guest' }}</h2>
            <p class="member-since">Member sejak {{ auth()->user() ? auth()->user()->created_at->year : date("Y") }}</p>
          </div>
          <a href="{{ route('edit.profil') }}" class="btn btn-primary">Edit Profil</a>
        </div>
        
        <!-- Profile Details -->
        <div class="profile-details">
          <div class="detail-item">
            <label>Email</label>
            <span>{{ auth()->user() ? auth()->user() ? auth()->user()->email : "Email belum tersedia" : 'Email belum tersedia' }}</span>
          </div>
          <div class="detail-item">
            <label>Telepon</label>
            <span>{{ auth()->user() ? (auth()->user()->phone ?? '+62 812 3456 7890') : '+62 812 3456 7890' }}</span>
          </div>
          <div class="detail-item">
            <label>Provinsi</label>
            <span>{{ auth()->user() ? (auth()->user()->province ?? 'Belum diisi') : 'Belum diisi' }}</span>
          </div>
          <div class="detail-item">
            <label>Kota/Kabupaten</label>
            <span>{{ auth()->user() ? (auth()->user()->city ?? 'Belum diisi') : 'Belum diisi' }}</span>
          </div>
          <div class="detail-item">
            <label>Kecamatan</label>
            <span>{{ auth()->user() ? (auth()->user()->district ?? 'Belum diisi') : 'Belum diisi' }}</span>
          </div>
          <div class="detail-item">
            <label>Kelurahan</label>
            <span>{{ auth()->user() ? (auth()->user()->ward ?? 'Belum diisi') : 'Belum diisi' }}</span>
          </div>
          <div class="detail-item">
            <label>Alamat Lengkap</label>
            <span>{{ auth()->user() ? (auth()->user()->full_address ?? auth()->user()->address ?? 'Alamat belum diisi') : 'Alamat belum diisi' }}</span>
          </div>
        </div>
      </div>
      
      <!-- NAVIGATION MENU -->
      <nav class="profile-navigation card">
        <a href="#" class="nav-item active" data-target="order-history">
          <i class="bi bi-bag-check"></i>
          <span>Riwayat Pesanan</span>
        </a>
        <a href="#" class="nav-item" data-target="wishlist-section">
          <i class="bi bi-heart"></i>
          <span>Wishlist</span>
        </a>
        <a href="#" class="nav-item" data-target="review-history">
          <i class="bi bi-chat-left-text"></i>
          <span>Ulasan Saya</span>
        </a>
        <a href="{{ route('customer.tickets') }}" class="nav-item">
          <i class="bi bi-ticket-detailed"></i>
          <span>Tiket Bantuan</span>
        </a>
        <a href="#" class="nav-item" data-target="account-settings">
          <i class="bi bi-gear"></i>
          <span>Pengaturan Akun</span>
        </a>
        <a href="#" class="nav-item js-logout" id="logoutBtn" data-target="logout">
          <i class="bi bi-box-arrow-right"></i>
          <span>Keluar</span>
        </a>
      </nav>
    </div>
    
    <!-- RIGHT COLUMN (Main Content) -->
    <div class="main-content">
      <!-- MY WISHLIST -->
      <section id="wishlist-section" class="wishlist-section card hidden-content">
        <div class="section-header">
          <h3>Wishlist Saya</h3>
          <a href="{{ route('halaman_wishlist') }}" class="view-all">Lihat Semua</a>
        </div>
        
        <div id="wishlistGrid" role="list" aria-live="polite">
          <!-- Wishlist items will be filled by JavaScript -->
        </div>

        <div id="wlEmpty" class="wl-empty" hidden>
          <p class="wl-empty__text">Belum ada item di wishlist.</p>
          <a href="{{ route('cust.welcome') }}" class="btn">Jelajahi Produk</a>
        </div>
      </section>

      <!-- ORDER HISTORY -->
      <section id="order-history" class="order-history card active-content">
        <div class="section-header">
          <h3>Riwayat Pesanan</h3>
          <a href="{{ route('customer.order.history') }}" class="view-all">Lihat Semua</a>
        </div>
        
        <div id="ordersLoading" class="loading-state">
          <p>Memuat pesanan...</p>
        </div>
        
        <div id="ordersList" class="order-list">
          <!-- Orders will be loaded here dynamically -->
        </div>
      </section>

      <!-- MY REVIEWS -->
      <section id="review-history" class="review-history card hidden-content">
        <div class="section-header">
          <h3>Ulasan Saya</h3>
          <a href="{{ route('halaman_ulasan') }}" class="view-all">Lihat Semua</a>
        </div>
        
        <div id="reviewsLoading" class="loading-state">
          <p>Memuat ulasan...</p>
        </div>
        
        <div id="reviewsList" class="review-list">
          <!-- Reviews will be loaded here dynamically -->
        </div>
      </section>
      
      <!-- ACCOUNT SETTINGS (New Content) -->
      <section id="account-settings" class="account-settings card hidden-content">
        <div class="section-header">
          <h3>Pengaturan Akun</h3>
        </div>
        
        <div class="settings-content">
          <!-- Keamanan Akun -->
          <div class="settings-card card">
            <h4>Keamanan Akun</h4>
            
            <div class="settings-item">
              <div class="settings-label">Kata Sandi</div>
              <button class="btn btn-outline" id="changePasswordBtn">Ubah</button>
            </div>
            

            
            <div class="settings-item">
              <div class="settings-label">Sesi Login Aktif</div>
              <button class="btn btn-outline" id="activeSessionBtn">Lihat & Kelola</button>
            </div>
          </div>
          
          <!-- Preferensi Notifikasi -->
          <div class="settings-card card">
            <h4>Preferensi Notifikasi</h4>
            
            <div class="notification-setting">
              <label class="switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
              </label>
              <span class="setting-label">Notifikasi Status Pesanan</span>
            </div>
            
            <div class="notification-setting">
              <label class="switch">
                <input type="checkbox" checked>
                <span class="slider"></span>
              </label>
              <span class="setting-label">Promo & Penawaran Spesial</span>
            </div>
            
            <div class="notification-setting">
              <label class="switch">
                <input type="checkbox">
                <span class="slider"></span>
              </label>
              <span class="setting-label">Newsletter Mingguan</span>
            </div>
            
            <button class="btn btn-primary" style="margin-top: 1rem;">Simpan Preferensi</button>
          </div>
          
          <!-- Privasi & Data -->
          <div class="settings-card card">
            <h4>Privasi & Data</h4>
            
            <div class="settings-item">
              <div class="settings-label">Unduh salinan data Anda</div>
              <button class="btn btn-outline">Minta Unduhan</button>
            </div>
            
            <div class="settings-item">
              <div class="settings-label">Nonaktifkan Akun Anda</div>
              <button class="btn btn-outline" id="deactivateAccountBtn">Nonaktifkan Akun</button>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Modal Ubah Kata Sandi -->
      <div id="changePasswordModal" class="modal">
        <div class="modal-content">
          <div class="modal-header">
            <h4>Ubah Kata Sandi</h4>
            <span class="close" id="closeChangePasswordModal">&times;</span>
          </div>
          <div class="modal-body">
            <form id="passwordChangeForm">
              <div class="form-group">
                <label for="currentPassword">Kata Sandi Saat Ini</label>
                <input type="password" id="currentPassword" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="newPassword">Kata Sandi Baru</label>
                <input type="password" id="newPassword" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="confirmNewPassword">Konfirmasi Kata Sandi Baru</label>
                <input type="password" id="confirmNewPassword" class="form-control" required>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" id="cancelPasswordChange">Batal</button>
            <button class="btn btn-primary" id="savePasswordChange">Simpan</button>
          </div>
        </div>
      </div>
      
      <!-- Modal Konfirmasi Nonaktifkan Akun -->
      <div id="deactivateAccountModal" class="modal">
        <div class="modal-content">
          <div class="modal-header">
            <h4>Nonaktifkan Akun?</h4>
            <span class="close" id="closeDeactivateAccountModal">&times;</span>
          </div>
          <div class="modal-body">
            <p> Akun Anda akan disembunyikan dari publik dan Anda akan otomatis logout. 
            Anda bisa mengaktifkan kembali akun Anda kapan saja dengan cara login ulang. 
            Lanjutkan?</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" id="cancelDeactivateAccount">Batal</button>
            <button class="btn btn-primary" id="confirmDeactivateAccount">Ya, Nonaktifkan</button>
          </div>
        </div>
      </div>
      
      <!-- Modal Konfirmasi Keluar -->
      <div id="logoutModal" class="modal">
        <div class="modal-content">
          <div class="modal-header">
            <h4>Keluar?</h4>
            <span class="close" id="closeLogoutModal">&times;</span>
          </div>
          <div class="modal-body">
            <p>Apakah Anda yakin ingin keluar dari akun Anda?</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" id="cancelLogout">Batal</button>
            <button class="btn btn-primary" id="confirmLogout">Ya, Keluar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script defer src="{{ asset('js/customer/profil/profil_pembeli.js') }}"></script>
  <script>
    // ====== Data Wishlist untuk profil ======
    // Data akan diambil dari backend melalui AJAX
    let WISHLIST = [];
    
    // Ambil data wishlist dari API
    async function fetchWishlist() {
      try {
        const response = await fetch('{{ route("api.wishlist") }}', {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        if(response.ok) {
          const data = await response.json();
          WISHLIST = Array.isArray(data) ? data : [];
        } else {
          WISHLIST = [];
        }
        renderWishlist();
      } catch (error) {
        WISHLIST = [];
        renderWishlist();
      }
    }

    // ====== Utils ======
    const $  = (s,root=document)=>root.querySelector(s);
    const $$ = (s,root=document)=>[...root.querySelectorAll(s)];
    const fmtIDR = n => (Number(n)||0).toLocaleString('id-ID', { minimumFractionDigits: 0 });

    // ====== Elements ======
    const grid = $('#wishlistGrid');
    const emptyBox = $('#wlEmpty');

    // ====== Templates ======
    function wishlistCardTemplate(item){
      const onStyle  = item.liked ? '' : 'display:none';
      const offStyle = item.liked ? 'display:none' : '';
      return `<article class="wl-card card" role="listitem" data-id="${item.id}">
        <div class="product">
          <div class="product__thumb">
            <img src="${item.img}" alt="${item.title}">
          </div>
          <div class="product__meta">
            <div class="product__title">${item.title}</div>
            <div class="product__shop">${item.shop}</div>
            <div class="wl-price">Rp ${fmtIDR(item.price)}</div>
          </div>
          <button class="wl-like" aria-pressed="true" title="Hapus dari wishlist" data-wishlist-id="${item.id}">
            <svg class="svg-off" style="${offStyle}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 47 47"><path d="M23.6962 36.3271L23.5003 36.5229L23.2849 36.3271C13.9828 27.8867 7.83366 22.3054 7.83366 16.6458C7.83366 12.7292 10.7712 9.79167 14.6878 9.79167C17.7037 9.79167 20.6412 11.75 21.6791 14.4133H25.3216C26.3595 11.75 29.297 9.79167 32.3128 9.79167C36.2295 9.79167 39.167 12.7292 39.167 16.6458C39.167 22.3054 33.0178 27.8867 23.6962 36.3271ZM32.3128 5.875C28.9053 5.875 25.6349 7.46125 23.5003 9.94833C21.3657 7.46125 18.0953 5.875 14.6878 5.875C8.65616 5.875 3.91699 10.5946 3.91699 16.6458C3.91699 24.0287 10.5753 30.08 20.6607 39.2254L23.5003 41.8104L26.3399 39.2254C36.4253 30.08 43.0837 24.0287 43.0837 16.6458C43.0837 10.5946 38.3445 5.875 32.3128 5.875Z" fill="#F24822"/></svg>
            <svg class="svg-on"  style="${onStyle}"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 47 47"><path d="M23.5003 41.8104L20.6607 39.2254C10.5753 30.08 3.91699 24.0287 3.91699 16.6458C3.91699 10.5946 8.65616 5.875 14.6878 5.875C18.0953 5.875 21.3657 7.46125 23.5003 9.94833C25.6349 7.46125 28.9053 5.875 32.3128 5.875C38.3445 5.875 43.0837 10.5946 43.0837 16.6458C43.0837 24.0287 36.4253 30.08 26.3399 39.2254L23.5003 41.8104Z" fill="#F24822"/></svg>
          </button>
          <div class="product__actions">
            <a class="btn-lihat" href="/produk_detail/${item.id}" data-product-id="${item.id}">Lihat Detail</a>
            <button class="btn-add" data-product-id="${item.id}" data-name="${item.title}">+ Keranjang</button>
          </div>
        </div>
      </article>`;
    }

    // ====== Render ======
    function renderWishlist(){
      if (!grid) return;
      
      // Pastikan WISHLIST adalah array
      const wishlistArray = Array.isArray(WISHLIST) ? WISHLIST : [];
      
      if (wishlistArray.length > 0) {
        grid.innerHTML = wishlistArray.map(wishlistCardTemplate).join('');
        if (emptyBox) emptyBox.hidden = true;
      } else {
        grid.innerHTML = '';
        if (emptyBox) emptyBox.hidden = false;
      }
    }

    // ====== API Functions ======
    async function removeFromWishlist(wishlistId) {
      try {
        const response = await fetch(`/wishlist/${wishlistId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
          },
        });
        
        if (!response.ok) {
          if (response.status === 401 || response.status === 403) {
            alert('Anda harus login terlebih dahulu untuk mengakses wishlist');
            window.location.href = '/login';
            throw new Error('Unauthenticated');
          }
          const errorData = await response.json();
          throw new Error(errorData.message || 'Gagal menghapus dari wishlist');
        }
        
        return await response.json();
      } catch (error) {
        // Handle authentication error in catch block as well
        if (error.message === 'Unauthenticated' || error.message.includes('401') || error.message.includes('403')) {
          throw error;
        }
        console.error('Error removing from wishlist:', error);
        throw error;
      }
    }
    
    // Add to cart functionality
    async function addToCart(productId, productName = 'Produk') {
      // Ambil CSRF token
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      
      if (!productId) {
        showNotification('Produk tidak ditemukan', 'error');
        return;
      }
      
      try {
        const response = await fetch('/cart/add', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({ 
            product_id: productId,
            quantity: 1 // Default quantity
          })
        });

        const result = await response.json();

        if (result.success) {
          showNotification(result.message || `${productName} berhasil ditambahkan ke keranjang`, 'success');

          // Update cart count in header via API
          if (window.updateCartCount) {
            window.updateCartCount();
          }
        } else {
          showNotification(result.message || 'Gagal menambahkan ke keranjang', 'error');
        }
      } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
      }
    }

    // ====== Events ======
    document.addEventListener('click', async (e)=>{
      const btn = e.target.closest('.wl-like');
      if (!btn) return;
      const art = btn.closest('.wl-card');
      const wishlistId = btn.dataset.wishlistId || art?.dataset.wishlistId;
      
      if (!wishlistId) return;
      
      try {
        // Hapus dari wishlist di backend
        await removeFromWishlist(wishlistId);
        
        // Hapus dari state lokal
        const idx = WISHLIST.findIndex(it => it.id == wishlistId);
        if (idx !== -1) {
          WISHLIST.splice(idx, 1);
          renderWishlist();
          
          // Tampilkan notifikasi sukses bahwa produk telah dihapus dari wishlist
          showNotification('Produk berhasil dihapus dari wishlist', 'success');
        }
      } catch (error) {
        // Handle unauthenticated error specifically
        if (error.message === 'Unauthenticated' || error.message.includes('401') || error.message.includes('403')) {
          // Error already handled in removeFromWishlist function
          return;
        }
        // Tampilkan notifikasi error dengan pesan yang lebih informatif
        const errorMessage = error.message || error.toString() || 'Terjadi kesalahan tidak terduga saat menghapus produk dari wishlist';
        showNotification('Gagal menghapus produk dari wishlist: ' + errorMessage, 'error');
      }
    });
    
    // Add to cart functionality for wishlist items
    document.addEventListener('click', async (e)=>{
      const btn = e.target.closest('.btn-add');
      if (!btn) return;
      
      const productId = btn.dataset.productId;
      const productName = btn.dataset.name;
      
      if (!productId) return;
      
      await addToCart(productId, productName);
    });

    // ====== Notification ======
    function showNotification(message, type = 'info') {
      // Buat elemen notifikasi
      const notification = document.createElement('div');
      notification.className = `notification notification-${type}`;
      notification.textContent = message;
      
      // Gaya dasar untuk notifikasi
      Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '12px 20px',
        borderRadius: '6px',
        color: '#fff',
        backgroundColor: type === 'error' ? '#dc3545' : type === 'success' ? '#28a745' : '#007bff',
        zIndex: '9999',
        boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
        fontSize: '14px',
        maxWidth: '400px',
        wordWrap: 'break-word'
      });
      
      // Tambahkan ke body
      document.body.appendChild(notification);
      
      // Hapus notifikasi setelah 3 detik
      setTimeout(() => {
        if (notification.parentNode) {
          notification.parentNode.removeChild(notification);
        }
      }, 3000);
    }

    // ====== Init ======
    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOM fully loaded and parsed');
      fetchWishlist();
      fetchUserReviews();
      fetchUserOrders();  // Add this to fetch orders as well
      
      // Initialize active session modal
      initActiveSessionModal();
    });
    
    // ====== Active Session Modal Initialization ======
    function initActiveSessionModal() {
      console.log('Initializing active session modal...');
      
      // Fungsi untuk menangani klik tombol "Lihat & Kelola" Sesi Login Aktif
      const activeSessionBtn = document.getElementById('activeSessionBtn');
      const activeSessionModal = document.getElementById('activeSessionModal');
      const closeActiveSessionModal = document.getElementById('closeActiveSessionModal');
      const logoutAllSessionsBtn = document.getElementById('logoutAllSessionsBtn');
      
      console.log('Active session elements found:', {
        activeSessionBtn: activeSessionBtn,
        activeSessionModal: activeSessionModal,
        closeActiveSessionModal: closeActiveSessionModal,
        logoutAllSessionsBtn: logoutAllSessionsBtn
      });
      
      if (activeSessionBtn && activeSessionModal) {
        console.log('Adding event listener to active session button');
        activeSessionBtn.addEventListener('click', function() {
          console.log('Active session button clicked');
          console.log('Active session modal element:', activeSessionModal);
          activeSessionModal.classList.add('show');
          console.log('Added "show" class to modal');
          // Load active sessions data
          loadActiveSessions();
        });
      } else {
        console.log('Warning: One or more active session elements not found');
        console.log('activeSessionBtn:', activeSessionBtn ? 'found' : 'not found');
        console.log('activeSessionModal:', activeSessionModal ? 'found' : 'not found');
      }
      
      if (closeActiveSessionModal && activeSessionModal) {
        console.log('Adding event listener to close modal button');
        closeActiveSessionModal.addEventListener('click', function() {
          console.log('Close modal button clicked');
          activeSessionModal.classList.remove('show');
        });
      }
      
      if (logoutAllSessionsBtn) {
        console.log('Adding event listener to logout all sessions button');
        logoutAllSessionsBtn.addEventListener('click', function() {
          if (confirm('Apakah Anda yakin ingin logout dari semua perangkat?')) {
            // Implementasi logout semua sesi
            logoutAllSessions();
            activeSessionModal.classList.remove('show');
          }
        });
      }
      
      // Close modal when clicking outside of it
      if (activeSessionModal) {
        console.log('Adding event listener to close modal when clicking outside');
        activeSessionModal.addEventListener('click', function(event) {
          if (event.target === activeSessionModal) {
            console.log('Clicked outside modal content');
            activeSessionModal.classList.remove('show');
          }
        });
      }
    }
    
    // ====== Fetch and Render User Reviews ======
    async function fetchUserReviews() {
      try {
        const response = await fetch('/api/reviews');
        if(response.ok) {
          const data = await response.json();
          renderReviews(data.reviews || []);
        } else {
          // Fallback to empty array if API fails
          renderReviews([]);
        }
      } catch (error) {
        console.error('Error fetching reviews:', error);
        renderReviews([]);
      }
    }

    // Template function for a single review
    function reviewCardTemplate(review) {
      // Create stars based on rating
      let starsHTML = '';
      for (let i = 1; i <= 5; i++) {
        if (i <= review.rating) {
          starsHTML += '<i class="bi bi-star-fill"></i>';
        } else {
          starsHTML += '<i class="bi bi-star"></i>';
        }
      }

      return `<article class="review-card">
        <div class="review-product">
          <img src="${review.product_image || '{{ asset("src/placeholder_produk.png") }}'}" alt="${review.product_name || 'Produk'}">
          <div class="product-info">
            <h4>${review.product_name || 'Nama Produk'}</h4>
            <p class="shop-name">${review.shop_name || 'Nama Toko'}</p>
            <div class="rating">
              ${starsHTML}
              <span>(${review.rating || 0})</span>
            </div>
          </div>
        </div>
        
        <div class="review-content">
          <p>${(review.review_text || 'Ulasan tidak tersedia').replace(/\(\d+\)/g, '')}</p>
        </div>
        
        <div class="review-actions">
          <button class="btn btn-icon edit-review-btn" title="Edit Ulasan" data-review-id="${review.id}">
            <i class="bi bi-pencil"></i>
          </button>
        </div>
      </article>`;
    }

    // Render reviews to the page
    function renderReviews(reviews) {
      const reviewsList = document.getElementById('reviewsList');
      const reviewsLoading = document.getElementById('reviewsLoading');
      
      if (!reviewsList) return;
      
      if (reviewsLoading) {
        reviewsLoading.remove();
      }
      
      if (reviews.length > 0) {
        reviewsList.innerHTML = reviews.map(reviewCardTemplate).join('');
        
        // Add event listeners for edit buttons
        document.querySelectorAll('.edit-review-btn').forEach(button => {
          button.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            // Redirect to the review editing page
            window.location.href = `/halaman_ulasan`;
          });
        });
      } else {
        reviewsList.innerHTML = '<p class="no-reviews">Anda belum memberikan ulasan untuk produk apapun.</p>';
      }
    }
    
    // Tambahkan fungsi renderWishlist yang benar
    function renderWishlistCorrected(){
      if (!grid) return;
      
      // Pastikan WISHLIST adalah array
      const wishlistArray = Array.isArray(WISHLIST) ? WISHLIST : [];
      
      if (wishlistArray.length > 0) {
        grid.innerHTML = wishlistArray.map(wishlistCardTemplate).join('');
        if (emptyBox) emptyBox.hidden = true;
      } else {
        grid.innerHTML = '';
        if (emptyBox) emptyBox.hidden = false;
      }
    }
    
    // Ganti fungsi renderWishlist dengan fungsi yang benar
    renderWishlist = renderWishlistCorrected;
    
    // ====== Broadcast Channel for Wishlist Sync ======
    const wishlistChannel = new BroadcastChannel('wishlist_sync');

    // Listen for changes from other tabs/windows
    wishlistChannel.addEventListener('message', (event) => {
      if (event.data && event.data.type === 'WISHLIST_UPDATE') {
        // Refresh the wishlist from server when changes happen in other tabs
        fetchWishlist();
      }
    });

    // Function to notify other tabs about wishlist changes
    function notifyWishlistChange() {
      wishlistChannel.postMessage({
        type: 'WISHLIST_UPDATE',
        timestamp: Date.now()
      });
    }

    // Override the removeFromWishlist function to notify other tabs
    const originalRemoveFromWishlist = removeFromWishlist;
    removeFromWishlist = async function(wishlistId) {
      const result = await originalRemoveFromWishlist(wishlistId);
      // Notify other tabs after successful removal
      notifyWishlistChange();
      return result;
    };

    // ====== Fetch and Render User Orders ======
    async function fetchUserOrders() {
      try {
        const response = await fetch('/api/orders');
        if(response.ok) {
          const data = await response.json();
          renderOrders(data.orders || []);
        } else {
          // Fallback to empty array if API fails
          renderOrders([]);
        }
      } catch (error) {
        console.error('Error fetching orders:', error);
        renderOrders([]);
      }
    }

    // Template function for a single order
    function orderCardTemplate(order) {
      // Determine status class based on order status
      let statusClass = '';
      let statusText = order.status;
      switch(order.status) {
        case 'completed':
          statusClass = 'status-completed';
          statusText = 'Selesai';
          break;
        case 'shipped':
          statusClass = 'status-shipping';
          statusText = 'Dikirim';
          break;
        case 'processing':
          statusClass = 'status-processing';
          statusText = 'Diproses';
          break;
        case 'cancelled':
          statusClass = 'status-cancelled';
          statusText = 'Dibatalkan';
          break;
        default:
          statusClass = 'status-pending';
          statusText = 'Menunggu';
      }

      // Get first item image and count for display
      const firstItem = order.items && order.items.length > 0 ? order.items[0] : null;
      const additionalItemsCount = order.items ? order.items.length - 1 : 0;
      
      // Format total amount with proper number formatting
      const formattedAmount = new Intl.NumberFormat('id-ID').format(order.total_amount);
      
      return `<article class="order-card">
        <div class="order-header">
          <div class="order-id">#${order.order_number}</div>
          <div class="order-status ${statusClass}">${statusText}</div>
        </div>
        
        <div class="order-details">
          <div class="order-date">${order.created_at}</div>
          <div class="order-amount">Rp ${formattedAmount}</div>
        </div>
        
        <div class="order-items">
          <div class="item-preview">
            ${firstItem ? `<img src="${firstItem.image}" alt="${firstItem.product_name}">` : ''}
            ${additionalItemsCount > 0 ? `<span class="item-count">+${additionalItemsCount} item lainnya</span>` : ''}
          </div>
          <div class="order-actions">
            <a href="/invoice/${order.order_number}" class="btn btn-outline">Lihat Detail</a>
            ${order.status === 'shipped' ? '<a href="#" class="btn btn-primary">Lacak Pesanan</a>' : ''}
          </div>
        </div>
      </article>`;
    }

    // Render orders to the page
    function renderOrders(orders) {
      const ordersList = document.getElementById('ordersList');
      const ordersLoading = document.getElementById('ordersLoading');
      
      if (!ordersList) return;
      
      if (ordersLoading) {
        ordersLoading.remove();
      }
      
      if (orders.length > 0) {
        ordersList.innerHTML = orders.map(orderCardTemplate).join('');
      } else {
        ordersList.innerHTML = '<p class="no-orders">Belum ada riwayat pesanan.</p>';
      }
    }
    
    // Fetch reviews when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
      fetchUserReviews();
      fetchUserOrders();  // Add this to fetch orders as well
      fetchWishlist(); // Tambahkan pemanggilan fetchWishlist
    });
    
    // ====== Modal Sesi Login Aktif ======
    
    // Fungsi untuk menangani klik tombol "Lihat & Kelola" Sesi Login Aktif
    const activeSessionBtn = document.getElementById('activeSessionBtn');
    const activeSessionModal = document.getElementById('activeSessionModal');
    const closeActiveSessionModal = document.getElementById('closeActiveSessionModal');
    const logoutAllSessionsBtn = document.getElementById('logoutAllSessionsBtn');
    
    if (activeSessionBtn && activeSessionModal) {
      activeSessionBtn.addEventListener('click', function() {
        console.log('Active session button clicked');
        console.log('Active session modal element:', activeSessionModal);
        activeSessionModal.classList.add('show');
        console.log('Added "show" class to modal');
        // Load active sessions data
        loadActiveSessions();
      });
    }
    
    if (closeActiveSessionModal && activeSessionModal) {
      closeActiveSessionModal.addEventListener('click', function() {
        activeSessionModal.classList.remove('show');
      });
    }
    
    if (logoutAllSessionsBtn) {
      logoutAllSessionsBtn.addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin logout dari semua perangkat?')) {
          // Implementasi logout semua sesi
          alert('Fitur logout semua sesi akan diimplementasikan di backend.');
          activeSessionModal.classList.remove('show');
        }
      });
    }
    
    // Close modal when clicking outside of it
    if (activeSessionModal) {
      activeSessionModal.addEventListener('click', function(event) {
        if (event.target === activeSessionModal) {
          activeSessionModal.classList.remove('show');
        }
      });
    }
    
    // Load active sessions data from API
    async function loadActiveSessions() {
      try {
        console.log('Loading active sessions from API...');
        const response = await fetch('/api/active-sessions');
        
        if (response.ok) {
          const data = await response.json();
          console.log('Active sessions data received:', data);
          renderActiveSessions(data.sessions || []);
        } else {
          console.error('Failed to load active sessions:', response.status);
          // Fallback to dummy data if API fails
          const dummySessions = [
            {
              id: 1,
              device: 'Chrome di Windows',
              location: 'Jakarta, Indonesia',
              lastActive: 'Baru saja',
              current: true,
              ip: '192.168.1.100'
            },
            {
              id: 2,
              device: 'Safari di iPhone',
              location: 'Bandung, Indonesia',
              lastActive: '2 jam yang lalu',
              current: false,
              ip: '203.123.45.67'
            },
            {
              id: 3,
              device: 'Firefox di Mac',
              location: 'Surabaya, Indonesia',
              lastActive: '1 hari yang lalu',
              current: false,
              ip: '118.90.12.34'
            }
          ];
          renderActiveSessions(dummySessions);
        }
      } catch (error) {
        console.error('Error loading active sessions:', error);
        // Fallback to dummy data if there's an error
        const dummySessions = [
          {
            id: 1,
            device: 'Chrome di Windows',
            location: 'Jakarta, Indonesia',
            lastActive: 'Baru saja',
            current: true,
            ip: '192.168.1.100'
          },
          {
            id: 2,
            device: 'Safari di iPhone',
            location: 'Bandung, Indonesia',
            lastActive: '2 jam yang lalu',
            current: false,
            ip: '203.123.45.67'
          },
          {
            id: 3,
            device: 'Firefox di Mac',
            location: 'Surabaya, Indonesia',
            lastActive: '1 hari yang lalu',
            current: false,
            ip: '118.90.12.34'
          }
        ];
        renderActiveSessions(dummySessions);
      }
    }
    
    // Function to logout a specific session
    async function logoutSession(sessionId) {
      try {
        console.log(`Logout session requested for session ID: ${sessionId}`);
        
        const response = await fetch(`/api/active-sessions/${sessionId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          if (data.success) {
            alert('Sesi berhasil diakhiri');
            // Reload active sessions to reflect changes
            loadActiveSessions();
          } else {
            alert(data.message || 'Gagal mengakhiri sesi');
          }
        } else {
          alert('Gagal mengakhiri sesi. Silakan coba lagi.');
        }
      } catch (error) {
        console.error('Error logging out session:', error);
        alert('Terjadi kesalahan saat mengakhiri sesi');
      }
    }
    
    // Function to logout all sessions
    async function logoutAllSessions() {
      try {
        console.log('Logout all sessions requested');
        
        const response = await fetch('/api/active-sessions', {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          if (data.success) {
            alert('Semua sesi lain berhasil diakhiri');
            // Reload active sessions to reflect changes
            loadActiveSessions();
          } else {
            alert(data.message || 'Gagal mengakhiri semua sesi');
          }
        } else {
          alert('Gagal mengakhiri semua sesi. Silakan coba lagi.');
        }
      } catch (error) {
        console.error('Error logging out all sessions:', error);
        alert('Terjadi kesalahan saat mengakhiri semua sesi');
      }
    }
  </script>
  
  <!-- Modal Sesi Login Aktif -->
  <div id="activeSessionModal" class="modal">
    <div class="modal-content" style="width: 90%; max-width: 600px;">
      <div class="modal-header">
        <h3>Sesi Login Aktif</h3>
        <span class="close" id="closeActiveSessionModal">&times;</span>
      </div>
      <div class="modal-body">
        <div class="sessions-list" id="sessionsList">
          <!-- Sessions will be loaded here by JavaScript -->
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline" id="logoutAllSessionsBtn">Logout dari Semua Perangkat</button>
      </div>
    </div>
  </div>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection

