@extends('layouts.app')

@section('title', 'Riwayat Pesanan')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/riwayat_pesanan.css') }}">
@endpush

@section('content')
  <!-- App Header -->
  <header class="appbar">
    <a href="{{ url()->previous() ?? route('profil.pembeli') }}" class="icon-btn" id="btnBack" aria-label="Kembali">
      <!-- back arrow -->
      <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
    </a>
    <h1 class="app-title">Riwayat Pesanan</h1>
    <div class="spacer"></div>
  </header>

  <main class="main">
    <div class="container">
      <!-- Order List -->
      <section class="order-list-section">
        @if($formattedOrders->count() > 0)
          <div class="order-list" id="orderList">
            @foreach($formattedOrders as $order)
              <article class="order-card">
                <div class="order-header">
                  <div class="order-id">#{{ $order['order_number'] }}</div>
                  <div class="order-status 
                    @if($order['status'] === 'completed') status-completed 
                    @elseif($order['status'] === 'shipped') status-shipping 
                    @elseif($order['status'] === 'processing') status-processing 
                    @elseif($order['status'] === 'cancelled') status-cancelled 
                    @else status-pending @endif">
                    {{ ucfirst($order['status']) }}
                  </div>
                </div>
                
                <div class="order-details">
                  <div class="order-date">{{ $order['created_at'] }}</div>
                  <div class="order-amount">Rp {{ number_format($order['total_amount'], 0, ',', '.') }}</div>
                </div>
                
                <div class="order-items">
                  <div class="item-preview">
                    @if($order['items']->count() > 0)
                      <img src="{{ $order['items']->first()['image'] }}" alt="{{ $order['items']->first()['product_name'] }}">
                      @if($order['items']->count() > 1)
                        <span class="item-count">+{{ $order['items']->count() - 1 }} item lainnya</span>
                      @endif
                    @endif
                  </div>

                  <div class="order-actions">
                    <a href="{{ route('order.invoice', ['order' => $order['order_number']]) }}" class="btn btn-outline">Lihat Detail</a>
                    @if($order['status'] === 'delivered')
                      <a href="{{ route('ulasan.create', ['orderItemId' => $order['items']->first()['id'] ?? null]) }}" class="btn btn-primary">Berikan Ulasan</a>
                    @endif
                  </div>
                </div>
              </article>
            @endforeach
          </div>
        @else
          <div class="empty-state">
            <div class="empty-img">
              <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
              </svg>
            </div>
            <p class="empty-text">Belum ada riwayat pesanan</p>
            <a href="{{ route('cust.welcome') }}" class="btn btn-primary">Mulai Belanja</a>
          </div>
        @endif
      </section>
    </div>
  </main>
@endsection

@push('scripts')
  <script src="{{ asset('js/customer/riwayat_pesanan.js') }}"></script>
@endpush