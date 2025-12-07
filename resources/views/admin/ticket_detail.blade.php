@extends('layouts.admin')

@section('title', 'Detail Tiket Bantuan - ' . $ticket->subject . ' - #' . str_pad($ticket->id, 6, '0', STR_PAD_LEFT) . ' - AKRAB')

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

    .btn-delete-reply {
      background: #dc3545;
      color: white;
      border: none;
      border-radius: 4px;
      padding: 2px 6px;
      font-size: 0.7rem;
      cursor: pointer;
      margin-left: 0.5rem;
    }

    .btn-delete-reply:hover {
      background: #c82333;
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

    .btn-light {
      background-color: var(--bg);
      color: var(--text);
      border-color: var(--border);
    }

    .btn-light:hover {
      background-color: #e2e6ea; /* Bootstrap's default hover for btn-light */
    }

    .btn-danger {
      background-color: #dc3545;
      border-color: #dc3545;
      color: white;
    }

    .btn-danger:hover {
      background-color: #c82333;
      border-color: #bd2130;
    }

    .canned-responses {
      margin-bottom: 1rem; /* Add space below the canned responses section */
    }

    .canned-responses + .btn-reply {
      margin-top: 1rem; /* Add space above the reply button */
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
              <h2 style="margin: 0; font-size: 1.5rem;">{{ $ticket->subject }} - #{{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</h2>
              <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                <div class="status-badge 
                  @if($ticket->status === 'open') status-new
                  @elseif($ticket->status === 'in_progress') status-processing
                  @elseif($ticket->status === 'resolved') status-completed
                  @elseif($ticket->status === 'closed') status-completed
                  @else status-new @endif">
                  {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                </div>
                <div class="priority-badge 
                  @if($ticket->priority === 'low') priority-low
                  @elseif($ticket->priority === 'medium') priority-medium
                  @elseif($ticket->priority === 'high') priority-high
                  @else priority-high @endif">
                  {{ ucfirst($ticket->priority) }}
                </div>
              </div>
            </div>
            
            <div class="ticket-info">
              <div class="info-row">
                <div class="info-label">Tanggal Dibuat:</div>
                <div class="info-value">{{ $ticket->created_at->format('d M Y, H:i') }} WIB</div>
              </div>
              <div class="info-row">
                <div class="info-label">Terakhir Diperbarui:</div>
                <div class="info-value">{{ $ticket->updated_at->format('d M Y, H:i') }} WIB</div>
              </div>
              <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                  <select class="status-select" onchange="updateTicketStatus({{ $ticket->id }}, this.value)">
                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Dibuka</option>
                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Diselesaikan</option>
                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Ditutup</option>
                  </select>
                </div>
              </div>
              <div class="info-row">
                <div class="info-label">Prioritas:</div>
                <div class="info-value">
                  <select class="priority-select" onchange="updateTicketPriority({{ $ticket->id }}, this.value)">
                    <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Rendah</option>
                    <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Menengah</option>
                    <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>Tinggi</option>
                    <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Darurat</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div class="chat-container" id="chatContainer">
              <div class="message user">
                <div class="message-sender">{{ $ticket->user->name ?? 'User Tidak Dikenal' }} (Pengirim Tiket)</div>
                <div class="message-text">{{ $ticket->message }}</div>
                <div class="message-time">{{ $ticket->created_at->format('d M Y, H:i') }} WIB</div>
              </div>
              @if($ticket->resolution_notes)
              <div class="message admin">
                <div class="message-sender">Catatan Penyelesaian</div>
                <div class="message-text">{{ $ticket->resolution_notes }}</div>
                <div class="message-time">{{ $ticket->resolved_at ? $ticket->resolved_at->format('d M Y, H:i') : $ticket->updated_at->format('d M Y, H:i') }} WIB</div>
              </div>
              @endif
              
              <!-- Pesan dinamis akan dimuat melalui JavaScript -->
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

              <div style="margin-top: 1rem;"></div> <!-- Add spacing between dropdown and button -->
              <button class="btn-reply" onclick="sendReply()">Kirim Balasan</button>
            </div>
          </div>
          
          <!-- Sidebar -->
          <div class="sidebar">
            <div class="sidebar-section">
              <h3 class="sidebar-title">Informasi Pengguna</h3>
              <div class="info-row">
                <div class="info-label">Nama:</div>
                <div class="info-value">{{ $ticket->user->name ?? 'N/A' }}</div>
              </div>
              <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $ticket->user->email ?? 'N/A' }}</div>
              </div>
              <div class="info-row">
                <div class="info-label">ID User:</div>
                <div class="info-value">USR-{{ str_pad($ticket->user->id ?? 0, 6, '0', STR_PAD_LEFT) }}</div>
              </div>
              <div class="info-row" style="margin-top: 1rem;">
                <a href="#" class="user-link">Lihat Profil Pengguna</a>
              </div>
            </div>
            
            <div class="sidebar-section">
              <h3 class="sidebar-title">Aksi Tiket</h3>
              <div class="action-buttons">
                <button class="btn-action btn-primary" onclick="sendReply()">Kirim Balasan</button>
                <button class="btn-action" onclick="updateTicketStatus({{ $ticket->id }}, 'in_progress')">Tandai Sedang Diproses</button>
                <button class="btn-action" onclick="updateTicketStatus({{ $ticket->id }}, 'resolved')">Tandai Selesai</button>
                <button class="btn-action" onclick="updateTicketStatus({{ $ticket->id }}, 'closed')">Tutup Tiket</button>
              </div>
            </div>
            
            <div class="sidebar-section">
              <h3 class="sidebar-title">Riwayat Tiket</h3>
              <ul class="ticket-log">
                <li class="log-item">
                  <div class="log-time">{{ $ticket->created_at->format('d M H:i') }} WIB</div>
                  <div class="log-message">Tiket dibuat oleh {{ $ticket->user->name ?? 'User' }}</div>
                </li>
                @if($ticket->assignee)
                <li class="log-item">
                  <div class="log-time">{{ $ticket->updated_at->format('d M H:i') }} WIB</div>
                  <div class="log-message">Ditugaskan ke {{ $ticket->assignee->name ?? 'Staff' }}</div>
                </li>
                @endif
                @if($ticket->resolution_notes)
                <li class="log-item">
                  <div class="log-time">{{ $ticket->resolved_at ? $ticket->resolved_at->format('d M H:i') : $ticket->updated_at->format('d M H:i') }} WIB</div>
                  <div class="log-message">Catatan penyelesaian ditambahkan</div>
                </li>
                @endif
              </ul>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <script>
    // Load ticket messages from API when page loads
    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOM loaded, loading ticket messages for ID: {{ $ticket->id }}');
      loadTicketMessages({{ $ticket->id }});
    });

    // Function to load ticket messages
    async function loadTicketMessages(ticketId) {
      console.log('Loading messages for ticket ID:', ticketId);

      try {
        const response = await fetch(`/api/tickets/${ticketId}/messages`);
        console.log('API response status:', response.status);

        const data = await response.json();
        console.log('API response data:', data);

        const chatContainer = document.getElementById('chatContainer');
        // Clear chat container
        chatContainer.innerHTML = '';

        // Add all messages from the API
        if (data.messages && Array.isArray(data.messages)) {
          data.messages.forEach(message => {
            console.log('Processing message:', message);
            const messageDiv = document.createElement('div');

            // Determine message class based on the type of message
            if (message.is_ticket_message) {
              messageDiv.className = 'message user';
              messageDiv.innerHTML =
                '<div class="message-sender">' + (message.sender_name || 'User Tidak Dikenal') + ' (Pengirim Tiket)</div>' +
                '<div class="message-text">' + message.message + '</div>' +
                '<div class="message-time">' + message.created_at + '</div>';
            } else if (message.is_resolution_note) {
              messageDiv.className = 'message admin';
              messageDiv.innerHTML =
                '<div class="message-sender">Catatan Penyelesaian</div>' +
                '<div class="message-text">' + message.message + '</div>' +
                '<div class="message-time">' + message.created_at + '</div>';
            } else {
              // This is a reply
              // Check if sender has admin/staff/support role
              let isFromAdmin = false;
              if (message.sender_role) {
                isFromAdmin = ['admin', 'staff', 'support'].includes(message.sender_role);
              }
              messageDiv.className = 'message ' + (isFromAdmin ? 'admin' : 'user');

              // Extract reply ID from the message ID (format: 'reply-XXX')
              const replyId = message.id.replace('reply-', '');

              // Check if current user can delete this message
              const currentUserId = {{ Auth::id() }}; // Current logged in user ID
              const currentUserRole = '{{ Auth::user()->role->name ?? "" }}';
              const canDelete = (message.sender_id == currentUserId) || (['admin', 'staff', 'support'].includes(currentUserRole));

              messageDiv.innerHTML =
                '<div class="message-sender">' + message.sender_name + '</div>' +
                '<div class="message-text">' + message.message + '</div>' +
                '<div class="message-time">' + message.created_at +
                (canDelete ? ' <button class="btn-delete-reply" onclick="deleteReply(' + {{ $ticket->id }} + ', ' + replyId + ')">hapus</button>' : '') +
                '</div>';
            }

            chatContainer.appendChild(messageDiv);
            console.log('Added message to chat container:', message.sender_name, '-', message.message);
          });
        } else {
          console.error('Invalid messages data:', data);
        }

        // Scroll to the bottom
        chatContainer.scrollTop = chatContainer.scrollHeight;
      } catch (error) {
        console.error('Error loading messages:', error);
      }
    }
    
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
      }

      // Create notification container
      const notification = document.createElement('div');
      notification.className = 'notification';
      notification.innerHTML = `
        <div class="alert alert-${alertType} alert-dismissible fade show" role="alert">
          ${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      `;

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

    // Function to send reply
    async function sendReply() {
      const replyText = document.getElementById('replyMessage').value;
      if (!replyText.trim()) {
        showNotification('Silakan tulis balasan terlebih dahulu.', 'error');
        return;
      }

      console.log('Sending reply:', replyText);

      try {
        const response = await fetch(`/api/tickets/{{ $ticket->id }}/replies`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            message: replyText,
            is_internal_note: false  // Set to true if it's an internal note
          })
        });

        console.log('API response status for reply:', response.status);

        const data = await response.json();
        console.log('API response data for reply:', data);

        if (data.success) {
          // Add the new reply to the chat container
          const chatContainer = document.getElementById('chatContainer');

          const replyDiv = document.createElement('div');
          replyDiv.className = 'message admin';
          replyDiv.innerHTML =
            '<div class="message-sender">' + data.reply.sender_name + '</div>' +
            '<div class="message-text">' + data.reply.message + '</div>' +
            '<div class="message-time">' + data.reply.created_at + '</div>';

          // Add to the chat container
          chatContainer.appendChild(replyDiv);

          // Clear the reply textbox
          document.getElementById('replyMessage').value = '';

          // Scroll to the bottom
          chatContainer.scrollTop = chatContainer.scrollHeight;

          // Show success notification
          showNotification(data.message, 'success');

          // Reload messages to ensure latest reply is shown
          loadTicketMessages({{ $ticket->id }});
        } else {
          showNotification('Gagal mengirim balasan: ' + (data.message || 'Terjadi kesalahan'), 'error');
        }
      } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat mengirim balasan', 'error');
      }
    }
    
    // Function to update ticket status
    async function updateTicketStatus(ticketId, newStatus) {
      try {
        const response = await fetch(`/support/tickets/${ticketId}/update-status`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            status: newStatus
          })
        });

        const data = await response.json();

        if (data.success) {
          // Update the status visually
          const statusBadge = document.querySelector('.status-badge');
          statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);

          // Change badge class based on status
          statusBadge.className = 'status-badge';
          if(newStatus === 'open' || newStatus === 'new') {
            statusBadge.classList.add('status-new');
          } else if(newStatus === 'in_progress' || newStatus === 'processing') {
            statusBadge.classList.add('status-processing');
          } else if(newStatus === 'resolved' || newStatus === 'closed' || newStatus === 'completed') {
            statusBadge.classList.add('status-completed');
          }

          showNotification(data.message, 'success');
        } else {
          showNotification('Gagal memperbarui status: ' + (data.message || 'Terjadi kesalahan'), 'error');
        }
      } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat memperbarui status', 'error');
      }
    }
    
    // Function to update ticket priority
    function updateTicketPriority(ticketId, newPriority) {
      // In a real implementation, this would make an AJAX call to update the priority
      // For demo purposes, we'll just log the action
      console.log(`Prioritas tiket \${ticketId} diubah menjadi \${newPriority}`);
      
      // Update the priority visually
      const priorityBadge = document.querySelector('.priority-badge');
      priorityBadge.textContent = newPriority.charAt(0).toUpperCase() + newPriority.slice(1);
      
      // Change badge class based on priority
      priorityBadge.className = 'priority-badge';
      if(newPriority === 'low') {
        priorityBadge.classList.add('priority-low');
      } else if(newPriority === 'medium') {
        priorityBadge.classList.add('priority-medium');
      } else if(newPriority === 'high' || newPriority === 'urgent') {
        priorityBadge.classList.add('priority-high');
      }
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
    // Function to show confirmation modal
    function showConfirmModal(message, onConfirm) {
      // Remove any existing modal
      const existingModal = document.getElementById('confirmModal');
      if (existingModal) {
        existingModal.remove();
      }

      // Create modal HTML
      const modalHtml = `
        <div id="confirmModal" class="modal fade show" tabindex="-1" style="display: block; padding-right: 17px; background: rgba(0,0,0,0.5);">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <p>${message}</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-light" id="cancelBtn">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmBtn">Hapus</button>
              </div>
            </div>
          </div>
        </div>
      `;

      // Add modal to document
      document.body.insertAdjacentHTML('beforeend', modalHtml);

      // Add event listeners
      document.getElementById('confirmBtn').addEventListener('click', function() {
        onConfirm();
        document.getElementById('confirmModal').remove();
      });

      document.getElementById('cancelBtn').addEventListener('click', function() {
        document.getElementById('confirmModal').remove();
      });

      // Also handle close button
      document.querySelector('#confirmModal .btn-close').addEventListener('click', function() {
        document.getElementById('confirmModal').remove();
      });
    }

    // Function to delete a ticket reply
    async function deleteReply(ticketId, replyId) {
      showConfirmModal('Apakah Anda yakin ingin menghapus balasan ini?', async function() {
        try {
          const response = await fetch(`/api/tickets/${ticketId}/replies/${replyId}`, {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          });

          const data = await response.json();

          if (data.success) {
            // Reload messages to reflect the deletion
            loadTicketMessages(ticketId);
            showNotification(data.message, 'success');
          } else {
            showNotification('Gagal menghapus balasan: ' + (data.message || 'Terjadi kesalahan'), 'error');
          }
        } catch (error) {
          console.error('Error:', error);
          showNotification('Terjadi kesalahan saat menghapus balasan', 'error');
        }
      });
    }
  </script>
@endsection