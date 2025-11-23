@extends('layouts.app')

@section('title', 'Tiket Bantuan Saya')

@push('styles')
<style>
    .tickets-container {
        padding: 2rem 1rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .tickets-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .tickets-title {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--primary-color-dark, #006E5C);
    }

    .create-ticket-btn {
        background: var(--primary-color-dark, #006E5C);
        color: white;
        border: none;
        padding: 0.8rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .create-ticket-btn:hover {
        background: #005a4a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 110, 92, 0.25);
    }

    .tickets-grid {
        display: grid;
        gap: 1.5rem;
    }

    .ticket-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border-color, #e9ecef);
        padding: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .ticket-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 110, 92, 0.15);
    }

    .ticket-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--primary-color-dark, #006E5C);
    }

    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .ticket-id {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 600;
        background: #f8f9fa;
        padding: 0.3rem 0.6rem;
        border-radius: 6px;
    }

    .ticket-date {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 500;
    }

    .ticket-subject {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text, #333);
        margin: 0 0 1rem 0;
        line-height: 1.4;
    }

    .ticket-category {
        display: inline-block;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .category-technical { background: #e0f7fa; color: #006064; }
    .category-billing { background: #fff8e1; color: #e65100; }
    .category-account { background: #e3f2fd; color: #1565c0; }
    .category-product { background: #e8f5e8; color: #2e7d32; }
    .category-other { background: #f5f5f5; color: #424242; }

    .ticket-priority {
        display: inline-block;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .priority-low { background: #e0e0e0; color: #424242; }
    .priority-medium { background: #e3f2fd; color: #1565c0; }
    .priority-high { background: #fff3e0; color: #ef6c00; }
    .priority-urgent { background: #ffebee; color: #c62828; }

    .ticket-status {
        display: inline-block;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .status-open { background: #fff3e0; color: #ef6c00; }
    .status-in_progress { background: #e3f2fd; color: #1565c0; }
    .status-resolved { background: #e8f5e8; color: #2e7d32; }
    .status-closed { background: #f5f5f5; color: #424242; }

    .ticket-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color, #e9ecef);
    }

    .btn-view-ticket {
        background: var(--primary-color-dark, #006E5C);
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-view-ticket:hover {
        background: #005a4a;
        transform: translateY(-1px);
    }

    .no-tickets {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border-color, #e9ecef);
    }

    .no-tickets-icon {
        font-size: 3rem;
        color: #ccc;
        margin-bottom: 1rem;
    }

    .no-tickets-title {
        font-size: 1.5rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .no-tickets-text {
        color: #6c757d;
        margin-bottom: 1.5rem;
    }

    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
        gap: 0.5rem;
    }

    .pagination {
        display: flex;
        gap: 0.25rem;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .page-item {
        border-radius: 6px;
        overflow: hidden;
    }

    .page-link {
        display: block;
        padding: 0.6rem 1rem;
        background: white;
        color: var(--primary-color-dark, #006E5C);
        text-decoration: none;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }

    .page-link:hover {
        background: var(--primary-color-dark, #006E5C);
        color: white;
    }

    .page-item.active .page-link {
        background: var(--primary-color-dark, #006E5C);
        color: white;
        border-color: var(--primary-color-dark, #006E5C);
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        background: #f8f9fa;
    }

    @media (max-width: 768px) {
        .tickets-container {
            padding: 1rem;
        }

        .tickets-header {
            flex-direction: column;
            align-items: stretch;
        }

        .create-ticket-btn {
            align-self: flex-start;
        }

        .ticket-card {
            padding: 1.25rem;
        }

        .ticket-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .ticket-actions {
            flex-direction: column;
        }

        .btn-view-ticket {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="tickets-container">
    <div class="tickets-header">
        <h2 class="tickets-title">Tiket Bantuan Saya</h2>
        <a href="{{ route('tickets.create') }}" class="create-ticket-btn">
            <i class="bi bi-plus-circle" style="font-size: 1.2rem;"></i>
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