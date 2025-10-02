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
            <select class="bulk-select" id="bulkSelect">
              <option value="">Pilih aksi...</option>
              <option value="approve">Setujui yang Dipilih</option>
              <option value="reject">Tolak yang Dipilih</option>
            </select>
            <button class="btn-bulk btn-approve" onclick="bulkAction('approve')">Setujui yang Dipilih</button>
            <button class="btn-bulk btn-reject" onclick="bulkAction('reject')">Tolak yang Dipilih</button>
          </div>
          
          <div class="requests-container">
            <table>
              <thead>
                <tr>
                  <th><input type="checkbox" id="selectAll"></th>
                  <th>ID Permintaan</th>
                  <th>Tanggal</th>
                  <th>Penjual</th>
                  <th>Jumlah</th>
                  <th>Saldo</th>
                  <th>Bank Tujuan</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><input type="checkbox" class="request-check" data-id="WD-2023-06-15-001"></td>
                  <td>WD-2023-06-15-001</td>
                  <td>15 Jun 2023</td>
                  <td><a href="#" class="seller-link">Warung Online</a></td>
                  <td class="amount">Rp 2.500.000</td>
                  <td class="amount">Rp 5.000.000</td>
                  <td>BCA - xxxx1234</td>
                  <td class="action-buttons">
                    <button class="btn-action btn-approve" onclick="approveRequest('WD-2023-06-15-001')">Setujui</button>
                    <button class="btn-action btn-reject" onclick="showRejectModal('WD-2023-06-15-001')">Tolak</button>
                  </td>
                </tr>
                <tr>
                  <td><input type="checkbox" class="request-check" data-id="WD-2023-06-14-002"></td>
                  <td>WD-2023-06-14-002</td>
                  <td>14 Jun 2023</td>
                  <td><a href="#" class="seller-link">Toko Elektronik</a></td>
                  <td class="amount">Rp 1.200.000</td>
                  <td class="amount">Rp 3.500.000</td>
                  <td>BRI - xxxx5678</td>
                  <td class="action-buttons">
                    <button class="btn-action btn-approve" onclick="approveRequest('WD-2023-06-14-002')">Setujui</button>
                    <button class="btn-action btn-reject" onclick="showRejectModal('WD-2023-06-14-002')">Tolak</button>
                  </td>
                </tr>
                <tr>
                  <td><input type="checkbox" class="request-check" data-id="WD-2023-06-13-003"></td>
                  <td>WD-2023-06-13-003</td>
                  <td>13 Jun 2023</td>
                  <td><a href="#" class="seller-link">Seni Kita</a></td>
                  <td class="amount">Rp 5.000.000</td>
                  <td class="amount">Rp 12.000.000</td>
                  <td>Mandiri - xxxx9012</td>
                  <td class="action-buttons">
                    <button class="btn-action btn-approve" onclick="approveRequest('WD-2023-06-13-003')">Setujui</button>
                    <button class="btn-action btn-reject" onclick="showRejectModal('WD-2023-06-13-003')">Tolak</button>
                  </td>
                </tr>
                <tr>
                  <td><input type="checkbox" class="request-check" data-id="WD-2023-06-12-004"></td>
                  <td>WD-2023-06-12-004</td>
                  <td>12 Jun 2023</td>
                  <td><a href="#" class="seller-link">Fashion Kita</a></td>
                  <td class="amount">Rp 3.750.000</td>
                  <td class="amount">Rp 8.500.000</td>
                  <td>BNI - xxxx3456</td>
                  <td class="action-buttons">
                    <button class="btn-action btn-approve" onclick="approveRequest('WD-2023-06-12-004')">Setujui</button>
                    <button class="btn-action btn-reject" onclick="showRejectModal('WD-2023-06-12-004')">Tolak</button>
                  </td>
                </tr>
                <tr>
                  <td><input type="checkbox" class="request-check" data-id="WD-2023-06-11-005"></td>
                  <td>WD-2023-06-11-005</td>
                  <td>11 Jun 2023</td>
                  <td><a href="#" class="seller-link">Buku Seru</a></td>
                  <td class="amount">Rp 1.800.000</td>
                  <td class="amount">Rp 4.200.000</td>
                  <td>BCA - xxxx7890</td>
                  <td class="action-buttons">
                    <button class="btn-action btn-approve" onclick="approveRequest('WD-2023-06-11-005')">Setujui</button>
                    <button class="btn-action btn-reject" onclick="showRejectModal('WD-2023-06-11-005')">Tolak</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div class="pagination">
            <div class="pagination-info">Menampilkan 1-5 dari 24 permintaan</div>
            <div class="pagination-controls">
              <button class="page-btn" id="prevBtn" disabled>‹ Sebelumnya</button>
              <button class="page-btn active">1</button>
              <button class="page-btn">2</button>
              <button class="page-btn">3</button>
              <button class="page-btn">Berikutnya ›</button>
            </div>
          </div>
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
                <option value="failed">Gagal</option>
              </select>
            </div>
          </div>
          
          <div class="requests-container">
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
                <tr>
                  <td>WD-2023-06-10-006</td>
                  <td>10 Jun 2023</td>
                  <td><a href="#" class="seller-link">Hadiah Spesial</a></td>
                  <td class="amount">Rp 3.200.000</td>
                  <td>BCA - xxxx2468</td>
                  <td><span class="status-badge status-approved">Berhasil</span></td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>WD-2023-06-09-007</td>
                  <td>09 Jun 2023</td>
                  <td><a href="#" class="seller-link">Aksesoris Modis</a></td>
                  <td class="amount">Rp 1.500.000</td>
                  <td>BRI - xxxx1357</td>
                  <td><span class="status-badge status-rejected">Ditolak</span></td>
                  <td>Data bank tidak valid</td>
                </tr>
                <tr>
                  <td>WD-2023-06-08-008</td>
                  <td>08 Jun 2023</td>
                  <td><a href="#" class="seller-link">Olahraga Store</a></td>
                  <td class="amount">Rp 4.100.000</td>
                  <td>Mandiri - xxxx9753</td>
                  <td><span class="status-badge status-failed">Gagal</span></td>
                  <td>Transfer gagal, saldo tidak mencukupi</td>
                </tr>
                <tr>
                  <td>WD-2023-06-07-009</td>
                  <td>07 Jun 2023</td>
                  <td><a href="#" class="seller-link">Toko Sejahtera</a></td>
                  <td class="amount">Rp 2.800.000</td>
                  <td>BNI - xxxx8642</td>
                  <td><span class="status-badge status-approved">Berhasil</span></td>
                  <td>-</td>
                </tr>
                <tr>
                  <td>WD-2023-06-06-010</td>
                  <td>06 Jun 2023</td>
                  <td><a href="#" class="seller-link">Elektronik Murah</a></td>
                  <td class="amount">Rp 6.500.000</td>
                  <td>BCA - xxxx1122</td>
                  <td><span class="status-badge status-rejected">Ditolak</span></td>
                  <td>Akun sedang dalam investigasi</td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div class="pagination">
            <div class="pagination-info">Menampilkan 1-5 dari 35 riwayat</div>
            <div class="pagination-controls">
              <button class="page-btn" id="prevHistoryBtn" disabled>‹ Sebelumnya</button>
              <button class="page-btn active">1</button>
              <button class="page-btn">2</button>
              <button class="page-btn">3</button>
              <button class="page-btn">Berikutnya ›</button>
            </div>
          </div>
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
        alert('Pilih setidaknya satu permintaan terlebih dahulu.');
        return;
      }
      
      if (action === 'approve') {
        if (confirm(`Yakin ingin menyetujui ${selectedRequests.length} permintaan?`)) {
          console.log('Menyetujui permintaan:', selectedRequests);
          alert(`Berhasil menyetujui ${selectedRequests.length} permintaan.`);
          location.reload(); // In a real app, this would be an AJAX call
        }
      } else if (action === 'reject') {
        // Store selected IDs for rejection
        localStorage.setItem('selectedForRejection', JSON.stringify(selectedRequests));
        showRejectModal();
      }
    }
    
    // Individual request approval
    function approveRequest(requestId) {
      if (confirm(`Yakin ingin menyetujui permintaan ${requestId}?`)) {
        console.log('Menyetujui permintaan:', requestId);
        alert(`Berhasil menyetujui permintaan ${requestId}.`);
        location.reload(); // In a real app, this would be an AJAX call
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
        console.log('Menolak permintaan:', currentRequestId, 'dengan alasan:', reason);
        alert(`Berhasil menolak permintaan ${currentRequestId}.`);
        closeRejectModal();
        location.reload(); // In a real app, this would be an AJAX call
      } else {
        // Bulk rejection
        const selectedRequests = JSON.parse(localStorage.getItem('selectedForRejection') || []);
        if (selectedRequests.length > 0) {
          console.log('Menolak permintaan:', selectedRequests, 'dengan alasan:', reason);
          alert(`Berhasil menolak ${selectedRequests.length} permintaan.`);
          localStorage.removeItem('selectedForRejection');
          closeRejectModal();
          location.reload(); // In a real app, this would be an AJAX call
        }
      }
    }
    
    // Close modal when clicking outside the box
    document.getElementById('rejectModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeRejectModal();
      }
    });
    
    // Export functionality
    document.getElementById('exportBtn').addEventListener('click', function() {
      alert('Fitur ekspor data akan segera tersedia. Data akan diunduh dalam format CSV.');
    });
    
    // Filter functionality for pending requests
    document.getElementById('searchFilter').addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      filterTableRows('#pendingTab tbody tr', searchTerm);
    });
    
    // Filter functionality for history
    document.getElementById('searchHistory').addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      filterTableRows('#historyTab tbody tr', searchTerm);
    });
    
    // Helper function to filter table rows
    function filterTableRows(tableSelector, searchTerm) {
      const rows = document.querySelectorAll(tableSelector);
      rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        let matches = false;
        
        for (let cell of cells) {
          if (cell.textContent.toLowerCase().includes(searchTerm)) {
            matches = true;
            break;
          }
        }
        
        row.style.display = matches ? '' : 'none';
      });
    }
    
    // Status filter for history tab
    document.getElementById('statusFilter').addEventListener('change', function() {
      const status = this.value;
      const rows = document.querySelectorAll('#historyTab tbody tr');
      
      rows.forEach(row => {
        if (!status) {
          row.style.display = '';
        } else {
          const statusCell = row.querySelector('.status-badge');
          const statusClass = statusCell ? statusCell.className : '';
          let matches = false;
          
          if (status === 'approved' && statusClass.includes('status-approved')) matches = true;
          if (status === 'rejected' && statusClass.includes('status-rejected')) matches = true;
          if (status === 'failed' && statusClass.includes('status-failed')) matches = true;
          
          row.style.display = matches ? '' : 'none';
        }
      });
    });
  </script>
@endsection