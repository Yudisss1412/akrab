@extends('layouts.admin')

@section('title', 'Tiket Bantuan - AKRAB')

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
    }
    
    .filter-group {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }
    
    .filters form {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      align-items: end;
    }
    
    .filters .filter-group {
      flex-direction: column;
      min-width: 120px; /* Agar form tetap rapi saat menyamping */
    }
    
    .filter-buttons {
      display: flex;
      gap: 0.5rem;
      align-self: flex-end; /* Menjaga tombol tetap sejajar bawah */
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
    
    .tickets-container {
      background: white;
      border-radius: var(--ak-radius);
      border: 1px solid var(--ak-border);
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,.05);
      margin-bottom: 1.5rem;
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
    
    .status-badge {
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: 500;
    }
    
    .status-new {
      background-color: #dbeafe;
      color: #2563eb;
    }
    
    .status-processing {
      background-color: #fef3c7;
      color: #d97706;
    }
    
    .status-completed {
      background-color: #dcfce7;
      color: #16a34a;
    }
    
    .priority-badge {
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: 500;
    }
    
    .priority-low {
      background-color: #dbeafe;
      color: #2563eb;
    }
    
    .priority-medium {
      background-color: #fef3c7;
      color: #d97706;
    }
    
    .priority-high {
      background-color: #fee2e2;
      color: #dc2626;
    }
    
    /* Custom styling for status and priority selects */
    .status-select, .priority-select {
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: 500;
      border: 1px solid var(--border);
      background: white;
      cursor: pointer;
    }
    
    .status-select:focus, .priority-select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 2px rgba(0, 110, 92, 0.2);
    }
    
    .status-select.status-new {
      background-color: #dbeafe;
      color: #2563eb;
    }
    
    .status-select.status-processing {
      background-color: #fef3c7;
      color: #d97706;
    }
    
    .status-select.status-completed {
      background-color: #dcfce7;
      color: #16a34a;
    }
    
    .priority-select.priority-low {
      background-color: #dbeafe;
      color: #2563eb;
    }
    
    .priority-select.priority-medium {
      background-color: #fef3c7;
      color: #d97706;
    }
    
    .priority-select.priority-high {
      background-color: #fee2e2;
      color: #dc2626;
    }
    
    .conversation-container {
      background: white;
      border-radius: var(--ak-radius);
      border: 1px solid var(--ak-border);
      box-shadow: 0 8px 20px rgba(0,0,0,.05);
      display: none;
    }
    
    .conversation-header {
      padding: 1.5rem;
      border-bottom: 1px solid var(--ak-border);
      background: linear-gradient(135deg, #006E5C 0%, #a8d5c9 100%);
      color: white;
    }
    
    .conversation-content {
      padding: 1.5rem;
      height: 400px;
      overflow-y: auto;
    }
    
    .message {
      margin-bottom: 1rem;
      padding: 0.75rem;
      border-radius: 8px;
      max-width: 80%;
    }
    
    .message.admin {
      background: #e0f7fa;
      margin-left: auto;
    }
    
    .message.user {
      background: white;
      border: 1px solid var(--border);
      margin-right: auto;
    }
    
    .message-sender {
      font-weight: 600;
      margin-bottom: 0.25rem;
    }
    
    .message-time {
      font-size: 0.8rem;
      color: var(--muted);
    }
    
    .reply-form {
      padding: 1.5rem;
      border-top: 1px solid var(--ak-border);
      background: var(--bg);
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
      min-height: 120px;
      resize: vertical;
    }
    
    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 110, 92, 0.1);
    }
    
    .btn-reply {
      background: var(--primary);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      width: 100%;
      font-size: 1rem;
    }
    
    .btn-reply:hover {
      background: #005a4a;
    }
    
    .btn-secondary {
      background: #6c757d;
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      font-size: 0.9rem;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }
    
    .btn-secondary:hover {
      background: #5a6268;
    }
    
    .ticket-details {
      margin-bottom: 1rem;
    }
    
    .detail-row {
      display: flex;
      margin-bottom: 0.5rem;
    }
    
    .detail-label {
      font-weight: 600;
      width: 120px;
      color: var(--muted);
    }
    
    .detail-value {
      flex: 1;
    }
    
    .close-conversation {
      background: none;
      border: none;
      color: white;
      font-size: 1.5rem;
      cursor: pointer;
      padding: 0.5rem;
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
  </style>
@endpush

@section('content')
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content">
        <!-- Daftar Tiket View -->
        <div id="ticketList">
          <div class="page-header">
            <h1>Tiket Bantuan</h1>
            <div>
              <button class="btn btn-primary">Ekspor Tiket</button>
            </div>
          </div>
          
          <div class="filters">
            <form method="GET" action="{{ route('support.tickets') }}">
              <div class="filter-group">
                <label for="statusFilter">Status</label>
                <select id="statusFilter" name="status">
                  <option value="">Semua Status</option>
                  <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Terbuka</option>
                  <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                  <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Diselesaikan</option>
                  <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Ditutup</option>
                </select>
              </div>
              
              <div class="filter-group">
                <label for="priorityFilter">Prioritas</label>
                <select id="priorityFilter" name="priority">
                  <option value="">Semua Prioritas</option>
                  <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                  <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Sedang</option>
                  <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                </select>
              </div>
              
              <div class="filter-group">
                <label for="searchFilter">Cari</label>
                <input type="text" id="searchFilter" name="search" placeholder="Cari di Akrab..." value="{{ request('search') }}">
              </div>
              
            </form>
          </div>
          
          <div class="tickets-container">
            <table id="ticketsTable">
              <thead>
                <tr>
                  <th>ID Tiket</th>
                  <th>Subjek</th>
                  <th>Pengirim</th>
                  <th>Status</th>
                  <th>Prioritas</th>
                  <th>Tanggal</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($tickets as $ticket)
                  <tr>
                    <td>{{ $ticket->id }}</td>
                    <td>{{ Str::limit($ticket->subject, 30) }}</td>
                    <td>{{ $ticket->user->name ?? 'N/A' }}</td>
                    <td>
                      <select class="status-select status-{{ strtolower($ticket->status) }}" data-ticket="{{ $ticket->id }}" onchange="updateTicketStatus(this)">
                        <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Terbuka</option>
                        <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                        <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Diselesaikan</option>
                        <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Ditutup</option>
                      </select>
                    </td>
                    <td>
                      <select class="priority-select priority-{{ strtolower($ticket->priority) }}" data-ticket="{{ $ticket->id }}" onchange="updateTicketPriority(this)">
                        <option value="low" {{ $ticket->priority == 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="medium" {{ $ticket->priority == 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high" {{ $ticket->priority == 'high' ? 'selected' : '' }}>Tinggi</option>
                      </select>
                    </td>
                    <td>{{ $ticket->created_at->format('d M Y') }}</td>
                    <td>
                      <a href="{{ route('support.tickets.detail', ['id' => $ticket->id]) }}" class="btn btn-outline">Lihat</a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada tiket ditemukan</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          
          <div class="pagination">
            <div class="pagination-info">Menampilkan 1-8 dari 24 tiket</div>
            <div class="pagination-controls">
              <button class="page-btn" id="prevBtn" disabled>‹ Sebelumnya</button>
              <button class="page-btn active">1</button>
              <button class="page-btn">2</button>
              <button class="page-btn">3</button>
              <button class="page-btn">Berikutnya ›</button>
            </div>
          </div>
        </div>
        
        <!-- Ruang Percakapan View -->
        <div id="conversationView" style="display: none;">
          <div class="page-header">
            <h1>Riwayat Percakapan Tiket</h1>
            <button class="btn btn-outline" onclick="showTicketList()">‹ Kembali ke Daftar</button>
          </div>
          
          <div class="conversation-container">
            <div class="conversation-header">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <h2 style="margin: 0; font-size: 1.5rem;">#TKT-XXXX-XX-XX-XXX</h2>
                  <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                    <div class="status-badge status-new">Baru</div>
                    <div class="priority-badge priority-high">Tinggi</div>
                  </div>
                </div>
                <button class="close-conversation" onclick="showTicketList()">×</button>
              </div>
            </div>
            
            <div class="ticket-details">
              <div class="detail-row">
                <div class="detail-label">Subjek:</div>
                <div class="detail-value">Judul Tiket</div>
              </div>
              <div class="detail-row">
                <div class="detail-label">Pengirim:</div>
                <div class="detail-value">Nama Pengirim</div>
              </div>
              <div class="detail-row">
                <div class="detail-label">Email:</div>
                <div class="detail-value">email@example.com</div>
              </div>
              <div class="detail-row">
                <div class="detail-label">Tanggal:</div>
                <div class="detail-value">Tanggal Pengiriman</div>
              </div>
              <div class="detail-row">
                <div class="detail-label">Deskripsi:</div>
                <div class="detail-value">Deskripsi lengkap tentang masalah yang dialami</div>
              </div>
            </div>
            
            <div class="conversation-content" id="conversationContent">
              <!-- Messages will be loaded here dynamically -->
              <div class="message user">
                <div class="message-sender">Warung Online (Penjual)</div>
                <div class="message-text">Saya mengalami kesulitan dalam mengunggah produk baru. Setiap kali saya mencoba mengunggah, sistem mengatakan file terlalu besar meskipun ukurannya hanya 2MB.</div>
                <div class="message-time">15 Jun 2023, 10:30 WIB</div>
              </div>
              <div class="message admin">
                <div class="message-sender">Tim Bantuan AKRAB</div>
                <div class="message-text">Terima kasih telah menghubungi kami. Kami akan mengecek sistem upload produk dan segera memberikan solusi.</div>
                <div class="message-time">15 Jun 2023, 11:00 WIB</div>
              </div>
            </div>
            
            <div class="reply-form">
              <div class="form-group">
                <label for="replyMessage">Balas Tiket</label>
                <textarea id="replyMessage" class="form-control" placeholder="Tulis balasan untuk pengirim tiket..."></textarea>
              </div>
              <button class="btn-reply" onclick="sendReply()">Kirim Balasan</button>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <script>
    // Function to show conversation view
    function showConversation(ticketId) {
      document.getElementById('ticketList').style.display = 'none';
      document.getElementById('conversationView').style.display = 'block';
      
      // Update ticket details in conversation view
      document.querySelector('#conversationView h2').textContent = ticketId;
      
      // In a real app, this would load the ticket details and conversation history
      // For now, we'll just show a placeholder
    }
    
    // Function to show ticket list view
    function showTicketList() {
      document.getElementById('ticketList').style.display = 'block';
      document.getElementById('conversationView').style.display = 'none';
    }
    
    // Function to send reply
    function sendReply() {
      const replyText = document.getElementById('replyMessage').value;
      if (!replyText.trim()) {
        alert('Silakan tulis balasan terlebih dahulu.');
        return;
      }
      
      // Add the reply to the conversation
      const conversationContent = document.getElementById('conversationContent');
      const now = new Date();
      const timeString = now.toLocaleDateString('id-ID', { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric' 
      }) + ', ' + now.toLocaleTimeString('id-ID', { 
        hour: '2-digit', 
        minute: '2-digit' 
      }) + ' WIB';
      
      const replyDiv = document.createElement('div');
      replyDiv.className = 'message admin';
      replyDiv.innerHTML = `
        <div class="message-sender">Tim Bantuan AKRAB</div>
        <div class="message-text">${replyText}</div>
        <div class="message-time">${timeString}</div>
      `;
      
      conversationContent.appendChild(replyDiv);
      
      // Clear the reply textbox
      document.getElementById('replyMessage').value = '';
      
      // Scroll to the bottom
      conversationContent.scrollTop = conversationContent.scrollHeight;
    }
    
    // Auto-filter functionality - automatically update URL and refresh page when filter changes
    const statusFilter = document.getElementById('statusFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const searchFilter = document.getElementById('searchFilter');

    // Add event listeners to filter elements for automatic filtering
    if (statusFilter) {
      statusFilter.addEventListener('change', updateFilters);
    }
    
    if (priorityFilter) {
      priorityFilter.addEventListener('change', updateFilters);
    }
    
    if (searchFilter) {
      searchFilter.addEventListener('input', function() {
        // Use a debounce to avoid too many requests while typing
        clearTimeout(this.debounceTimeout);
        this.debounceTimeout = setTimeout(() => {
          updateFilters();
        }, 300);
      });
    }

    // Set initial values based on URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status')) {
      statusFilter.value = urlParams.get('status');
    }
    if (urlParams.get('priority')) {
      priorityFilter.value = urlParams.get('priority');
    }
    if (urlParams.get('search')) {
      searchFilter.value = urlParams.get('search');
    }
    
    function updateFilters() {
      const status = statusFilter.value;
      const priority = priorityFilter.value;
      const search = searchFilter.value;

      // Build URL with filter parameters
      let url = new URL(window.location);
      url.searchParams.delete('status');    // Remove old status parameter
      url.searchParams.delete('priority');  // Remove old priority parameter
      url.searchParams.delete('search');    // Remove old search parameter

      if (status) url.searchParams.set('status', status);
      if (priority) url.searchParams.set('priority', priority);
      if (search) url.searchParams.set('search', search);

      // Reload page with updated parameters
      window.location = url.toString();
    }
    
    // Pagination
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.querySelector('.page-btn:last-child');
    
    prevBtn.addEventListener('click', () => {
      console.log('Previous page');
    });
    
    nextBtn.addEventListener('click', () => {
      console.log('Next page');
    });
    
    // Function to update ticket status
    function updateTicketStatus(selectElement) {
      const ticketId = selectElement.getAttribute('data-ticket');
      const newStatus = selectElement.value;
      
      // Make AJAX call to update the status on the server
      fetch(`/support/tickets/${ticketId}/update-status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          status: newStatus
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification('Status tiket berhasil diperbarui', 'success');
          // Update the class on the select element to reflect the new status color
          updateStatusSelectClass(selectElement, newStatus);
        } else {
          showNotification('Gagal memperbarui status tiket', 'error');
          // Restore original value if update failed
          selectElement.value = selectElement.getAttribute('data-original-value');
        }
      })
      .catch(error => {
        console.error('Error updating ticket status:', error);
        showNotification('Terjadi kesalahan saat memperbarui status tiket', 'error');
        // Restore original value if update failed
        selectElement.value = selectElement.getAttribute('data-original-value');
      });
    }
    
    // Function to update ticket priority
    function updateTicketPriority(selectElement) {
      const ticketId = selectElement.getAttribute('data-ticket');
      const newPriority = selectElement.value;
      
      // Make AJAX call to update the priority on the server
      fetch(`/support/tickets/${ticketId}/update-status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          priority: newPriority
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification('Prioritas tiket berhasil diperbarui', 'success');
          // Update the class on the select element to reflect the new priority color
          updatePrioritySelectClass(selectElement, newPriority);
        } else {
          showNotification('Gagal memperbarui prioritas tiket', 'error');
          // Restore original value if update failed
          selectElement.value = selectElement.getAttribute('data-original-value');
        }
      })
      .catch(error => {
        console.error('Error updating ticket priority:', error);
        showNotification('Terjadi kesalahan saat memperbarui prioritas tiket', 'error');
        // Restore original value if update failed
        selectElement.value = selectElement.getAttribute('data-original-value');
      });
    }
    
    // Helper function to update status select styling
    function updateStatusSelectClass(selectElement, status) {
      // Remove all status classes
      selectElement.classList.remove('status-new', 'status-processing', 'status-completed', 'status-open', 'status-in_progress', 'status-resolved', 'status-closed');
      
      // Add the appropriate class based on status
      if (status === 'new' || status === 'open') {
        selectElement.classList.add('status-open');
      } else if (status === 'processing' || status === 'in_progress') {
        selectElement.classList.add('status-in_progress');
      } else if (status === 'completed' || status === 'resolved' || status === 'closed') {
        selectElement.classList.add('status-resolved');
      }
    }
    
    // Helper function to update priority select styling
    function updatePrioritySelectClass(selectElement, priority) {
      // Remove all priority classes
      selectElement.classList.remove('priority-low', 'priority-medium', 'priority-high');
      
      // Add the appropriate class based on priority
      if (priority === 'low') {
        selectElement.classList.add('priority-low');
      } else if (priority === 'medium') {
        selectElement.classList.add('priority-medium');
      } else if (priority === 'high') {
        selectElement.classList.add('priority-high');
      }
    }
    
    // Initialize styling for all selects when page loads
    document.addEventListener('DOMContentLoaded', function() {
      const statusSelects = document.querySelectorAll('.status-select');
      const prioritySelects = document.querySelectorAll('.priority-select');
      
      // Store original values for rollback on error
      statusSelects.forEach(select => {
        select.setAttribute('data-original-value', select.value);
        const selectedStatus = select.value;
        updateStatusSelectClass(select, selectedStatus);
      });
      
      prioritySelects.forEach(select => {
        select.setAttribute('data-original-value', select.value);
        const selectedPriority = select.value;
        updatePrioritySelectClass(select, selectedPriority);
      });
    });
  </script>
@endsection