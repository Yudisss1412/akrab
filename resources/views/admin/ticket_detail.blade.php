@extends('layouts.admin')

@section('title', 'Detail Tiket Bantuan - ' . $ticket->subject . ' - #' . str_pad($ticket->id, 6, '0', STR_PAD_LEFT) . ' - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/ticket_detail.css') }}">
@endpush

@section('content')
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content">
        <div class="page-header">
          <h1>Detail Tiket Bantuan</h1>
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
                  @if($ticket->status === 'open') Terbuka
                  @elseif($ticket->status === 'in_progress') Dalam Proses
                  @elseif($ticket->status === 'resolved') Diselesaikan
                  @elseif($ticket->status === 'closed') Ditutup
                  @else Terbuka
                  @endif
                </div>
                <div class="priority-badge
                  @if($ticket->priority === 'low') priority-low
                  @elseif($ticket->priority === 'medium') priority-medium
                  @elseif($ticket->priority === 'high') priority-high
                  @elseif($ticket->priority === 'urgent') priority-high
                  @else priority-low @endif">
                  @if($ticket->priority === 'low') Rendah
                  @elseif($ticket->priority === 'medium') Sedang
                  @elseif($ticket->priority === 'high') Tinggi
                  @elseif($ticket->priority === 'urgent') Darurat
                  @else Rendah
                  @endif
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
                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Terbuka</option>
                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
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
                    <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Sedang</option>
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
        </div>

        <!-- User info card -->
        <div class="card user-info-card">
          <div class="card-header">
            <h3 class="card-title">Informasi Pengguna</h3>
          </div>
          <div class="card-body">
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
              <a href="#" class="user-link" onclick="showUserProfileModal(); return false;">Lihat Profil Pengguna</a>
            </div>
          </div>
        </div>

        <!-- Ticket history card -->
        <div class="card ticket-history-card">
          <div class="card-header">
            <h3 class="card-title">Riwayat Tiket</h3>
          </div>
          <div class="card-body">
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

          // Convert to display text
          let displayStatus = newStatus;
          if(newStatus === 'open') displayStatus = 'Terbuka';
          else if(newStatus === 'in_progress') displayStatus = 'Dalam Proses';
          else if(newStatus === 'resolved') displayStatus = 'Diselesaikan';
          else if(newStatus === 'closed') displayStatus = 'Ditutup';

          statusBadge.textContent = displayStatus;

          // Change badge class based on status
          statusBadge.className = 'status-badge';
          if(newStatus === 'open' || newStatus === 'new') {
            statusBadge.classList.add('status-new');
          } else if(newStatus === 'in_progress' || newStatus === 'processing') {
            statusBadge.classList.add('status-processing');
          } else if(newStatus === 'resolved' || newStatus === 'closed' || newStatus === 'completed') {
            statusBadge.classList.add('status-completed');
          }

          // Notify other pages/tabs of the status change using localStorage
          const statusUpdate = {
            ticketId: ticketId,
            status: newStatus,
            priority: document.querySelector('.priority-select')?.value || null,
            timestamp: Date.now()
          };

          localStorage.setItem('ticketStatusUpdate', JSON.stringify(statusUpdate));

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
    async function updateTicketPriority(ticketId, newPriority) {
      try {
        const response = await fetch(`/support/tickets/${ticketId}/update-status`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            priority: newPriority
          })
        });

        const data = await response.json();

        if (data.success) {
          // Update the priority visually
          const priorityBadge = document.querySelector('.priority-badge');

          // Convert to display text
          let displayPriority = newPriority;
          if(newPriority === 'low') displayPriority = 'Rendah';
          else if(newPriority === 'medium') displayPriority = 'Sedang';
          else if(newPriority === 'high') displayPriority = 'Tinggi';
          else if(newPriority === 'urgent') displayPriority = 'Darurat';

          priorityBadge.textContent = displayPriority;

          // Change badge class based on priority
          priorityBadge.className = 'priority-badge';
          if(newPriority === 'low') {
            priorityBadge.classList.add('priority-low');
          } else if(newPriority === 'medium') {
            priorityBadge.classList.add('priority-medium');
          } else if(newPriority === 'high' || newPriority === 'urgent') {
            priorityBadge.classList.add('priority-high');
          }

          // Notify other pages/tabs of the priority change using localStorage
          const priorityUpdate = {
            ticketId: ticketId,
            status: document.querySelector('.status-select')?.value || null,
            priority: newPriority,
            timestamp: Date.now()
          };

          localStorage.setItem('ticketStatusUpdate', JSON.stringify(priorityUpdate));

          showNotification(data.message, 'success');
        } else {
          showNotification('Gagal memperbarui prioritas: ' + (data.message || 'Terjadi kesalahan'), 'error');
        }
      } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat memperbarui prioritas', 'error');
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

    // Function to show user profile modal
    async function showUserProfileModal() {
      // Remove any existing user profile modal
      const existingModal = document.getElementById('userProfileModal');
      if (existingModal) {
        existingModal.remove();
      }

      // Extract user ID from the user info card by finding the element with 'ID User' label
      const userCard = document.querySelector('.user-info-card');
      let userId = '0';

      // Find all info rows in the card
      const infoRows = userCard.querySelectorAll('.info-row');

      for (let row of infoRows) {
        const labelElement = row.querySelector('.info-label');
        const valueElement = row.querySelector('.info-value');

        if (labelElement && valueElement) {
          const labelText = labelElement.textContent.trim();
          if (labelText === 'ID User:') {
            const userIdText = valueElement.textContent;
            console.log('Raw userIdText:', userIdText);

            const match = userIdText.match(/USR-(\d+)/);
            if (match) {
              userId = match[1];
              console.log('Extracted userId from match:', userId);
              break;
            } else {
              // If the regex doesn't match, try to extract any number from the text
              const numberMatch = userIdText.match(/\d+/);
              if (numberMatch) {
                userId = numberMatch[0];
                console.log('Extracted userId from numberMatch:', userId);
                break;
              } else {
                console.log('Could not extract user ID from:', userIdText);
              }
            }
          }
        }
      }

      // Create loading modal first
      const loadingModalHtml = `
        <div id="userProfileModal" class="modal fade show" tabindex="-1" style="display: block; padding-right: 17px; background: rgba(0,0,0,0.5);">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Profil Pengguna</h5>
                <button type="button" class="btn-close" onclick="document.getElementById('userProfileModal').remove();"></button>
              </div>
              <div class="modal-body" style="text-align: center; padding: 2rem;">
                <div class="spinner-border" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <p style="margin-top: 1rem;">Memuat informasi pengguna...</p>
              </div>
            </div>
          </div>
        </div>
      `;

      // Add loading modal to document
      document.body.insertAdjacentHTML('beforeend', loadingModalHtml);

      // Log the API URL we're trying to call
      console.log('Fetching user profile from URL:', '/api/users/' + userId + '/profile');

      try {
        // Fetch user data from API
        const response = await fetch('/api/users/' + userId + '/profile');
        const data = await response.json();

        if (data.success) {
          const user = data.user;

          // Create modal HTML with real user data
          const modalHtml = `
            <div id="userProfileModal" class="modal fade show" tabindex="-1" style="display: block; padding-right: 17px; background: rgba(0,0,0,0.5);">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Profil Pengguna</h5>
                    <button type="button" class="btn-close" onclick="document.getElementById('userProfileModal').remove();"></button>
                  </div>
                  <div class="modal-body">
                    <div class="info-row" style="margin-bottom: 0.5rem;">
                      <div class="info-label" style="font-weight: 600; width: 150px; color: var(--muted); flex-shrink: 0;">Nama:</div>
                      <div class="info-value">` + user.name + `</div>
                    </div>
                    <div class="info-row" style="margin-bottom: 0.5rem;">
                      <div class="info-label" style="font-weight: 600; width: 150px; color: var(--muted); flex-shrink: 0;">Email:</div>
                      <div class="info-value">` + user.email + `</div>
                    </div>
                    <div class="info-row" style="margin-bottom: 0.5rem;">
                      <div class="info-label" style="font-weight: 600; width: 150px; color: var(--muted); flex-shrink: 0;">ID User:</div>
                      <div class="info-value">USR-` + String(user.id).padStart(6, '0') + `</div>
                    </div>
                    <div class="info-row" style="margin-bottom: 0.5rem;">
                      <div class="info-label" style="font-weight: 600; width: 150px; color: var(--muted); flex-shrink: 0;">Nomor Telepon:</div>
                      <div class="info-value">` + user.phone + `</div>
                    </div>
                    <div class="info-row" style="margin-bottom: 0.5rem;">
                      <div class="info-label" style="font-weight: 600; width: 150px; color: var(--muted); flex-shrink: 0;">Alamat:</div>
                      <div class="info-value">` + user.address + `</div>
                    </div>
                    <div class="info-row" style="margin-top: 1rem;">
                      <div class="info-label" style="font-weight: 600; width: 150px; color: var(--muted); flex-shrink: 0;">Tanggal Registrasi:</div>
                      <div class="info-value">` + user.created_at + `</div>
                    </div>
                    <div class="info-row" style="margin-top: 0.5rem;">
                      <div class="info-label" style="font-weight: 600; width: 150px; color: var(--muted); flex-shrink: 0;">Terakhir Login:</div>
                      <div class="info-value">` + user.last_login + `</div>
                    </div>
                    <div class="info-row" style="margin-top: 0.5rem;">
                      <div class="info-label" style="font-weight: 600; width: 150px; color: var(--muted); flex-shrink: 0;">Status Akun:</div>
                      <div class="info-value">` + user.status + `</div>
                    </div>
                    <div class="info-row" style="margin-top: 0.5rem;">
                      <div class="info-label" style="font-weight: 600; width: 150px; color: var(--muted); flex-shrink: 0;">Jumlah Pesanan:</div>
                      <div class="info-value">` + user.total_orders + `</div>
                    </div>
                    <div class="info-row" style="margin-top: 0.5rem;">
                      <div class="info-label" style="font-weight: 600; width: 150px; color: var(--muted); flex-shrink: 0;">Jumlah Tiket:</div>
                      <div class="info-value">` + user.total_tickets + `</div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-light" onclick="document.getElementById('userProfileModal').remove();">Tutup</button>
                  </div>
                </div>
              </div>
            </div>
          `;

          // Replace the loading modal with the populated modal
          document.getElementById('userProfileModal').outerHTML = modalHtml;
        } else {
          // Handle error case
          const errorModalHtml = `
            <div id="userProfileModal" class="modal fade show" tabindex="-1" style="display: block; padding-right: 17px; background: rgba(0,0,0,0.5);">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Profil Pengguna</h5>
                    <button type="button" class="btn-close" onclick="document.getElementById('userProfileModal').remove();"></button>
                  </div>
                  <div class="modal-body" style="text-align: center; padding: 2rem;">
                    <div class="alert alert-danger">Gagal memuat informasi pengguna: ` + (data.error || 'Unknown error') + `</div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-light" onclick="document.getElementById('userProfileModal').remove();">Tutup</button>
                  </div>
                </div>
              </div>
            </div>
          `;

          // Replace the loading modal with the error modal
          document.getElementById('userProfileModal').outerHTML = errorModalHtml;
        }
      } catch (error) {
        // Handle network error
        const errorModalHtml = `
          <div id="userProfileModal" class="modal fade show" tabindex="-1" style="display: block; padding-right: 17px; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Profil Pengguna</h5>
                  <button type="button" class="btn-close" onclick="document.getElementById('userProfileModal').remove();"></button>
                </div>
                <div class="modal-body" style="text-align: center; padding: 2rem;">
                  <div class="alert alert-danger">Terjadi kesalahan saat memuat informasi pengguna</div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-light" onclick="document.getElementById('userProfileModal').remove();">Tutup</button>
                </div>
              </div>
            </div>
          </div>
        `;

        // Replace the loading modal with the error modal
        document.getElementById('userProfileModal').outerHTML = errorModalHtml;

        console.error('Error fetching user profile:', error);
      }
    }
  </script>
@endsection
