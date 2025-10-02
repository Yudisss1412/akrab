@extends('layouts.admin')

@section('title', 'Profil Admin - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <style>
    /* Admin Profile Specific Styles */
    .profile-container {
      max-width: 800px;
      margin: 0 auto;
      background: var(--white);
      border-radius: var(--ak-radius);
      border: 1px solid var(--ak-border);
      box-shadow: 0 8px 20px rgba(0,0,0,.05);
      overflow: hidden;
    }
    
    .profile-header {
      background: var(--ak-primary);
      color: white;
      padding: 1.5rem;
      text-align: center;
    }
    
    .profile-content {
      padding: 1.5rem;
    }
    
    .profile-section {
      margin-bottom: 1.5rem;
    }
    
    .profile-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--ak-text);
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid var(--ak-border);
    }
    
    .profile-info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }
    
    .info-item {
      margin-bottom: 0.75rem;
    }
    
    .info-label {
      font-weight: 500;
      color: var(--muted);
      font-size: 0.875rem;
      margin-bottom: 0.25rem;
    }
    
    .info-value {
      font-weight: 600;
      color: var(--text);
      font-size: 1rem;
    }
    
    .profile-actions {
      display: flex;
      gap: 0.75rem;
      margin-top: 1.5rem;
      flex-wrap: wrap;
    }
    
    .btn {
      padding: 8px 16px;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: var(--white);
      cursor: pointer;
      font-weight: 500;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }
    
    .btn-primary {
      background: var(--primary);
      color: #fff;
      border-color: var(--primary);
    }
    
    .btn-outline {
      background: transparent;
      border: 1px solid var(--primary);
      color: var(--primary);
    }
    
    .profile-avatar {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--ak-primary-light);
      margin: 0 auto 1rem;
      display: block;
    }
  </style>
@endpush

@section('content')
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content">
        <div class="profile-container">
          <div class="profile-header">
            <h1>Profil Admin</h1>
            <p>Kelola informasi akun Anda</p>
          </div>
          
          <div class="profile-content">
            <div class="profile-section text-center">
              <img src="{{ asset('src/admin-avatar.png') }}" alt="Admin Avatar" class="profile-avatar" onerror="this.src='https://ui-avatars.com/api/?name=Admin+User&background=006E5C&color=fff&size=100'">
              <h2 style="margin-top: 0.5rem; margin-bottom: 0.25rem;">Admin Utama</h2>
              <p style="margin: 0; color: var(--muted);">Administrator Sistem</p>
            </div>
            
            <div class="profile-section">
              <h3 class="profile-title">Informasi Pribadi</h3>
              <div class="profile-info-grid">
                <div class="info-item">
                  <div class="info-label">Nama Lengkap</div>
                  <div class="info-value">Admin Utama</div>
                </div>
                <div class="info-item">
                  <div class="info-label">Email</div>
                  <div class="info-value">admin@akrab.com</div>
                </div>
                <div class="info-item">
                  <div class="info-label">Nomor Telepon</div>
                  <div class="info-value">+62 812-3456-7890</div>
                </div>
                <div class="info-item">
                  <div class="info-label">Jabatan</div>
                  <div class="info-value">Administrator</div>
                </div>
                <div class="info-item">
                  <div class="info-label">Tanggal Bergabung</div>
                  <div class="info-value">1 Januari 2024</div>
                </div>
                <div class="info-item">
                  <div class="info-label">Status Akun</div>
                  <div class="info-value" style="color: #10b981; font-weight: 600;">Aktif</div>
                </div>
              </div>
            </div>
            
            <div class="profile-section">
              <h3 class="profile-title">Statistik Admin</h3>
              <div class="profile-info-grid">
                <div class="info-item">
                  <div class="info-label">Jumlah Penjual Terdaftar</div>
                  <div class="info-value" style="font-size: 1.25rem;">127</div>
                </div>
                <div class="info-item">
                  <div class="info-label">Jumlah Produk Aktif</div>
                  <div class="info-value" style="font-size: 1.25rem;">843</div>
                </div>
                <div class="info-item">
                  <div class="info-label">Transaksi Bulan Ini</div>
                  <div class="info-value" style="font-size: 1.25rem;">2,156</div>
                </div>
                <div class="info-item">
                  <div class="info-label">Total Pendapatan</div>
                  <div class="info-value" style="font-size: 1.25rem;">Rp 1.245.678.000</div>
                </div>
              </div>
            </div>
            
            <div class="profile-section">
              <h3 class="profile-title">Akses Sistem</h3>
              <div class="profile-info-grid">
                <div class="info-item">
                  <div class="info-label">Terakhir Login</div>
                  <div class="info-value">Hari ini, 14:30 WIB</div>
                </div>
                <div class="info-item">
                  <div class="info-label">IP Terakhir</div>
                  <div class="info-value">192.168.1.100</div>
                </div>
                <div class="info-item">
                  <div class="info-label">Perangkat Login</div>
                  <div class="info-value">Desktop - Windows 10</div>
                </div>
                <div class="info-item">
                  <div class="info-label">Level Akses</div>
                  <div class="info-value">Super Admin</div>
                </div>
              </div>
            </div>
            
            <div class="profile-actions">
              <a href="#" class="btn btn-primary">Edit Profil</a>
              <a href="#" class="btn btn-outline">Ganti Password</a>
              <a href="#" class="btn btn-outline">Riwayat Aktivitas</a>
              <a href="{{ route('dashboard.admin') }}" class="btn btn-outline">Kembali ke Dashboard</a>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
@endsection