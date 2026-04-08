@extends('layouts.admin')

@section('title', 'Manajemen Penjual - AKRAB')

@include('components.admin_penjual.header')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <style>
    /* Modern Design System for Seller Management */
    :root {
      --primary: #006E5C;
      --primary-light: #a8d5c9;
      --primary-dark: #005a4a;
      --secondary: #64748b;
      --success: #10b981;
      --danger: #ef4444;
      --warning: #f59e0b;
      --info: #06b6d4;
      --light: #f8fafc;
      --dark: #1e293b;
      --white: #ffffff;
      --gray-50: #f9fafb;
      --gray-100: #f3f4f6;
      --gray-200: #e5e7eb;
      --gray-300: #d1d5db;
      --gray-400: #9ca3af;
      --gray-500: #6b7280;
      --gray-600: #4b5563;
      --gray-700: #374151;
      --gray-800: #1f2937;
      --border: #e5e7eb;
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
      --radius-sm: 0.375rem;
      --radius: 0.5rem;
      --radius-md: 0.75rem;
      --radius-lg: 1rem;
      --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
      box-sizing: border-box;
    }

    body {
      background-color: var(--gray-50);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      color: var(--gray-800);
      line-height: 1.6;
    }

    /* Modern Container */
    .modern-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 1.5rem;
    }

    /* Page Header Modern */
    .page-header-modern {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .page-header-left {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .back-button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: var(--radius);
      background: var(--white);
      border: 1px solid var(--border);
      color: var(--gray-600);
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
    }

    .back-button:hover {
      background: var(--gray-100);
      color: var(--primary);
      border-color: var(--primary);
    }

    .page-title-modern {
      margin: 0;
      font-size: 1.875rem;
      font-weight: 700;
      color: var(--dark);
      letter-spacing: -0.025em;
    }

    .page-subtitle {
      font-size: 0.875rem;
      color: var(--gray-500);
      margin-top: 0.25rem;
    }

    /* Tab Navigation Modern */
    .tabs-modern {
      display: flex;
      gap: 0.5rem;
      background: var(--white);
      padding: 0.5rem;
      border-radius: var(--radius-md);
      border: 1px solid var(--border);
      margin-bottom: 2rem;
      box-shadow: var(--shadow-sm);
    }

    .tab-modern {
      flex: 1;
      padding: 0.75rem 1.5rem;
      border-radius: var(--radius);
      border: none;
      background: transparent;
      color: var(--gray-600);
      font-weight: 600;
      font-size: 0.9375rem;
      cursor: pointer;
      transition: var(--transition);
      text-align: center;
    }

    .tab-modern:hover {
      background: var(--gray-100);
      color: var(--primary);
    }

    .tab-modern.active {
      background: var(--primary);
      color: var(--white);
      box-shadow: var(--shadow);
    }

    /* Action Bar Modern */
    .action-bar {
      display: flex;
      gap: 0.75rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }

    .btn-modern {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.75rem 1.25rem;
      border-radius: var(--radius);
      border: none;
      font-weight: 600;
      font-size: 0.875rem;
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
      white-space: nowrap;
    }

    .btn-primary-modern {
      background: var(--primary);
      color: var(--white);
    }

    .btn-primary-modern:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }

    .btn-secondary-modern {
      background: var(--white);
      color: var(--gray-700);
      border: 1px solid var(--border);
    }

    .btn-secondary-modern:hover {
      background: var(--gray-50);
      border-color: var(--gray-300);
    }

    .btn-danger-modern {
      background: var(--danger);
      color: var(--white);
    }

    .btn-danger-modern:hover {
      background: #dc2626;
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }

    .btn-success-modern {
      background: var(--success);
      color: var(--white);
    }

    .btn-success-modern:hover {
      background: #059669;
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }

    .btn-warning-modern {
      background: var(--warning);
      color: var(--white);
    }

    .btn-warning-modern:hover {
      background: #d97706;
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }

    /* Card Modern */
    .card-modern {
      background: var(--white);
      border-radius: var(--radius-lg);
      border: 1px solid var(--border);
      box-shadow: var(--shadow);
      overflow: hidden;
      transition: var(--transition);
    }

    .card-modern:hover {
      box-shadow: var(--shadow-lg);
    }

    .card-header-modern {
      padding: 1.25rem 1.5rem;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      color: var(--white);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .card-title-modern {
      margin: 0;
      font-size: 1.125rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .card-body-modern {
      padding: 1.5rem;
    }

    /* Search & Filter Card Modern */
    .filter-card {
      margin-bottom: 1.5rem;
    }

    .filter-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }

    .form-group-modern {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .form-label-modern {
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--gray-700);
      margin-bottom: 0;
    }

    .form-input-modern {
      padding: 0.625rem 0.875rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      font-size: 0.875rem;
      transition: var(--transition);
      background: var(--white);
    }

    .form-input-modern:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 110, 92, 0.1);
    }

    .form-select-modern {
      padding: 0.625rem 0.875rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      font-size: 0.875rem;
      transition: var(--transition);
      background: var(--white);
      cursor: pointer;
    }

    .form-select-modern:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 110, 92, 0.1);
    }

    /* Seller Card Grid (Mobile) */
    .seller-card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1.25rem;
    }

    .seller-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      border: 1px solid var(--border);
      box-shadow: var(--shadow);
      padding: 1.25rem;
      transition: var(--transition);
      position: relative;
    }

    .seller-card:hover {
      box-shadow: var(--shadow-lg);
      transform: translateY(-2px);
      border-color: var(--primary-light);
    }

    .seller-card-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1rem;
      gap: 0.75rem;
    }

    .seller-store-name {
      font-size: 1.125rem;
      font-weight: 700;
      color: var(--dark);
      margin: 0 0 0.25rem 0;
      text-decoration: none;
    }

    .seller-store-name:hover {
      color: var(--primary);
    }

    .seller-id {
      font-size: 0.75rem;
      color: var(--gray-400);
    }

    .seller-status-badge {
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
      white-space: nowrap;
    }

    .status-active {
      background: rgba(16, 185, 129, 0.1);
      color: var(--success);
    }

    .status-suspended {
      background: rgba(239, 68, 68, 0.1);
      color: var(--danger);
    }

    .status-pending {
      background: rgba(245, 158, 11, 0.1);
      color: var(--warning);
    }

    .seller-info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0.75rem;
      margin-bottom: 1rem;
    }

    .seller-info-item {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
    }

    .seller-info-label {
      font-size: 0.75rem;
      color: var(--gray-500);
      font-weight: 500;
    }

    .seller-info-value {
      font-size: 0.875rem;
      color: var(--gray-800);
      font-weight: 600;
    }

    .seller-stats {
      display: flex;
      gap: 0.75rem;
      margin-bottom: 1rem;
      padding: 0.75rem;
      background: var(--gray-50);
      border-radius: var(--radius);
    }

    .seller-stat {
      flex: 1;
      text-align: center;
    }

    .seller-stat-value {
      font-size: 1rem;
      font-weight: 700;
      color: var(--primary);
    }

    .seller-stat-label {
      font-size: 0.7rem;
      color: var(--gray-500);
      margin-top: 0.125rem;
    }

    .seller-card-actions {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
    }

    .seller-card-actions .btn-modern {
      flex: 1;
      min-width: 70px;
      padding: 0.5rem 0.75rem;
      font-size: 0.8rem;
    }

    /* Table View (Desktop) */
    .table-modern {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }

    .table-modern thead th {
      background: var(--gray-50);
      padding: 0.875rem 1rem;
      text-align: left;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--gray-600);
      border-bottom: 2px solid var(--border);
    }

    .table-modern tbody tr {
      transition: var(--transition);
    }

    .table-modern tbody tr:hover {
      background: var(--gray-50);
    }

    .table-modern tbody td {
      padding: 1rem;
      border-bottom: 1px solid var(--border);
      font-size: 0.875rem;
    }

    /* Pagination Modern */
    .pagination-modern {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 0.5rem;
      margin-top: 2rem;
      flex-wrap: wrap;
    }

    .pagination-btn {
      padding: 0.5rem 0.875rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background: var(--white);
      color: var(--gray-700);
      font-weight: 600;
      font-size: 0.875rem;
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
    }

    .pagination-btn:hover {
      background: var(--gray-100);
      border-color: var(--gray-300);
    }

    .pagination-btn.active {
      background: var(--primary);
      color: var(--white);
      border-color: var(--primary);
    }

    .pagination-btn.disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    /* Responsive - Mobile First */
    @media (max-width: 768px) {
      .modern-container {
        padding: 1rem;
      }

      .page-header-modern {
        flex-direction: column;
        align-items: flex-start;
      }

      .page-title-modern {
        font-size: 1.5rem;
      }

      .tabs-modern {
        flex-direction: column;
      }

      .tab-modern {
        width: 100%;
      }

      .action-bar {
        flex-direction: column;
      }

      .btn-modern {
        width: 100%;
      }

      .filter-grid {
        grid-template-columns: 1fr;
      }

      .seller-card-grid {
        grid-template-columns: 1fr;
      }

      .seller-info-grid {
        grid-template-columns: 1fr;
      }

      .seller-card-actions {
        flex-direction: column;
      }

      .seller-card-actions .btn-modern {
        width: 100%;
      }

      /* Hide table on mobile */
      .table-desktop {
        display: none;
      }
    }

    @media (min-width: 769px) {
      /* Hide card grid on desktop */
      .seller-card-grid {
        display: none;
      }

      /* Show table on desktop */
      .table-desktop {
        display: block;
      }
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 3rem 1.5rem;
    }

    .empty-state-icon {
      font-size: 3rem;
      color: var(--gray-300);
      margin-bottom: 1rem;
    }

    .empty-state-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--gray-700);
      margin-bottom: 0.5rem;
    }

    .empty-state-text {
      color: var(--gray-500);
      font-size: 0.875rem;
    }

    /* Checkbox Modern */
    .checkbox-modern {
      width: 1.125rem;
      height: 1.125rem;
      border: 2px solid var(--border);
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: var(--transition);
    }

    .checkbox-modern:checked {
      background: var(--primary);
      border-color: var(--primary);
    }

    /* Bulk Actions Modern */
    .bulk-actions {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 1rem;
      background: rgba(0, 110, 92, 0.05);
      border-left: 4px solid var(--primary);
      border-radius: var(--radius);
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }

    .bulk-actions label {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--gray-700);
    }

    /* Loading Skeleton */
    .skeleton {
      background: linear-gradient(90deg, var(--gray-100) 25%, var(--gray-200) 50%, var(--gray-100) 75%);
      background-size: 200% 100%;
      animation: skeleton-loading 1.5s infinite;
      border-radius: var(--radius);
    }

    @keyframes skeleton-loading {
      0% {
        background-position: 200% 0;
      }
      100% {
        background-position: -200% 0;
      }
    }
  </style>
@endpush

@section('content')
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content main-content">
        <div class="page-header">
          <h2 class="page-title">Manajemen Pengguna</h2>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="userTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link {{ (isset($tab) && $tab === 'buyers') ? '' : 'active' }}" id="sellers-tab" data-bs-toggle="tab" data-bs-target="#sellers-tab-pane" type="button" role="tab">Penjual</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link {{ (isset($tab) && $tab === 'buyers') ? 'active' : '' }}" id="buyers-tab" data-bs-toggle="tab" data-bs-target="#buyers-tab-pane" type="button" role="tab">Pembeli</button>
          </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="userTabsContent">
          <!-- Sellers Tab -->
          <div class="tab-pane fade {{ (isset($tab) && $tab === 'buyers') ? '' : 'show active' }}" id="sellers-tab-pane" role="tabpanel">
            <div class="mt-3">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('sellers.create') }}" class="btn btn-primary">
                  <i class="fas fa-plus"></i> Tambah Penjual Baru
                </a>
              </div>

              <!-- Bulk Actions Toolbar -->
              <div class="bulk-actions-toolbar mb-3">
                <form id="bulkActionForm" method="POST" action="{{ route('sellers.bulk_action') }}">
                  @csrf
                  <div class="row align-items-center">
                    <div class="col-md-2">
                      <input type="checkbox" id="selectAll" class="form-check-input">
                      <label for="selectAll" class="form-check-label ms-1">Pilih Semua</label>
                    </div>
                    <div class="col-md-3">
                      <select name="action" class="form-select" required>
                        <option value="">Pilih Aksi</option>
                        <option value="suspend">Tangguhkan</option>
                        <option value="activate">Aktifkan</option>
                        <option value="delete">Hapus</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin melakukan aksi ini pada penjual terpilih?')">Terapkan</button>
                    </div>
                    <div class="col-md-5 text-end">
                      <a href="{{ route('sellers.export_sellers') }}" class="btn btn-primary">
                        <i class="fas fa-download"></i> Ekspor Data
                      </a>
                    </div>
                  </div>
                  </div>
                  <input type="hidden" name="seller_ids" id="sellerIdsInput">
                </form>
              </div>

              <!-- Filter Panel - Horizontal Layout -->
              <div class="filter-panel">
                <div class="filter-header">
                  <h3 class="filter-title"><i class="fas fa-filter"></i> Filter dan Pencarian</h3>
                </div>
                <div class="filter-content">
                  <form id="filterForm" method="GET" action="{{ route('sellers.index') }}?tab=sellers">
                    <div class="row g-2">
                      <div class="col-md-5 mb-2">
                        <label for="search" class="form-label">Cari Penjual</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nama Toko, Nama Pemilik, atau Email">
                      </div>
                      <div class="col-md-2 mb-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                          <option value="">Semua</option>
                          @foreach($statusOptions as $value => $label)
                              <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                  {{ $label }}
                              </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-2 mb-2">
                        <label for="join_date_from" class="form-label">Bergabung Dari</label>
                        <input type="date" class="form-control" id="join_date_from" name="join_date_from" value="{{ request('join_date_from') }}">
                      </div>
                      <div class="col-md-2 mb-2">
                        <label for="join_date_to" class="form-label">Bergabung Sampai</label>
                        <input type="date" class="form-control" id="join_date_to" name="join_date_to" value="{{ request('join_date_to') }}">
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Sellers Table -->
              <div class="table-container mt-2">
                <div class="table-header">
                  <h3 class="table-title">Daftar Penjual</h3>
                </div>
                <div class="table-content">
                  <div class="table-responsive">
                  <table class="table table-striped table-hover">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" id="selectAllHeader" class="form-check-input">
                        </th>
                        <th scope="col">Nama Toko</th>
                        <th scope="col">Info Pemilik</th>
                        <th scope="col">Statistik</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($sellers ?? collect() as $seller)
                      <tr id="seller-row-{{ $seller->id }}">
                        <td>
                          <input type="checkbox" class="form-check-input seller-checkbox" value="{{ $seller->id }}" name="seller_ids[]">
                        </td>
                        <td>
                          <a href="{{ route('sellers.show', $seller) }}" class="font-weight-bold text-primary d-block">
                            {{ $seller->store_name }}
                          </a>
                          <small class="text-muted d-block">ID: {{ $seller->id }}</small>
                        </td>
                        <td>
                          <div><strong>{{ $seller->owner_name }}</strong></div>
                          <small class="text-muted">{{ $seller->email }}</small><br>
                          <small class="text-muted">Bergabung: {{ $seller->join_date ? $seller->join_date->format('d M Y') : '-' }}</small>
                        </td>
                        <td>
                          <div>Produk: <strong>{{ $seller->active_products_count }}</strong></div>
                          <div>GMV: <strong>Rp {{ number_format($seller->total_sales, 2, ',', '.') }}</strong></div>
                          <div>Rating:
                            <span class="badge bg-warning text-dark">
                              <i class="fas fa-star"></i> {{ number_format($seller->rating, 1) }}
                            </span>
                          </div>
                        </td>
                        <td>
                          @php
                            $statusClass = '';
                            $statusText = '';
                            switch($seller->status) {
                              case 'aktif':
                                $statusClass = 'bg-success';
                                $statusText = 'Aktif';
                                break;
                              case 'ditangguhkan':
                                $statusClass = 'bg-danger';
                                $statusText = 'Ditangguhkan';
                                break;
                              case 'menunggu_verifikasi':
                                $statusClass = 'bg-warning text-dark';
                                $statusText = 'Menunggu Verifikasi';
                                break;
                              case 'baru':
                                $statusClass = 'bg-info';
                                $statusText = 'Baru';
                                break;
                              default:
                                $statusClass = 'bg-secondary';
                                $statusText = 'Tidak Dikenal';
                            }
                          @endphp
                          <span class="badge rounded-pill {{ $statusClass }} d-block">{{ $statusText }}</span>
                        </td>
                        <td>
                          <div class="btn-group d-block" role="group">
                            <a href="{{ route('sellers.show', $seller) }}" class="btn btn-info d-block text-center square-btn" title="Lihat Detail">
                              <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('sellers.edit', $seller) }}" class="btn btn-primary d-block text-center square-btn" title="Edit">
                              <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('sellers.destroy', $seller) }}" method="POST" class="d-inline delete-seller-form">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger d-block text-center square-btn" title="Hapus">
                                <i class="fas fa-trash"></i>
                              </button>
                            </form>
                            @if($seller->status !== 'ditangguhkan')
                              <form action="{{ route('sellers.suspend', $seller) }}" method="POST" class="d-inline suspend-seller-form">
                                @csrf
                                <button type="submit" class="btn btn-warning text-dark d-block text-center square-btn" title="Tangguhkan">
                                  <i class="fas fa-pause"></i>
                                </button>
                              </form>
                            @else
                              <form action="{{ route('sellers.activate', $seller) }}" method="POST" class="d-inline activate-seller-form">
                                @csrf
                                <button type="submit" class="btn btn-success d-block text-center square-btn" title="Aktifkan">
                                  <i class="fas fa-play"></i>
                                </button>
                              </form>
                            @endif
                          </div>
                        </td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="6" class="text-center py-4">Tidak ada data penjual ditemukan.</td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3 d-flex justify-content-between align-items-center">
                  @if($sellers)
                  <div>
                    Menampilkan {{ $sellers->firstItem() }} sampai {{ $sellers->lastItem() }}
                    dari {{ $sellers->total() }} penjual
                  </div>
                  <nav aria-label="Halaman penjual">
                    <ul class="pagination mb-0">
                      @if ($sellers->onFirstPage())
                        <li class="page-item disabled">
                          <span class="page-link">&laquo;&laquo; Sebelumnya</span>
                        </li>
                      @else
                        <li class="page-item">
                          <a class="page-link" href="{{ $sellers->previousPageUrl() }}&tab=sellers" aria-label="Previous">
                            <span aria-hidden="true">&laquo;&laquo; Sebelumnya</span>
                          </a>
                        </li>
                      @endif

                      @for ($i = max(1, $sellers->currentPage() - 2); $i <= min($sellers->lastPage(), $sellers->currentPage() + 2); $i++)
                        @if ($i == $sellers->currentPage())
                          <li class="page-item active"><span class="page-link" style="color: white;">{{ $i }}</span></li>
                        @else
                          <li class="page-item"><a class="page-link" href="{{ $sellers->url($i) }}&tab=sellers">{{ $i }}</a></li>
                        @endif
                      @endfor

                      @if ($sellers->hasMorePages())
                        <li class="page-item">
                          <a class="page-link" href="{{ $sellers->nextPageUrl() }}&tab=sellers" aria-label="Next">
                            <span aria-hidden="true">Berikutnya &raquo;&raquo;</span>
                          </a>
                        </li>
                      @else
                        <li class="page-item disabled">
                          <span class="page-link">Berikutnya &raquo;&raquo;</span>
                        </li>
                      @endif
                    </ul>
                  </nav>
                  @else
                  <div>Tidak ada data penjual</div>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <!-- Buyers Tab -->
          <div class="tab-pane fade {{ (isset($tab) && $tab === 'buyers') ? 'show active' : '' }}" id="buyers-tab-pane" role="tabpanel">
            <div class="mt-3">
              <!-- Bulk Actions Toolbar for Buyers -->
              <div class="bulk-actions-toolbar mb-3">
                <form id="bulkActionFormBuyer" method="POST" action="{{ route('sellers.bulk_action') }}">
                  @csrf
                  <input type="hidden" name="user_type" value="buyer">
                  <div class="row align-items-center">
                    <div class="col-md-2">
                      <input type="checkbox" id="selectAllBuyers" class="form-check-input">
                      <label for="selectAllBuyers" class="form-check-label ms-1">Pilih Semua</label>
                    </div>
                    <div class="col-md-3">
                      <select name="action" class="form-select" required>
                        <option value="">Pilih Aksi</option>
                        <option value="suspend">Tangguhkan</option>
                        <option value="activate">Aktifkan</option>
                        <option value="delete">Hapus</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin melakukan aksi ini pada pembeli terpilih?')">Terapkan</button>
                    </div>
                    <div class="col-md-5 text-end">
                      <a href="{{ route('sellers.export_buyers') }}" class="btn btn-primary">
                        <i class="fas fa-download"></i> Ekspor Data
                      </a>
                    </div>
                  </div>
                  <input type="hidden" name="user_ids" id="userIdsInput">
                </form>
              </div>

              <!-- Filter Panel for Buyers -->
              <div class="filter-panel">
                <div class="filter-header">
                  <h3 class="filter-title"><i class="fas fa-filter"></i> Filter dan Pencarian</h3>
                </div>
                <div class="filter-content">
                  <form id="filterFormBuyers" method="GET" action="{{ route('sellers.index') }}?tab=buyers">
                    <div class="row g-2">
                      <div class="col-md-5 mb-2">
                        <label for="buyer_search" class="form-label">Cari Pembeli</label>
                        <input type="text" class="form-control" id="buyer_search" name="search" value="{{ request('search') }}" placeholder="Nama Pembeli, Email">
                      </div>
                      <div class="col-md-2 mb-2">
                        <label for="buyer_status" class="form-label">Status</label>
                        <select class="form-select" id="buyer_status" name="status">
                          <option value="">Semua</option>
                          <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                          <option value="ditangguhkan" {{ request('status') === 'ditangguhkan' ? 'selected' : '' }}>Ditangguhkan</option>
                        </select>
                      </div>
                      <div class="col-md-2 mb-2">
                        <label for="join_date_from_buyer" class="form-label">Bergabung Dari</label>
                        <input type="date" class="form-control" id="join_date_from_buyer" name="join_date_from" value="{{ request('join_date_from') }}">
                      </div>
                      <div class="col-md-2 mb-2">
                        <label for="join_date_to_buyer" class="form-label">Bergabung Sampai</label>
                        <input type="date" class="form-control" id="join_date_to_buyer" name="join_date_to" value="{{ request('join_date_to') }}">
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Buyers Table -->
              <div class="table-container mt-2">
                <div class="table-header">
                  <h3 class="table-title">Daftar Pembeli</h3>
                </div>
                <div class="table-content">
                  <div class="table-responsive">
                  <table class="table table-striped table-hover">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" id="selectAllBuyersHeader" class="form-check-input">
                        </th>
                        <th scope="col">Info Pengguna</th>
                        <th scope="col">Statistik</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($buyers ?? collect() as $buyer)
                      <tr id="user-row-{{ $buyer->id }}">
                        <td>
                          <input type="checkbox" class="form-check-input buyer-checkbox" value="{{ $buyer->id }}" name="user_ids[]">
                        </td>
                        <td>
                          <div><strong>{{ $buyer->name }}</strong></div>
                          <small class="text-muted">{{ $buyer->email }}</small><br>
                          <small class="text-muted">Bergabung: {{ $buyer->created_at ? $buyer->created_at->format('d M Y') : '-' }}</small>
                        </td>
                        <td>
                          @php
                            // Hitung total pembelian dari order pengguna
                            $totalOrders = isset($buyer->orders) ? $buyer->orders->count() : $buyer->orders()->count();
                            $totalSpending = isset($buyer->orders) ? $buyer->orders->sum('total_amount') : $buyer->orders()->sum('total_amount');
                          @endphp
                          <div>Transaksi: <strong>{{ $totalOrders }}</strong></div>
                          <div>Spending: <strong>Rp {{ number_format($totalSpending, 0, ',', '.') }}</strong></div>
                        </td>
                        <td>
                          @php
                            $statusClass = $buyer->status === 'suspended' ? 'bg-danger' : 'bg-success';
                            $statusText = $buyer->status === 'suspended' ? 'Ditangguhkan' : 'Aktif';
                          @endphp
                          <span class="badge rounded-pill {{ $statusClass }} d-block">{{ $statusText }}</span>
                        </td>
                        <td>
                          <div class="btn-group d-block" role="group">
                            <a href="{{ route('sellers.user_history', $buyer) }}" class="btn btn-info d-block text-center square-btn" title="Lihat Riwayat Transaksi">
                              <i class="fas fa-history"></i>
                            </a>
                            <a href="{{ route('sellers.edit_user', $buyer) }}" class="btn btn-primary d-block text-center square-btn" title="Edit Profil">
                              <i class="fas fa-edit"></i>
                            </a>
                            @if($buyer->status === 'suspended')
                              <form action="{{ route('sellers.activate_user', $buyer) }}" method="POST" class="d-inline activate-user-form">
                                @csrf
                                <button type="submit" class="btn btn-success d-block text-center square-btn" title="Aktifkan Kembali Akun">
                                  <i class="fas fa-play"></i>
                                </button>
                              </form>
                            @else
                              <form action="{{ route('sellers.suspend_user', $buyer) }}" method="POST" class="d-inline suspend-user-form">
                                @csrf
                                <button type="submit" class="btn btn-warning text-dark d-block text-center square-btn" title="Tangguhkan Akun">
                                  <i class="fas fa-pause"></i>
                                </button>
                              </form>
                            @endif
                          </div>
                        </td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="5" class="text-center py-4">Tidak ada data pembeli ditemukan.</td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                  </div>

                  <!-- Pagination for buyers -->
                  <div class="mt-3 d-flex justify-content-between align-items-center">
                    @if($buyers)
                    <div>
                      Menampilkan {{ $buyers->firstItem() }} sampai {{ $buyers->lastItem() }}
                      dari {{ $buyers->total() }} pembeli
                    </div>
                    <nav aria-label="Halaman pembeli">
                      <ul class="pagination mb-0">
                        @if ($buyers->onFirstPage())
                          <li class="page-item disabled">
                            <span class="page-link">&laquo;&laquo; Sebelumnya</span>
                          </li>
                        @else
                          <li class="page-item">
                            <a class="page-link" href="{{ $buyers->previousPageUrl() }}&tab=buyers" aria-label="Previous">
                              <span aria-hidden="true">&laquo;&laquo; Sebelumnya</span>
                            </a>
                          </li>
                        @endif

                        @for ($i = max(1, $buyers->currentPage() - 2); $i <= min($buyers->lastPage(), $buyers->currentPage() + 2); $i++)
                          @if ($i == $buyers->currentPage())
                            <li class="page-item active"><span class="page-link" style="color: white;">{{ $i }}</span></li>
                          @else
                            <li class="page-item"><a class="page-link" href="{{ $buyers->url($i) }}&tab=buyers">{{ $i }}</a></li>
                          @endif
                        @endfor

                        @if ($buyers->hasMorePages())
                          <li class="page-item">
                            <a class="page-link" href="{{ $buyers->nextPageUrl() }}&tab=buyers" aria-label="Next">
                              <span aria-hidden="true">Berikutnya &raquo;&raquo;</span>
                            </a>
                          </li>
                        @else
                          <li class="page-item disabled">
                            <span class="page-link">Berikutnya &raquo;&raquo;</span>
                          </li>
                        @endif
                      </ul>
                    </nav>
                    @else
                    <div>Tidak ada data pembeli</div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <!-- JavaScript for Select All functionality, Bulk Actions, and Auto-filtering -->
  <script>
  function submitForm() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);

    // Build URL with parameters
    let url = new URL(window.location.href);
    url.search = '';

    for (const [key, value] of formData.entries()) {
      if (value) {
        url.searchParams.append(key, value);
      }
    }

    // Ensure tab parameter is preserved
    url.searchParams.set('tab', 'sellers');

    // Navigate to the URL with parameters
    window.location.href = url.toString();
  }

  function clearFilters() {
    // Reset all filter form fields
    document.getElementById('search').value = '';
    document.getElementById('status').value = '';
    document.getElementById('join_date_from').value = '';
    document.getElementById('join_date_to').value = '';

    // Submit the form to refresh results
    submitForm();
  }

  function submitBuyerForm() {
    const form = document.getElementById('filterFormBuyers');
    const formData = new FormData(form);

    // Build URL with parameters
    let url = new URL(window.location.href);
    url.search = '';

    for (const [key, value] of formData.entries()) {
      if (value) {
        url.searchParams.append(key, value);
      }
    }

    // Ensure tab parameter is preserved
    url.searchParams.set('tab', 'buyers');

    // Navigate to the URL with parameters
    window.location.href = url.toString();
  }

  function clearBuyerFilters() {
    // Reset all filter form fields
    document.getElementById('buyer_search').value = '';
    document.getElementById('buyer_status').value = '';
    document.getElementById('join_date_from_buyer').value = '';
    document.getElementById('join_date_to_buyer').value = '';

    // Submit the form to refresh results
    submitBuyerForm();
  }

  // Auto-filter functionality for sellers
  function updateSellerFilters() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);

    // Build URL with parameters
    let url = new URL(window.location.href);
    url.search = '';

    for (const [key, value] of formData.entries()) {
      if (value) {
        url.searchParams.append(key, value);
      }
    }

    // Ensure tab parameter is preserved
    url.searchParams.set('tab', 'sellers');

    // Navigate to the URL with parameters
    window.location.href = url.toString();
  }

  // Auto-filter functionality for buyers
  function updateBuyerFilters() {
    const form = document.getElementById('filterFormBuyers');
    const formData = new FormData(form);

    // Build URL with parameters
    let url = new URL(window.location.href);
    url.search = '';

    for (const [key, value] of formData.entries()) {
      if (value) {
        url.searchParams.append(key, value);
      }
    }

    // Ensure tab parameter is preserved
    url.searchParams.set('tab', 'buyers');

    // Navigate to the URL with parameters
    window.location.href = url.toString();
  }

  // Add event listeners to filter elements for automatic filtering
  document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality for sellers
    const selectAll = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const sellerCheckboxes = document.querySelectorAll('.seller-checkbox');

  // Handle tab switching to update URL and ensure correct tab is active
  document.addEventListener('DOMContentLoaded', function() {
      // Add click event listeners to the tab buttons to update URL
      const buyersTab = document.getElementById('buyers-tab');
      const sellersTab = document.getElementById('sellers-tab');

      if (buyersTab) {
          buyersTab.addEventListener('click', function(e) {
              // Update URL to include tab=buyers parameter
              const newUrl = new URL(window.location);
              newUrl.searchParams.set('tab', 'buyers');
              window.history.pushState({}, '', newUrl);
          });
      }

      if (sellersTab) {
          sellersTab.addEventListener('click', function(e) {
              // Update URL to include tab=sellers parameter (or remove tab param for default)
              const newUrl = new URL(window.location);
              newUrl.searchParams.set('tab', 'sellers');
              window.history.pushState({}, '', newUrl);
          });
      }

      // Handle browser back/forward buttons
      window.addEventListener('popstate', function(e) {
          const urlParams = new URLSearchParams(window.location.search);
          const tab = urlParams.get('tab');

          if (tab === 'buyers') {
              const buyersTabEl = document.getElementById('buyers-tab');
              if (buyersTabEl) {
                  bootstrap.Tab.getInstance(buyersTabEl)?.show() || new bootstrap.Tab(buyersTabEl).show();
              }
          } else {
              const sellersTabEl = document.getElementById('sellers-tab');
              if (sellersTabEl) {
                  bootstrap.Tab.getInstance(sellersTabEl)?.show() || new bootstrap.Tab(sellersTabEl).show();
              }
          }
      });
  });

    // Add event listeners to seller filter elements for automatic filtering
    const sellerSearch = document.getElementById('search');
    const sellerStatus = document.getElementById('status');
    const sellerJoinDateFrom = document.getElementById('join_date_from');
    const sellerJoinDateTo = document.getElementById('join_date_to');

    if (sellerSearch) {
      sellerSearch.addEventListener('input', function() {
        // Use a debounce to avoid too many requests while typing
        clearTimeout(this.debounceTimeout);
        this.debounceTimeout = setTimeout(() => {
          updateSellerFilters();
        }, 300);
      });
    }

    if (sellerStatus) {
      sellerStatus.addEventListener('change', updateSellerFilters);
    }

    if (sellerJoinDateFrom) {
      sellerJoinDateFrom.addEventListener('change', updateSellerFilters);
    }

    if (sellerJoinDateTo) {
      sellerJoinDateTo.addEventListener('change', updateSellerFilters);
    }

    // Add event listeners to buyer filter elements for automatic filtering
    const buyerSearch = document.getElementById('buyer_search');
    const buyerStatus = document.getElementById('buyer_status');
    const buyerJoinDateFrom = document.getElementById('join_date_from_buyer');
    const buyerJoinDateTo = document.getElementById('join_date_to_buyer');

    if (buyerSearch) {
      buyerSearch.addEventListener('input', function() {
        // Use a debounce to avoid too many requests while typing
        clearTimeout(this.debounceTimeout);
        this.debounceTimeout = setTimeout(() => {
          updateBuyerFilters();
        }, 300);
      });
    }

    if (buyerStatus) {
      buyerStatus.addEventListener('change', updateBuyerFilters);
    }

    if (buyerJoinDateFrom) {
      buyerJoinDateFrom.addEventListener('change', updateBuyerFilters);
    }

    if (buyerJoinDateTo) {
      buyerJoinDateTo.addEventListener('change', updateBuyerFilters);
    }

    // Select all functionality for sellers
    selectAll && selectAll.addEventListener('change', function() {
      sellerCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
    });

    selectAllHeader && selectAllHeader.addEventListener('change', function() {
      sellerCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      selectAll.checked = this.checked;
    });

    // Select all functionality for buyers
    const selectAllBuyers = document.getElementById('selectAllBuyers');
    const selectAllBuyersHeader = document.getElementById('selectAllBuyersHeader');
    const buyerCheckboxes = document.querySelectorAll('.buyer-checkbox');

    selectAllBuyers && selectAllBuyers.addEventListener('change', function() {
      buyerCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
    });

    selectAllBuyersHeader && selectAllBuyersHeader.addEventListener('change', function() {
      buyerCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      selectAllBuyers.checked = this.checked;
    });

    // Custom notification function
    function showNotification(message, type = 'info') {
      // Remove any existing notifications
      const existingNotification = document.querySelector('.custom-notification');
      if (existingNotification) {
        existingNotification.remove();
      }

      // Determine icon based on type
      let icon = 'ℹ️';
      if (type === 'success') {
        icon = '✅';
      } else if (type === 'error') {
        icon = '❌';
      } else if (type === 'warning') {
        icon = '⚠️';
      } else if (type === 'info') {
        icon = 'ℹ️';
      }

      // Create notification container
      const notification = document.createElement('div');
      notification.className = 'custom-notification';
      notification.innerHTML =
        '<div class="alert alert-' + (type === 'error' ? 'danger' : type) + ' alert-dismissible fade show" role="alert">' +
          '<div class="alert-content">' +
            '<span class="alert-icon">' + icon + '</span>' +
            '<span class="alert-message">' + message + '</span>' +
          '</div>' +
          '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
        '</div>';

      // Add to document
      document.body.appendChild(notification);

      // Trigger animation
      setTimeout(() => {
        notification.classList.add('show');
      }, 10);

      // Auto remove after 5 seconds
      setTimeout(() => {
        const alertElement = notification.querySelector('.alert');
        if (alertElement) {
          alertElement.classList.remove('show');
          setTimeout(() => {
            if (notification.parentNode) {
              notification.parentNode.removeChild(notification);
            }
          }, 150);
        }
      }, 5000);
    }

    // Custom confirm function
    function showConfirm(message, onConfirm) {
      // Remove any existing confirm modals
      const existingModal = document.querySelector('.custom-confirm-modal');
      if (existingModal) {
        existingModal.remove();
      }

      // Create modal backdrop
      const backdrop = document.createElement('div');
      backdrop.className = 'modal-backdrop fade';
      backdrop.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1050;';

      // Create modal container
      const modal = document.createElement('div');
      modal.className = 'custom-confirm-modal';
      modal.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1051;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        padding: 1.5rem;
        min-width: 300px;
        max-width: 400px;
      `;

      modal.innerHTML = `
        <div class="confirm-content">
          <div class="confirm-message" style="margin-bottom: 1rem;">${message}</div>
          <div class="confirm-buttons" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
            <button class="btn btn-secondary btn-sm cancel-btn">Batal</button>
            <button class="btn btn-danger btn-sm confirm-btn confirm-danger">Ya, Lanjutkan</button>
          </div>
        </div>
      `;

      // Add to document
      document.body.appendChild(backdrop);
      document.body.appendChild(modal);

      // Add event listeners
      const cancelBtn = modal.querySelector('.cancel-btn');
      const confirmBtn = modal.querySelector('.confirm-btn');

      const closeConfirm = () => {
        backdrop.classList.add('fade');
        modal.classList.add('fade');
        setTimeout(() => {
          if (backdrop.parentNode) backdrop.parentNode.removeChild(backdrop);
          if (modal.parentNode) modal.parentNode.removeChild(modal);
        }, 150);
      };

      cancelBtn.addEventListener('click', closeConfirm);
      backdrop.addEventListener('click', closeConfirm);

      confirmBtn.addEventListener('click', () => {
        closeConfirm();
        onConfirm();
      });
    }

    // Bulk actions for sellers
    const bulkActionForm = document.getElementById('bulkActionForm');
    if (bulkActionForm) {
      bulkActionForm.addEventListener('submit', function(e) {
        const selectedSellers = Array.from(sellerCheckboxes).filter(checkbox => checkbox.checked).map(checkbox => checkbox.value);
        if (selectedSellers.length === 0) {
          e.preventDefault();
          showNotification('Silakan pilih setidaknya satu penjual terlebih dahulu.', 'warning');
          return;
        }
        document.getElementById('sellerIdsInput').value = JSON.stringify(selectedSellers);
      });
    }

    // Bulk actions for buyers
    const bulkActionFormBuyer = document.getElementById('bulkActionFormBuyer');
    if (bulkActionFormBuyer) {
      bulkActionFormBuyer.addEventListener('submit', function(e) {
        const selectedUsers = Array.from(buyerCheckboxes).filter(checkbox => checkbox.checked).map(checkbox => checkbox.value);
        if (selectedUsers.length === 0) {
          e.preventDefault();
          showNotification('Silakan pilih setidaknya satu pembeli terlebih dahulu.', 'warning');
          return;
        }
        document.getElementById('userIdsInput').value = JSON.stringify(selectedUsers);
      });
    }

    // Event listeners for confirmation
    document.addEventListener('click', function(e) {
      // Handle bulk action forms
      if (e.target.closest('#bulkActionForm button[type="submit"]')) {
        e.preventDefault();
        const form = e.target.closest('#bulkActionForm');
        showConfirm('Apakah Anda yakin ingin melakukan aksi ini pada penjual terpilih?', function() {
          form.submit();
        });
      } else if (e.target.closest('#bulkActionFormBuyer button[type="submit"]')) {
        e.preventDefault();
        const form = e.target.closest('#bulkActionFormBuyer');
        showConfirm('Apakah Anda yakin ingin melakukan aksi ini pada pembeli terpilih?', function() {
          form.submit();
        });
      }
    });

    // Handle individual action forms
    document.addEventListener('submit', function(e) {
      // Handle suspend seller form
      if (e.target.classList.contains('suspend-seller-form')) {
        e.preventDefault();
        showConfirm('Apakah Anda yakin ingin menangguhkan penjual ini?', function() {
          e.target.submit();
        });
      }
      // Handle activate seller form
      else if (e.target.classList.contains('activate-seller-form')) {
        e.preventDefault();
        showConfirm('Apakah Anda yakin ingin mengaktifkan kembali penjual ini?', function() {
          e.target.submit();
        });
      }
      // Handle delete seller form
      else if (e.target.classList.contains('delete-seller-form')) {
        e.preventDefault();
        showConfirm('Apakah Anda yakin ingin menghapus penjual ini?', function() {
          e.target.submit();
        });
      }
      // Handle suspend user form
      else if (e.target.classList.contains('suspend-user-form')) {
        e.preventDefault();
        showConfirm('Apakah Anda yakin ingin menangguhkan pembeli ini?', function() {
          e.target.submit();
        });
      }
      // Handle activate user form
      else if (e.target.classList.contains('activate-user-form')) {
        e.preventDefault();
        showConfirm('Apakah Anda yakin ingin mengaktifkan kembali pembeli ini?', function() {
          e.target.submit();
        });
      }
    });
  });


  </script>

@endsection
