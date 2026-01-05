@extends('layouts.app')

@section('title', 'Tiket Bantuan Saya')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer/tickets/index.css') }}">
@endpush

@section('content')
<div class="tickets-container">
    <div class="tickets-header">
        <h2 class="tickets-title">Tiket Bantuan Saya</h2>
        <a href="{{ route('tickets.create') }}" class="create-ticket-btn">
            <i class="bi bi-plus-circle"></i>
            Buat Tiket Baru
        </a>
    </div>

    @if($tickets->count() > 0)
        <div class="tickets-grid">
            @foreach($tickets as $ticket)
                <div class="ticket-card">
                    <div class="ticket-header">
                        <div class="ticket-id">#{{ str_pad($ticket->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div class="ticket-date">{{ $ticket->created_at->format('d M Y H:i') }}</div>
                    </div>

                    <h3 class="ticket-subject">{{ $ticket->subject }}</h3>

                    <div class="ticket-meta">
                        <span class="ticket-category category-{{ $ticket->category }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->category)) }}
                        </span>
                        <span class="ticket-priority priority-{{ $ticket->priority }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                        <span class="ticket-status status-{{ $ticket->status }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </div>

                    <div class="ticket-actions">
                        <a href="{{ route('customer.tickets.detail', $ticket->id) }}" class="btn-view-ticket">
                            <i class="bi bi-eye"></i>
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination-container">
            {{ $tickets->links() }}
        </div>
    @else
        <div class="no-tickets">
            <div class="no-tickets-icon">
                <i class="bi bi-ticket-detailed"></i>
            </div>
            <h3 class="no-tickets-title">Anda Belum Memiliki Tiket</h3>
            <p class="no-tickets-text">Tiket bantuan yang Anda buat akan muncul di sini</p>
            <a href="{{ route('tickets.create') }}" class="create-ticket-btn">
                <i class="bi bi-plus-circle"></i>
                Buat Tiket Pertama
            </a>
        </div>
    @endif
</div>
@endsection
