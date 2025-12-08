@extends('layouts.admin')

@section('title', 'Riwayat Transaksi - ' . $user->name . ' - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <style>
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 1rem;
      border: 1px solid var(--border);
      border-radius: 8px;
      text-decoration: none;
      color: var(--text);
      font-weight: 500;
    }

    .back-btn:hover {
      background-color: var(--bg);
    }

    .user-info {
      background: white;
      border-radius: var(--ak-radius);
      border: 1px solid var(--ak-border);
      box-shadow: 0 8px 20px rgba(0,0,0,.05);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .info-row {
      display: flex;
      margin-bottom: 0.5rem;
      flex-wrap: wrap;
    }

    .info-label {
      font-weight: 600;
      width: 150px;
      color: var(--muted);
      flex-shrink: 0;
    }

    .info-value {
      flex: 1;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .order-container {
      background: white;
      border-radius: var(--ak-radius);
      border: 1px solid var(--ak-border);
      box-shadow: 0 8px 20px rgba(0,0,0,.05);
    }

    .order-header {
      padding: 1.5rem;
      border-bottom: 1px solid var(--ak-border);
      background: linear-gradient(135deg, #006E5C 0%, #a8d5c9 100%);
      color: white;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 0.75rem 1rem;
      text-align: left;
      border-bottom: 1px solid var(--ak-border);
    }

    th {
      background: var(--bg);
      font-weight: 600;
      color: var(--text);
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:hover {
      background-color: var(--bg);
    }

    .status-badge {
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: 500;
    }

    .status-pending {
      background-color: #fef3c7;
      color: #d97706;
    }

    .status-processing {
      background-color: #fffbeb;
      color: #f59e0b;
    }

    .status-shipped {
      background-color: #dbeafe;
      color: #3b82f6;
    }

    .status-delivered {
      background-color: #dcfce7;
      color: #16a34a;
    }

    .status-cancelled {
      background-color: # fee2e2;
      color: #dc2626;
    }

  </style>
@endpush

@section('content')
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content">
        <div class="page-header">
          <h1>Riwayat Transaksi - {{ $user->name }}</h1>
        </div>

        <div class="user-info">
          <div class="info-row">
            <div class="info-label">Nama:</div>
            <div class="info-value">{{ $user->name }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $user->email }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
              <span class="status-badge {{ $user->status === 'active' ? 'status-delivered' : 'status-cancelled' }}">
                {{ ucfirst($user->status) }}
              </span>
            </div>
          </div>
          <div class="info-row">
            <div class="info-label">Tanggal Registrasi:</div>
            <div class="info-value">{{ $user->created_at->format('d M Y, H:i') }}</div>
          </div>
        </div>

        <div class="order-container">
          <div class="order-header">
            <h2 style="margin: 0; font-size: 1.2rem;">Daftar Transaksi</h2>
          </div>
          <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th>ID Pesanan</th>
                  <th>Tanggal</th>
                  <th>Total</th>
                  <th>Metode Pembayaran</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($orders as $order)
                <tr>
                  <td>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                  <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                  <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                  <td>{{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}</td>
                  <td>
                    <span class="status-badge
                      @if(in_array($order->status, ['pending', 'menunggu_pembayaran'])) status-pending
                      @elseif(in_array($order->status, ['processing', 'diproses'])) status-processing
                      @elseif(in_array($order->status, ['shipped', 'dikirim'])) status-shipped
                      @elseif(in_array($order->status, ['delivered', 'selesai', 'diterima'])) status-delivered
                      @elseif(in_array($order->status, ['cancelled', 'dibatalkan'])) status-cancelled
                      @else status-processing
                      @endif">
                      {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                  </td>
                  <td>
                    <a href="{{ route('order.invoice', $order->order_number) }}" class="btn btn-outline btn-sm">Lihat</a>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" style="text-align: center;">Tidak ada transaksi ditemukan</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')
@endsection