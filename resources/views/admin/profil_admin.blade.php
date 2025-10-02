@extends('layouts.admin')

@section('title', 'Profil Admin - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/penjual/profil_penjual.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <style>
    /* Sesuaikan beberapa elemen admin dengan desain profil penjual */
    .admin-profile .info-list > div {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .75rem;
      padding: .4rem 0;
      border-bottom: 1px dashed #eceff1;
    }
    
    .admin-profile .info-list > div:last-child {
      border-bottom: none;
    }
    
    .admin-profile .info-list dt {
      font-weight: 600;
      flex: 1;
    }
    
    .admin-profile .info-list dd {
      margin: 0;
      color: var(--text-muted);
      text-align: right;
      flex: 1;
    }
    
    .admin-orders-viewport {
      max-height: 500px;
      overflow: auto;
      margin-top: 1rem;
    }
    
    .admin-order-card {
      border: 1px solid var(--border-color);
      border-radius: var(--radius-lg);
      padding: 1rem;
      background: #fff;
      margin-bottom: .9rem;
    }
    
    .admin-order-card .item-desc {
      word-wrap: break-word;
      overflow-wrap: break-word;
      white-space: normal;
      flex: 1;
      min-width: 0; /* Memastikan elemen bisa mengecil saat teks terlalu panjang */
    }
    
    .admin-order-card .item-body {
      min-width: 0;
      display: flex;
      flex-direction: column;
      gap: .4rem;
      width: 100%; /* Memastikan lebar penuh digunakan */
    }
    
    .admin-order-card .item-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      width: 100%; /* Memastikan lebar penuh digunakan */
    }
    
    /* Perbaikan untuk tampilan log personal */
    .admin-order-card .order-items {
      width: 100%;
    }
    
    .admin-order-card .order-item {
      display: flex;
      flex-direction: column; /* Ubah ke kolom agar teks panjang tidak menyebabkan overflow */
      gap: .4rem;
      width: 100%;
    }
    
    /* Pengaturan untuk membuat header custom muncul di atas dan navbar Bootstrap muncul di bawah */
    
    /* Header custom tetap menggunakan sticky tapi kita akan menyembunyikan navbar Bootstrap */
    .ak-navbar {
      position: sticky;
      top: 0;
      z-index: 1001;
    }
    
    /* Sembunyikan navbar Bootstrap asli dan buat elemen pengganti */
    body .navbar {
      display: none !important;
    }
    
    /* Buat elemen untuk menempatkan tombol back di bawah header custom */
    .custom-back-nav {
      position: static; /* Mengubah dari sticky ke static agar tidak mengikuti scroll */
      background-color: #f8f9fa; /* Warna latar navbar Bootstrap */
      padding: 0.75rem 1.5rem;
      border-bottom: 1px solid #dee2e6;
      z-index: 1000;
      display: flex;
      align-items: center;
    }
    
    .custom-back-nav a {
      display: flex;
      align-items: center;
      text-decoration: none;
      color: inherit;
    }
    
    .main-layout {
      margin-top: 0; /* Hilangkan margin-top agar tidak terlalu jauh dari tombol back */
    }
    
    /* Menjaga agar konten tidak tertutup oleh header sticky */
    .content.admin-page-content {
      margin-top: 0;
      padding-top: 30px; /* Kurangi ruang antara tombol back dan isi konten */
    }
  </style>
@endpush

@section('content')
  @include('components.admin_penjual.header')
  <!-- Custom back navigation -->
  <div class="custom-back-nav">
    <a href="javascript:history.back()" class="navbar-brand" title="Kembali ke halaman sebelumnya">
      <i class="fas fa-arrow-left me-2"></i>UMKM AKRAB Admin
    </a>
  </div>
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="content admin-page-content" role="main">
          <!-- Profil Admin -->
          <section class="card card-profile admin-profile" aria-labelledby="adminTitle">
            <div class="seller-hero">
              <!-- kiri: identitas -->
              <div class="seller-identity">
                <div class="avatar" aria-hidden="true">
                  <span>A</span>
                  <i class="dot online"></i>
                </div>
                <div class="seller-meta">
                  <h1 id="adminTitle" class="seller-name">Admin Utama</h1>
                  <div class="seller-mail">admin@akrab.com</div>
                  <div class="seller-since">Bergabung sejak <strong>2024</strong></div>
                </div>
              </div>
              <!-- kanan: aksi -->
              <div class="profile-actions">
                <a href="{{ route('edit.profil.admin') }}" id="btnEditProfile" class="btn btn-primary btn-sm">
                  Edit Profil
                </a>
              </div>
            </div>

            <dl class="info-list" aria-label="Info Admin">
              <div>
                <dt>Nama Lengkap</dt>
                <dd>Admin Utama</dd>
              </div>
              <div>
                <dt>Email</dt>
                <dd>admin@akrab.com</dd>
              </div>
              <div>
                <dt>No. HP</dt>
                <dd>+62 812-3456-7890</dd>
              </div>
              <div>
                <dt>Jabatan</dt>
                <dd>Administrator</dd>
              </div>
              <div>
                <dt>Level Akses</dt>
                <dd>Super Admin</dd>
              </div>
              <div>
                <dt>Status Akun</dt>
                <dd style="color: #10b981; font-weight: 600;">Aktif</dd>
              </div>
            </dl>
          </section>



          <!-- Log Personal -->
          <section class="card reviews-section" aria-labelledby="logTitle">
            <div class="card-head">
              <h2 id="logTitle" class="card-title">Log Personal</h2>
            </div>
            <div class="admin-orders-viewport">
              <article class="admin-order-card" data-order-id="LOG-001">
                <div class="order-head">
                  <div class="shop-name">Anda</div>
                  <div class="order-meta">
                    <span>Tanggal:
                      <time datetime="2025-09-05">05 Sep 2025</time>
                    </span>
                    • <span>Status: <strong>Berhasil</strong></span>
                  </div>
                </div>

                <div class="order-items">
                  <div class="order-item">
                    <div class="item-body">
                      <div class="item-row">
                        <div class="item-name">Menyetujui penarikan dana</div>
                      </div>
                      <div class="item-desc scrollable">
                        Anda menyetujui penarikan dana untuk Toko Aneka Roti.
                      </div>
                    </div>
                  </div>
                </div>
              </article>
              
              <article class="admin-order-card" data-order-id="LOG-002">
                <div class="order-head">
                  <div class="shop-name">Anda</div>
                  <div class="order-meta">
                    <span>Tanggal:
                      <time datetime="2025-09-04">04 Sep 2025</time>
                    </span>
                    • <span>Status: <strong>Selesai</strong></span>
                  </div>
                </div>

                <div class="order-items">
                  <div class="order-item">
                    <div class="item-body">
                      <div class="item-row">
                        <div class="item-name">Menangguhkan akun pembeli</div>
                      </div>
                      <div class="item-desc scrollable">
                        Anda menangguhkan akun pembeli Jerome Bell.
                      </div>
                    </div>
                  </div>
                </div>
              </article>
              
              <article class="admin-order-card" data-order-id="LOG-003">
                <div class="order-head">
                  <div class="shop-name">Anda</div>
                  <div class="order-meta">
                    <span>Tanggal:
                      <time datetime="2025-09-03">03 Sep 2025</time>
                    </span>
                    • <span>Status: <strong>Selesai</strong></span>
                  </div>
                </div>

                <div class="order-items">
                  <div class="order-item">
                    <div class="item-body">
                      <div class="item-row">
                        <div class="item-name">Mengubah pengaturan komisi</div>
                      </div>
                      <div class="item-desc scrollable">
                        Anda mengubah pengaturan komisi platform.
                      </div>
                    </div>
                  </div>
                </div>
              </article>
            </div>
          </section>
        </main>
      </div>
    </div>
  @include('components.admin_penjual.footer')
@endsection