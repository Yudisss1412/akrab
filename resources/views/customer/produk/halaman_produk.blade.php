@extends('layouts.app')

@section('title', 'Daftar Produk — AKRAB')

@push('styles')
  <link href="{{ asset('css/customer/produk/halaman_produk.css') }}?v=3" rel="stylesheet"/>
@endpush

@section('content')
  {{-- KONTEN HALAMAN PRODUK --}}
  <main class="produk-page" role="main">
    <div class="produk-header-row">
      <h1 class="produk-title" id="produk-heading">Daftar Produk</h1>
      <div aria-label="Filter produk" class="produk-filter-right" role="region">
        <label for="filter-kategori">Kategori</label>
        <select aria-controls="produk-grid" id="filter-kategori" name="kategori">
          <option value="all">Semua</option>
          <option value="Minuman">Minuman</option>
          <option value="Camilan">Camilan</option>
          <option value="Kerajinan">Kerajinan</option>
          <option value="Kopi">Kopi</option>
          <option value="Sabun">Sabun</option>
          <option value="Kue">Kue</option>
          <option value="Lainnya">Lainnya</option>
        </select>
      </div>
    </div>
    <div aria-busy="false" aria-live="polite" class="produk-grid" id="produk-grid"></div>
    <nav aria-label="Navigasi halaman produk" class="produk-pagination" id="produk-pagination"></nav>
    <section aria-labelledby="rekom-title" class="rekomendasi-section">
      <div class="rekomendasi-header">
        <h3 id="rekom-title">Rekomendasi Produk Terkait / Lainnya</h3>
        <span class="rekomendasi-sub">Kamu mungkin juga suka</span>
      </div>
      <div aria-busy="false" aria-live="polite" class="rekom-grid" id="rekom-grid"></div>
      <nav aria-label="Navigasi rekomendasi" class="produk-pagination" id="rekom-pagination"></nav>
    </section>
    <noscript><p>Aktifkan JavaScript untuk menampilkan daftar produk.</p></noscript>
  </main>

  {{-- JS khusus halaman ini --}}
  <script>
    // Pastikan script produk pakai input navbar (ID sama seperti di cust_welcome)
    window.__AKRAB_SEARCH_INPUT_ID__ = 'navbar-search';
  </script>
@endsection

@push('scripts')
  <script defer src="{{ asset('js/customer/produk/halaman_produk.js') }}?v=3"></script>
@endpush