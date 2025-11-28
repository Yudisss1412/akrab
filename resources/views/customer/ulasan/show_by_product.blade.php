@extends('layouts.app')

@section('title', 'Ulasan Produk - ' . $product->name)

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/ulasan/show_by_product.css') }}">
@endpush

@section('content')
  <div class="reviews-page">
    <div class="product-header">
      <div class="product-info">
        <img src="{{ $product->main_image ? asset('storage/' . $product->main_image) : asset('src/placeholder_produk.png') }}" alt="{{ $product->name }}">
        <div class="product-details">
          <h1>{{ $product->name }}</h1>
          <p class="shop-name">Toko: {{ $product->seller->name ?? 'Toko Tidak Diketahui' }}</p>
        </div>
      </div>
    </div>
    
    <div class="reviews-container">
      <h2>Ulasan Pelanggan</h2>
      
      <div class="overall-rating">
        <div class="average-rating">
          <span class="rating-value">{{ number_format($product->averageRating, 1) }}</span>
          <div class="rating-stars">
            @for($i = 1; $i <= 5; $i++)
              @if($i <= floor($product->averageRating))
                <span class="star filled">★</span>
              @elseif($i - 0.5 <= $product->averageRating)
                <span class="star half-filled">★</span>
              @else
                <span class="star">★</span>
              @endif
            @endfor
          </div>
          <span class="review-count">({{ $product->reviews_count }} ulasan)</span>
        </div>
      </div>
      
      @if($reviews->count() > 0)
        <div class="reviews-list">
          @foreach($reviews as $review)
            <div class="review-item">
              <div class="review-header">
                <div class="user-info">
                  <div class="user-avatar">{{ substr($review->user->name, 0, 1) }}</div>
                  <div class="user-details">
                    <h4>{{ $review->user->name }}</h4>
                    <p class="review-date">{{ $review->created_at->format('d M Y') }}</p>
                  </div>
                </div>
                <div class="rating">
                  @for($i = 1; $i <= 5; $i++)
                    @if($i <= $review->rating)
                      <span class="star filled">★</span>
                    @else
                      <span class="star">★</span>
                    @endif
                  @endfor
                </div>
              </div>
              
              <div class="review-content">
                <p>{!! preg_replace('/\(\d+\)/', '', $review->review_text) !!}</p>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="no-reviews">
          <p>Belum ada ulasan untuk produk ini.</p>
        </div>
      @endif
    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('js/customer/ulasan/show_by_product.js') }}"></script>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection