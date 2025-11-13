@extends('layouts.app')

@section('title', 'Detail Tiket #' . $ticket->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('support.tickets') }}">Tiket Bantuan</a></li>
                        <li class="breadcrumb-item active">Detail Tiket #{{ $ticket->id }}</li>
                    </ol>
                </div>
                <h4 class="page-title">Detail Tiket #{{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="header-title">{{ $ticket->subject }}</h4>
                            <p class="text-muted">
                                Dibuat oleh <strong>{{ $ticket->user->name ?? 'N/A' }}</strong> 
                                pada {{ $ticket->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div>
                            @if($ticket->status === 'open')
                                <span class="badge bg-warning">Dibuka</span>
                            @elseif($ticket->status === 'in_progress')
                                <span class="badge bg-info">Dalam Proses</span>
                            @elseif($ticket->status === 'resolved')
                                <span class="badge bg-success">Diselesaikan</span>
                            @elseif($ticket->status === 'closed')
                                <span class="badge bg-secondary">Ditutup</span>
                            @endif
                        </div>
                    </div>

                    <div class="ticket-content mt-4">
                        <p class="mb-0">{{ $ticket->message }}</p>
                    </div>

                    @if($ticket->resolution_notes)
                    <div class="ticket-resolution mt-4 p-3 bg-light rounded">
                        <h5 class="text-success">Catatan Penyelesaian</h5>
                        <p class="mb-0">{{ $ticket->resolution_notes }}</p>
                        @if($ticket->resolved_at)
                        <small class="text-muted">Diselesaikan pada {{ $ticket->resolved_at->format('d M Y H:i') }}</small>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Update Status Form -->
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Update Status Tiket</h4>
                </div>
                <div class="card-body">
                    <form id="updateTicketForm" action="{{ route('support.tickets.update-status', $ticket->id) }}" method="POST">
                        @csrf
                        @method('POST')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Dibuka</option>
                                        <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                                        <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Diselesaikan</option>
                                        <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Ditutup</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Prioritas</label>
                                    <select class="form-select" id="priority" name="priority">
                                        <option value="low" {{ $ticket->priority == 'low' ? 'selected' : '' }}>Rendah</option>
                                        <option value="medium" {{ $ticket->priority == 'medium' ? 'selected' : '' }}>Sedang</option>
                                        <option value="high" {{ $ticket->priority == 'high' ? 'selected' : '' }}>Tinggi</option>
                                        <option value="urgent" {{ $ticket->priority == 'urgent' ? 'selected' : '' }}>Darurat</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="assignee_id" class="form-label">Staff Penanggung Jawab</label>
                            <select class="form-select" id="assignee_id" name="assignee_id">
                                <option value="">Pilih Staff</option>
                                @php
                                    $staffUsers = \App\Models\User::whereHas('role', function($query) {
                                        $query->whereIn('name', ['admin', 'staff', 'support']);
                                    })->get();
                                @endphp
                                @foreach($staffUsers as $staff)
                                    <option value="{{ $staff->id }}" {{ $ticket->assignee_id == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="resolution_notes" class="form-label">Catatan Penyelesaian (jika status menjadi 'Diselesaikan')</label>
                            <textarea class="form-control" id="resolution_notes" name="resolution_notes" rows="3" placeholder="Tambahkan catatan penyelesaian jika diperlukan...">{{ $ticket->resolution_notes ?? '' }}</textarea>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Update Tiket</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Detail Tiket</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td width="40%"><strong>ID Tiket</strong></td>
                                    <td>#{{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kategori</strong></td>
                                    <td>
                                        @if($ticket->category === 'technical')
                                            <span class="badge bg-info">Teknis</span>
                                        @elseif($ticket->category === 'billing')
                                            <span class="badge bg-warning">Penagihan</span>
                                        @elseif($ticket->category === 'account')
                                            <span class="badge bg-primary">Akun</span>
                                        @elseif($ticket->category === 'product')
                                            <span class="badge bg-success">Produk</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($ticket->category) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Prioritas</strong></td>
                                    <td>
                                        @if($ticket->priority === 'low')
                                            <span class="badge bg-secondary">Rendah</span>
                                        @elseif($ticket->priority === 'medium')
                                            <span class="badge bg-info">Sedang</span>
                                        @elseif($ticket->priority === 'high')
                                            <span class="badge bg-warning">Tinggi</span>
                                        @elseif($ticket->priority === 'urgent')
                                            <span class="badge bg-danger">Darurat</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>
                                        @if($ticket->status === 'open')
                                            <span class="badge bg-warning">Dibuka</span>
                                        @elseif($ticket->status === 'in_progress')
                                            <span class="badge bg-info">Dalam Proses</span>
                                        @elseif($ticket->status === 'resolved')
                                            <span class="badge bg-success">Diselesaikan</span>
                                        @elseif($ticket->status === 'closed')
                                            <span class="badge bg-secondary">Ditutup</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Pembuat</strong></td>
                                    <td>{{ $ticket->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Penanggung Jawab</strong></td>
                                    <td>{{ $ticket->assignee->name ?? 'Belum Ditugaskan' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Pada</strong></td>
                                    <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                @if($ticket->resolved_at)
                                <tr>
                                    <td><strong>Selesai Pada</strong></td>
                                    <td>{{ $ticket->resolved_at->format('d M Y H:i') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Tickets -->
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Tiket Terkait</h4>
                    
                    <div class="timeline">
                        @php
                            $relatedTickets = \App\Models\Ticket::where('user_id', $ticket->user_id)
                                ->where('id', '!=', $ticket->id)
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        
                        @forelse($relatedTickets as $relatedTicket)
                            <div class="timeline-item">
                                <div class="timeline-item-info">
                                    <a href="{{ route('support.tickets.detail', $relatedTicket->id) }}" class="text-body fw-bold">
                                        #{{ str_pad($relatedTicket->id, 6, '0', STR_PAD_LEFT) }}
                                    </a>
                                    <p class="text-muted mb-0">{{ Str::limit($relatedTicket->subject, 40) }}</p>
                                    <small class="text-muted">{{ $relatedTicket->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">Tidak ada tiket terkait dari pengguna ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('updateTicketForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(form);
            
            // Send AJAX request
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert('Tiket berhasil diperbarui!');
                    // Optionally reload the page to see updated information
                    location.reload();
                } else {
                    alert('Gagal memperbarui tiket: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memperbarui tiket.');
            });
        });
    }
});
</script>
@endsection