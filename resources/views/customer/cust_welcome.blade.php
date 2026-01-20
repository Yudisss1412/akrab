@extends('layouts.app')

@section('title', 'Selamat Datang - UMKM AKRAB')

@php
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount ?? 0, 0, ',', '.');
}

function createStarsHTML($rating) {
    $rating = (float) $rating;
    $fullStars = floor($rating);
    $halfStar = 0;
    $emptyStars = 5 - $fullStars;

    // Logika yang sama dengan createStarsHTML JavaScript
    $frac = $rating - $fullStars;
    if ($frac >= 0.75) {
        $fullStars += 1;
    } else if ($frac >= 0.25) {
        $halfStar = 1;
        $emptyStars -= 1;
    }

    $html = str_repeat('<svg width="20" height="20" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF600"/></svg>', $fullStars);

    if ($halfStar) {
        $html .= '<svg width="20" height="20" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M29.6691 32.9982L28.0534 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535V29.2285L29.6691 32.9982ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF700"/></svg>';
    }

    $html .= str_repeat('<svg width="20" height="20" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M17.3316 32.9493L23.5003 29.2285L29.6691 32.9982L28.0535 25.9482L33.4878 21.1993L26.3399 20.6118L23.5003 13.9535L20.6607 20.5628L13.5128 21.1993L18.9472 25.9482L17.3316 32.9493M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#D1D5DB"/></svg>', $emptyStars);

    return $html;
}
@endphp

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/cust_welcome.css') }}?v={{ time() }}">
@endpush

@section('content')
<script>
    const auth = JSON.parse(localStorage.getItem('auth') || '{}');
    if (!auth.role || auth.role !== 'buyer') window.location.href = '/cust_welcome';
</script>

<main class="welcome-page">
    <section class="welcome-banner">
        <h2>Selamat datang, {{ auth()->user() ? auth()->user()->name : 'Pengunjung' }}!</h2>
        <p>Temukan produk UMKM terbaik dari seluruh Indonesia.</p>
    </section>

    <section class="content-section">
        <div class="produk-populer-header">
            <h3>Produk Populer</h3>
            <a href="/halaman_produk" class="btn-lihat-semua">Lihat Semua</a>
        </div>
        <div class="popular-products-grid" id="produk-populer-grid">
            @foreach($popularProducts as $product)
            <div class="produk-card" data-product-id="{{ $product->id }}">
                <img src="{{ $product->main_image ? asset('storage/' . $product->main_image) : ($product->images->first() ? asset('storage/' . $product->images->first()->image_path) : asset('src/product_1.png')) }}"
                     alt="{{ $product->name }}"
                     onerror="this.onerror=null; this.src='{{ asset('src/product_1.png') }}'">
                <div class="produk-card-info">
                    <div class="produk-card-content">
                        <h3 class="produk-card-name">{{ $product->name }}</h3>
                        <div class="produk-card-sub">{{ $product->category ? $product->category->name : 'Umum' }}</div>
                        <div class="produk-card-price">{{ formatRupiah($product->price ?? 0) }}</div>
                        @if($product->stock <= 0)
                        <div class="produk-card-stock out-of-stock">
                            <i class="fas fa-exclamation-triangle"></i> Stok Habis
                        </div>
                        @elseif($product->stock <= 5)
                        <div class="produk-card-stock low-stock">
                            <i class="fas fa-exclamation-circle"></i> Stok Terbatas: {{ $product->stock }} pcs
                        </div>
                        @else
                        <div class="produk-card-stock in-stock">
                            <i class="fas fa-check-circle"></i> Stok Tersedia
                        </div>
                        @endif
                        <div class="produk-card-toko">
                            <a href="/toko/{{ $product->seller ? $product->seller->id : 'toko-tidak-ditemukan' }}"
                               class="toko-link"
                               data-seller-name="{{ $product->seller ? $product->seller->store_name : 'Toko Umum' }}">
                                {{ $product->seller ? $product->seller->store_name : 'Toko Umum' }}
                            </a>
                        </div>
                        <div class="produk-card-stars" aria-label="Rating {{ $product->rating ?? rand(3, 5) }} dari 5">
                            {!! createStarsHTML($product->rating ?? rand(3, 5)) !!}
                        </div>
                    </div>
                </div>
                <div class="produk-card-actions">
                    <a href="{{ route('produk.detail', $product->id) }}" class="btn-lihat lihat-detail-btn">Lihat Detail</a>
                    @if($product->stock > 0)
                    <button class="btn-add"
                            data-product-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            type="button">+ Keranjang</button>
                    @else
                    <button class="btn-add btn-disabled" disabled
                            title="Stok habis">Stok Habis</button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
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
</main>

<script src="{{ asset('js/customer/cust_welcome.js') }}?v={{ time() }}"></script>
@endsection

@section('footer')
  @include('components.customer.footer.footer')
@endsection