@extends('layouts.app')

@section('title', ($produk['nama'] ?? request('nama') ?? 'Detail Produk') . ' — AKRAB')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  {{-- CSS khusus halaman produk detail --}}
  <link href="{{ asset('css/customer/produk/produk_detail.css') }}" rel="stylesheet"/>
  <link href="{{ asset('css/customer/produk/halaman_produk.css') }}" rel="stylesheet"/>
@endpush

@php
function createStarsHTML($rating, $size = 20) {
    $rating = (float) $rating;
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
    $emptyStars = 5 - $fullStars - $halfStar;

    $html = '';
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF600"/></svg>';
    }
    if ($halfStar) {
        $html .= '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M29.6691 32.9982L28.0534 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535V29.2285L29.6691 32.9982ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF700"/></svg>';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M17.3316 32.9493L23.5003 29.2285L29.6691 32.9982L28.0535 25.9482L33.4878 21.1993L26.3399 20.6118L23.5003 13.9535L20.6607 20.5628L13.5128 21.1993L18.9472 25.9482L17.3316 32.9493ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#D1D5DB"/></svg>';
    }

    return $html;
}
@endphp

@section('content')
  <div class="pd-wrap" data-product-id="{{ $produk['id'] }}">
    <div class="pd-top">
      <!-- Product Gallery -->
      <div class="gallery-card card">
        <div class="main-img-wrap">
          <img id="mainImage" src="{{ $produk['gambar_utama'] ?? 'https://via.placeholder.com/600x600' }}" alt="{{ $produk['nama'] ?? 'Produk' }}" class="main-img">
        </div>
        <div class="thumbs" id="thumbnails">
          @foreach(($produk['gambar'] ?? []) as $index => $img)
            <img src="{{ $img }}" alt="{{ $produk['nama'] ?? 'Produk' }} {{ $index + 1 }}"
                 class="thumb {{ $index === 0 ? 'is-active' : '' }}" data-index="{{ $index }}">
          @endforeach
        </div>
      </div>

      <!-- Product Info -->
      <div class="info-card card">
        <h1 class="pd-title" id="pdTitle" data-product-id="{{ $produk['id'] ?? '' }}" data-name="{{ $produk['nama'] ?? 'Nama Produk' }}">{{ $produk['nama'] ?? 'Nama Produk' }}</h1>

        <div class="stars-row">
          <div class="rating-stars">
            {!! createStarsHTML($produk['rating'] ?? 0) !!}
          </div>
          <span class="rating-text">({{ $produk['jumlah_ulasan'] ?? '128' }} ulasan)</span>
        </div>

        <div class="price-wish-row">
          <div class="price">Rp<span class="price-number">{{ number_format($produk['harga'] ?? 100000, 2, ',', '.') }}</span></div>
          <button class="wish-small {{ $produk['di_wishlist'] ?? false ? 'active' : '' }}" type="button" aria-label="Wishlist">
            <svg class="heart-icon {{ $produk['di_wishlist'] ?? false ? 'heart-fill' : 'heart-outline' }}" viewBox="0 0 24 24" width="24" height="24" fill="{{ $produk['di_wishlist'] ?? false ? '#FF4757' : 'none' }}" stroke="{{ $produk['di_wishlist'] ?? false ? '#FF4757' : 'currentColor' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
          </button>
        </div>

        <div class="desc-box">
          <p>{{ $produk['deskripsi'] ?? 'Deskripsi lengkap produk akan muncul di sini. Ini adalah contoh deskripsi produk yang menjelaskan fitur-fitur, spesifikasi, dan manfaat dari produk ini.' }}</p>
        </div>

        <ul class="spec-list">
          @foreach(($produk['spesifikasi'] ?? []) as $spec)
            <li class="spec-item">
              <span class="spec-key">{{ $spec['nama'] ?? 'Spesifikasi' }}:</span>
              <span class="spec-value">{{ $spec['nilai'] ?? '-' }}</span>
            </li>
          @endforeach
        </ul>

        <div class="cta-row">
          <button class="btn btn-add" data-product-id="{{ $produk['id'] }}">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="9" cy="21" r="1"/>
              <circle cx="20" cy="21" r="1"/>
              <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            + Keranjang
          </button>
          <button class="btn btn-lihat" data-product-id="{{ $produk['id'] }}">Beli Sekarang</button>
        </div>

        <!-- Link ke halaman wishlist -->
        <div class="wishlist-link-row">
          <a href="{{ route('wishlist.index') }}" class="btn btn-wishlist-page">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
            Lihat Wishlist
          </a>
        </div>
      </div>
    </div>

    <!-- Reviews Section -->
    <div class="review-box">
      <div class="reviews-head">
        <h2 class="section-title">Ulasan Pelanggan</h2>
        <a href="{{ route('ulasan.show_by_product', ['productId' => $produk['id']]) }}" class="see-all">Lihat Semua</a>
      </div>

      <div class="review-scroller">
        <div class="review-row" id="reviewsContainer">
          @foreach(($produk['ulasan'] ?? []) as $review)
            <article class="review-card">
              <div class="rev-avatar">{{ substr($review['user'], 0, 1) }}</div>
              <div class="rev-content">
                <div class="rev-head">
                  <div class="rev-name">{{ $review['user'] ?? 'Nama Pembeli' }}</div>
                  <time class="rev-date">{{ $review['created_at'] ?? '1 Jan 2023' }}</time>
                </div>
                <div class="rev-stars">
                  {!! createStarsHTML($review['rating'] ?? 0, 18) !!}
                </div>
                <p class="rev-text">{!! preg_replace('/\(\d+\)/', '', $review['review_text'] ?? 'Ulasan pelanggan tentang produk ini.') !!}</p>
              </div>
            </article>
          @endforeach
        </div>

        @if(empty($produk['ulasan']))
          <p class="no-reviews">Belum ada ulasan untuk produk ini. Jadilah yang pertama memberikan ulasan!</p>
        @endif
      </div>
    </div>
  </div>
@endsection

@section('footer')
  @include('components.customer.footer.footer')
@endsection

@push('scripts')
  <style>
    .wishlist-link-row {
      margin-top: 15px;
      text-align: center;
    }

    .btn-wishlist-page {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 10px 16px;
      border: 1px solid var(--primary-color-dark);
      border-radius: 8px;
      background-color: #fff;
      color: var(--primary-color-dark);
      text-decoration: none;
      font-weight: 600;
      transition: all 0.2s;
    }

    .btn-wishlist-page:hover {
      background-color: var(--primary-color-dark);
      color: #fff;
    }

    .notification {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, 500%) scale(0.9);
      padding: 16px 24px;
      border-radius: 8px;
      color: #fff;
      z-index: 9999;
      box-shadow: 0 6px 20px rgba(0,0,0,0.25);
      font-size: 15px;
      max-width: 420px;
      width: max-content;
      word-wrap: break-word;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 12px;
      min-width: 300px;
      text-align: center;
    }
    .notification.show {
      transform: translate(-50%, 500%) scale(1);
    }
    .notification-success {
      background-color: #10b981;
    }
    .notification-error {
      background-color: #ef4444;
    }
    .notification-info {
      background-color: #3b82f6;
    }
    .notification-icon {
      font-size: 20px;
    }
  </style>
  <script>
    // Basic functionality for quantity selector and wishlist
    // Create notification function
    function showNotification(message, type = 'info') {
      // Remove any existing notifications
      const existingNotifications = document.querySelectorAll('.notification');
      existingNotifications.forEach(notification => {
        notification.remove();
      });

      // Create notification element
      const notification = document.createElement('div');
      notification.className = `notification notification-${type}`;

      // Add icon based on type
      let icon = 'ℹ️'; // default info icon
      if (type === 'success') {
        icon = '✅';
      } else if (type === 'error') {
        icon = '❌';
      }

      notification.innerHTML = `
        <span class="notification-icon">${icon}</span>
        <span class="notification-message">${message}</span>
      `;

      document.body.appendChild(notification);

      // Show notification
      setTimeout(() => {
        notification.classList.add('show');
      }, 10);

      // Auto remove after 3 seconds
      setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
          if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
          }
        }, 300);
      }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function() {
      // Wishlist button
      const wishlistBtn = document.querySelector('.wish-small');
      if (wishlistBtn) {
        // Store product ID in the wishlist button
        const productId = '{{ $produk['id'] }}';
        wishlistBtn.dataset.productId = productId;

        wishlistBtn.addEventListener('click', async function() {
          const heartIcon = this.querySelector('.heart-icon');
          const productId = this.dataset.productId;
          const wasActive = this.classList.contains('active');

          try {
            let response;
            if (!wasActive) {
              // Add to wishlist
              response = await fetch('/wishlist', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                  'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                  product_id: productId
                })
              });
            } else {
              // Remove from wishlist using product_id
              response = await fetch(`/wishlist/product/${productId}`, {
                method: 'DELETE',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                  'X-Requested-With': 'XMLHttpRequest'
                }
              });
            }

            const result = await response.json();

            if (response.ok) {
              // Update visual state based on successful operation
              if (!wasActive) {
                // Successfully added to wishlist
                this.classList.add('active');
                heartIcon.setAttribute('fill', '#FF4757');
                heartIcon.setAttribute('stroke', '#FF4757');
                heartIcon.classList.remove('heart-outline');
                heartIcon.classList.add('heart-fill');
              } else {
                // Successfully removed from wishlist
                this.classList.remove('active');
                heartIcon.setAttribute('fill', 'none');
                heartIcon.setAttribute('stroke', 'currentColor');
                heartIcon.classList.remove('heart-fill');
                heartIcon.classList.add('heart-outline');
              }

              // Show success message
              if(result.message) {
                showNotification(result.message, 'success');
              } else if (!wasActive) {
                showNotification('Produk berhasil ditambahkan ke wishlist', 'success');
              } else {
                showNotification('Produk berhasil dihapus dari wishlist', 'info');
              }
            } else {
              // Handle various error scenarios
              if (response.status === 401 || response.status === 403) {
                showNotification('Anda harus login terlebih dahulu untuk menambahkan produk ke wishlist', 'error');
                setTimeout(() => {
                  window.location.href = '/login';
                }, 1500);
              } else if (response.status === 400 && result?.message?.includes('sudah')) {
                // Product already in wishlist - just update UI to reflect this
                this.classList.add('active');
                heartIcon.setAttribute('fill', '#FF4757');
                heartIcon.setAttribute('stroke', '#FF4757');
                heartIcon.classList.remove('heart-outline');
                heartIcon.classList.add('heart-fill');
                showNotification('Produk sudah ada di wishlist', 'info');
              } else {
                showNotification('Gagal memperbarui wishlist: ' + (result?.message || 'Terjadi kesalahan saat memperbarui wishlist'), 'error');
              }
            }
          } catch (error) {
            // Handle network errors
            if (error.message && (error.message.includes('401') || error.message.includes('403'))) {
              showNotification('Anda harus login terlebih dahulu untuk menambahkan produk ke wishlist', 'error');
              setTimeout(() => {
                window.location.href = '/login';
              }, 1500);
            } else {
              console.error('Error updating wishlist:', error);
              showNotification('Terjadi kesalahan saat memperbarui wishlist: ' + (error.message || error.toString() || 'Terjadi kesalahan tidak terduga'), 'error');
            }
          }
        });
      }
      
      // Initialize wishlist icon state on page load by checking actual wishlist status
      const wishlistSmall = document.querySelector('.wish-small');
      if (wishlistSmall) {
        const heartIcon = wishlistSmall.querySelector('.heart-icon');
        const productId = '{{ $produk['id'] }}';

        // Store product ID in the wishlist button
        wishlistSmall.dataset.productId = productId;

        // Check if product is in wishlist via API to ensure correct initial state
        checkWishlistStatus(productId, wishlistSmall, heartIcon);
      }

      // Function to check wishlist status and update UI accordingly
      async function checkWishlistStatus(productId, button, heartIcon) {
        try {
          // Get the complete wishlist to check if product exists
          const response = await fetch('/wishlist', {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          if (response.ok) {
            const result = await response.json();
            let isInWishlist = false;

            // Check if the product ID exists in the wishlist
            if (result && Array.isArray(result.data)) {
              isInWishlist = result.data.some(item => item.product_id == productId || item.id == productId);
            } else if (result && Array.isArray(result)) {
              isInWishlist = result.some(item => item.product_id == productId || item.id == productId);
            }

            // Update button and icon based on actual wishlist status
            if (isInWishlist) {
              // Product is in wishlist
              button.classList.add('active');
              heartIcon.setAttribute('fill', '#FF4757');
              heartIcon.setAttribute('stroke', '#FF4757');
              heartIcon.classList.remove('heart-outline');
              heartIcon.classList.add('heart-fill');
            } else {
              // Product is not in wishlist
              button.classList.remove('active');
              heartIcon.setAttribute('fill', 'none');
              heartIcon.setAttribute('stroke', 'currentColor');
              heartIcon.classList.remove('heart-fill');
              heartIcon.classList.add('heart-outline');
            }
          } else {
            // Fallback to data from server side if API call fails
            const initialStatus = <?php echo json_encode($produk['di_wishlist'] ?? false); ?>;
            if (initialStatus) {
              button.classList.add('active');
              heartIcon.setAttribute('fill', '#FF4757');
              heartIcon.setAttribute('stroke', '#FF4757');
              heartIcon.classList.remove('heart-outline');
              heartIcon.classList.add('heart-fill');
            } else {
              button.classList.remove('active');
              heartIcon.setAttribute('fill', 'none');
              heartIcon.setAttribute('stroke', 'currentColor');
              heartIcon.classList.remove('heart-fill');
              heartIcon.classList.add('heart-outline');
            }
          }
        } catch (error) {
          // Fallback to server-side data if API call fails
          const initialStatus = <?php echo json_encode($produk['di_wishlist'] ?? false); ?>;
          if (initialStatus) {
            button.classList.add('active');
            heartIcon.setAttribute('fill', '#FF4757');
            heartIcon.setAttribute('stroke', '#FF4757');
            heartIcon.classList.remove('heart-outline');
            heartIcon.classList.add('heart-fill');
          } else {
            button.classList.remove('active');
            heartIcon.setAttribute('fill', 'none');
            heartIcon.setAttribute('stroke', 'currentColor');
            heartIcon.classList.remove('heart-fill');
            heartIcon.classList.add('heart-outline');
          }
        }
      }
      
      // Image gallery
      const thumbnails = document.querySelectorAll('.thumb');
      const mainImage = document.getElementById('mainImage');
      
      if (thumbnails.length > 0 && mainImage) {
        thumbnails.forEach(thumb => {
          thumb.addEventListener('click', function() {
            // Remove active class from all thumbs
            thumbnails.forEach(t => t.classList.remove('is-active'));
            // Add active class to clicked thumb
            this.classList.add('is-active');
            // Update main image
            mainImage.src = this.src;
          });
        });
      }
      
      // Add to cart functionality
      const addToCartBtn = document.querySelector('.btn-add-cart');
      if (addToCartBtn) {
        addToCartBtn.addEventListener('click', async function() {
          const productId = this.getAttribute('data-product-id');
          
          try {
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<span>Mengirim...</span>';
            this.disabled = true;
            
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
            
            if (result.success) {
              // Show success message
              showNotification(result.message, 'success');

              // Update cart count in header if exists
              const cartCount = document.querySelector('.cart-count');
              if (cartCount) {
                let count = parseInt(cartCount.textContent) || 0;
                cartCount.textContent = count + 1;
              }
            } else {
              showNotification(result.message || 'Gagal menambahkan ke keranjang', 'error');
            }
          } catch (error) {
            console.error('Error adding to cart:', error);
            showNotification('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
          } finally {
            // Restore original button state
            this.innerHTML = originalText;
            this.disabled = false;
          }
        });
      }
      
      // Buy now functionality
      const buyNowBtn = document.querySelector('.btn-buy-now');
      if (buyNowBtn) {
        buyNowBtn.addEventListener('click', async function() {
          const productId = this.getAttribute('data-product-id');
          
          try {
            // Show loading state
            const originalText = this.textContent;
            this.textContent = 'Memproses...';
            this.disabled = true;
            
            // First, add the product to cart (temporarily)
            const addToCartResponse = await fetch('/cart/add', {
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
            
            const addToCartResult = await addToCartResponse.json();
            
            if (addToCartResult.success) {
              // Redirect to checkout page
              window.location.href = '/keranjang';
            } else {
              showNotification(addToCartResult.message || 'Gagal menambahkan ke keranjang', 'error');
            }
          } catch (error) {
            console.error('Error with buy now:', error);
            showNotification('Terjadi kesalahan saat proses pembelian', 'error');
          } finally {
            // Restore original button state
            this.textContent = originalText;
            this.disabled = false;
          }
        });
      }
    });
  </script>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection