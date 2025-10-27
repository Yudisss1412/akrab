@extends('layouts.app')

@section('title', 'Tiket Bantuan Saya')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('tickets.create') }}" class="btn btn-primary">Buat Tiket Baru</a>
                </div>
                <h4 class="page-title">Tiket Bantuan Saya</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Tiket</th>
                                    <th>Subjek</th>
                                    <th>Kategori</th>
                                    <th>Prioritas</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                <tr>
                                    <td>
                                        <a href="{{ route('support.tickets.detail', $ticket->id) }}" class="text-body fw-bold">#{{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</a>
                                    </td>
                                    <td>
                                        <h5 class="m-0 fw-normal">{{ $ticket->subject }}</h5>
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
                                        {{ $ticket->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('support.tickets.detail', $ticket->id) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Anda belum memiliki tiket bantuan</td>
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
@endsection