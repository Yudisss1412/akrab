@extends('layouts.app')

@section('title', 'Wishlist — AKRAB')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/koleksi/halaman_wishlist.css') }}">
@endpush

@section('content')
  <div class="wl-shell">
    <header class="wl-header">
      <div class="wl-left">
        <a href="{{ url()->previous() }}" class="wl-back" aria-label="Kembali">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
          </svg>
        </a>
        <h1 class="wl-title">Wishlist</h1>
      </div>

      <div class="wl-toolbar" id="wlToolbar" style="display: none;">
        <select id="wlFilter" class="wl-select" aria-label="Urutkan">
          <option value="terbaru" selected>Terbaru</option>
          <option value="termurah">Termurah</option>
          <option value="termahal">Termahal</option>
        </select>
      </div>
    </header>

    <div id="wishlistGrid" class="wl-grid" role="list" aria-live="polite">
      @isset($wishlists)
        @foreach($wishlists as $item)
          <article class="wl-card" role="listitem" data-id="{{ $item['id'] }}">
            <header class="card__head">
              <div class="user__name">{{ $item['shop'] }}</div>
              <time class="card__time">{{ $item['date'] }}</time>
            </header>

            <div class="card__body">
              <a class="product" href="{{ $item['url'] }}" aria-label="Lihat produk {{ $item['title'] }}">
                <div class="product__thumb">
                  <img src="{{ $item['img'] }}" alt="{{ $item['title'] }}">
                </div>
                <div class="product__meta">
                  <div class="product__title">{{ $item['title'] }}</div>
                  <div class="product__shop">{{ $item['shop'] }}</div>
                </div>
              </a>
            </div>

            <footer class="card__foot">
              <div class="wl-price">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
              <button class="wl-like" aria-pressed="true" title="Hapus dari wishlist" data-id="{{ $item['id'] }}" data-wishlist-id="{{ $item['id'] }}">
                <svg class="svg-off" style="display:none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 47 47"><path d="M23.6962 36.3271L23.5003 36.5229L23.2849 36.3271C13.9828 27.8867 7.83366 22.3054 7.83366 16.6458C7.83366 12.7292 10.7712 9.79167 14.6878 9.79167C17.7037 9.79167 20.6412 11.75 21.6791 14.4133H25.3216C26.3595 11.75 29.297 9.79167 32.3128 9.79167C36.2295 9.79167 39.167 12.7292 39.167 16.6458C39.167 22.3054 33.0178 27.8867 23.6962 36.3271ZM32.3128 5.875C28.9053 5.875 25.6349 7.46125 23.5003 9.94833C21.3657 7.46125 18.0953 5.875 14.6878 5.875C8.65616 5.875 3.91699 10.5946 3.91699 16.6458C3.91699 24.0287 10.5753 30.08 20.6607 39.2254L23.5003 41.8104L26.3399 39.2254C36.4253 30.08 43.0837 24.0287 43.0837 16.6458C43.0837 10.5946 38.3445 5.875 32.3128 5.875Z" fill="#F24822"/></svg>
                <svg class="svg-on" style="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 47 47"><path d="M23.5003 41.8104L20.6607 39.2254C10.5753 30.08 3.91699 24.0287 3.91699 16.6458C3.91699 10.5946 8.65616 5.875 14.6878 5.875C18.0953 5.875 21.3657 7.46125 23.5003 9.94833C25.6349 7.46125 28.9053 5.875 32.3128 5.875C38.3445 5.875 43.0837 10.5946 43.0837 16.6458C43.0837 24.0287 36.4253 30.08 26.3399 39.2254L23.5003 41.8104Z" fill="#F24822"/></svg>
              </button>
            </footer>
          </article>
        @endforeach
      @endisset
    </div>

    <div id="wlEmpty" class="wl-empty" {{ isset($wishlists) && count($wishlists) > 0 ? '' : 'hidden' }}>
      <img src="{{ asset('img/empty-wishlist.svg') }}" alt="" class="wl-empty__img" />
      <p class="wl-empty__text">Belum ada item di wishlist.</p>
      <a href="{{ route('cust.welcome') }}" class="btn">Jelajahi Produk</a>
    </div>
  </div>

  <script>
    // Pass data wishlist ke JavaScript
    window.__WISHLIST__ = @isset($wishlists) @json($wishlists) @else [] @endisset;
    
    // Tampilkan toolbar jika ada item wishlist
    document.addEventListener('DOMContentLoaded', function() {
      const toolbar = document.getElementById('wlToolbar');
      const wishlistData = window.__WISHLIST__;
      
      if (wishlistData && wishlistData.length > 0) {
        toolbar.style.display = 'block';
      }
    });
  </script>
@endsection

@push('scripts')
  <script defer src="{{ asset('js/customer/koleksi/halaman_wishlist.js') }}"></script>
@endpush