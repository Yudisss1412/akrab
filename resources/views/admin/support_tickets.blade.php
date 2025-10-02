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
            <div class="filter-group">
              <label for="statusFilter">Status</label>
              <select id="statusFilter">
                <option value="">Semua Status</option>
                <option value="new">Baru</option>
                <option value="processing">Sedang Diproses</option>
                <option value="completed">Selesai</option>
              </select>
            </div>
            
            <div class="filter-group">
              <label for="priorityFilter">Prioritas</label>
              <select id="priorityFilter">
                <option value="">Semua Prioritas</option>
                <option value="low">Rendah</option>
                <option value="medium">Sedang</option>
                <option value="high">Tinggi</option>
              </select>
            </div>
            
            <div class="filter-group">
              <label for="searchFilter">Cari</label>
              <input type="text" id="searchFilter" placeholder="Cari tiket...">
            </div>
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
                <tr>
                  <td>#TKT-2023-06-15-001</td>
                  <td>Kesulitan mengunggah produk</td>
                  <td>Warung Online</td>
                  <td>
                    <select class="status-select" data-ticket="#TKT-2023-06-15-001" onchange="updateTicketStatus(this)">
                      <option value="new" selected>Baru</option>
                      <option value="processing">Sedang Diproses</option>
                      <option value="completed">Selesai</option>
                    </select>
                  </td>
                  <td>
                    <select class="priority-select" data-ticket="#TKT-2023-06-15-001" onchange="updateTicketPriority(this)">
                      <option value="low">Rendah</option>
                      <option value="medium">Sedang</option>
                      <option value="high" selected>Tinggi</option>
                    </select>
                  </td>
                  <td>15 Jun 2023</td>
                  <td>
                    <a href="{{ route('support.tickets.detail', ['id' => 'TKT-2023-06-15-001']) }}" class="btn btn-outline">Lihat</a>
                  </td>
                </tr>
                <tr>
                  <td>#TKT-2023-06-14-002</td>
                  <td>Pembayaran tidak masuk</td>
                  <td>Toko Elektronik</td>
                  <td>
                    <select class="status-select" data-ticket="#TKT-2023-06-14-002" onchange="updateTicketStatus(this)">
                      <option value="new">Baru</option>
                      <option value="processing" selected>Sedang Diproses</option>
                      <option value="completed">Selesai</option>
                    </select>
                  </td>
                  <td>
                    <select class="priority-select" data-ticket="#TKT-2023-06-14-002" onchange="updateTicketPriority(this)">
                      <option value="low">Rendah</option>
                      <option value="medium" selected>Sedang</option>
                      <option value="high">Tinggi</option>
                    </select>
                  </td>
                  <td>14 Jun 2023</td>
                  <td>
                    <a href="{{ route('support.tickets.detail', ['id' => 'TKT-2023-06-14-002']) }}" class="btn btn-outline">Lihat</a>
                  </td>
                </tr>
                <tr>
                  <td>#TKT-2023-06-13-003</td>
                  <td>Kesulitan verifikasi akun</td>
                  <td>Seni Kita</td>
                  <td>
                    <select class="status-select" data-ticket="#TKT-2023-06-13-003" onchange="updateTicketStatus(this)">
                      <option value="new">Baru</option>
                      <option value="processing">Sedang Diproses</option>
                      <option value="completed" selected>Selesai</option>
                    </select>
                  </td>
                  <td>
                    <select class="priority-select" data-ticket="#TKT-2023-06-13-003" onchange="updateTicketPriority(this)">
                      <option value="low" selected>Rendah</option>
                      <option value="medium">Sedang</option>
                      <option value="high">Tinggi</option>
                    </select>
                  </td>
                  <td>13 Jun 2023</td>
                  <td>
                    <a href="{{ route('support.tickets.detail', ['id' => 'TKT-2023-06-13-003']) }}" class="btn btn-outline">Lihat</a>
                  </td>
                </tr>
                <tr>
                  <td>#TKT-2023-06-12-004</td>
                  <td>Produk tidak muncul di katalog</td>
                  <td>Fashion Kita</td>
                  <td>
                    <select class="status-select" data-ticket="#TKT-2023-06-12-004" onchange="updateTicketStatus(this)">
                      <option value="new" selected>Baru</option>
                      <option value="processing">Sedang Diproses</option>
                      <option value="completed">Selesai</option>
                    </select>
                  </td>
                  <td>
                    <select class="priority-select" data-ticket="#TKT-2023-06-12-004" onchange="updateTicketPriority(this)">
                      <option value="low">Rendah</option>
                      <option value="medium">Sedang</option>
                      <option value="high" selected>Tinggi</option>
                    </select>
                  </td>
                  <td>12 Jun 2023</td>
                  <td>
                    <a href="{{ route('support.tickets.detail', ['id' => 'TKT-2023-06-12-004']) }}" class="btn btn-outline">Lihat</a>
                  </td>
                </tr>
                <tr>
                  <td>#TKT-2023-06-11-005</td>
                  <td>Pengiriman tidak terupdate</td>
                  <td>Buku Seru</td>
                  <td>
                    <select class="status-select" data-ticket="#TKT-2023-06-11-005" onchange="updateTicketStatus(this)">
                      <option value="new">Baru</option>
                      <option value="processing" selected>Sedang Diproses</option>
                      <option value="completed">Selesai</option>
                    </select>
                  </td>
                  <td>
                    <select class="priority-select" data-ticket="#TKT-2023-06-11-005" onchange="updateTicketPriority(this)">
                      <option value="low">Rendah</option>
                      <option value="medium" selected>Sedang</option>
                      <option value="high">Tinggi</option>
                    </select>
                  </td>
                  <td>11 Jun 2023</td>
                  <td>
                    <a href="{{ route('support.tickets.detail', ['id' => 'TKT-2023-06-11-005']) }}" class="btn btn-outline">Lihat</a>
                  </td>
                </tr>
                <tr>
                  <td>#TKT-2023-06-10-006</td>
                  <td>Kesulitan menggunakan fitur diskon</td>
                  <td>Hadiah Spesial</td>
                  <td>
                    <select class="status-select" data-ticket="#TKT-2023-06-10-006" onchange="updateTicketStatus(this)">
                      <option value="new">Baru</option>
                      <option value="processing">Sedang Diproses</option>
                      <option value="completed" selected>Selesai</option>
                    </select>
                  </td>
                  <td>
                    <select class="priority-select" data-ticket="#TKT-2023-06-10-006" onchange="updateTicketPriority(this)">
                      <option value="low" selected>Rendah</option>
                      <option value="medium">Sedang</option>
                      <option value="high">Tinggi</option>
                    </select>
                  </td>
                  <td>10 Jun 2023</td>
                  <td>
                    <a href="{{ route('support.tickets.detail', ['id' => 'TKT-2023-06-10-006']) }}" class="btn btn-outline">Lihat</a>
                  </td>
                </tr>
                <tr>
                  <td>#TKT-2023-06-09-007</td>
                  <td>Laporan bug di dashboard</td>
                  <td>Aksesoris Modis</td>
                  <td>
                    <select class="status-select" data-ticket="#TKT-2023-06-09-007" onchange="updateTicketStatus(this)">
                      <option value="new" selected>Baru</option>
                      <option value="processing">Sedang Diproses</option>
                      <option value="completed">Selesai</option>
                    </select>
                  </td>
                  <td>
                    <select class="priority-select" data-ticket="#TKT-2023-06-09-007" onchange="updateTicketPriority(this)">
                      <option value="low">Rendah</option>
                      <option value="medium">Sedang</option>
                      <option value="high" selected>Tinggi</option>
                    </select>
                  </td>
                  <td>09 Jun 2023</td>
                  <td>
                    <a href="{{ route('support.tickets.detail', ['id' => 'TKT-2023-06-09-007']) }}" class="btn btn-outline">Lihat</a>
                  </td>
                </tr>
                <tr>
                  <td>#TKT-2023-06-08-008</td>
                  <td>Pengembalian dana tertunda</td>
                  <td>Olahraga Store</td>
                  <td>
                    <select class="status-select" data-ticket="#TKT-2023-06-08-008" onchange="updateTicketStatus(this)">
                      <option value="new">Baru</option>
                      <option value="processing" selected>Sedang Diproses</option>
                      <option value="completed">Selesai</option>
                    </select>
                  </td>
                  <td>
                    <select class="priority-select" data-ticket="#TKT-2023-06-08-008" onchange="updateTicketPriority(this)">
                      <option value="low">Rendah</option>
                      <option value="medium" selected>Sedang</option>
                      <option value="high">Tinggi</option>
                    </select>
                  </td>
                  <td>08 Jun 2023</td>
                  <td>
                    <a href="{{ route('support.tickets.detail', ['id' => 'TKT-2023-06-08-008']) }}" class="btn btn-outline">Lihat</a>
                  </td>
                </tr>
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
    
    // Filter functionality
    const statusFilter = document.getElementById('statusFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const searchFilter = document.getElementById('searchFilter');
    
    statusFilter.addEventListener('change', filterTickets);
    priorityFilter.addEventListener('change', filterTickets);
    searchFilter.addEventListener('input', filterTickets);
    
    function filterTickets() {
      const status = statusFilter.value;
      const priority = priorityFilter.value;
      const search = searchFilter.value.toLowerCase();
      
      const rows = document.querySelectorAll('#ticketsTable tbody tr');
      
      rows.forEach(row => {
        // Extract text content from cells, considering the badge content
        const statusCell = row.cells[3].textContent.trim().toLowerCase();
        const priorityCell = row.cells[4].textContent.trim().toLowerCase();
        const ticketId = row.cells[0].textContent.trim().toLowerCase();
        const subject = row.cells[1].textContent.trim().toLowerCase();
        const sender = row.cells[2].textContent.trim().toLowerCase();
        
        let matches = true;
        
        // Status filter
        if (status) {
          let statusMatch = false;
          if (status === 'new' && statusCell.includes('baru')) statusMatch = true;
          if (status === 'processing' && statusCell.includes('sedang diproses')) statusMatch = true;
          if (status === 'completed' && statusCell.includes('selesai')) statusMatch = true;
          
          if (!statusMatch) matches = false;
        }
        
        // Priority filter
        if (priority && matches) {
          let priorityMatch = false;
          if (priority === 'low' && priorityCell.includes('rendah')) priorityMatch = true;
          if (priority === 'medium' && priorityCell.includes('sedang')) priorityMatch = true;
          if (priority === 'high' && priorityCell.includes('tinggi')) priorityMatch = true;
          
          if (!priorityMatch) matches = false;
        }
        
        // Search filter (applies to all columns)
        if (search && matches) {
          if (!ticketId.includes(search) && 
              !subject.includes(search) && 
              !sender.includes(search)) {
            matches = false;
          }
        }
        
        row.style.display = matches ? '' : 'none';
      });
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
      
      // In a real application, this would make an AJAX call to update the status on the server
      // For demonstration, we'll just show an alert
      console.log(`Status updated for ticket ${ticketId} to ${newStatus}`);
      
      // Update the class on the select element to reflect the new status color
      updateStatusSelectClass(selectElement, newStatus);
    }
    
    // Function to update ticket priority
    function updateTicketPriority(selectElement) {
      const ticketId = selectElement.getAttribute('data-ticket');
      const newPriority = selectElement.value;
      
      // In a real application, this would make an AJAX call to update the priority on the server
      // For demonstration, we'll just show an alert
      console.log(`Priority updated for ticket ${ticketId} to ${newPriority}`);
      
      // Update the class on the select element to reflect the new priority color
      updatePrioritySelectClass(selectElement, newPriority);
    }
    
    // Helper function to update status select styling
    function updateStatusSelectClass(selectElement, status) {
      // Remove all status classes
      selectElement.classList.remove('status-new', 'status-processing', 'status-completed');
      
      // Add the appropriate class based on status
      if (status === 'new') {
        selectElement.classList.add('status-new');
      } else if (status === 'processing') {
        selectElement.classList.add('status-processing');
      } else if (status === 'completed') {
        selectElement.classList.add('status-completed');
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
      
      statusSelects.forEach(select => {
        const selectedStatus = select.value;
        updateStatusSelectClass(select, selectedStatus);
      });
      
      prioritySelects.forEach(select => {
        const selectedPriority = select.value;
        updatePrioritySelectClass(select, selectedPriority);
      });
    });
  </script>
@endsection