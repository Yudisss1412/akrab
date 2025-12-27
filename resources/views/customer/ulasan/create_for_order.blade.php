@extends('layouts.app')

@section('title', 'Tambah Ulasan Produk - ' . $order->order_number)

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/ulasan/create.css') }}">
@endpush

@section('content')
  <div class="review-form-container">
    <div class="review-form-card">
      <h1>Beri Ulasan untuk Produk dalam Pesanan #{{ $order->order_number }}</h1>

      <div class="order-info">
        <p><strong>Tanggal Pesanan:</strong> {{ $order->created_at->format('d M Y') }}</p>
        <p><strong>Status Pesanan:</strong> 
          <span class="
            @if($order->status === 'completed') status-completed
            @elseif($order->status === 'shipped') status-shipping
            @elseif($order->status === 'processing') status-processing
            @elseif($order->status === 'cancelled') status-cancelled
            @else status-pending @endif">
            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
          </span>
        </p>
      </div>

      @if($itemsWithReviewStatus->count() > 0)
        <div class="items-list">
          @foreach($itemsWithReviewStatus as $item)
            <div class="item-review-card">
              <div class="product-info">
                <img src="{{ $item['product']->main_image ? asset('storage/' . $item['product']->main_image) : asset('src/placeholder_produk.png') }}" alt="{{ $item['product']->name }}">
                <div class="product-details">
                  <h2>{{ $item['product']->name }}</h2>
                  <p class="shop-name">Toko: {{ $item['product']->seller->name ?? $item['product']->seller_name ?? 'Toko Tidak Diketahui' }}</p>
                  <p class="item-quantity">Jumlah: {{ $item['quantity'] }} | Harga: Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</p>
                  
                  @if($item['has_review'])
                    <p class="review-status">Anda sudah memberikan ulasan untuk produk ini</p>
                    <a href="{{ route('halaman_ulasan') }}" class="btn btn-outline">Lihat Ulasan</a>
                  @else
                    <a href="{{ route('ulasan.create', ['orderItemId' => $item['id']]) }}" class="btn btn-primary">Beri Ulasan</a>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <p class="no-items">Tidak ada item dalam pesanan ini.</p>
      @endif

      <div class="form-actions">
        <a href="{{ url()->previous() ?? route('profil.pembeli') }}" class="btn btn-outline">Kembali</a>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    // Tambahkan script jika diperlukan
  </script>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection