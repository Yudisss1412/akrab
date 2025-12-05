@extends('layouts.app')

@section('title', $seller->store_name . ' - Toko di AKRAB')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link href="{{ asset('css/customer/toko/show.css') }}?v=1" rel="stylesheet"/>
@endpush

@section('content')
  <main class="toko-page">
    <div class="container">
      <!-- Header Toko -->
      <div class="toko-header">
        <div class="toko-identity">
          <div class="toko-avatar">
            @if($seller->profile_image)
              <img src="{{ asset('storage/' . $seller->profile_image) }}" alt="{{ $seller->store_name }} profile">
            @else
              <img src="{{ asset('src/user.png') }}" alt="{{ $seller->store_name }} profile">
            @endif
          </div>
          <div class="toko-info">
            <h1 class="toko-name">{{ $seller->store_name }}</h1>
            <div class="toko-stats">
              <span class="toko-rating">
                <span class="stars">
                  @for($i = 0; $i < 5; $i++)
                    @if($i < floor($averageRating))
                      <i class="star filled">★</i>
                    @elseif($i == floor($averageRating) && $averageRating - floor($averageRating) >= 0.5)
                      <i class="star half">★</i>
                    @else
                      <i class="star empty">☆</i>
                    @endif
                  @endfor
                </span>
                <span class="rating-value">{{ number_format($averageRating, 1) }}</span>
                <span class="review-count">({{ $reviews->count() }} ulasan)</span>
              </span>
              <span class="toko-products">{{ $productCount }} produk</span>
            </div>
            <div class="toko-description">
              <p>{{ $seller->description ?? 'Toko ini belum memiliki deskripsi.' }}</p>
            </div>
          </div>
        </div>
        <div class="toko-actions">
          <button class="btn btn-primary">Ikuti Toko</button>
        </div>
      </div>

      <!-- Filter Produk -->
      <div class="toko-filter-bar">
        <div class="filter-section">
          <label for="filter-kategori">Kategori</label>
          <select id="filter-kategori" name="kategori">
            <option value="all">Semua Kategori</option>
            @foreach($categories as $category)
              <option value="{{ $category->name }}">{{ $category->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="filter-section">
          <label for="filter-harga">Harga</label>
          <select id="filter-harga" name="harga">
            <option value="all">Semua Harga</option>
            <option value="0-50000">Rp 0 - Rp 50.000</option>
            <option value="50000-100000">Rp 50.000 - Rp 100.000</option>
            <option value="100000-500000">Rp 100.000 - Rp 500.000</option>
            <option value="500000+">Rp 500.000+</option>
          </select>
        </div>

        <div class="filter-section">
          <label for="urutkan">Urutkan</label>
          <select id="urutkan" name="urutkan">
            <option value="terbaru">Terbaru</option>
            <option value="harga-terendah">Harga Terendah</option>
            <option value="harga-tertinggi">Harga Tertinggi</option>
            <option value="rating-tertinggi">Rating Tertinggi</option>
          </select>
        </div>
      </div>

      <!-- Produk-produk Toko -->
      <div class="toko-products-section">
        <h2>Produk dari {{ $seller->store_name }}</h2>
        <div class="produk-grid" id="produk-grid">
          @forelse($products as $product)
            <div class="produk-card" data-product-id="{{ $product->id }}">
              <img src="{{ $product->main_image ? asset('storage/' . $product->main_image) : asset('src/placeholder_produk.png') }}" 
                   alt="{{ $product->name }}" 
                   class="produk-img"
                   onerror="this.onerror=null; this.src='{{ asset('src/placeholder_produk.png') }}';">
              <div class="produk-card-info">
                <div class="produk-card-content">
                  <h3 class="produk-card-name">{{ is_object($product) ? $product->name : (is_array($product) ? ($product['name'] ?? 'Nama Produk') : 'Nama Produk') }}</h3>
                  <div class="produk-card-sub">{{ is_object($product->subcategory) && is_object($product->subcategory) ? $product->subcategory->name : (is_object($product->category) && is_object($product->category) ? $product->category->name : 'Umum') }}</div>
                  <div class="produk-card-price">Rp {{ number_format(is_object($product) ? $product->price : (is_array($product) ? ($product['price'] ?? 0) : 0), 0, ',', '.') }}</div>
                  <div class="produk-card-toko">
                    @if(is_object($product) && $product->seller && is_object($product->seller))
                      <a href="{{ route('toko.show', $product->seller->id ?? $product->seller_id) }}" class="toko-link" data-seller-name="{{ $product->seller->store_name }}">{{ $product->seller->store_name }}</a>
                    @elseif(is_object($product) && $product->seller_id)
                      <a href="{{ route('toko.show', $product->seller_id) }}" class="toko-link" data-seller-name="{{ $product->seller_name ?? 'Toko' }}">Toko</a>
                    @else
                      <span class="toko-link">-</span>
                    @endif
                  </div>
                  <div class="produk-card-stars" aria-label="Rating {{ round($product->averageRating ?? 0, 1) }} dari 5">
                    @for($i = 0; $i < 5; $i++)
                      @if($i < floor($product->averageRating ?? 0))
                        <i class="star filled">★</i>
                      @elseif($i == floor($product->averageRating ?? 0) && ($product->averageRating ?? 0) - floor($product->averageRating ?? 0) >= 0.5)
                        <i class="star half">★</i>
                      @else
                        <i class="star empty">☆</i>
                      @endif
                    @endfor
                    <span class="rating-value">({{ round($product->averageRating ?? 0, 1) }})</span>
                  </div>
                </div>
              </div>
              <div class="produk-card-actions">
                <a class="btn-lihat lihat-detail-btn" 
                   data-product-id="{{ $product->id }}"
                   href="{{ route('produk.detail', $product->id) }}">Lihat Detail</a>
                <button class="btn-add"
                        data-product-id="{{ $product->id }}"
                        data-name="{{ $product->name }}"
                        type="button">+ Keranjang</button>
              </div>
            </div>
          @empty
            <div class="no-products-message">
              <p>Toko ini belum memiliki produk.</p>
            </div>
          @endforelse
        </div>

        <!-- Pagination -->
        <div class="produk-pagination">
          {{ $products->links() }}
        </div>
      </div>

      <!-- Ulasan Toko -->
      <div class="toko-reviews-section">
        <h2>Ulasan dari Pembeli</h2>
        @if($reviews->count() > 0)
          <div class="reviews-grid">
            @foreach($reviews as $review)
              <div class="review-card">
                <div class="review-header">
                  <div class="review-user">
                    <span class="user-avatar">{{ strtoupper(substr($review->user && is_object($review->user) ? $review->user->name : 'A', 0, 1)) }}</span>
                    <span class="user-name">{{ $review->user && is_object($review->user) ? $review->user->name : 'Anonim' }}</span>
                  </div>
                  <div class="review-rating">
                    <span class="stars">
                      @for($i = 0; $i < 5; $i++)
                        @if($i < $review->rating)
                          <i class="star filled">★</i>
                        @else
                          <i class="star empty">☆</i>
                        @endif
                      @endfor
                    </span>
                  </div>
                </div>
                <div class="review-content">
                  <p>{{ $review->review_text }}</p>
                </div>
                <div class="review-product">
                  <small>Produk: {{ $review->product && is_object($review->product) ? $review->product->name : ($review->product_name ?? 'Produk') }}</small>
                </div>
                <div class="review-date">
                  <small>{{ $review->created_at && is_object($review->created_at) ? $review->created_at->format('d M Y') : 'Tanggal' }}</small>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <p>Toko ini belum memiliki ulasan.</p>
        @endif
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <script src="{{ asset('js/customer/toko/show.js') }}"></script>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection