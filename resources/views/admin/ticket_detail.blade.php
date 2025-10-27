@extends('layouts.app')

@section('title', 'Detail Tiket #'.str_pad($ticket->id, 6, '0', STR_PAD_LEFT))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('support.tickets') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar Tiket</a>
                </div>
                <h4 class="page-title">Detail Tiket #{{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <h4 class="card-title">{{ $ticket->subject }}</h4>
                            <div class="d-flex align-items-center mt-2">
                                <span class="badge 
                                    @if($ticket->category === 'technical') bg-info 
                                    @elseif($ticket->category === 'billing') bg-warning 
                                    @elseif($ticket->category === 'account') bg-primary 
                                    @elseif($ticket->category === 'product') bg-success 
                                    @else bg-secondary @endif
                                ms-1">
                                    {{ ucfirst($ticket->category) }}
                                </span>
                                <span class="badge 
                                    @if($ticket->priority === 'low') bg-secondary ms-1
                                    @elseif($ticket->priority === 'medium') bg-info ms-1
                                    @elseif($ticket->priority === 'high') bg-warning ms-1
                                    @else bg-danger ms-1 @endif">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown">
                                <i class="mdi mdi-dots-horizontal"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="javascript:void(0);" class="dropdown-item" onclick="updateTicketStatus({{ $ticket->id }})">Update Status</a>
                                <a href="javascript:void(0);" class="dropdown-item">Tambah Catatan</a>
                                <a href="javascript:void(0);" class="dropdown-item">Cetak Tiket</a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex mb-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($ticket->user->name ?? 'User') }}&background=random" alt="user-image" class="me-3 rounded-circle" width="40">
                            <div class="flex-grow-1">
                                <h5 class="m-0">{{ $ticket->user->name ?? 'N/A' }}</h5>
                                <p class="text-muted mb-0">{{ $ticket->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div>
                                <span class="badge 
                                    @if($ticket->status === 'open') bg-warning 
                                    @elseif($ticket->status === 'in_progress') bg-info 
                                    @elseif($ticket->status === 'resolved') bg-success 
                                    @else bg-dark @endif">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <p class="text-muted">
                            {{ $ticket->message }}
                        </p>
                    </div>

                    <!-- Resolution Notes -->
                    @if($ticket->resolution_notes)
                    <div class="mt-4 mb-4 p-3 bg-light rounded">
                        <h5 class="text-muted">Catatan Penyelesaian:</h5>
                        <p class="mb-0">{{ $ticket->resolution_notes }}</p>
                        <small class="text-muted">Diperbarui oleh: {{ $ticket->assignee->name ?? 'N/A' }} pada {{ $ticket->resolved_at ? $ticket->resolved_at->format('d M Y H:i') : '-' }}</small>
                    </div>
                    @endif

                    <!-- Chat/Messages Section -->
                    <div class="mt-4">
                        <h5 class="mb-3">Balasan dan Komentar</h5>
                        <div class="chat-conversation">
                            <ul class="conversation-list ps-0" id="messages-container">
                                <!-- Messages will be loaded here -->
                            </ul>
                        </div>

                        <div class="pt-3">
                            <form id="reply-form">
                                @csrf
                                <div class="row">
                                    <div class="col">
                                        <input type="text" id="message-input" class="form-control" placeholder="Tulis pesan balasan...">
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary">Kirim</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Detail Tiket</h5>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td><strong>ID Tiket</strong></td>
                                    <td>#{{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>
                                        <span class="badge 
                                            @if($ticket->status === 'open') bg-warning 
                                            @elseif($ticket->status === 'in_progress') bg-info 
                                            @elseif($ticket->status === 'resolved') bg-success 
                                            @else bg-dark @endif">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Prioritas</strong></td>
                                    <td>
                                        <span class="badge 
                                            @if($ticket->priority === 'low') bg-secondary 
                                            @elseif($ticket->priority === 'medium') bg-info 
                                            @elseif($ticket->priority === 'high') bg-warning 
                                            @else bg-danger @endif">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Kategori</strong></td>
                                    <td>
                                        <span class="badge 
                                            @if($ticket->category === 'technical') bg-info 
                                            @elseif($ticket->category === 'billing') bg-warning 
                                            @elseif($ticket->category === 'account') bg-primary 
                                            @elseif($ticket->category === 'product') bg-success 
                                            @else bg-secondary @endif">
                                            {{ ucfirst($ticket->category) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat</strong></td>
                                    <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Terakhir Diperbarui</strong></td>
                                    <td>{{ $ticket->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ditugaskan Ke</strong></td>
                                    <td>{{ $ticket->assignee->name ?? 'Belum Ditugaskan' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pemohon</strong></td>
                                    <td>{{ $ticket->user->name ?? 'N/A' }}</td>
                                </tr>
                                @if($ticket->resolved_at)
                                <tr>
                                    <td><strong>Selesai</strong></td>
                                    <td>{{ $ticket->resolved_at->format('d M Y H:i') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary btn-sm w-100" onclick="updateTicketStatus({{ $ticket->id }})">Update Status Tiket</button>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tindakan Lainnya</h5>
                    <div class="list-group list-group-flush">
                        <a href="javascript:void(0);" class="list-group-item list-group-item-action" onclick="closeTicket({{ $ticket->id }})">
                            <i class="mdi mdi-check-circle-outline me-2 text-success"></i> Tutup Tiket
                        </a>
                        <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                            <i class="mdi mdi-email-outline me-2 text-info"></i> Kirim Email ke Pemohon
                        </a>
                        <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                            <i class="mdi mdi-printer me-2 text-secondary"></i> Cetak Tiket
                        </a>
                        <a href="javascript:void(0);" class="list-group-item list-group-item-action">
                            <i class="mdi mdi-file-document-outline me-2 text-warning"></i> Export Tiket
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Status Tiket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm">
                    @csrf
                    @method('POST')
                    <div class="mb-3">
                        <label for="statusSelect" class="form-label">Status</label>
                        <select class="form-select" id="statusSelect" name="status">
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Dibuka</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Diselesaikan</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Ditutup</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assigneeSelect" class="form-label">Tugaskan Ke</label>
                        <select class="form-select" id="assigneeSelect" name="assignee_id">
                            <option value="">Tidak Ditugaskan</option>
                            <!-- Assignee options will be loaded via AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="resolutionNotes" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="resolutionNotes" name="resolution_notes" rows="3" placeholder="Tambahkan catatan penyelesaian...">{{ $ticket->resolution_notes }}</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitStatusUpdate()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentTicketId = {{ $ticket->id }};

    $(document).ready(function() {
        loadMessages();
        loadAssignees();
    });

    function loadMessages() {
        // In a real implementation, this would load messages from the API
        // For now, we'll simulate with a few sample replies
        const messagesHtml = `
            <li class="clearfix odd">
                <div class="chat-avatar">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($ticket->user->name ?? 'User') }}&background=blue" alt="user-image" class="rounded-circle">
                    <i>10 Apr 2023, 10:00</i>
                </div>
                <div class="conversation-text">
                    <div class="ctext-wrap">
                        <i>{{ $ticket->user->name ?? 'User' }}</i>
                        <p>{{ $ticket->message }}</p>
                    </div>
                </div>
            </li>
        `;
        
        $('#messages-container').html(messagesHtml);
    }

    function submitStatusUpdate() {
        const formData = {
            status: $('#statusSelect').val(),
            assignee_id: $('#assigneeSelect').val(),
            resolution_notes: $('#resolutionNotes').val(),
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'POST'
        };

        $.ajax({
            url: `/support/tickets/${currentTicketId}/update-status`,
            method: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    alert('Status tiket berhasil diperbarui');
                    $('#updateStatusModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat memperbarui status tiket');
            }
        });
    }

    function updateTicketStatus(ticketId) {
        currentTicketId = ticketId;
        $('#updateStatusModal').modal('show');
    }

    function closeTicket(ticketId) {
        if(confirm('Apakah Anda yakin ingin menutup tiket ini?')) {
            $.ajax({
                url: `/support/tickets/${ticketId}/update-status`,
                method: 'POST',
                data: {
                    status: 'closed',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.success) {
                        alert('Tiket berhasil ditutup');
                        location.reload();
                    }
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menutup tiket');
                }
            });
        }
    }

    function loadAssignees() {
        $.ajax({
            url: '/api/staff',
            method: 'GET',
            success: function(response) {
                let options = '<option value="">Tidak Ditugaskan</option>';
                response.staff.forEach(function(staff) {
                    let selected = {{ $ticket->assignee_id }} === staff.id ? 'selected' : '';
                    options += `<option value="${staff.id}" ${selected}>${staff.name}</option>`;
                });
                $('#assigneeSelect').html(options);
            },
            error: function(xhr) {
                console.error('Error loading staff:', xhr);
            }
        });
    }

    // Handle reply submission
    $('#reply-form').on('submit', function(e) {
        e.preventDefault();
        const message = $('#message-input').val();
        
        if(message.trim() === '') return;
        
        $.ajax({
            url: '/api/tickets/' + currentTicketId + '/replies',
            method: 'POST',
            data: {
                message: message,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#message-input').val('');
                loadMessages(); // Reload messages after sending
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat mengirim balasan');
            }
        });
    });
</script>
@endsection