@extends('layouts.app')

@section('title', 'Selamat Datang - UMKM AKRAB')

@section('header')
  @include('components.customer.header.header')
@endsection

@section('content')
<script>
    const auth = JSON.parse(localStorage.getItem('auth') || '{}');
    if (auth.role !== 'buyer') window.location.href = '/cust_welcome';
</script>

<section class="welcome-banner">
    <h2>Selamat datang, {{ auth()->user() ? auth()->user()->name : 'Pengunjung' }}!</h2>
    <p>Temukan produk UMKM terbaik dari seluruh Indonesia.</p>
</section>

<section class="content-section">
    <div class="produk-populer-header">
        <h3>Produk Populer</h3>
        <a href="/halaman_produk" class="btn-lihat-semua">Lihat Semua</a>
    </div>
    <div class="popular-products-grid" id="produk-populer-grid"></div>
</section>

<section class="content-section">
    <h3>Jelajahi Kategori</h3>
    <div class="category-grid">
        <a href="{{ route('kategori.kuliner') }}" class="category-card-link">
            <div class="category-card"><span>🍽️</span><h4>Kuliner</h4></div>
        </a>
        <a href="{{ route('kategori.fashion') }}" class="category-card-link">
            <div class="category-card"><span>👕</span><h4>Fashion</h4></div>
        </a>
        <a href="{{ route('kategori.kerajinan') }}" class="category-card-link">
            <div class="category-card"><span>👜</span><h4>Kerajinan Tangan</h4></div>
        </a>
        <a href="{{ route('kategori.berkebun') }}" class="category-card-link">
            <div class="category-card"><span>🌿</span><h4>Produk Berkebun</h4></div>
        </a>
        <a href="{{ route('kategori.kesehatan') }}" class="category-card-link">
            <div class="category-card"><span>🧼</span><h4>Produk Kesehatan</h4></div>
        </a>
        <a href="{{ route('kategori.mainan') }}" class="category-card-link">
            <div class="category-card"><span>🧒</span><h4>Mainan</h4></div>
        </a>
        <a href="{{ route('kategori.hampers') }}" class="category-card-link">
            <div class="category-card"><span>🎁</span><h4>Hampers</h4></div>
        </a>
    </div>
</section>

<!-- Modal Produk Populer -->
<div class="modal-detail-produk" id="modal-detail-produk" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content-new">
        <div class="modal-title-row">
            <span id="modal-product"></span>
            <button class="modal-close-new" id="modal-close-btn">&times;</button>
        </div>
        <div class="modal-img-section">
            <img class="modal-img-main" id="modal-img" src="" alt="Foto Produk">
            <div class="modal-thumbs-new" id="modal-thumbs"></div>
        </div>
        <div class="modal-price-new" id="modal-price"></div>
        <div class="modal-desc-box-new">
            <div class="modal-desc" id="modal-desc"></div>
            <ul class="modal-detail-list" id="modal-specs"></ul>
        </div>
        <div class="modal-actions">
            <button class="modal-btn modal-btn-primary" id="modal-addcart-btn">Tambah ke Keranjang</button>
            <button class="modal-btn modal-btn-secondary" id="modal-lihatdetail-btn">Lihat Detail</button>
        </div>
    </div>
</div>

<script src="{{ asset('js/customer/cust_welcome.js') }}"></script>
@endsection

@section('footer')
  @include('components.customer.footer.footer')
@endsection