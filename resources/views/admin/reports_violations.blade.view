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
          <form id="filterForm" method="GET" action="{{ route('reports.violations.filter') }}">
            <div class="d-flex flex-wrap align-items-end gap-3">
              <div class="filter-group">
                <label for="searchFilter">Cari</label>
                <input type="text" name="search" id="searchFilter" placeholder="Cari di Akrab..." value="{{ request('search', '') }}">
              </div>

              <div class="filter-group">
                <label for="startDateFilter">Tanggal Awal</label>
                <input type="date" name="start_date" id="startDateFilter" value="{{ request('start_date', '') }}">
              </div>

              <div class="filter-group">
                <label for="endDateFilter">Tanggal Akhir</label>
                <input type="date" name="end_date" id="endDateFilter" value="{{ request('end_date', '') }}">
              </div>

              <div class="filter-group">
                <label for="violationTypeFilter">Jenis Pelanggaran</label>
                <select name="violation_type" id="violationTypeFilter">
                  <option value="">Semua Jenis</option>
                  <option value="product" {{ request('violation_type') === 'product' ? 'selected' : '' }}>Produk Palsu</option>
                  <option value="content" {{ request('violation_type') === 'content' ? 'selected' : '' }}>Konten Tidak Pantas</option>
                  <option value="scam" {{ request('violation_type') === 'scam' ? 'selected' : '' }}>Penipuan</option>
                  <option value="copyright" {{ request('violation_type') === 'copyright' ? 'selected' : '' }}>Pelanggaran Hak Cipta</option>
                  <option value="other" {{ request('violation_type') === 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
              </div>

              <div class="filter-group">
                <label for="statusFilter">Status</label>
                <select name="status" id="statusFilter">
                  <option value="">Semua Status</option>
                  <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                  <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>Sedang Ditinjau</option>
                  <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Diselesaikan</option>
                  <option value="dismissed" {{ request('status') === 'dismissed' ? 'selected' : '' }}>Ditolak</option>
                </select>
              </div>


            </div>
          </form>
        </div>

        <div class="table-container">
          <table id="violationsTable">
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
        </div>
          </table>
        </div>

        <div class="pagination">
          <div class="pagination-info" id="paginationInfo">
            @if($reports)
              Menampilkan {{ $reports->firstItem() }}-{{ $reports->lastItem() }} dari {{ $reports->total() }} laporan
            @else
              Menampilkan 0-0 dari 0 laporan
            @endif
          </div>
          <div class="pagination-controls">
            @if($reports)
              @if($reports->onFirstPage())
                <button class="page-btn" id="firstBtn" disabled>&lt;&lt; Pertama</button>
                <button class="page-btn" id="prevBtn" disabled>‹ Sebelumnya</button>
              @else
                <a href="{{ $reports->url(1) }}" class="page-btn" id="firstBtn">&lt;&lt; Pertama</a>
                <a href="{{ $reports->previousPageUrl() }}" class="page-btn" id="prevBtn">‹ Sebelumnya</a>
              @endif

              @for($i = max(1, $reports->currentPage() - 2); $i <= min($reports->lastPage(), $reports->currentPage() + 2); $i++)
                <a href="{{ $reports->url($i) }}" class="page-btn {{ $i == $reports->currentPage() ? 'active' : '' }}" data-page="{{ $i }}">{{ $i }}</a>
              @endfor

              @if($reports->hasMorePages())
                <a href="{{ $reports->nextPageUrl() }}" class="page-btn" id="nextBtn">Berikutnya ›</a>
                <a href="{{ $reports->url($reports->lastPage()) }}" class="page-btn" id="lastBtn">Terakhir &gt;&gt;</a>
              @else
                <button class="page-btn" id="nextBtn" disabled>Berikutnya ›</button>
                <button class="page-btn" id="lastBtn" disabled>Terakhir &gt;&gt;</button>
              @endif
            @endif
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <script>
    // Function to update report status
    async function updateReportStatus(reportId) {
      // In a real implementation, this would open a modal to update the status
      // For now, we'll simulate with a simple prompt
      const status = prompt('Masukkan status baru untuk laporan (pending, investigating, resolved, dismissed):', 'resolved');

      if (status) {
        try {
          const response = await fetch(`/reports/violations/${reportId}/status`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              status: status,
              admin_notes: 'Laporan diproses oleh admin'
            })
          });

          const data = await response.json();

          if (data.success) {
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
    }

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
      
      // Add event listeners to filter elements for automatic filtering
      const searchFilter = document.getElementById('searchFilter');
      const startDateFilter = document.getElementById('startDateFilter');
      const endDateFilter = document.getElementById('endDateFilter');
      const violationTypeFilter = document.getElementById('violationTypeFilter');
      const statusFilter = document.getElementById('statusFilter');
      
      // Function to submit the form automatically
      function submitFilterForm() {
        document.getElementById('filterForm').submit();
      }
      
      // Add event listeners to trigger filtering
      if (searchFilter) {
        searchFilter.addEventListener('input', debounce(submitFilterForm, 500)); // 500ms delay to avoid excessive requests
      }
      
      if (startDateFilter) {
        startDateFilter.addEventListener('change', submitFilterForm);
      }
      
      if (endDateFilter) {
        endDateFilter.addEventListener('change', submitFilterForm);
      }
      
      if (violationTypeFilter) {
        violationTypeFilter.addEventListener('change', submitFilterForm);
      }
      
      if (statusFilter) {
        statusFilter.addEventListener('change', submitFilterForm);
      }
    });
    
    // Debounce function to limit the rate at which a function is called
    function debounce(func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    }
  </script>
@endsection