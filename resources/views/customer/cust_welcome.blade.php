@extends('layouts.app')

@section('title', 'Selamat Datang - UMKM AKRAB')

@php
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount ?? 0, 0, ',', '.');
}

function createStarsHTML($rating) {
    $rating = (float) $rating;
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
    $emptyStars = 5 - $fullStars - $halfStar;

    $html = '';
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<svg width="20" height="20" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF600"/></svg>';
    }
    if ($halfStar) {
        $html .= '<svg width="20" height="20" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M29.6691 32.9982L28.0534 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535V29.2285L29.6691 32.9982ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF700"/></svg>';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<svg width="20" height="20" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M17.3316 32.9493L23.5003 29.2285L29.6691 32.9982L28.0535 25.9482L33.4878 21.1993L26.3399 20.6118L23.5003 13.9535L20.6607 20.5628L13.5128 21.1993L18.9472 25.9482L17.3316 32.9493ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#D1D5DB"/></svg>';
    }

    return $html;
}
@endphp

@section('header')
  @include('components.customer.header.header')
@endsection

@section('content')
<script>
    const auth = JSON.parse(localStorage.getItem('auth') || '{}');
    if (!auth.role || auth.role !== 'buyer') window.location.href = '/cust_welcome';
</script>

<style>
    /* ================================
       AKRAB — Customer Welcome Page
       ================================ */

    /* ========== Welcome Page Specific Styles ========== */
    .welcome-page {
      padding: 2.2rem 2.7rem 70px 2.7rem;
      max-width: 1450px;
      margin: 0 auto;
      flex: 1;
    }

    /* Welcome Banner */
    .welcome-banner {
      background: var(--primary-color);
      color: #156158;
      border-radius: 22px;
      padding: 2.2rem 2rem 1.5rem 2rem;
      margin-bottom: 2.2rem;
    }

    .welcome-banner h2 {
      font-size: 2.2rem;
      font-weight: 700;
      margin: 0 0 0.5rem 0;
    }

    .welcome-banner p {
      font-size: 1.13rem;
      margin: 0;
    }

    /* ========== Produk Populer ========== */
    .content-section {
      margin-bottom: 2.8rem;
    }

    .produk-populer-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      margin: 0 0 1.2rem 0;
    }

    .produk-populer-header h3 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--dark-text-color);
    }

    .btn-lihat-semua {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.56rem 1.1rem;
      font-weight: 600;
      font-size: 1rem;
      text-decoration: none;
      color: var(--primary-color-dark);
      background: #fff;
      border: 1.6px solid var(--primary-color-dark);
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0, 110, 92, 0.08);
      transition: background 0.18s, color 0.18s, box-shadow 0.18s, transform 0.12s;
      line-height: 1;
    }

    .btn-lihat-semua:hover {
      background: var(--primary-color-dark);
      color: #fff;
      box-shadow: 0 6px 18px rgba(0, 110, 92, 0.18);
      transform: translateY(-1px);
    }

    .popular-products-grid {
      display: grid !important;
      grid-template-columns: repeat(4, 1fr) !important;
      gap: 18px !important;
      margin-bottom: 2.8rem;
    }

    /* Styling card produk populer agar seragam dengan halaman produk */
    .produk-card {
      position: relative !important;
      background: var(--secondary-color) !important;
      border: 1px solid var(--primary-color) !important;
      border-radius: 16px !important;
      box-shadow: var(--shadow-sm) !important;
      overflow: hidden !important;
      display: flex !important;
      flex-direction: column !important;
      padding: 12px !important;
      transition: box-shadow .15s, border-color .15s, transform .15s !important;
    }

    .produk-card img {
      width: 100% !important;
      height: 145px !important;
      object-fit: cover !important;
      border-radius: 10px !important;
      background: var(--background-color) !important;
      flex-shrink: 0 !important;
    }

    .produk-card-info {
      padding: 8px 4px 6px !important;
    }

    .produk-card-name {
      margin: 8px 0 4px !important;
      font-size: 16px !important;
      font-weight: 800 !important;
      color: var(--primary-color-dark) !important;
    }

    .produk-card-sub {
      font-size: 13px !important;
      color: var(--grey-text-color) !important;
      margin-bottom: 6px !important;
    }

    /* Styling untuk link nama toko */
    .produk-card .produk-card-toko a.toko-link {
      color: var(--grey-text-color) !important;
      text-decoration: none !important;
      font-size: 13px !important;
      transition: color 0.2s !important;
    }
    
    .produk-card .produk-card-toko a.toko-link:hover {
      color: var(--primary-color-dark) !important;
      text-decoration: underline !important;
    }
    
    /* Agar link toko tidak mempengaruhi layout card */
    .produk-card .produk-card-toko {
      margin-bottom: 6px !important;
    }

    .produk-card-price {
      font-weight: 700 !important;
      margin-bottom: 6px !important;
      color: var(--primary-color-dark) !important;
    }

    .produk-card-stars { 
      display: flex !important; 
      gap: 2px !important; 
      margin-bottom: 6px !important;
    }
    .produk-card-stars svg { 
      width: 18px !important; 
      height: 18px !important; 
    }

    .produk-rating-angka {
      font-size: 14px !important;
      color: var(--grey-text-color) !important;
      margin-top: 4px !important;
    }

    .produk-card-actions {
      margin-top: .5rem !important;
      display: flex !important;
      gap: .55rem !important;
    }
    
    .produk-card-actions .btn-lihat,
    .produk-card-actions .btn-add {
      flex: 1 !important;
      border: none !important;
      border-radius: 8px !important;
      padding: .55rem .75rem !important;
      font-weight: 600 !important;
      cursor: pointer !important;
      transition: background .15s, color .15s !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      text-decoration: none !important;
      text-align: center !important;
    }
    
    .produk-card-actions .btn-lihat {
      background: var(--primary-color-dark) !important;
      color: #fff !important;
    }
    
    .produk-card-actions .btn-lihat:hover {
      background: var(--primary-color) !important;
      color: var(--primary-color-dark) !important;
    }
    
    .produk-card-actions .btn-add {
      background: #f5fbf8 !important;
      color: var(--primary-color-dark) !important;
      border: 1px solid var(--primary-color-dark) !important;
    }
    
    .produk-card-actions .btn-add:hover {
      background: var(--primary-color-dark) !important;
      color: #fff !important;
    }

    /* ========== Kategori ========== */
    .content-section h3 {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--dark-text-color);
      margin: 0 0 1.2rem 0;
    }

    .category-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 1.2rem;
      align-items: stretch;
    }

    .category-card-link {
      text-decoration: none;
      color: inherit;
      height: 100%;
      display: block;
    }

    .category-card {
      background: var(--secondary-color);
      border-radius: 13px;
      padding: 1.5rem 0.5rem;
      text-align: center;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      font-size: 1.09rem;
      font-weight: 600;
      color: var(--dark-text-color);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.3rem;
      transition: box-shadow 0.16s, transform 0.16s;
      cursor: pointer;
      height: 100%;
      justify-content: center;
      min-height: 120px;
    }

    .category-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }

    .category-card span {
      font-size: 2.35rem;
      margin-bottom: 0.33rem;
      line-height: 1;
    }

    .category-card h4 {
      font-weight: 600;
      font-size: 1.09rem;
      margin: 0;
      line-height: 1.3;
      text-align: center;
    }

    /* ========== Modal Produk ========== */
    .modal-detail-produk {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(0, 0, 0, 0.5);
      overflow-y: auto;
    }

    .modal-content-new {
      max-width: 420px;
      width: 90%;
      max-height: 90vh;
      background: #fff;
      border-radius: 25px;
      box-shadow: 0 8px 38px rgba(0, 0, 0, 0.22);
      display: flex;
      flex-direction: column;
      position: relative;
      overflow: hidden;
      margin: 20px; /* Jarak dari sisi viewport */
    }
    
    .modal-img-section {
      display: flex;
      flex-direction: column;
      background: #fafbfc;
      padding: 0 0 1.2rem 0;
      flex-shrink: 0; /* Prevents the image section from shrinking */
    }
    
    @media (max-width: 480px) {
      .modal-content-new {
        width: 95%;
        max-height: 95vh;
      }
      
      .modal-body-scrollable {
        max-height: calc(95vh - 280px);
      }
    }

    .modal-title-row {
      padding: 1.2rem 2rem 0.5rem 2rem;
      font-size: 1.25rem;
      font-weight: 600;
      color: #333;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .close {
      font-size: 1.8rem;
      font-weight: bold;
      color: #aaa;
      cursor: pointer;
      line-height: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      transition: all 0.2s ease;
    }
    
    .close:hover,
    .close:focus {
      color: #000;
      text-decoration: none;
      background-color: #f0f0f0;
      cursor: pointer;
    }

    .modal-img-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      background: #fafbfc;
      padding: 0 0 1.2rem 0;
    }

    .modal-img-main {
      width: 274.14px;
      height: 145px;
      object-fit: cover;
      border-radius: 16px;
      background: #e5e5e5;
    }

    .modal-thumbs-new {
      display: flex;
      gap: 0.6rem;
      margin-bottom: 0.5rem;
    }

    .modal-thumbs-new img {
      width: 48px;
      height: 36px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid #eee;
      background: #eaeaea;
      cursor: pointer;
      transition: border 0.18s;
    }

    .modal-thumbs-new img.active {
      border-color: var(--primary-color-dark);
    }

    .modal-price-new {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--primary-color-dark);
      margin: 0 0 0.6rem 0;
      padding: 0 2rem;
    }

    .modal-desc-box-new {
      background: #f2fbf7;
      border-radius: 15px;
      padding: 1rem 1.3rem 0.8rem 1.3rem;
      margin: 0 2rem 1.1rem 2rem;
      color: #444;
      font-size: 1rem;
    }

    .modal-desc {
      margin: 0 0 0.7rem 0;
    }

    .modal-detail-list {
      margin: 0;
      padding-left: 1.1rem;
    }

    .modal-detail-list li {
      margin-bottom: 0.2rem;
      font-size: 1rem;
    }

    .modal-body-scrollable {
      max-height: calc(90vh - 320px); /* Adjusted for larger image section height */
      overflow-y: auto;
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: 0 2rem 1.1rem 2rem;
    }
    
    /* Scrollbar styling for better UX */
    .modal-body-scrollable::-webkit-scrollbar {
      width: 6px;
    }
    
    .modal-body-scrollable::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    
    .modal-body-scrollable::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 10px;
    }
    
    .modal-body-scrollable::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }
    
    .modal-actions {
      display: flex;
      justify-content: space-between;
      gap: 1.1rem;
      padding: 0 2rem 1.2rem 2rem;
    }

    .modal-btn {
      flex: 1;
      border: none;
      padding: 0.6rem 0;
      font-size: 1.07rem;
      font-weight: 600;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.15s, transform 0.12s;
    }

    .modal-btn:hover {
      transform: translateY(-1px);
    }

    .modal-btn-primary {
      background: var(--primary-color-dark);
      color: #fff;
    }

    .modal-btn-primary:hover {
      background: var(--primary-color);
      color: var(--primary-color-dark);
    }

    .modal-btn-secondary {
      background: #fff;
      border: 1.5px solid var(--primary-color-dark);
      color: var(--primary-color-dark);
    }

    /* ========== Responsive ========== */
    @media (max-width: 1200px) {
      .popular-products-grid {
        grid-template-columns: repeat(3, 1fr);
      }
      
      .category-grid {
        grid-template-columns: repeat(6, 1fr);
      }
    }

    @media (max-width: 992px) {
      .popular-products-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .category-grid {
        grid-template-columns: repeat(4, 1fr);
      }
      
      .welcome-page {
        padding: 1.8rem 1.5rem 70px;
      }
      
      .header {
        padding: 0 1.5rem;
      }
    }

    @media (max-width: 640px) {
      .popular-products-grid {
        grid-template-columns: 1fr;
      }
      
      .category-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .search-bar {
        min-width: 0;
        max-width: 100%;
      }
      
      .produk-populer-header,
      .section-header,
      .produk-populer-titlebar {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.6rem;
      }
      
      /* Footer stack rapi di mobile */
      .footer .footer__inner.footer-3col {
        grid-template-columns: 1fr;
        row-gap: 0.75rem;
        text-align: center;
      }
      
      .footer-left,
      .footer-center,
      .footer-right {
        justify-self: center;
      }
    }

    /* ============================================================
       FOOTER — LOCKED LAYOUT (© kiri • sosmed center • Privasi kanan)
       ============================================================ */
    @media (min-width: 768px) {
      footer.footer {
        position: relative !important;
        min-height: 64px !important;
      }
      
      /* pastikan inner bukan anchor */
      footer.footer .footer__inner {
        position: static !important;
      }
      
      /* KIRI: © */
      footer.footer .footer-left {
        position: absolute !important;
        left: 2rem !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        margin: 0 !important;
        white-space: nowrap !important;
        display: flex !important;
        align-items: center !important;
      }
      
      /* TENGAH: kapsul sosmed */
      footer.footer .footer-center,
      footer.footer .social-icons-background {
        position: absolute !important;
        left: 50% !important;
        top: 50% !important;
        transform: translate(-50%, -50%) !important;
        z-index: 2 !important;
        margin: 0 !important;
        text-align: center !important;
      }
      
      /* KANAN: kebijakan privasi */
      footer.footer .footer-right,
      footer.footer .footer-privacy {
        position: absolute !important;
        right: 2rem !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        margin: 0 !important;
        text-align: right !important;
        white-space: nowrap !important;
        display: flex !important;
        align-items: center !important;
      }
    }
</style>

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
                     onerror="this.src='{{ asset('src/product_1.png') }}'">
                <div class="produk-card-info">
                    <h3 class="produk-card-name">{{ $product->name }}</h3>
                    <div class="produk-card-sub">{{ $product->category ? $product->category->name : 'Umum' }}</div>
                    <div class="produk-card-price">{{ formatRupiah($product->price ?? 0) }}</div>
                    <div class="produk-card-toko">
                        <a href="/toko/{{ $product->seller ? $product->seller->id : 'toko-tidak-ditemukan' }}"
                           class="toko-link"
                           data-seller-name="{{ $product->seller ? $product->seller->store_name : 'Toko Umum' }}">
                            {{ $product->seller ? $product->seller->store_name : 'Toko Umum' }}
                        </a>
                    </div>
                    <div class="produk-card-stars" aria-label="Rating {{ $product->rating ?? 0 }} dari 5">
                        {!! createStarsHTML($product->rating ?? 0) !!}
                    </div>
                    <div class="produk-rating-angka">{{ $product->rating ?? 0 }}</div>
                </div>
                <div class="produk-card-actions">
                    <button class="btn-lihat lihat-detail-btn"
                            data-product-id="{{ $product->id }}"
                            data-idx="{{ $loop->index }}">Lihat Detail</button>
                    <button class="btn-add"
                            data-product-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            type="button">+ Keranjang</button>
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

<!-- Modal Produk Populer -->
<div class="modal-detail-produk" id="modal-detail-produk" style="display: none;">
    <div class="modal-content-new">
        <div class="modal-title-row">
            <span id="modal-product"></span>
            <span class="close" id="modal-close-btn">&times;</span>
        </div>
        <div class="modal-img-section">
            <img class="modal-img-main" id="modal-img" src="" alt="Foto Produk">
            <div class="modal-thumbs-new" id="modal-thumbs"></div>
        </div>
        <div class="modal-body-scrollable">
            <div class="modal-price-new" id="modal-price"></div>
            <div class="modal-desc-box-new">
                <div class="modal-desc" id="modal-desc"></div>
                <ul class="modal-detail-list" id="modal-specs"></ul>
            </div>
        </div>

        <div class="modal-actions">
            <button class="modal-btn modal-btn-primary" id="modal-addcart-btn">Tambah ke Keranjang</button>
            <button class="modal-btn modal-btn-secondary" id="modal-lihatdetail-btn">Lihat Detail</button>
        </div>
    </div>
</div>

<script>
// Check if products are already loaded from server, if so, use them instead of fetching from API
document.addEventListener('DOMContentLoaded', function() {
    // If products are already rendered from server, attach event listeners to them
    const produkCards = document.querySelectorAll('.produk-card');
    if (produkCards.length > 0) {
        // Products are already loaded from server, attach event listeners
        produkCards.forEach((card, idx) => {
            const lihatDetailBtn = card.querySelector('.btn-lihat');
            const addToCartBtn = card.querySelector('.btn-add');

            if (lihatDetailBtn) {
                lihatDetailBtn.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    openProdukModal(idx, productId);
                });
            }

            if (addToCartBtn) {
                addToCartBtn.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    addToCart(productId);
                });
            }
        });
    } else {
        // If no products rendered from server, fall back to loading via JS (for compatibility)
        loadProdukPopulerFromJS();
    }
});

// Fallback function to load from JS if needed
async function loadProdukPopulerFromJS() {
    // This would implement the original fetch logic from cust_welcome.js
    // But since we now render from server, this is just for backup
    try {
        const response = await fetch('/api/products/popular');
        if (response.ok) {
            const data = await response.json();
            renderProdukPopulerJS(data);
        }
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

// Modified modal functions to work with server-rendered content
let currentProduk = null;

function openProdukModal(idx, productId) {
    // Try to get product data from server-rendered content first
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    if (productCard) {
        // Get product details from the card (or fetch from API if needed)
        fetch(`/api/products/${productId}`)
            .then(response => response.json())
            .then(produk => {
                currentProduk = produk;

                const modal = document.getElementById('modal-detail-produk');
                const modalProduct = document.getElementById('modal-product');
                const modalImg = document.getElementById('modal-img');
                const modalPrice = document.getElementById('modal-price');
                const modalDesc = document.getElementById('modal-desc');
                const modalSpecs = document.getElementById('modal-specs');
                const modalThumbs = document.getElementById('modal-thumbs');

                modalProduct.textContent = produk.name || 'Nama Produk Tidak Tersedia';
                modalImg.src = produk.image || '{{ asset("src/product_1.png") }}';
                modalImg.alt = produk.name || 'Foto Produk';
                modalPrice.textContent = typeof produk.price === 'number' ?
                    new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(produk.price) : produk.price;
                modalDesc.textContent = produk.description || produk.desc || 'Deskripsi tidak tersedia';

                // Clear and populate specifications
                modalSpecs.innerHTML = '';
                if (produk.spesifikasi || produk.specifications) {
                    const specs = produk.spesifikasi || produk.specifications;

                    // Handle both object and array formats
                    if (typeof specs === 'object' && specs !== null && !Array.isArray(specs)) {
                        // If it's an object format: { key: value }
                        Object.entries(specs).forEach(([key, value]) => {
                            if (value !== null && value !== undefined && value !== '') {
                                const li = document.createElement('li');
                                li.innerHTML = `<strong>${key}:</strong> ${value}`;
                                modalSpecs.appendChild(li);
                            }
                        });
                    } else if (Array.isArray(specs)) {
                        // If it's an array format: [ { key: 'Kategori', value: 'Kuliner' }, ... ]
                        specs.forEach(spec => {
                            if (typeof spec === 'object' && spec.key && spec.value !== undefined) {
                                const li = document.createElement('li');
                                li.innerHTML = `<strong>${spec.key}:</strong> ${spec.value}`;
                                modalSpecs.appendChild(li);
                            } else if (typeof spec === 'string' && spec.includes(':')) {
                                // If it's a string like "Kategori: Kuliner"
                                const [key, ...valueParts] = spec.split(':');
                                const value = valueParts.join(':').trim();
                                const li = document.createElement('li');
                                li.innerHTML = `<strong>${key.trim()}:</strong> ${value}`;
                                modalSpecs.appendChild(li);
                            }
                        });
                    } else {
                        // For any other format, just try to display it properly
                        const specsStr = String(specs);
                        specsStr.split('\n').forEach(spec => {
                            if (spec && spec.includes(':')) {
                                const [key, ...valueParts] = spec.split(':');
                                const value = valueParts.join(':').trim();
                                const li = document.createElement('li');
                                li.innerHTML = `<strong>${key.trim()}:</strong> ${value}`;
                                modalSpecs.appendChild(li);
                            }
                        });
                    }
                }

                // Set up thumbnail images
                modalThumbs.innerHTML = '';
                if (produk.images && produk.images.length > 0) {
                    produk.images.forEach((img, i) => {
                        const thumb = document.createElement('img');
                        thumb.src = img.image_path;
                        thumb.alt = `Gambar produk ${i+1}`;
                        thumb.className = 'modal-thumb';
                        thumb.onclick = () => {
                            modalImg.src = img.image_path;
                        };
                        modalThumbs.appendChild(thumb);
                    });
                } else {
                    const thumb = document.createElement('img');
                    thumb.src = produk.image || '{{ asset("src/product_1.png") }}';
                    thumb.alt = 'Gambar produk';
                    thumb.className = 'modal-thumb';
                    modalThumbs.appendChild(thumb);
                }

                // Store product ID in the add to cart button
                document.getElementById('modal-addcart-btn').setAttribute('data-product-id', productId);

                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            })
            .catch(error => {
                console.error('Error loading product details:', error);
                alert('Gagal memuat detail produk');
            });
    }
}

function closeProdukModal() {
    const modal = document.getElementById('modal-detail-produk');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto'; // Re-enable scrolling
}

// Close modal when clicking outside
document.getElementById('modal-detail-produk').addEventListener('click', function(e) {
    if (e.target === this) {
        closeProdukModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeProdukModal();
    }
});

// Add to cart function
async function addToCart(productId) {
    if (!productId) {
        alert('Produk tidak ditemukan');
        return;
    }

    try {
        const response = await fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        });

        const result = await response.json();

        if (response.ok) {
            alert(result.message || 'Produk berhasil ditambahkan ke keranjang');
        } else {
            alert(result.message || 'Gagal menambahkan produk ke keranjang');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        alert('Terjadi kesalahan saat menambahkan ke keranjang');
    }
}

// Attach event listeners to modal buttons
document.getElementById('modal-close-btn').addEventListener('click', closeProdukModal);
document.getElementById('modal-addcart-btn').addEventListener('click', function() {
    const productId = this.getAttribute('data-product-id');
    addToCart(productId);
});
document.getElementById('modal-lihatdetail-btn').addEventListener('click', function() {
    if (currentProduk && currentProduk.id) {
        window.location.href = `/produk_detail/${currentProduk.id}`;
    } else {
        alert('Menuju halaman detail produk (demo)');
    }
});

// Prevent modal from closing when clicking on content
document.querySelector('.modal-content-new').addEventListener('click', function(e) {
    e.stopPropagation();
});
</script>
<script src="{{ asset('js/customer/cust_welcome.js') }}?v=22"></script>
@endsection

@section('footer')
  @include('components.customer.footer.footer')
@endsection