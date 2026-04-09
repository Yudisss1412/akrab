@extends('layouts.admin')

@section('title', 'Laporan Pelanggaran - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/reports_violations.css') }}">
  <style>
    /* SHOPEE-STYLE MOBILE CARDS FOR VIOLATIONS */
    .violation-card {
        background-color: #ffffff !important;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        margin-bottom: 0.75rem;
        overflow: hidden;
        border: 1px solid #f0f0f0;
    }

    .violation-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        background: #fafafa;
        border-bottom: 1px solid #f0f0f0;
        gap: 0.5rem;
    }

    .violation-info {
        flex: 1;
        min-width: 0;
    }

    .violation-type {
        font-weight: 600;
        font-size: 0.95rem;
        color: #222222 !important;
        display: block;
        margin-bottom: 0.15rem;
    }

    .violation-date {
        font-size: 0.75rem;
        color: #6b7280 !important;
    }

    .violation-card-body {
        padding: 1rem;
    }

    .violation-info-row {
        display: flex;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
        gap: 0.5rem;
    }

    .violation-info-row:last-child {
        margin-bottom: 0;
    }

    .violation-label {
        color: #757575 !important;
        min-width: 75px;
        font-weight: 500;
        flex-shrink: 0;
    }

    .violation-value {
        color: #222222 !important;
        font-weight: 600;
        flex: 1;
        text-align: right;
        word-break: break-word;
    }

    .violation-card-footer {
        display: flex;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border-top: 1px solid #f0f0f0;
        background: #fafafa;
        flex-wrap: wrap;
    }

    .violation-action-btn {
        flex: 1;
        min-width: 70px;
        padding: 0.5rem;
        font-size: 0.8rem;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        background: #ffffff;
        color: #222222 !important;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        text-decoration: none;
        white-space: nowrap;
    }

    .violation-action-btn:hover {
        background: #f8f9fa;
        border-color: #006E5C;
        color: #006E5C !important;
    }

    .violation-action-btn.btn-primary {
        background: #006E5C;
        color: #ffffff !important;
        border-color: #006E5C;
    }

    /* Status Pills */
    .status-pill {
        padding: 0.25rem 0.6rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-pill-pending {
        background: #fef3c7;
        color: #92400e !important;
    }

    .status-pill-investigating {
        background: #e0f2fe;
        color: #0369a1 !important;
    }

    .status-pill-resolved {
        background: #e6fffa;
        color: #006E5C !important;
    }

    .status-pill-dismissed {
        background: #e5e7eb;
        color: #374151 !important;
    }

    /* Hide mobile cards on desktop, show on mobile */
    .mobile-cards { display: none; }
    
    @media (max-width: 991px) {
        .desktop-table { display: none !important; }
        .mobile-cards { display: block !important; }
    }

    /* SMALL MOBILE FIXES */
    @media (max-width: 360px) {
        .violation-card { padding: 0.5rem !important; }
        .violation-card-header { padding: 0.6rem !important; }
        .violation-card-body { padding: 0.75rem 0.5rem !important; }
        .violation-info-row { gap: 0.25rem !important; }
        .violation-label { min-width: 60px !important; }
        .violation-card-footer { padding: 0.6rem 0.5rem !important; gap: 0.4rem !important; }
        .violation-action-btn { padding: 0.4rem 0.3rem !important; font-size: 0.7rem !important; min-width: 55px !important; }
        
        /* Pagination fixes */
        .mt-4 .d-flex { flex-direction: column !important; text-align: center; }
        .pagination-controls { justify-content: center !important; }
        .page-btn { padding: 0.35rem 0.5rem !important; font-size: 0.75rem !important; }
    }

    @media (max-width: 320px) {
        .violation-card { padding: 0.4rem !important; }
        .violation-card-header { padding: 0.5rem !important; flex-wrap: wrap; }
        .violation-type { font-size: 0.9rem !important; }
        .violation-card-body { padding: 0.6rem 0.4rem !important; }
        .violation-label { min-width: 55px !important; font-size: 0.75rem !important; }
        .violation-value { font-size: 0.8rem !important; }
        .violation-card-footer { padding: 0.5rem 0.4rem !important; gap: 0.3rem !important; }
        .violation-action-btn { flex: 1 1 calc(50% - 0.3rem) !important; padding: 0.35rem 0.2rem !important; font-size: 0.65rem !important; }
        
        /* Pagination fixes */
        .mt-4 .d-flex { font-size: 0.8rem !important; }
        .page-btn { padding: 0.3rem 0.4rem !important; font-size: 0.7rem !important; }
    }

    /* Force hide inactive tabs */
    .tab-content > .tab-pane:not(.active),
    .tab-pane.fade:not(.show) {
        display: none !important;
    }

    /* Remove flex stretching */
    .content-wrapper, .admin-page-content, .main-content, .main-layout {
        flex: none !important; 
        height: auto !important;
        min-height: 0 !important;
        margin-bottom: 1rem;
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
          <!-- Desktop Table -->
          <table id="violationsTable" class="desktop-table">
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
                  @if($report->violation_type === 'product') Produk Palsu
                  @elseif($report->violation_type === 'content') Konten Tidak Pantas
                  @elseif($report->violation_type === 'scam') Penipuan
                  @elseif($report->violation_type === 'copyright') Pelanggaran Hak Cipta
                  @elseif($report->violation_type === 'other') Lainnya
                  @else {{ ucfirst(str_replace('_', ' ', $report->violation_type)) }} @endif
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

          <!-- Mobile Cards -->
          <div class="mobile-cards">
            <h5 class="mb-3" style="font-weight: 600; color: #374151;">Daftar Pelanggaran</h5>
            @forelse($reports as $report)
            <div class="violation-card">
              <div class="violation-card-header">
                <div class="violation-info">
                  <span class="violation-type">
                    @if($report->violation_type === 'product') Produk Palsu
                    @elseif($report->violation_type === 'content') Konten Tidak Pantas
                    @elseif($report->violation_type === 'scam') Penipuan
                    @elseif($report->violation_type === 'copyright') Pelanggaran Hak Cipta
                    @elseif($report->violation_type === 'other') Lainnya
                    @else {{ ucfirst(str_replace('_', ' ', $report->violation_type)) }} @endif
                  </span>
                  <span class="violation-date">{{ $report->created_at->format('d M Y') }}</span>
                </div>
                @php
                  $pillClass = '';
                  $statusText = '';
                  switch($report->status) {
                    case 'pending': $pillClass = 'status-pill-pending'; $statusText = 'Pending'; break;
                    case 'investigating': $pillClass = 'status-pill-investigating'; $statusText = 'Ditinjau'; break;
                    case 'resolved': $pillClass = 'status-pill-resolved'; $statusText = 'Selesai'; break;
                    case 'dismissed': $pillClass = 'status-pill-dismissed'; $statusText = 'Ditolak'; break;
                    default: $pillClass = 'status-pill-pending'; $statusText = 'Pending';
                  }
                @endphp
                <span class="status-pill {{ $pillClass }}">{{ $statusText }}</span>
              </div>
              <div class="violation-card-body">
                <div class="violation-info-row">
                  <span class="violation-label">Penjual</span>
                  <span class="violation-value">{{ $report->violator->name ?? 'N/A' }}</span>
                </div>
                <div class="violation-info-row">
                  <span class="violation-label">Produk</span>
                  <span class="violation-value">{{ $report->product->name ?? 'N/A' }}</span>
                </div>
                <div class="violation-info-row">
                  <span class="violation-label">Pelapor</span>
                  <span class="violation-value">{{ $report->reporter->name ?? 'N/A' }}</span>
                </div>
              </div>
              <div class="violation-card-footer">
                <a href="{{ route('reports.violations.detail', $report->id) }}" class="violation-action-btn" title="Lihat"><i class="fas fa-eye"></i> Lihat</a>
                @if($report->status === 'pending' || $report->status === 'investigating')
                <button class="violation-action-btn btn-primary" onclick="updateReportStatus({{ $report->id }})" title="Selesaikan"><i class="fas fa-check"></i> Selesaikan</button>
                @endif
              </div>
            </div>
            @empty
            <div class="text-center py-5" style="color: #6b7280;">
              <i class="fas fa-inbox fa-3x mb-3"></i>
              <p>Tidak ada laporan pelanggaran ditemukan.</p>
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
