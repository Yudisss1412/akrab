@extends('layouts.admin')

@section('title', 'Laporan Pelanggaran - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <style>
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .filters {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
      align-items: flex-end;
    }

    .filter-group {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      min-width: 150px;
    }

    .filter-group label {
      font-size: 0.9rem;
      color: var(--muted);
    }

    .filter-group input,
    .filter-group select {
      padding: 0.5rem;
      border: 1px solid var(--border);
      border-radius: 6px;
      background: white;
    }

    .table-container {
      background: white;
      border-radius: var(--ak-radius);
      border: 1px solid var(--ak-border);
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,.05);
    }

    .status-badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 1rem;
      font-size: 0.75rem;
      font-weight: 500;
    }

    .status-pending {
      background: rgba(245, 158, 11, 0.1);
      color: #d97706;
    }

    .status-investigating {
      background: rgba(59, 130, 246, 0.1);
      color: #2563eb;
    }

    .status-resolved {
      background: rgba(34, 197, 94, 0.1);
      color: #16a34a;
    }

    .status-dismissed {
      background: rgba(239, 68, 68, 0.1);
      color: #dc2626;
    }

    .pagination {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1.5rem;
    }

    .pagination-info {
      color: var(--ak-muted);
      font-size: 0.875rem;
    }

    .pagination-nav {
      display: flex;
      gap: 0.5rem;
    }

    .pagination-btn {
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--ak-border);
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      font-size: 0.875rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .pagination-btn:hover:not(:disabled) {
      background: var(--ak-primary);
      color: white;
      border-color: var(--ak-primary);
    }

    .pagination-btn.active {
      background: var(--ak-primary);
      color: white;
      border-color: var(--ak-primary);
    }

    .pagination-btn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 0.75rem 1rem;
      text-align: left;
      border-bottom: 1px solid var(--ak-border);
    }

    th {
      background: var(--bg);
      font-weight: 600;
      color: var(--text);
      position: sticky;
      top: 0;
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:hover {
      background-color: var(--bg);
    }

    .sort-indicator {
      display: inline-block;
      margin-left: 0.25rem;
      font-size: 0.8rem;
      opacity: 0.5;
    }

    .sort-asc .sort-indicator::after {
      content: " ↑";
      opacity: 1;
    }

    .sort-desc .sort-indicator::after {
      content: " ↓";
      opacity: 1;
    }

    .status-badge {
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: 500;
    }

    .status-pending {
      background-color: #fffbeb;
      color: #f59e0b;
    }

    .status-verified {
      background-color: #ecfdf5;
      color: #10b981;
    }

    .status-resolved {
      background-color: #eff6ff;
      color: #3b82f6;
    }

    .status-rejected {
      background-color: #fef2f2;
      color: #ef4444;
    }

    .pagination {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1.5rem;
    }

    .pagination-info {
      color: var(--muted);
      font-size: 0.9rem;
    }

    .pagination-controls {
      display: flex;
      gap: 0.5rem;
    }

    .page-btn {
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: var(--white);
      cursor: pointer;
      font-size: 0.9rem;
    }

    .page-btn.active {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
    }

    .page-btn:disabled {
      opacity: 0.4;
      cursor: not-allowed;
    }

    .page-btn:hover:not(:disabled):not(.active) {
      background-color: var(--bg);
    }

    .action-buttons {
      display: flex;
      gap: 0.5rem;
    }

    .btn-action {
      padding: 0.25rem 0.5rem;
      border: 1px solid var(--border);
      border-radius: 4px;
      background: white;
      cursor: pointer;
      font-size: 0.8rem;
    }

    .btn-view {
      color: var(--primary);
    }

    .btn-resolve {
      color: #10b981;
    }

    .btn-reject {
      color: #ef4444;
    }

    .modern-date-input {
      padding: 0.5rem 0.75rem;
      border: 1px solid var(--border);
      border-radius: 8px;
      background: white;
      font-size: 0.9rem;
      color: var(--text);
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
      cursor: pointer;
    }

    .modern-date-input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 110, 92, 0.1);
    }

    /* Modern date picker styling */
    .modern-date-input::-webkit-calendar-picker-indicator {
      cursor: pointer;
      padding: 4px;
      border-radius: 4px;
      transition: background-color 0.2s ease;
    }

    .modern-date-input::-webkit-calendar-picker-indicator:hover {
      background-color: var(--bg);
    }

    /* Styling for the date picker dropdown */
    .modern-date-input::-webkit-datetime-edit {
      padding: 2px 4px;
    }

    /* Custom date picker appearance (where supported) */
    .modern-date-input::-webkit-inner-spin-button,
    .modern-date-input::-webkit-calendar-picker-indicator {
      -webkit-appearance: none;
      appearance: none;
    }

    /* Fallback for Firefox */
    .modern-date-input {
      -webkit-appearance: none;
      -moz-appearance: textfield;
    }
    /* CSS untuk ukuran modal di mobile dan tablet */
    @media (max-width: 768px) {
      .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
      }

      .modal-content {
        border-radius: 12px;
      }

      .modal-body {
        padding: 1rem;
      }

      /* Penyesuaian untuk tabel di mobile */
      .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      table {
        min-width: 600px; /* Membuat tabel bisa di-scroll horizontal */
      }

      /* Penyesuaian ukuran elemen di mobile */
      .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
      }

      .btn {
        font-size: 0.875rem;
      }

      .active-filters-info {
        flex: 1;
        overflow-x: auto;
        white-space: nowrap;
      }

      th, td {
        padding: 0.5rem;
        font-size: 0.85rem;
      }
    }

    @media (max-width: 576px) {
      .modal-dialog {
        margin: 0.25rem;
        max-width: calc(100% - 0.5rem);
      }

      .modal-body {
        padding: 0.75rem;
      }

      th, td {
        padding: 0.4rem;
        font-size: 0.8rem;
      }
    }

    /* Penyesuaian posisi modal agar lebih ke atas di mobile */
    @media (max-width: 768px) {
      .modal-dialog {
        margin: 1rem auto 0; /* Memberikan margin atas dan menghilangkan margin bawah */
        max-height: 85vh; /* Membatasi tinggi maksimal modal */
      }
    }

    /* Card view untuk mobile dan tablet */
    @media (max-width: 991.98px) {
      .card-view-container {
        padding: 0.5rem;
      }

      .card {
        border: 1px solid var(--ak-border);
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      }

      .card-body {
        padding: 1rem;
      }

      .row > div {
        padding-bottom: 0.25rem;
      }

      .status-badge {
        font-size: 0.75rem;
      }

      .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
      }
    }

    /* Tabel untuk desktop */
    @media (min-width: 992px) {
      .d-lg-table {
        width: 100%;
        border-collapse: collapse;
      }

      .d-lg-table th, .d-lg-table td {
        padding: 0.75rem 1rem;
      }
    }

    /* Pagination untuk mobile */
    @media (max-width: 991.98px) {
      .pagination {
        padding: 0 0.5rem;
      }

      .pagination-controls {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding: 0.25rem 0;
        justify-content: flex-start !important;
      }

      .page-btn {
        min-width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
        font-size: 0.875rem;
        margin: 0 1px;
      }

      .pagination-info {
        width: 100%;
        text-align: center;
      }
    }

    /* Penyesuaian untuk tampilan desktop - memperbaiki lebar modal agar tidak seperti mobile */
    @media (min-width: 992px) {
      .modal-dialog {
        max-width: 500px; /* Ukuran yang lebih proporsional untuk desktop */
        margin: 1.75rem auto;
      }
    }

    /* Penyesuaian untuk tampilan tablet */
    @media (min-width: 769px) and (max-width: 991px) {
      .modal-dialog {
        max-width: 600px;
        margin: 2rem auto;
      }
    }

    /* Penyesuaian untuk tampilan mobile */
    @media (max-width: 768px) {
      .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
      }
    }
  </style>
@endpush

@section('content')
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content">
        <div class="page-header">
          <h1>Laporan Pelanggaran</h1>
          <button class="btn btn-primary">Ekspor Laporan</button>
        </div>

        <div class="filters">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="active-filters-info">
              @if(request('search') || request('start_date') || request('end_date') || request('violation_type') || request('status'))
                <small class="text-muted">
                  Filter aktif:
                  @if(request('search')) <span class="badge bg-primary">Cari: {{ request('search') }}</span> @endif
                  @if(request('start_date')) <span class="badge bg-primary">Tgl Awal: {{ request('start_date') }}</span> @endif
                  @if(request('end_date')) <span class="badge bg-primary">Tgl Akhir: {{ request('end_date') }}</span> @endif
                  @if(request('violation_type'))
                    @php
                      $violationTypes = ['product' => 'Produk Palsu', 'content' => 'Konten Tidak Pantas', 'scam' => 'Penipuan', 'copyright' => 'Hak Cipta', 'other' => 'Lainnya'];
                    @endphp
                    <span class="badge bg-primary">Jenis: {{ $violationTypes[request('violation_type')] ?? request('violation_type') }}</span>
                  @endif
                  @if(request('status'))
                    @php
                      $statusTypes = ['pending' => 'Pending', 'investigating' => 'Ditinjau', 'resolved' => 'Diselesaikan', 'dismissed' => 'Ditolak'];
                    @endphp
                    <span class="badge bg-primary">Status: {{ $statusTypes[request('status')] ?? request('status') }}</span>
                  @endif
                </small>
              @endif
            </div>
            <button class="btn btn-outline-primary btn-sm d-lg-none" id="toggleFilterBtn" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
              <i class="fas fa-filter"></i> Filter
            </button>
          </div>

          <!-- Form filter yang langsung tampil di desktop -->
          <div class="filter-form-container d-none d-lg-block">
            <form id="filterForm" method="GET" action="{{ route('reports.violations.filter') }}">
              <div class="row g-2"> <!-- Mengurangi gap antar elemen -->
                <div class="col-md-2 mb-2">
                  <label for="searchFilter" class="form-label small">Cari</label>
                  <input type="text" name="search" id="searchFilter" class="form-control form-control-sm" placeholder="Cari..." value="{{ request('search', '') }}">
                </div>

                <div class="col-md-2 mb-2">
                  <label for="startDateFilter" class="form-label small">Tgl Awal</label>
                  <input type="date" name="start_date" id="startDateFilter" class="form-control form-control-sm" value="{{ request('start_date', '') }}">
                </div>

                <div class="col-md-2 mb-2">
                  <label for="endDateFilter" class="form-label small">Tgl Akhir</label>
                  <input type="date" name="end_date" id="endDateFilter" class="form-control form-control-sm" value="{{ request('end_date', '') }}">
                </div>

                <div class="col-md-2 mb-2">
                  <label for="violationTypeFilter" class="form-label small">Jenis Pelanggaran</label>
                  <select name="violation_type" id="violationTypeFilter" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    <option value="product" {{ request('violation_type') === 'product' ? 'selected' : '' }}>Produk Palsu</option>
                    <option value="content" {{ request('violation_type') === 'content' ? 'selected' : '' }}>Konten Tidak Pantas</option>
                    <option value="scam" {{ request('violation_type') === 'scam' ? 'selected' : '' }}>Penipuan</option>
                    <option value="copyright" {{ request('violation_type') === 'copyright' ? 'selected' : '' }}>Pelanggaran Hak Cipta</option>
                    <option value="other" {{ request('violation_type') === 'other' ? 'selected' : '' }}>Lainnya</option>
                  </select>
                </div>

                <div class="col-md-2 mb-2">
                  <label for="statusFilter" class="form-label small">Status</label>
                  <select name="status" id="statusFilter" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>Sedang Ditinjau</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Diselesaikan</option>
                    <option value="dismissed" {{ request('status') === 'dismissed' ? 'selected' : '' }}>Ditolak</option>
                  </select>
                </div>

                <div class="col-md-2 mb-2">
                  <label class="form-label small">&nbsp;</label> <!-- Untuk align kolom -->
                  <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Terapkan</button>
                    <a href="{{ route('reports.violations') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">Hapus</a>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <!-- Modal untuk filter (hanya muncul di mobile) -->
          <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="filterModalLabel">Filter Laporan Pelanggaran</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form id="mobileFilterForm" method="GET" action="{{ route('reports.violations.filter') }}">
                    <div class="row g-2">
                      <div class="col-12 mb-2">
                        <label for="mobileSearchFilter" class="form-label small">Cari</label>
                        <input type="text" name="search" id="mobileSearchFilter" class="form-control form-control-sm" placeholder="Cari di Akrab..." value="{{ request('search', '') }}">
                      </div>

                      <div class="col-12 mb-2">
                        <label for="mobileStartDateFilter" class="form-label small">Tanggal Awal</label>
                        <input type="date" name="start_date" id="mobileStartDateFilter" class="form-control form-control-sm" value="{{ request('start_date', '') }}">
                      </div>

                      <div class="col-12 mb-2">
                        <label for="mobileEndDateFilter" class="form-label small">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="mobileEndDateFilter" class="form-control form-control-sm" value="{{ request('end_date', '') }}">
                      </div>

                      <div class="col-12 mb-2">
                        <label for="mobileViolationTypeFilter" class="form-label small">Jenis Pelanggaran</label>
                        <select name="violation_type" id="mobileViolationTypeFilter" class="form-select form-select-sm">
                          <option value="">Semua Jenis</option>
                          <option value="product" {{ request('violation_type') === 'product' ? 'selected' : '' }}>Produk Palsu</option>
                          <option value="content" {{ request('violation_type') === 'content' ? 'selected' : '' }}>Konten Tidak Pantas</option>
                          <option value="scam" {{ request('violation_type') === 'scam' ? 'selected' : '' }}>Penipuan</option>
                          <option value="copyright" {{ request('violation_type') === 'copyright' ? 'selected' : '' }}>Pelanggaran Hak Cipta</option>
                          <option value="other" {{ request('violation_type') === 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                      </div>

                      <div class="col-12 mb-2">
                        <label for="mobileStatusFilter" class="form-label small">Status</label>
                        <select name="status" id="mobileStatusFilter" class="form-select form-select-sm">
                          <option value="">Semua Status</option>
                          <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                          <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>Sedang Ditinjau</option>
                          <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Diselesaikan</option>
                          <option value="dismissed" {{ request('status') === 'dismissed' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                      </div>

                      <div class="col-12">
                        <div class="d-grid gap-2 d-md-flex">
                          <button type="submit" class="btn btn-primary btn-sm flex-md-grow-1">Terapkan Filter</button>
                          <a href="{{ route('reports.violations') }}" class="btn btn-outline-secondary btn-sm flex-md-grow-1">Hapus Filter</a>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="table-container">
          <!-- Tabel normal untuk desktop -->
          <table id="violationsTable" class="d-none d-lg-table">
            <thead>
              <tr>
                <th class="sortable" data-column="date">Tanggal <span class="sort-indicator"></span></th>
                <th class="sortable" data-column="violationType">Jenis Pelanggaran <span class="sort-indicator"></span></th>
                <th class="sortable" data-column="seller">Nama Penjual <span class="sort-indicator"></span></th>
                <th>Produk Terkait</th>
                <th>Pelapor</th>
                <th class="sortable" data-column="status">Status <span class="sort-indicator"></span></th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($reports as $report)
              <tr>
                <td>{{ $report->created_at->format('Y-m-d') }}</td>
                <td>
                  @if($report->violation_type === 'product')
                    Produk Palsu
                  @elseif($report->violation_type === 'content')
                    Konten Tidak Pantas
                  @elseif($report->violation_type === 'scam')
                    Penipuan
                  @elseif($report->violation_type === 'copyright')
                    Pelanggaran Hak Cipta
                  @elseif($report->violation_type === 'other')
                    Lainnya
                  @else
                    {{ ucfirst(str_replace('_', ' ', $report->violation_type)) }}
                  @endif
                </td>
                <td>{{ $report->violator->name ?? 'Penjual Tidak Ditemukan' }}</td>
                <td>{{ $report->product->name ?? 'Tidak Ada Produk Terkait' }}</td>
                <td>{{ $report->reporter->name ?? 'Pelapor Tidak Ditemukan' }}</td>
                <td>
                  <span class="status-badge
                    @if($report->status === 'pending') status-pending
                    @elseif($report->status === 'investigating') status-investigating
                    @elseif($report->status === 'resolved') status-resolved
                    @elseif($report->status === 'dismissed') status-dismissed
                    @else status-pending @endif">
                    {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                  </span>
                </td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', $report->id) }}" class="btn-action btn-view">Lihat</a>
                  @if($report->status === 'pending' || $report->status === 'investigating')
                  <button class="btn-action btn-resolve" onclick="updateReportStatus({{ $report->id }})">Selesaikan</button>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" style="text-align: center; padding: 2rem;">Tidak ada laporan pelanggaran ditemukan</td>
              </tr>
              @endforelse
            </tbody>
          </table>

          <!-- Card view untuk mobile dan tablet -->
          <div class="card-view-container d-lg-none">
            @forelse($reports as $report)
            <div class="card mb-3">
              <div class="card-body">
                <div class="row mb-2">
                  <div class="col-4"><strong>Tanggal:</strong></div>
                  <div class="col-8">{{ $report->created_at->format('Y-m-d') }}</div>
                </div>
                <div class="row mb-2">
                  <div class="col-4"><strong>Jenis:</strong></div>
                  <div class="col-8">
                    @if($report->violation_type === 'product')
                      Produk Palsu
                    @elseif($report->violation_type === 'content')
                      Konten Tidak Pantas
                    @elseif($report->violation_type === 'scam')
                      Penipuan
                    @elseif($report->violation_type === 'copyright')
                      Pelanggaran Hak Cipta
                    @elseif($report->violation_type === 'other')
                      Lainnya
                    @else
                      {{ ucfirst(str_replace('_', ' ', $report->violation_type)) }}
                    @endif
                  </div>
                </div>
                <div class="row mb-2">
                  <div class="col-4"><strong>Penjual:</strong></div>
                  <div class="col-8">{{ $report->violator->name ?? 'Penjual Tidak Ditemukan' }}</div>
                </div>
                <div class="row mb-2">
                  <div class="col-4"><strong>Produk:</strong></div>
                  <div class="col-8">{{ $report->product->name ?? 'Tidak Ada Produk Terkait' }}</div>
                </div>
                <div class="row mb-2">
                  <div class="col-4"><strong>Pelapor:</strong></div>
                  <div class="col-8">{{ $report->reporter->name ?? 'Pelapor Tidak Ditemukan' }}</div>
                </div>
                <div class="row mb-3">
                  <div class="col-4"><strong>Status:</strong></div>
                  <div class="col-8">
                    <span class="status-badge
                      @if($report->status === 'pending') status-pending
                      @elseif($report->status === 'investigating') status-investigating
                      @elseif($report->status === 'resolved') status-resolved
                      @elseif($report->status === 'dismissed') status-dismissed
                      @else status-pending @endif">
                      {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                    </span>
                  </div>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                  <a href="{{ route('reports.violations.detail', $report->id) }}" class="btn btn-outline-primary btn-sm">Lihat</a>
                  @if($report->status === 'pending' || $report->status === 'investigating')
                  <button class="btn btn-success btn-sm" onclick="updateReportStatus({{ $report->id }})">Selesaikan</button>
                  @endif
                </div>
              </div>
            </div>
            @empty
            <div class="text-center p-4">
              <p>Tidak ada laporan pelanggaran ditemukan</p>
            </div>
            @endforelse
          </div>
        </div>

        <div class="pagination mt-4">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="pagination-info text-center text-md-start" id="paginationInfo">
              @if($reports)
                Menampilkan {{ $reports->firstItem() }}-{{ $reports->lastItem() }} dari {{ $reports->total() }} laporan
              @else
                Menampilkan 0-0 dari 0 laporan
              @endif
            </div>
            <div class="pagination-controls d-flex flex-wrap justify-content-center gap-1">
              @if($reports)
                @if($reports->onFirstPage())
                  <button class="page-btn" id="firstBtn" disabled>&lt;&lt;</button>
                  <button class="page-btn" id="prevBtn" disabled>&lt;</button>
                @else
                  <a href="{{ $reports->url(1) }}" class="page-btn" id="firstBtn">&lt;&lt;</a>
                  <a href="{{ $reports->previousPageUrl() }}" class="page-btn" id="prevBtn">&lt;</a>
                @endif

                @for($i = max(1, $reports->currentPage() - 2); $i <= min($reports->lastPage(), $reports->currentPage() + 2); $i++)
                  <a href="{{ $reports->url($i) }}" class="page-btn {{ $i == $reports->currentPage() ? 'active' : '' }}" data-page="{{ $i }}">{{ $i }}</a>
                @endfor

                @if($reports->hasMorePages())
                  <a href="{{ $reports->nextPageUrl() }}" class="page-btn" id="nextBtn">&gt;</a>
                  <a href="{{ $reports->url($reports->lastPage()) }}" class="page-btn" id="lastBtn">&gt;&gt;</a>
                @else
                  <button class="page-btn" id="nextBtn" disabled>&gt;</button>
                  <button class="page-btn" id="lastBtn" disabled>&gt;&gt;</button>
                @endif
              @endif
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal untuk Ubah Status Laporan -->
  <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="statusModalLabel">Ubah Status Laporan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="newStatus" class="form-label">Pilih Status Baru</label>
            <select class="form-select" id="newStatus">
              <option value="pending">Pending</option>
              <option value="investigating">Sedang Ditinjau</option>
              <option value="resolved" selected>Diselesaikan</option>
              <option value="dismissed">Ditolak</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="adminNotes" class="form-label">Catatan Admin (Opsional)</label>
            <textarea class="form-control" id="adminNotes" rows="3" placeholder="Tambahkan catatan tambahan..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" id="confirmStatusBtn">Ubah Status</button>
        </div>
      </div>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <script>
    let currentReportId = null; // Variable to store the current report ID

    // Function to update report status
    function updateReportStatus(reportId) {
      currentReportId = reportId;
      // Show the modal
      const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
      statusModal.show();
    }

    // Handle confirmation
    document.getElementById('confirmStatusBtn').addEventListener('click', async function() {
      const newStatus = document.getElementById('newStatus').value;
      const adminNotes = document.getElementById('adminNotes').value;

      if (currentReportId && newStatus) {
        try {
          const response = await fetch(`/reports/violations/${currentReportId}/status`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              status: newStatus,
              admin_notes: adminNotes || 'Laporan diproses oleh admin'
            })
          });

          const data = await response.json();

          if (data.success) {
            // Close the modal
            const statusModal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
            statusModal.hide();

            alert('Status laporan berhasil diperbarui');
            // Reload the page to show updated status
            location.reload();
          } else {
            alert('Gagal memperbarui status laporan: ' + (data.message || 'Unknown error'));
          }
        } catch (error) {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat memperbarui status laporan');
        }
      }
    });

    // Update pagination links to include current filter parameters
    document.addEventListener('DOMContentLoaded', function() {
      const params = new URLSearchParams(window.location.search);
      if (params.toString()) {
        const paginationLinks = document.querySelectorAll('.pagination-controls a.page-btn');
        paginationLinks.forEach(link => {
          const linkUrl = new URL(link.href);
          // Add current parameters to pagination links
          for (const [key, value] of params) {
            linkUrl.searchParams.set(key, value);
          }
          link.href = linkUrl.toString();
        });
      }
    });
  </script>
@endsection