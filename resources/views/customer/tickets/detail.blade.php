@extends('layouts.app')

@section('title', 'Detail Tiket #' . $ticket->id)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer/tickets/detail.css') }}">
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
    </div>
</div>
@endsection
