@extends('layouts.app')

@section('title', 'Tiket Bantuan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tiket Bantuan</li>
                    </ol>
                </div>
                <h4 class="page-title">Tiket Bantuan</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <a href="#" class="btn btn-danger mb-2"><i class="mdi mdi-plus-circle me-2"></i> Tambah Tiket Baru</a>
                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end">
                                <div class="d-lg-flex">
                                    <div class="me-2">
                                        <select class="form-select" id="filterCategory">
                                            <option value="">Semua Kategori</option>
                                            <option value="technical">Teknis</option>
                                            <option value="billing">Penagihan</option>
                                            <option value="account">Akun</option>
                                            <option value="product">Produk</option>
                                            <option value="other">Lainnya</option>
                                        </select>
                                    </div>
                                    <div class="me-2">
                                        <select class="form-select" id="filterStatus">
                                            <option value="">Semua Status</option>
                                            <option value="open">Dibuka</option>
                                            <option value="in_progress">Sedang Diproses</option>
                                            <option value="resolved">Diselesaikan</option>
                                            <option value="closed">Ditutup</option>
                                        </select>
                                    </div>
                                    <div class="me-2">
                                        <select class="form-select" id="filterPriority">
                                            <option value="">Semua Prioritas</option>
                                            <option value="low">Rendah</option>
                                            <option value="medium">Menengah</option>
                                            <option value="high">Tinggi</option>
                                            <option value="urgent">Darurat</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Tiket</th>
                                    <th>Subjek</th>
                                    <th>Pemohon</th>
                                    <th>Kategori</th>
                                    <th>Prioritas</th>
                                    <th>Status</th>
                                    <th>Penugasan</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tickets-table-body">
                                @forelse($tickets as $ticket)
                                <tr>
                                    <td>
                                        <a href="{{ route('support.tickets.detail', $ticket->id) }}" class="text-body fw-bold">#{{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</a>
                                    </td>
                                    <td>
                                        <h5 class="m-0 fw-normal">{{ $ticket->subject }}</h5>
                                    </td>
                                    <td>
                                        {{ $ticket->user->name ?? 'N/A' }}
                                    </td>
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
                                    <td>
                                        <span class="badge 
                                            @if($ticket->priority === 'low') bg-secondary 
                                            @elseif($ticket->priority === 'medium') bg-info 
                                            @elseif($ticket->priority === 'high') bg-warning 
                                            @else bg-danger @endif">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($ticket->status === 'open') bg-warning 
                                            @elseif($ticket->status === 'in_progress') bg-info 
                                            @elseif($ticket->status === 'resolved') bg-success 
                                            @else bg-dark @endif">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $ticket->assignee->name ?? 'Belum Ditugaskan' }}
                                    </td>
                                    <td>
                                        {{ $ticket->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown">
                                                <i class="mdi mdi-dots-horizontal"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ route('support.tickets.detail', $ticket->id) }}" class="dropdown-item">Lihat Detail</a>
                                                <a href="#" class="dropdown-item" onclick="updateTicketStatus({{ $ticket->id }})">Update Status</a>
                                                <a href="#" class="dropdown-item">Balas Tiket</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Belum ada tiket bantuan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="float-end">
                                {{ $tickets->links() }}
                            </div>
                        </div>
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
                    @method('PUT')
                    <div class="mb-3">
                        <label for="statusSelect" class="form-label">Status</label>
                        <select class="form-select" id="statusSelect" name="status">
                            <option value="open">Dibuka</option>
                            <option value="in_progress">Sedang Diproses</option>
                            <option value="resolved">Diselesaikan</option>
                            <option value="closed">Ditutup</option>
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
                        <textarea class="form-control" id="resolutionNotes" name="resolution_notes" rows="3" placeholder="Tambahkan catatan penyelesaian..."></textarea>
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
    let currentTicketId = null;

    function updateTicketStatus(ticketId) {
        currentTicketId = ticketId;
        $('#updateStatusModal').modal('show');
    }

    function submitStatusUpdate() {
        if (!currentTicketId) return;
        
        const formData = {
            status: $('#statusSelect').val(),
            assignee_id: $('#assigneeSelect').val(),
            resolution_notes: $('#resolutionNotes').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
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

    // Load staff/assignees when modal opens
    $('#updateStatusModal').on('shown.bs.modal', function() {
        loadAssignees();
    });

    function loadAssignees() {
        $.ajax({
            url: '/api/staff',
            method: 'GET',
            success: function(response) {
                let options = '<option value="">Tidak Ditugaskan</option>';
                response.staff.forEach(function(staff) {
                    options += `<option value="${staff.id}">${staff.name}</option>`;
                });
                $('#assigneeSelect').html(options);
            },
            error: function(xhr) {
                console.error('Error loading staff:', xhr);
            }
        });
    }
</script>
@endsection