@extends('layouts.customer')

@section('title', ($produk->nama ?? request('nama') ?? 'Detail Produk').' — AKRAB')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/produk/produk_detail.css') }}">
@endpush

@section('content')
  {{-- Konten hanya halaman ini. Header & footer disediakan oleh layout --}}
  <div class="main-layout pd-page" itemscope itemtype="http://schema.org/Product">
    <meta itemprop="name" content="{{ $produk->nama ?? request('nama') ?? 'Produk' }}">

    <div class="pd-wrap">
      <div class="pd-top">
        {{-- KIRI: GALERI --}}
        <article class="card gallery-card">
          <div class="main-img-wrap">
            <img id="mainImg" class="main-img"
                 src="{{ asset($produk->gambar ?? 'src/product_1.png') }}"
                 alt="{{ $produk->nama ?? 'Foto Produk' }}"
                 itemprop="image">
          </div>

          <div id="thumbs" class="thumbs">
            @php
              $thumbs = $produk->thumbs ?? [
                asset('src/product_1.png'),
                asset('src/product_1.png'),
                asset('src/product_1.png'),
              ];
            @endphp
            @foreach($thumbs as $i => $src)
              <img class="thumb {{ $i===0 ? 'is-active' : '' }}" src="{{ $src }}" alt="Thumbnail {{ $i+1 }}">
            @endforeach
          </div>
        </article>

        {{-- KANAN: INFO PRODUK --}}
        <aside class="card info-card">
          <h1 id="pdTitle" class="pd-title"
              data-name="{{ $produk->nama ?? request('nama') ?? 'Nama Produk' }}">
            {{ $produk->nama ?? request('nama') ?? 'Nama Produk' }}
          </h1>

          <div class="price-wish-row">
            <div class="price" id="pdPrice" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
              <span itemprop="priceCurrency" content="IDR">Rp</span>
              <span itemprop="price" content="{{ isset($produk->harga) ? $produk->harga : 62000 }}">
                {{ isset($produk->harga) ? number_format($produk->harga,0,',','.') : '62.000' }}
              </span>
            </div>

            {{-- fallback icon; akan ditimpa oleh JS sesuai state wishlist --}}
            <button id="wishBtn" class="wish-btn wish-small" aria-label="Wishlist" aria-pressed="false">
              <svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg">
                <path d="M23.6962 36.3271L23.5003 36.5229L23.2849 36.3271C13.9828 27.8867 7.83366 22.3054 7.83366 16.6458C7.83366 12.7292 10.7712 9.79167 14.6878 9.79167C17.7037 9.79167 20.6412 11.75 21.6791 14.4133H25.3216C26.3595 11.75 29.297 9.79167 32.3128 9.79167C36.2295 9.79167 39.167 12.7292 39.167 16.6458C39.167 22.3054 33.0178 27.8867 23.6962 36.3271ZM32.3128 5.875C28.9053 5.875 25.6349 7.46125 23.5003 9.94833C21.3657 7.46125 18.0953 5.875 14.6878 5.875C8.65616 5.875 3.91699 10.5946 3.91699 16.6458C3.91699 24.0287 10.5753 30.08 20.6607 39.2254L23.5003 41.8104L26.3399 39.2254C36.4253 30.08 43.0837 24.0287 43.0837 16.6458C43.0837 10.5946 38.3445 5.875 32.3128 5.875Z" fill="#FF0000"/>
              </svg>
            </button>
          </div>

          <div class="stars-row" id="pdStars">
            <span class="rating-text">
              {{ number_format($produk->rating ?? 4.8,1) }}
              ({{ $produk->jml_ulasan ?? 128 }})
            </span>
          </div>

          <div class="desc-box">
            <p id="pdDesc">
              {{ $produk->deskripsi ?? 'Madu hutan asli, rasa bold, tanpa pemanasan. Kristalisasi alami bisa terjadi — ini tanda madu raw.' }}
            </p>
            <ul class="spec-list">
              <li><strong>Kategori:</strong> <span id="sp-kat">{{ $produk->kategori ?? 'Minuman' }}</span></li>
              <li><strong>Ukuran:</strong> <span id="sp-ukr">{{ $produk->ukuran ?? '250 ml' }}</span></li>
              <li><strong>Bahan Utama:</strong> <span id="sp-bhn">{{ $produk->bahan ?? 'Madu hutan murni' }}</span></li>
            </ul>
          </div>

          <div class="cta-row">
            <button type="button" id="btnAdd" class="btn btn-primary">Tambah ke Keranjang</button>
            <button type="button" id="btnBuy" class="btn btn-outline">Beli Sekarang</button>
          </div>
        </aside>
      </div>

      {{-- ULASAN --}}
      <section class="card review-box" aria-labelledby="ulasan-title">
        <div class="reviews-head">
          <h3 id="ulasan-title">Ulasan Pembeli</h3>
          <div id="reviewsCount" class="reviews-count">
            {{ isset($ulasan) ? count($ulasan) : 0 }} ulasan
          </div>
        </div>

        <div class="review-scroller">
          <button class="rev-nav rev-prev" type="button" aria-label="Geser kiri">‹</button>

          <div id="reviewGrid"
               class="review-row"
               tabindex="0"
               role="region"
               aria-label="Ulasan pembeli (geser horizontal)">
            @forelse(($ulasan ?? []) as $r)
              <div class="review-card">
                <div class="rev-avatar">{{ strtoupper(mb_substr($r->nama,0,1)) }}</div>
                <div class="rev-content">
                  <div class="rev-head">
                    <span class="rev-name">{{ $r->nama }}</span>
                    <span class="rev-date">{{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d M Y') }}</span>
                  </div>
                  <div class="rev-stars" data-score="{{ $r->rating ?? 5 }}"></div>
                  <p class="rev-text">{{ $r->teks }}</p>
                </div>
              </div>
            @empty
              {{-- akan diisi dummy oleh JS --}}
            @endforelse
          </div>

          <button class="rev-nav rev-next" type="button" aria-label="Geser kanan">›</button>
        </div>
      </section>
    </div>
  </div>
@endsection

@push('scripts')
  <script defer src="{{ asset('js/produk_detail.js') }}"></script>
@endpush
