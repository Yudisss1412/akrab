@extends('layouts.app')

@section('title', 'Penilaian Saya — Riwayat Ulasan')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/koleksi/halaman_ulasan.css') }}" />
@endpush

@section('content')
  <!-- App Header -->
  <header class="appbar">
    <button class="icon-btn" id="btnBack" aria-label="Kembali">
      <!-- back arrow -->
      <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M15.41 7.41 14 6l-6 6 6 6 1.41-1.41L10.83 12z" fill="currentColor"/></svg>
    </button>
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
        <div class="empty-state" id="emptyState" style="display: none;">
          <div class="empty-img" style="width: 100px; height: 100px; background-color: #eee; margin: 0 auto 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <span style="font-size: 40px;">⭐</span>
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
        <div class="form-actions">
          <button type="button" class="btn btn-secondary" id="deleteReview">Hapus</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('js/customer/koleksi/halaman_ulasan.js') }}"></script>
@endpush