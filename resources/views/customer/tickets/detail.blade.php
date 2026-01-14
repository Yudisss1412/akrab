@extends('layouts.app')

@section('title', 'Detail Tiket #' . $ticket->id)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer/tickets/detail.css') }}">
<style>
.ticket-conversation-container {
    margin: 20px 0;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.conversation-title {
    margin-bottom: 15px;
    color: #495057;
    font-size: 1.2rem;
}

.ticket-conversation {
    max-height: 400px;
    overflow-y: auto;
    padding: 10px;
}

.message {
    margin-bottom: 15px;
    padding: 12px;
    border-radius: 8px;
    position: relative;
}

.user-message {
    background-color: #e3f2fd;
    border-left: 4px solid #2196f3;
    align-self: flex-start;
}

.admin-message {
    background-color: #f3e5f5;
    border-left: 4px solid #9c27b0;
    align-self: flex-end;
}

.message-sender {
    font-weight: 600;
    margin-bottom: 5px;
    color: #212529;
}

.message-text {
    margin-bottom: 5px;
    color: #495057;
    line-height: 1.5;
}

.message-time {
    font-size: 0.8rem;
    color: #6c757d;
    text-align: right;
}

.loading-messages {
    text-align: center;
    padding: 20px;
    color: #6c757d;
    font-style: italic;
}

.error-message {
    text-align: center;
    padding: 20px;
    color: #dc3545;
    font-weight: 500;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
    margin: 10px 0;
}

.no-messages {
    text-align: center;
    padding: 20px;
    color: #6c757d;
    font-style: italic;
}
</style>
@endpush

@section('content')
<div class="ticket-detail-container">
    <div class="ticket-breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="ticket-breadcrumb-divider">/</span>
        <a href="{{ route('customer.tickets') }}">Tiket Saya</a>
        <span class="ticket-breadcrumb-divider">/</span>
        <span>Detail Tiket #{{ $ticket->id }}</span>
    </div>

    <div class="ticket-header">
        <div class="ticket-header-top">
            <div class="ticket-info">
                <div class="ticket-id">#{{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</div>
                <h2 class="ticket-title">{{ $ticket->subject }}</h2>
                <div class="ticket-date">Dikirim pada {{ $ticket->created_at->format('d M Y H:i') }}</div>
            </div>

            <div class="ticket-status status-{{ $ticket->status }}">
                @if($ticket->status === 'open')
                    <i class="bi bi-exclamation-circle"></i> Dibuka
                @elseif($ticket->status === 'in_progress')
                    <i class="bi bi-hourglass-split"></i> Dalam Proses
                @elseif($ticket->status === 'resolved')
                    <i class="bi bi-check-circle"></i> Diselesaikan
                @elseif($ticket->status === 'closed')
                    <i class="bi bi-lock"></i> Ditutup
                @endif
            </div>
        </div>

        <div class="ticket-message">
            <p class="ticket-message-content">{{ $ticket->message }}</p>
        </div>

        @if($ticket->resolution_notes)
        <div class="ticket-resolution">
            <h4 class="resolution-title">
                <i class="bi bi-check-all"></i> Catatan Penyelesaian
            </h4>
            <p class="resolution-content">{{ $ticket->resolution_notes }}</p>
            @if($ticket->resolved_at)
            <p class="resolution-date">Diselesaikan pada {{ $ticket->resolved_at->format('d M Y H:i') }}</p>
            @endif
        </div>
        @endif
    </div>

    <!-- Chat container to display conversation -->
    <div class="ticket-conversation-container">
        <h3 class="conversation-title">Percakapan Tiket</h3>
        <div class="ticket-conversation" id="ticketConversation">
            <!-- Messages loaded server-side -->
            <div class="message user-message">
                <div class="message-sender">{{ $ticket->user->name ?? 'Anda' }} (Pengirim Tiket)</div>
                <div class="message-text">{{ $ticket->message }}</div>
                <div class="message-time">{{ $ticket->created_at->format('d M Y, H:i') }}</div>
            </div>

            @if($ticket->resolution_notes)
            <div class="message admin-message">
                <div class="message-sender">Catatan Penyelesaian</div>
                <div class="message-text">{{ $ticket->resolution_notes }}</div>
                <div class="message-time">{{ $ticket->resolved_at ? $ticket->resolved_at->format('d M Y, H:i') : $ticket->updated_at->format('d M Y, H:i') }}</div>
            </div>
            @endif

            @isset($ticket->replies)
                @foreach($ticket->replies as $reply)
                <div class="message {{ $reply->user->role->name === 'admin' || $reply->user->role->name === 'staff' || $reply->user->role->name === 'support' ? 'admin-message' : 'user-message' }}">
                    <div class="message-sender">{{ $reply->user->name }}</div>
                    <div class="message-text">{{ $reply->message }}</div>
                    <div class="message-time">{{ $reply->created_at->format('d M Y, H:i') }}</div>
                </div>
                @endforeach
            @endisset

            <!-- Reply form for customer -->
            @if($ticket->status !== 'closed' && $ticket->status !== 'resolved')
            <div class="reply-form-container" style="margin-top: 20px; padding: 20px; background-color: #f8f9fa; border-radius: 8px; border: 1px solid #e9ecef;">
                <h3 class="reply-title" style="margin-bottom: 15px; color: #495057;">Balas Tiket</h3>
                <form id="replyForm" method="POST" action="{{ route('api.tickets.replies', ['id' => $ticket->id]) }}">
                    @csrf
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="replyMessage" style="display: block; margin-bottom: 5px; font-weight: 600;">Pesan Balasan</label>
                        <textarea id="replyMessage" name="message" class="form-control" rows="4" placeholder="Tulis balasan Anda di sini..." style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="background-color: #006E5C; border-color: #006E5C; color: white; padding: 8px 16px; border-radius: 4px; cursor: pointer;">Kirim Balasan</button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <div class="ticket-details-card">
        <h3 class="ticket-details-title">Detail Tiket</h3>

        <div class="ticket-details-grid">
            <div class="detail-item">
                <span class="detail-label">ID Tiket</span>
                <span class="detail-value">#{{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Kategori</span>
                <span class="detail-value">
                    <span class="badge
                        @if($ticket->category === 'technical') category-technical
                        @elseif($ticket->category === 'billing') category-billing
                        @elseif($ticket->category === 'account') category-account
                        @elseif($ticket->category === 'product') category-product
                        @else category-other @endif">
                        {{ ucfirst(str_replace('_', ' ', $ticket->category)) }}
                    </span>
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Prioritas</span>
                <span class="detail-value">
                    <span class="badge
                        @if($ticket->priority === 'low') priority-low
                        @elseif($ticket->priority === 'medium') priority-medium
                        @elseif($ticket->priority === 'high') priority-high
                        @else priority-urgent @endif">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Status</span>
                <span class="detail-value">
                    <span class="badge
                        @if($ticket->status === 'open') status-open
                        @elseif($ticket->status === 'in_progress') bg-info
                        @elseif($ticket->status === 'resolved') status-resolved
                        @else status-closed @endif">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Penanggung Jawab</span>
                <span class="detail-value">{{ $ticket->assignee->name ?? 'Belum Ditugaskan' }}</span>
            </div>

            <div class="detail-item">
                <span class="detail-label">Dikirim Pada</span>
                <span class="detail-value">{{ $ticket->created_at->format('d M Y H:i') }}</span>
            </div>

            @if($ticket->resolved_at)
            <div class="detail-item">
                <span class="detail-label">Selesai Pada</span>
                <span class="detail-value">{{ $ticket->resolved_at->format('d M Y H:i') }}</span>
            </div>
            @endif
        </div>
    </div>

    <div class="actions-container">
        <a href="{{ route('tickets.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat Tiket Baru
        </a>
        <a href="{{ route('customer.tickets') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Tiket Saya
        </a>

@push('scripts')
<script>
    // Percakapan ditampilkan secara server-side, tidak perlu memuat dari API
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Percakapan tiket dimuat secara server-side');

        // Handle reply form submission
        const replyForm = document.getElementById('replyForm');
        if (replyForm) {
            replyForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const messageInput = document.getElementById('replyMessage');
                const message = messageInput.value.trim();

                if (!message) {
                    alert('Silakan masukkan pesan balasan terlebih dahulu.');
                    return;
                }

                const formData = new FormData();
                formData.append('message', message);

                try {
                    const response = await fetch(`/api/tickets/{{ $ticket->id }}/replies`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        // Tampilkan pesan sukses
                        alert(result.message || 'Balasan berhasil dikirim');

                        // Kosongkan form
                        messageInput.value = '';

                        // Refresh halaman untuk menampilkan balasan terbaru
                        location.reload();
                    } else {
                        alert('Gagal mengirim balasan: ' + (result.message || 'Terjadi kesalahan'));
                    }
                } catch (error) {
                    console.error('Error sending reply:', error);
                    alert('Terjadi kesalahan saat mengirim balasan. Silakan coba lagi.');
                }
            });
        }
    });
</script>
@endpush

@endsection
