@extends('layouts.customer')

@section('title', 'Wishlist — AKRAB')

{{-- Pakai navbar compact (tanpa search) --}}
@section('navbar')
  @includeIf('partials.navbar_compact')
@endsection

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

      <div class="wl-toolbar">
        <select id="wlFilter" class="wl-select" aria-label="Urutkan">
          <option value="terbaru" selected>Terbaru</option>
          <option value="termurah">Termurah</option>
          <option value="termahal">Termahal</option>
        </select>
      </div>
    </header>

    <div id="wishlistGrid" class="wl-grid" role="list" aria-live="polite"></div>

    <div id="wlEmpty" class="wl-empty" hidden>
      <img src="{{ asset('img/empty-wishlist.svg') }}" alt="" class="wl-empty__img" />
      <p class="wl-empty__text">Belum ada item di wishlist.</p>
      <a href="{{ route('cust.welcome') }}" class="btn">Jelajahi Produk</a>
    </div>
  </div>
@endsection

@push('scripts')
  <script defer src="{{ asset('js/halaman_wishlist.js') }}"></script>
@endpush
