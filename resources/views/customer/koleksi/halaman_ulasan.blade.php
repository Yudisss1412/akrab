@extends('layouts.app')

@section('title', 'Penilaian Saya — Riwayat Ulasan')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/koleksi/halaman_ulasan.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/customer/koleksi/halaman_ulasan_additional.css') }}" />
@endpush

@section('content')
  <!-- App Header -->
  <header class="appbar">
    <a href="{{ url()->previous() }}" class="icon-btn" id="btnBack" aria-label="Kembali">
      <!-- back arrow -->
      <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
    </a>
    <h1 class="app-title">Penilaian Saya</h1>
    <div class="spacer"></div>
  </header>

  <main class="main">
    <div class="container">
      <div class="tabs">
        <button class="tab active" data-tab="ulasan">Ulasan Saya</button>
      </div>

      <section class="tab-content active" id="ulasan-content">
        <div class="review-list" id="reviewList">
          <!-- Reviews will be loaded here -->
        </div>
        <div class="empty-state" id="emptyState">
          <div class="empty-img">
            <span class="empty-star">⭐</span>
          </div>
          <p class="empty-text">Belum ada ulasan produk.</p>
          <a href="{{ route('cust.welcome') }}" class="btn btn-primary">Belanja Sekarang</a>
        </div>
      </section>
    </div>
  </main>

  <!-- Modal for editing review -->
  <div class="modal" id="editReviewModal" hidden>
    <div class="modal-overlay"></div>
    <div class="modal-content">
      <header class="modal-header">
        <h2>Edit Ulasan</h2>
        <button class="modal-close" id="closeModal">&times;</button>
      </header>
      <form id="editReviewForm">
        <input type="hidden" id="reviewId">
        <div class="form-group">
          <label for="productName">Produk</label>
          <input type="text" id="productName" readonly>
        </div>
        <div class="form-group">
          <label for="rating">Rating</label>
          <div class="rating" id="rating">
            <span class="star" data-value="1">★</span>
            <span class="star" data-value="2">★</span>
            <span class="star" data-value="3">★</span>
            <span class="star" data-value="4">★</span>
            <span class="star" data-value="5">★</span>
          </div>
        </div>
        <div class="form-group">
          <label for="reviewText">Ulasan</label>
          <textarea id="reviewText" rows="6" placeholder="Bagikan pengalamanmu menggunakan produk ini..."></textarea>
        </div>
        <div class="form-group">
          <label>Foto Produk (Opsional)</label>
          <div class="drop-area" id="dropArea">
            <div class="drop-area-content">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
              </svg>
              <p class="drop-text">Seret & lepas foto di sini</p>
              <p class="drop-hint">atau <span class="browse-link">pilih dari komputer</span></p>
              <input type="file" id="fileInput" accept="image/*" multiple class="file-input-hidden">
            </div>
          </div>
          <div class="preview-container" id="previewContainer">
            <div class="preview-header">
              <span>Foto yang dipilih:</span>
              <button type="button" class="clear-all-btn" id="clearAllBtn">Hapus Semua</button>
            </div>
            <div class="image-previews" id="imagePreviews"></div>
          </div>
        </div>
        <div class="form-actions">
          <button type="button" class="btn btn-secondary" id="deleteReview">Hapus</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    // Add CSRF token for API requests
    document.head.innerHTML += '<meta name="csrf-token" content="{{ csrf_token() }}">';
  </script>
  <script src="{{ asset('js/customer/koleksi/halaman_ulasan.js') }}"></script>
@endpush