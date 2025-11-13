@extends('layouts.app')

@section('title', 'Detail Tiket #' . $ticket->id)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customer.tickets') }}">Tiket Saya</a></li>
                        <li class="breadcrumb-item active">Detail Tiket #{{ $ticket->id }}</li>
                    </ol>
                </div>
                <h4 class="page-title">Detail Tiket #{{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</h4>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="header-title">{{ $ticket->subject }}</h4>
                            <p class="text-muted">
                                Dikirim pada <strong>{{ $ticket->created_at->format('d M Y H:i') }}</strong>
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

            <!-- Ticket Details -->
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Detail Tiket</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td width="30%"><strong>ID Tiket</strong></td>
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
                                    <td><strong>Penanggung Jawab</strong></td>
                                    <td>{{ $ticket->assignee->name ?? 'Belum Ditugaskan' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dikirim Pada</strong></td>
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

            <!-- Contact Support Button -->
            <div class="text-center mt-4">
                <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                    Buat Tiket Baru
                </a>
                <a href="{{ route('customer.tickets') }}" class="btn btn-secondary">
                    Kembali ke Tiket Saya
                </a>
            </div>
        </div>
    </div>
</div>
@endsection