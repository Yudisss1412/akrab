@extends('layouts.app')

@section('title', 'Daftar Produk — AKRAB')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link href="{{ asset('css/customer/produk/halaman_produk.css') }}?v=26" rel="stylesheet"/>
@endpush

@section('content')
  {{-- KONTEN HALAMAN PRODUK --}}
  <main class="produk-page" role="main">
    <div class="produk-header-row">
      <h1 class="produk-title" id="produk-heading">Daftar Produk</h1>
      <!-- Button Filter yang hanya tampil di mobile -->
      <button class="mobile-filter-toggle" id="mobile-filter-btn" aria-expanded="false">
        Filter & Urutkan
      </button>
    </div>

    <!-- Container untuk filter - disembunyikan di mobile -->
    <div class="produk-filter-container" id="mobile-filter-container">
      <div aria-label="Filter produk" class="produk-filter-right" role="region">
        <div class="filter-group">
          <label for="filter-kategori">Kategori</label>
          <select aria-controls="produk-grid" id="filter-kategori" name="kategori">
            <option value="all">Semua</option>
            @foreach($categories as $category)
            <option value="{{ $category->name }}">{{ $category->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="filter-group">
          <label for="filter-subkategori">Sub Kategori</label>
          <select aria-controls="produk-grid" id="filter-subkategori" name="subkategori">
            <option value="">Semua</option>
          </select>
        </div>

        <div class="filter-group">
          <label for="filter-harga-min">Harga Min</label>
          <input type="number" id="filter-harga-min" name="min_price" placeholder="Rp0" min="0">
        </div>

        <div class="filter-group">
          <label for="filter-harga-max">Harga Max</label>
          <input type="number" id="filter-harga-max" name="max_price" placeholder="Rp999.999.999" min="0">
        </div>

        <div class="filter-group">
          <label for="filter-rating">Rating</label>
          <select aria-controls="produk-grid" id="filter-rating" name="rating">
            <option value="">Semua Rating</option>
            <option value="4">4+ Bintang</option>
            <option value="3">3+ Bintang</option>
            <option value="2">2+ Bintang</option>
            <option value="1">1+ Bintang</option>
          </select>
        </div>

        <div class="filter-group">
          <label for="filter-urutkan">Urutkan</label>
          <select aria-controls="produk-grid" id="filter-urutkan" name="sort">
            <option value="popular">Terpopuler</option>
            <option value="newest">Terbaru</option>
            <option value="price-low">Harga Terendah</option>
            <option value="price-high">Harga Tertinggi</option>
          </select>
        </div>
      </div>
    </div>

    {{-- GRID PRODUK --}}
    <div class="produk-grid" id="produk-grid" role="feed">
      {{-- Produk akan diisi oleh JavaScript --}}
    </div>

    {{-- REKOMENDASI PRODUK --}}
    <div class="produk-rekomendasi produk-section" role="region" aria-labelledby="rekomendasi-heading">
      <div class="rekomendasi-header">
        <h3 id="rekomendasi-heading" class="produk-subtitle">Rekomendasi Produk</h3>
        <a class="produk-see-all" href="#">Lihat Semua</a>
      </div>
      <div class="produk-grid" id="rekom-grid" role="feed">
        {{-- Rekomendasi akan diisi oleh JavaScript --}}
      </div>
      <div class="produk-pagination" id="rekom-pagination">
        {{-- Rekomendasi pagination akan diisi oleh JavaScript --}}
      </div>
    </div>

    {{-- POPULER --}}
    <div class="produk-populer produk-section" role="region" aria-labelledby="populer-heading">
      <div class="produk-populer-header">
        <h3 id="populer-heading" class="produk-subtitle">Produk Paling Populer</h3>
        <a class="produk-see-all" href="#">Lihat Semua</a>
      </div>
      <div class="produk-grid" id="populer-grid" role="feed">
        {{-- Populer akan diisi oleh JavaScript --}}
      </div>
      <div class="produk-pagination" id="populer-pagination">
        {{-- Pagination untuk produk populer akan diisi oleh JavaScript --}}
      </div>
    </div>
  </main>

  {{-- JS khusus halaman ini --}}
  <script>
    // Pastikan script produk pakai input navbar (ID sama seperti di cust_welcome)
    window.__AKRAB_SEARCH_INPUT_ID__ = 'navbar-search';

    // Fungsi untuk toggle filter di mobile
    document.addEventListener('DOMContentLoaded', function() {
      const filterToggleBtn = document.getElementById('mobile-filter-btn');
      const filterContainer = document.getElementById('mobile-filter-container');

      if (filterToggleBtn && filterContainer) {
        // Set initial arrow direction
        filterToggleBtn.innerHTML = 'Filter & Urutkan 🔽';

        filterToggleBtn.addEventListener('click', function() {
          const isExpanded = filterToggleBtn.getAttribute('aria-expanded') === 'true';

          // Toggle show/hide filter container
          filterContainer.classList.toggle('show', !isExpanded);

          // Update aria-expanded attribute
          filterToggleBtn.setAttribute('aria-expanded', !isExpanded);

          // Update arrow icon based on state
          if (isExpanded) {
            filterToggleBtn.innerHTML = 'Filter & Urutkan 🔽';  // Arrow down when collapsed
          } else {
            filterToggleBtn.innerHTML = 'Filter & Urutkan 🔼';  // Arrow up when expanded
          }
        });
      }
    });
  </script>
@endsection

@push('scripts')
  <script defer src="{{ asset('js/customer/produk/halaman_produk.js') }}?v=26"></script>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection