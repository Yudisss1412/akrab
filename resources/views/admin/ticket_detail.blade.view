@extends('layouts.admin')

@section('title', 'Detail Tiket Bantuan - Tidak Bisa Melakukan Penarikan Dana - #12345 - AKRAB')

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
    
    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 1rem;
      border: 1px solid var(--border);
      border-radius: 8px;
      text-decoration: none;
      color: var(--text);
      font-weight: 500;
    }
    
    .back-btn:hover {
      background-color: var(--bg);
    }
    
    .ticket-container {
      display: grid;
      grid-template-columns: 70% 30%;
      gap: 1.5rem;
      height: 100%;
    }
    
    .main-content {
      background: white;
      border-radius: var(--ak-radius);
      border: 1px solid var(--ak-border);
      box-shadow: 0 8px 20px rgba(0,0,0,.05);
      overflow: hidden;
    }
    
    .ticket-header {
      padding: 1.5rem;
      border-bottom: 1px solid var(--ak-border);
      background: linear-gradient(135deg, #006E5C 0%, #a8d5c9 100%);
      color: white;
    }
    
    .ticket-info {
      padding: 1.5rem;
      border-bottom: 1px solid var(--ak-border);
      background: var(--bg);
    }
    
    .info-row {
      display: flex;
      margin-bottom: 0.5rem;
      flex-wrap: wrap;
    }
    
    .info-label {
      font-weight: 600;
      width: 150px;
      color: var(--muted);
      flex-shrink: 0;
    }
    
    .info-value {
      flex: 1;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .user-link {
      color: var(--primary);
      text-decoration: none;
    }
    
    .user-link:hover {
      text-decoration: underline;
    }
    
    .status-priority {
      display: flex;
      gap: 1rem;
      margin-top: 1rem;
    }
    
    .status-select, .priority-select {
      padding: 0.5rem;
      border: 1px solid var(--border);
      border-radius: 6px;
      background: white;
      font-size: 0.9rem;
    }
    
    .chat-container {
      height: 400px;
      overflow-y: auto;
      padding: 1.5rem;
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
      margin-top: 0.25rem;
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
    
    .sidebar {
      background: white;
      border-radius: var(--ak-radius);
      border: 1px solid var(--ak-border);
      box-shadow: 0 8px 20px rgba(0,0,0,.05);
      height: fit-content;
    }
    
    .sidebar-section {
      padding: 1.5rem;
      border-bottom: 1px solid var(--ak-border);
    }
    
    .sidebar-section:last-child {
      border-bottom: none;
    }
    
    .sidebar-title {
      font-size: 1rem;
      font-weight: 600;
      color: var(--text);
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid var(--ak-border);
    }
    
    .action-buttons {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }
    
    .btn-action {
      padding: 0.75rem;
      border: 1px solid var(--border);
      border-radius: 8px;
      background: var(--white);
      cursor: pointer;
      font-weight: 500;
      text-align: center;
    }
    
    .btn-primary {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
    }
    
    .ticket-log {
      list-style: none;
      padding: 0;
    }
    
    .log-item {
      margin-bottom: 1rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--ak-border);
    }
    
    .log-item:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }
    
    .log-time {
      font-size: 0.8rem;
      color: var(--muted);
      margin-bottom: 0.25rem;
    }
    
    .log-message {
      font-size: 0.9rem;
    }
    
    .attachment-area {
      border: 2px dashed var(--border);
      border-radius: 8px;
      padding: 1rem;
      text-align: center;
      margin-bottom: 1rem;
      background: var(--bg);
    }
    
    .canned-responses {
      margin-top: 1rem;
    }
    
    .canned-responses label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--text);
    }
    
    .canned-responses select {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid var(--border);
      border-radius: 6px;
    }
    
    .rich-editor {
      display: flex;
      flex-wrap: wrap;
      gap: 0.25rem;
      margin-bottom: 0.5rem;
    }
    
    .rich-btn {
      padding: 0.25rem 0.5rem;
      border: 1px solid var(--border);
      background: white;
      cursor: pointer;
      border-radius: 4px;
    }
    
    .rich-btn:hover {
      background: var(--bg);
    }
  </style>
@endpush

@section('content')
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content">
        <div class="page-header">
          <h1>Detail Tiket Bantuan</h1>
          <a href="{{ route('support.tickets') }}" class="back-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Kembali ke Daftar Tiket
          </a>
        </div>
        
        <div class="ticket-container">
          <!-- Main Content -->
          <div class="main-content">
            <div class="ticket-header">
              <h2 style="margin: 0; font-size: 1.5rem;">Tidak Bisa Melakukan Penarikan Dana - #12345</h2>
              <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                <div class="status-badge status-new">Baru</div>
                <div class="priority-badge priority-high">Tinggi</div>
              </div>
            </div>
            
            <div class="ticket-info">
              <div class="info-row">
                <div class="info-label">Tanggal Dibuat:</div>
                <div class="info-value">28 September 2023, 14:30 WIB</div>
              </div>
              <div class="info-row">
                <div class="info-label">Terakhir Diperbarui:</div>
                <div class="info-value">28 September 2023, 14:30 WIB</div>
              </div>
              <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                  <select class="status-select">
                    <option value="new" selected>Baru</option>
                    <option value="processing">Sedang Diproses</option>
                    <option value="completed">Selesai</option>
                  </select>
                </div>
              </div>
              <div class="info-row">
                <div class="info-label">Prioritas:</div>
                <div class="info-value">
                  <select class="priority-select">
                    <option value="low">Rendah</option>
                    <option value="medium">Sedang</option>
                    <option value="high" selected>Tinggi</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div class="chat-container" id="chatContainer">
              <div class="message user">
                <div class="message-sender">Warung Online (Penjual)</div>
                <div class="message-text">Saya tidak bisa melakukan penarikan dana dari akun saya. Setiap kali saya mencoba menarik dana, muncul pesan error bahwa saldo tidak mencukupi, padahal saya melihat saldo saya cukup.</div>
                <div class="message-time">28 Sep 2023, 14:32 WIB</div>
              </div>
              <div class="message admin">
                <div class="message-sender">Tim Bantuan AKRAB</div>
                <div class="message-text">Terima kasih telah menghubungi kami. Kami akan mengecek akun Anda dan segera memberikan solusi.</div>
                <div class="message-time">28 Sep 2023, 14:35 WIB</div>
              </div>
            </div>
            
            <div class="reply-form">
              <div class="rich-editor">
                <button class="rich-btn" title="Tebal">B</button>
                <button class="rich-btn" title="Miring">I</button>
                <button class="rich-btn" title="Garis Bawah">U</button>
                <button class="rich-btn" title="Daftar">•</button>
                <button class="rich-btn" title="Daftar Angka">1.</button>
                <button class="rich-btn" title="Link">🔗</button>
              </div>
              
              <div class="form-group">
                <label for="replyMessage">Balasan Publik</label>
                <textarea id="replyMessage" class="form-control" placeholder="Tulis balasan untuk pengguna..."></textarea>
              </div>
              
              <div class="attachment-area">
                <p style="margin: 0.5rem 0;">Tarik & lepas file atau <a href="#" onclick="document.getElementById('fileInput').click(); return false;">pilih file</a></p>
                <input type="file" id="fileInput" style="display: none;" multiple>
                <div style="font-size: 0.8rem; color: var(--muted);">Mendukung gambar, PDF, DOC (maks. 10MB)</div>
              </div>
              
              <div class="canned-responses">
                <label for="cannedResponse">Balasan Siap Pakai</label>
                <select id="cannedResponse">
                  <option value="">Pilih balasan siap pakai...</option>
                  <option value="1">Terima kasih atas laporan Anda. Kami sedang mengecek masalahnya.</option>
                  <option value="2">Kami telah menyelesaikan masalahnya. Silakan coba kembali.</option>
                  <option value="3">Mohon maaf atas ketidaknyamanannya. Kami akan segera menindaklanjuti.</option>
                </select>
              </div>
              
              <button class="btn-reply" onclick="sendReply()">Kirim Balasan</button>
            </div>
          </div>
          
          <!-- Sidebar -->
          <div class="sidebar">
            <div class="sidebar-section">
              <h3 class="sidebar-title">Informasi Pengguna</h3>
              <div class="info-row">
                <div class="info-label">Nama:</div>
                <div class="info-value">Warung Online</div>
              </div>
              <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">warungonline@example.com</div>
              </div>
              <div class="info-row">
                <div class="info-label">ID Penjual:</div>
                <div class="info-value">SLR-001234</div>
              </div>
              <div class="info-row" style="margin-top: 1rem;">
                <a href="#" class="user-link">Lihat Profil Penjual</a>
              </div>
              <div class="info-row">
                <a href="#" class="user-link">Lihat Riwayat Transaksi</a>
              </div>
            </div>
            
            <div class="sidebar-section">
              <h3 class="sidebar-title">Aksi Tiket</h3>
              <div class="action-buttons">
                <button class="btn-action btn-primary">Kirim Balasan</button>
                <button class="btn-action">Tambahkan Catatan Internal</button>
                <button class="btn-action">Tandai Sebagai Penipuan</button>
                <button class="btn-action">Tutup Tiket</button>
              </div>
            </div>
            
            <div class="sidebar-section">
              <h3 class="sidebar-title">Riwayat Tiket</h3>
              <ul class="ticket-log">
                <li class="log-item">
                  <div class="log-time">28 Sep 14:30 WIB</div>
                  <div class="log-message">Tiket dibuat oleh Warung Online</div>
                </li>
                <li class="log-item">
                  <div class="log-time">28 Sep 14:32 WIB</div>
                  <div class="log-message">Status diubah dari Baru menjadi Sedang Diproses oleh Admin</div>
                </li>
                <li class="log-item">
                  <div class="log-time">28 Sep 14:35 WIB</div>
                  <div class="log-message">Balasan dikirim oleh Tim Bantuan</div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <script>
    // Function to send reply
    function sendReply() {
      const replyText = document.getElementById('replyMessage').value;
      if (!replyText.trim()) {
        alert('Silakan tulis balasan terlebih dahulu.');
        return;
      }
      
      // Add the reply to the conversation
      const chatContainer = document.getElementById('chatContainer');
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
      
      chatContainer.appendChild(replyDiv);
      
      // Clear the reply textbox
      document.getElementById('replyMessage').value = '';
      
      // Scroll to the bottom
      chatContainer.scrollTop = chatContainer.scrollHeight;
    }
    
    // Function for canned responses
    document.getElementById('cannedResponse').addEventListener('change', function() {
      const selectedResponse = this.value;
      const replyText = document.getElementById('replyMessage');
      
      if (selectedResponse === '1') {
        replyText.value = 'Terima kasih atas laporan Anda. Kami sedang mengecek masalahnya.';
      } else if (selectedResponse === '2') {
        replyText.value = 'Kami telah menyelesaikan masalahnya. Silakan coba kembali.';
      } else if (selectedResponse === '3') {
        replyText.value = 'Mohon maaf atas ketidaknyamanannya. Kami akan segera menindaklanjuti.';
      }
    });
    
    // Function to handle rich text editor buttons (simplified)
    const richButtons = document.querySelectorAll('.rich-btn');
    richButtons.forEach(button => {
      button.addEventListener('click', function() {
        const textarea = document.getElementById('replyMessage');
        const buttonTitle = this.getAttribute('title');
        
        switch(buttonTitle) {
          case 'Tebal':
            insertAtCursor(textarea, '**Teks Tebal**');
            break;
          case 'Link':
            insertAtCursor(textarea, '[Teks Link](url)');
            break;
          case 'Daftar':
            insertAtCursor(textarea, '- ');
            break;
        }
      });
    });
    
    // Helper function to insert text at cursor position
    function insertAtCursor(myField, myValue) {
      if (document.selection) {
        myField.focus();
        const sel = document.selection.createRange();
        sel.text = myValue;
      } else if (myField.selectionStart || myField.selectionStart === '0') {
        const startPos = myField.selectionStart;
        const endPos = myField.selectionEnd;
        const before = myField.value.substring(0, startPos);
        const after = myField.value.substring(endPos, myField.value.length);
        
        myField.value = before + myValue + after;
        myField.selectionStart = startPos + myValue.length;
        myField.selectionEnd = startPos + myValue.length;
      } else {
        myField.value += myValue;
      }
    }
  </script>
@endsection