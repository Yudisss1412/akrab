@extends('layouts.app')

@section('title', 'Detail Tiket #' . $ticket->id)

@push('styles')
<style>
    .ticket-detail-container {
        padding: 2rem 1rem;
        max-width: 900px;
        margin: 0 auto;
    }

    .ticket-breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
        color: #6c757d;
    }

    .ticket-breadcrumb a {
        color: var(--primary-color-dark, #006E5C);
        text-decoration: none;
    }

    .ticket-breadcrumb a:hover {
        text-decoration: underline;
    }

    .ticket-breadcrumb-divider {
        color: #ccc;
    }

    .ticket-header {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border-color, #e9ecef);
        padding: 1.75rem;
        margin-bottom: 1.5rem;
    }

    .ticket-header-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .ticket-info {
        flex: 1;
    }

    .ticket-id {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 600;
        background: #f8f9fa;
        padding: 0.3rem 0.8rem;
        border-radius: 6px;
        display: inline-block;
        margin-bottom: 0.5rem;
    }

    .ticket-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text, #333);
        margin: 0 0 0.75rem 0;
        line-height: 1.3;
    }

    .ticket-date {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .ticket-status {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-open { background: #fff3e0; color: #ef6c00; }
    .status-in_progress { background: #e3f2fd; color: #1565c0; }
    .status-resolved { background: #e8f5e8; color: #2e7d32; }
    .status-closed { background: #f5f5f5; color: #424242; }

    .ticket-message {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
        border-left: 4px solid var(--primary-color-dark, #006E5C);
    }

    .ticket-message-content {
        font-size: 1.1rem;
        line-height: 1.6;
        color: #444;
        margin: 0;
    }

    .ticket-resolution {
        background: #e8f5e8;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        border-left: 4px solid #2e7d32;
    }

    .resolution-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2e7d32;
        margin: 0 0 1rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .resolution-content {
        font-size: 1rem;
        line-height: 1.6;
        color: #2e7d32;
        margin: 0 0 1rem 0;
    }

    .resolution-date {
        font-size: 0.9rem;
        color: #6c757d;
        margin: 0;
    }

    .ticket-details-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border-color, #e9ecef);
        padding: 1.75rem;
        margin-bottom: 1.5rem;
    }

    .ticket-details-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text, #333);
        margin: 0 0 1.5rem 0;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-color, #e9ecef);
    }

    .ticket-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .detail-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .detail-value {
        font-size: 1rem;
        color: var(--text, #333);
        font-weight: 500;
    }

    .badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
    }

    .category-technical { background: #e0f7fa; color: #006064; }
    .category-billing { background: #fff8e1; color: #e65100; }
    .category-account { background: #e3f2fd; color: #1565c0; }
    .category-product { background: #e8f5e8; color: #2e7d32; }
    .category-other { background: #f5f5f5; color: #424242; }

    .priority-low { background: #e0e0e0; color: #424242; }
    .priority-medium { background: #e3f2fd; color: #1565c0; }
    .priority-high { background: #fff3e0; color: #ef6c00; }
    .priority-urgent { background: #ffebee; color: #c62828; }

    .actions-container {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }

    .btn {
        padding: 0.8rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.2s ease;
        gap: 0.5rem;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    .btn-primary {
        background: var(--primary-color-dark, #006E5C);
        color: white;
    }

    .btn-primary:hover {
        background: #005a4a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 110, 92, 0.25);
    }

    @media (max-width: 768px) {
        .ticket-detail-container {
            padding: 1rem;
        }

        .ticket-header {
            padding: 1.25rem;
        }

        .ticket-header-top {
            flex-direction: column;
            align-items: stretch;
        }

        .ticket-title {
            font-size: 1.3rem;
        }

        .ticket-details-grid {
            grid-template-columns: 1fr;
        }

        .actions-container {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
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