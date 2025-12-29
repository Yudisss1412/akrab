@extends('layouts.admin')

@section('title', 'Permintaan Penarikan Dana - AKRAB')

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

    .tabs {
      display: flex;
      border-bottom: 1px solid var(--ak-border);
      margin-bottom: 1.5rem;
    }

    .tab {
      padding: 0.75rem 1.5rem;
      cursor: pointer;
      border-bottom: 3px solid transparent;
      font-weight: 500;
      color: var(--muted);
    }

    .tab.active {
      border-bottom: 3px solid var(--primary);
      color: var(--primary);
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
    }

    .filters {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }

    .filter-group {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      flex: 1;
      min-width: 200px;
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

    .bulk-actions {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      align-items: center;
    }

    .bulk-select {
      padding: 0.5rem;
      border: 1px solid var(--border);
      border-radius: 6px;
      background: white;
    }

    .btn-bulk {
      padding: 0.5rem 1rem;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: var(--white);
      cursor: pointer;
      font-weight: 500;
    }

    .btn-approve {
      background: #dcfce7;
      color: #16a34a;
      border-color: #16a34a;
    }

    .btn-reject {
      background: #fee2e2;
      color: #dc2626;
      border-color: #dc2626;
    }

    .requests-container {
      background: white;
      border-radius: var(--ak-radius);
      border: 1px solid var(--ak-border);
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,.05);
    }

    .table-container {
      overflow-x: auto;
      -ms-overflow-style: auto;  /* IE and Edge */
      scrollbar-width: auto;     /* Firefox */
    }

    .table-container::-webkit-scrollbar {
      height: 8px;
    }

    .table-container::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb {
      background: #ccc;
      border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
      background: #aaa;
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

    .seller-link {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
    }

    .seller-link:hover {
      text-decoration: underline;
    }

    .amount {
      font-weight: 600;
      color: var(--primary);
    }

    .status-badge {
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: 500;
    }

    .status-pending {
      background-color: #fef3c7;
      color: #d97706;
    }

    .status-approved {
      background-color: #dcfce7;
      color: #16a34a;
    }

    .status-rejected {
      background-color: #fee2e2;
      color: #dc2626;
    }

    .status-failed {
      background-color: #fecaca;
      color: #b91c1c;
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

    .btn-approve {
      color: #16a34a;
    }

    .btn-reject {
      color: #dc2626;
    }

    /* Modal Styles */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      display: none;
    }

    .modal-overlay.show {
      display: flex;
    }

    .modal-box {
      background: white;
      border-radius: 8px;
      width: 90%;
      max-width: 500px;
      padding: 1.5rem;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .modal-title {
      font-size: 1.2rem;
      font-weight: 600;
      margin: 0;
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--muted);
    }

    .form-group {
      margin-bottom: 1rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--text);
    }

    textarea.form-control {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid var(--border);
      border-radius: 8px;
      font-size: 1rem;
      background: white;
      min-height: 100px;
      resize: vertical;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 110, 92, 0.1);
    }

    .btn-modal {
      background: var(--primary);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      width: 100%;
      font-size: 1rem;
      margin-top: 0.5rem;
    }

    .pagination {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1.5rem;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .pagination-info {
      color: var(--muted);
      font-size: 0.9rem;
      flex-shrink: 0;
    }

    .pagination-controls {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
      justify-content: center;
      min-width: auto;
    }

    .page-btn {
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: var(--white);
      cursor: pointer;
      font-size: 0.9rem;
      flex-shrink: 0;
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

    /* Mobile Responsive Table Container */
    @media (max-width: 768px) {
      .table-container {
        margin: 0 -1rem;
        padding: 0 1rem;
      }

      .table-container table {
        min-width: 600px;
      }

      .requests-container {
        border-left: none;
        border-right: none;
        border-radius: 0;
      }

      th, td {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
      }
    }

    /* Mobile Responsive Pagination */
    @media (max-width: 768px) {
      .pagination {
        flex-direction: column;
        align-items: flex-start;
      }

      .pagination-info {
        width: 100%;
        margin-bottom: 0.5rem;
        text-align: center;
      }

      .pagination-controls {
        width: 100%;
        justify-content: center;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;     /* Firefox */
      }

      .pagination-controls::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
      }

      /* Limit the width of controls on small screens */
      .pagination-controls .page-btn {
        flex-shrink: 0; /* Prevent button shrinking */
      }
    }

    @media (max-width: 480px) {
      .pagination {
        gap: 0.5rem;
      }

      .pagination-controls {
        justify-content: flex-start;
      }
    }

    .export-btn {
      background: var(--primary);
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    /* Notification styles - Match Bootstrap styling, centered */
    .notification {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) translateY(20px);
      z-index: 9999;
      opacity: 0;
      transition: all 0.3s ease;
    }

    .notification.show {
      opacity: 1;
      transform: translate(-50%, -50%) translateY(0);
    }

    .notification .alert {
      margin-bottom: 0;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
      min-width: 300px;
      text-align: center;
    }
  </style>
@endpush

@section('content')
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content">
        <div class="page-header">
          <h1>Permintaan Penarikan Dana</h1>
          <button class="btn btn-primary" id="exportBtn">Ekspor Data</button>
        </div>

        <div class="tabs">
          <div class="tab active" data-tab="pending">Permintaan Tertunda</div>
          <div class="tab" data-tab="history">Riwayat</div>
        </div>

        <!-- Tab 1: Permintaan Tertunda -->
        <div id="pendingTab" class="tab-content active">
          <div class="filters">
            <div class="filter-group">
              <label for="searchFilter">Cari Permintaan</label>
              <input type="text" id="searchFilter" placeholder="Nama Penjual, ID Penjual, atau ID Permintaan">
            </div>

            <div class="filter-group">
              <label for="dateFrom">Tanggal Mulai</label>
              <input type="date" id="dateFrom">
            </div>

            <div class="filter-group">
              <label for="dateTo">Tanggal Akhir</label>
              <input type="date" id="dateTo">
            </div>

            <div class="filter-group">
              <label for="amountFilter">Jumlah Minimum</label>
              <input type="number" id="amountFilter" placeholder="Rp 10.000.000">
            </div>
          </div>

          <div class="bulk-actions">
            <button class="btn-bulk btn-approve" onclick="bulkAction('approve')">Setujui yang Dipilih</button>
            <button class="btn-bulk btn-reject" onclick="bulkAction('reject')">Tolak yang Dipilih</button>
          </div>

          <div class="requests-container">
            <div class="table-container">
              <table>
                <thead>
                  <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>ID Permintaan</th>
                    <th>Tanggal</th>
                    <th>Penjual</th>
                    <th>Jumlah</th>
                    <th>Bank Tujuan</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($withdrawalRequests->where('status', 'pending') as $withdrawal)
                  <tr>
                    <td><input type="checkbox" class="request-check" data-id="{{ $withdrawal->id }}"></td>
                    <td>{{ $withdrawal->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($withdrawal->created_at)->format('d M Y') }}</td>
                    <td><a href="{{ route('sellers.show', $withdrawal->seller_id) }}" class="seller-link">{{ $withdrawal->seller->store_name ?? 'Penjual Tidak Ditemukan' }}</a></td>
                    <td class="amount">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
                    <td>{{ $withdrawal->bank_name ?? 'N/A' }} - {{ $withdrawal->account_number ?? 'xxxx' }}</td>
                    <td class="action-buttons">
                      <button class="btn-action btn-approve" onclick="approveRequest({{ $withdrawal->id }})">Setujui</button>
                      <button class="btn-action btn-reject" onclick="showRejectModal({{ $withdrawal->id }})">Tolak</button>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center">Tidak ada permintaan penarikan dana yang tertunda</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          @if($withdrawalRequests->hasPages())
          <div class="pagination">
            <div class="pagination-info">Menampilkan {{ $withdrawalRequests->firstItem() }}-{{ $withdrawalRequests->lastItem() }} dari {{ $withdrawalRequests->total() }} permintaan</div>
            <div class="pagination-controls">
              @if($withdrawalRequests->onFirstPage())
                <button class="page-btn" id="prevBtn" disabled>‹ Sebelumnya</button>
              @else
                <button class="page-btn" id="prevBtn" onclick="location.href='{{ $withdrawalRequests->previousPageUrl() }}'">‹ Sebelumnya</button>
              @endif

              @for($i = 1; $i <= $withdrawalRequests->lastPage(); $i++)
                <button class="page-btn {{ $withdrawalRequests->currentPage() == $i ? 'active' : '' }}" onclick="location.href='{{ $withdrawalRequests->url($i) }}'">{{ $i }}</button>
              @endfor

              @if($withdrawalRequests->hasMorePages())
                <button class="page-btn" onclick="location.href='{{ $withdrawalRequests->nextPageUrl() }}'">Berikutnya ›</button>
              @else
                <button class="page-btn" disabled>Berikutnya ›</button>
              @endif
            </div>
          </div>
          @endif
        </div>

        <!-- Tab 2: Riwayat -->
        <div id="historyTab" class="tab-content">
          <div class="filters">
            <div class="filter-group">
              <label for="searchHistory">Cari Riwayat</label>
              <input type="text" id="searchHistory" placeholder="Nama Penjual, ID Permintaan">
            </div>

            <div class="filter-group">
              <label for="dateFromHistory">Tanggal Diproses</label>
              <input type="date" id="dateFromHistory">
            </div>

            <div class="filter-group">
              <label for="statusFilter">Status</label>
              <select id="statusFilter">
                <option value="">Semua Status</option>
                <option value="approved">Berhasil</option>
                <option value="rejected">Ditolak</option>
                <option value="processing">Diproses</option>
                <option value="completed">Selesai</option>
              </select>
            </div>
          </div>

          <div class="requests-container">
            <div class="table-container">
              <table>
                <thead>
                  <tr>
                    <th>ID Permintaan</th>
                    <th>Tanggal Diproses</th>
                    <th>Penjual</th>
                    <th>Jumlah</th>
                    <th>Bank Tujuan</th>
                    <th>Status</th>
                    <th>Catatan</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($withdrawalRequests->whereNotIn('status', ['pending']) as $withdrawal)
                  <tr>
                    <td>{{ $withdrawal->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($withdrawal->updated_at)->format('d M Y') }}</td>
                    <td><a href="{{ route('sellers.show', $withdrawal->seller_id) }}" class="seller-link">{{ $withdrawal->seller->store_name ?? 'Penjual Tidak Ditemukan' }}</a></td>
                    <td class="amount">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
                    <td>{{ $withdrawal->bank_name ?? 'N/A' }} - {{ $withdrawal->account_number ?? 'xxxx' }}</td>
                    <td>
                      @if($withdrawal->status == 'pending')
                        <span class="status-badge status-pending">{{ ucfirst(str_replace('_', ' ', $withdrawal->status)) }}</span>
                      @elseif($withdrawal->status == 'approved')
                        <span class="status-badge status-approved">{{ ucfirst(str_replace('_', ' ', $withdrawal->status)) }}</span>
                      @elseif($withdrawal->status == 'rejected')
                        <span class="status-badge status-rejected">{{ ucfirst(str_replace('_', ' ', $withdrawal->status)) }}</span>
                      @elseif($withdrawal->status == 'processing')
                        <span class="status-badge status-processing">{{ ucfirst(str_replace('_', ' ', $withdrawal->status)) }}</span>
                      @elseif($withdrawal->status == 'completed')
                        <span class="status-badge status-approved">{{ ucfirst(str_replace('_', ' ', $withdrawal->status)) }}</span>
                      @else
                        <span class="status-badge">{{ ucfirst(str_replace('_', ' ', $withdrawal->status)) }}</span>
                      @endif
                    </td>
                    <td>{{ $withdrawal->rejection_reason ?? '-' }}</td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center">Tidak ada riwayat penarikan dana</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          @if($withdrawalRequests->hasPages())
          <div class="pagination">
            <div class="pagination-info">Menampilkan {{ $withdrawalRequests->firstItem() }}-{{ $withdrawalRequests->lastItem() }} dari {{ $withdrawalRequests->total() }} riwayat</div>
            <div class="pagination-controls">
              @if($withdrawalRequests->onFirstPage())
                <button class="page-btn" id="prevHistoryBtn" disabled>‹ Sebelumnya</button>
              @else
                <button class="page-btn" id="prevHistoryBtn" onclick="location.href='{{ $withdrawalRequests->previousPageUrl() }}'">‹ Sebelumnya</button>
              @endif

              @for($i = 1; $i <= $withdrawalRequests->lastPage(); $i++)
                <button class="page-btn {{ $withdrawalRequests->currentPage() == $i ? 'active' : '' }}" onclick="location.href='{{ $withdrawalRequests->url($i) }}'">{{ $i }}</button>
              @endfor

              @if($withdrawalRequests->hasMorePages())
                <button class="page-btn" onclick="location.href='{{ $withdrawalRequests->nextPageUrl() }}'">Berikutnya ›</button>
              @else
                <button class="page-btn" disabled>Berikutnya ›</button>
              @endif
            </div>
          </div>
          @endif
        </div>
      </main>
    </div>
  </div>

  <!-- Reject Modal -->
  <div class="modal-overlay" id="rejectModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3 class="modal-title">Tolak Permintaan</h3>
        <button class="modal-close" onclick="closeRejectModal()">&times;</button>
      </div>
      <div class="form-group">
        <label for="rejectReason">Alasan Penolakan</label>
        <textarea id="rejectReason" class="form-control" placeholder="Masukkan alasan penolakan..."></textarea>
      </div>
      <button class="btn-modal" onclick="confirmReject()">Tolak Permintaan</button>
    </div>
  </div>

  <!-- Approve Modal -->
  <div class="modal-overlay" id="approveModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3 class="modal-title">Konfirmasi Persetujuan</h3>
        <button class="modal-close" onclick="closeApproveModal()">&times;</button>
      </div>
      <div class="form-group">
        <p>Apakah Anda yakin ingin menyetujui permintaan penarikan dana ini?</p>
      </div>
      <button class="btn-modal" onclick="confirmApprove()">Setujui Permintaan</button>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <script>
    // Tab switching functionality
    document.querySelectorAll('.tab').forEach(tab => {
      tab.addEventListener('click', () => {
        // Remove active class from all tabs and content
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

        // Add active class to clicked tab and corresponding content
        tab.classList.add('active');
        const tabId = tab.getAttribute('data-tab') + 'Tab';
        document.getElementById(tabId).classList.add('active');
      });
    });

    // Select all checkbox functionality
    document.getElementById('selectAll').addEventListener('change', function() {
      const checkboxes = document.querySelectorAll('.request-check');
      checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
    });

    // Bulk action functionality
    function bulkAction(action) {
      const selectedRequests = [];
      document.querySelectorAll('.request-check:checked').forEach(checkbox => {
        selectedRequests.push(checkbox.getAttribute('data-id'));
      });

      if (selectedRequests.length === 0) {
        showNotification('Pilih setidaknya satu permintaan terlebih dahulu.', 'error');
        return;
      }

      if (action === 'approve') {
        if (confirm(`Yakin ingin menyetujui ${selectedRequests.length} permintaan?`)) {
          // Send approval request to server
          fetch('/admin/withdrawals/approve-bulk', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              ids: selectedRequests
            })
          })
          .then(response => response.json())
          .then(data => {
            if(data.success) {
              showNotification(`Berhasil menyetujui ${selectedRequests.length} permintaan.`, 'success');
              window.location.reload();
            } else {
              showNotification('Gagal menyetujui permintaan: ' + data.message, 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat menyetujui permintaan.', 'error');
          });
        }
      } else if (action === 'reject') {
        // Store selected IDs for rejection
        localStorage.setItem('selectedForRejection', JSON.stringify(selectedRequests));
        showRejectModal();
      }
    }

    // Individual request approval
    let currentApproveRequestId = null;

    function approveRequest(requestId) {
      currentApproveRequestId = requestId;
      document.getElementById('approveModal').classList.add('show');
    }

    // Show approve modal
    function showApproveModal(requestId = null) {
      currentApproveRequestId = requestId;
      document.getElementById('approveModal').classList.add('show');
    }

    // Close approve modal
    function closeApproveModal() {
      document.getElementById('approveModal').classList.remove('show');
      currentApproveRequestId = null;
    }

    // Confirm approval
    function confirmApprove() {
      if (currentApproveRequestId) {
        // Send approval request to server
        fetch(`/api/withdrawals/${currentApproveRequestId}/approve`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => response.json())
        .then(data => {
          if(data.success) {
            showNotification(`Berhasil menyetujui permintaan ${currentApproveRequestId}.`, 'success');
            document.getElementById('approveModal').classList.remove('show');
            window.location.reload();
          } else {
            showNotification('Gagal menyetujui permintaan: ' + data.message, 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showNotification('Terjadi kesalahan saat menyetujui permintaan.', 'error');
        });
      }
    }

    // Show reject modal
    let currentRequestId = null;

    function showRejectModal(requestId = null) {
      currentRequestId = requestId;
      document.getElementById('rejectModal').classList.add('show');
      document.getElementById('rejectReason').value = '';
    }

    // Close reject modal
    function closeRejectModal() {
      document.getElementById('rejectModal').classList.remove('show');
      currentRequestId = null;
    }

    // Confirm rejection
    function confirmReject() {
      const reason = document.getElementById('rejectReason').value;
      if (!reason.trim()) {
        alert('Silakan masukkan alasan penolakan.');
        return;
      }

      if (currentRequestId) {
        // Individual rejection
        // Send rejection request to server
        fetch(`/api/withdrawals/${currentRequestId}/reject`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            reason: reason
          })
        })
        .then(response => response.json())
        .then(data => {
          if(data.success) {
            showNotification(`Berhasil menolak permintaan ${currentRequestId}.`, 'success');
            window.location.reload();
          } else {
            showNotification('Gagal menolak permintaan: ' + data.message, 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showNotification('Terjadi kesalahan saat menolak permintaan.', 'error');
        });
      } else {
        // Bulk rejection
        const selectedRequests = JSON.parse(localStorage.getItem('selectedForRejection') || []);
        if (selectedRequests.length > 0) {
          // Send bulk rejection request to server
          fetch('/admin/withdrawals/reject-bulk', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              ids: selectedRequests,
              reason: reason
            })
          })
          .then(response => response.json())
          .then(data => {
            if(data.success) {
              showNotification(`Berhasil menolak ${selectedRequests.length} permintaan.`, 'success');
              localStorage.removeItem('selectedForRejection');
              window.location.reload();
            } else {
              showNotification('Gagal menolak permintaan: ' + data.message, 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat menolak permintaan.', 'error');
          });
        }
      }
    }

    // Close modal when clicking outside the box
    document.getElementById('rejectModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeRejectModal();
      }
    });

    // Close approve modal when clicking outside the box
    document.getElementById('approveModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeApproveModal();
      }
    });

    // Export functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
      // Redirect to export route
      window.location.href = '/admin/withdrawals/export';
    });

    // Filter functionality for pending requests
    document.getElementById('searchFilter').addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      // In a real implementation, this would trigger an AJAX request
      console.log('Search term:', searchTerm);
    });

    // Filter functionality for history
    document.getElementById('searchHistory').addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      // In a real implementation, this would trigger an AJAX request
      console.log('History search term:', searchTerm);
    });

    // Status filter for history tab
    document.getElementById('statusFilter').addEventListener('change', function() {
      const status = this.value;
      // In a real implementation, this would trigger an AJAX request
      console.log('Status filter:', status);
    });

    // Function to show notification
    function showNotification(message, type = 'success') {
      // Remove any existing notifications
      const existingNotification = document.querySelector('.notification');
      if (existingNotification) {
        existingNotification.remove();
      }

      // Map our types to Bootstrap alert types
      let alertType = 'success';
      if (type === 'error') {
        alertType = 'danger';
      } else if (type === 'warning') {
        alertType = 'warning';
      } else if (type === 'info') {
        alertType = 'info';
      }

      // Create notification container
      const notification = document.createElement('div');
      notification.className = 'notification';
      notification.innerHTML =
        '<div class="alert alert-' + alertType + ' alert-dismissible fade show" role="alert">' +
          message +
          '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
        '</div>';

      // Add to document
      document.body.appendChild(notification);

      // Trigger animation
      setTimeout(() => {
        notification.classList.add('show');
      }, 10);

      // Auto remove after 5 seconds to allow reading in center position
      setTimeout(() => {
        const alertElement = notification.querySelector('.alert');
        if (alertElement) {
          // Trigger Bootstrap's fade out
          alertElement.classList.remove('show');
          setTimeout(() => {
            if (notification.parentNode) {
              notification.parentNode.removeChild(notification);
            }
          }, 150);
        }
      }, 5000);
    }
  </script>
@endsection