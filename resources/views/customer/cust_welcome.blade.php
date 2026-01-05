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

    $html .= str_repeat('<svg width="20" height="20" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M17.3316 32.9493L23.5003 29.2285L29.6691 32.9982L28.0535 25.9482L33.4878 21.1993L26.3399 20.6118L23.5003 13.9535L20.6607 20.5628L13.5128 21.1993L18.9472 25.9482L17.3316 32.9493ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#D1D5DB"/></svg>', $emptyStars);

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

{{-- <script>
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
// Check if currentProduk is already declared in external JS to prevent duplicate declaration
if (typeof currentProduk === 'undefined') {
    let currentProduk = null;
}

function openProdukModal(idx, productId) {
    // Try to get product data from server-rendered content first
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    if (productCard) {
        // Get product details from the card (or fetch from API if needed)
        fetch(`/api/products/${productId}`)
            .then(async response => {
                // Check if response is ok before parsing JSON
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                // Check content type before parsing JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Response is not JSON:', text);
                    throw new Error('Response is not JSON');
                }

                return response.json();
            })
            .then(produk => {
                // Use window.currentProduk to make it accessible globally
                window.currentProduk = produk;

                const modal = document.getElementById('modal-detail-produk');
                const modalProduct = document.getElementById('modal-product');
                const modalImg = document.getElementById('modal-img');
                const modalPrice = document.getElementById('modal-price');
                const modalDesc = document.getElementById('modal-desc');
                // const modalSpecs = document.getElementById('modal-specs');
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
                // modalSpecs.innerHTML = '';
                // if (produk.spesifikasi || produk.specifications) {
                //     const specs = produk.spesifikasi || produk.specifications;

                //     // Handle both object and array formats
                //     if (typeof specs === 'object' && specs !== null && !Array.isArray(specs)) {
                //         // If it's an object format: { key: value }
                //         Object.entries(specs).forEach(([key, value]) => {
                //             if (value !== null && value !== undefined && value !== '') {
                //                 const li = document.createElement('li');
                //                 li.innerHTML = `<strong>${key}:</strong> ${value}`;
                //                 modalSpecs.appendChild(li);
                //             }
                //         });
                //     } else if (Array.isArray(specs)) {
                //         // If it's an array format: [ { key: 'Kategori', value: 'Kuliner' }, ... ]
                //         specs.forEach(spec => {
                //             if (typeof spec === 'object' && spec.key && spec.value !== undefined) {
                //                 const li = document.createElement('li');
                //                 li.innerHTML = `<strong>${spec.key}:</strong> ${spec.value}`;
                //                 modalSpecs.appendChild(li);
                //             } else if (typeof spec === 'string' && spec.includes(':')) {
                //                 // If it's a string like "Kategori: Kuliner"
                //                 const [key, ...valueParts] = spec.split(':');
                //                 const value = valueParts.join(':').trim();
                //                 const li = document.createElement('li');
                //                 li.innerHTML = `<strong>${key.trim()}:</strong> ${value}`;
                //                 modalSpecs.appendChild(li);
                //             }
                //         });
                //     } else {
                //         // For any other format, just try to display it properly
                //         const specsStr = String(specs);
                //         specsStr.split('\n').forEach(spec => {
                //             if (spec && spec.includes(':')) {
                //                 const [key, ...valueParts] = spec.split(':');
                //                 const value = valueParts.join(':').trim();
                //                 const li = document.createElement('li');
                //                 li.innerHTML = `<strong>${key.trim()}:</strong> ${value}`;
                //                 modalSpecs.appendChild(li);
                //             }
                //         });
                //     }
                // }

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

            // Update cart count in header via API
            if (window.updateCartCount) {
                window.updateCartCount();
            }
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
        // Check if currentProduk exists in this scope, otherwise try the one from external JS
        const productToUse = typeof currentProduk !== 'undefined' && currentProduk ? currentProduk : window.currentProduk;
        if (productToUse && productToUse.id) {
            window.location.href = `/produk_detail/${productToUse.id}`;
        } else {
            alert('Menuju halaman detail produk (demo)');
        }
    }
});

// Prevent modal from closing when clicking on content
document.querySelector('.modal-content-new').addEventListener('click', function(e) {
    e.stopPropagation();
});

// Mobile-specific enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Menambahkan fungsi untuk mengelola menu mobile
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const mobileNav = document.querySelector('.mobile-nav');

    if (hamburgerMenu && mobileNav) {
        hamburgerMenu.addEventListener('click', function() {
            // Toggle mobile navigation
            mobileNav.classList.toggle('active');
            hamburgerMenu.classList.toggle('active');

            // Prevent background scrolling when menu is open
            if (mobileNav.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const isClickInsideHamburger = hamburgerMenu.contains(event.target);
            const isClickInsideMobileNav = mobileNav.contains(event.target);

            if (!isClickInsideHamburger && !isClickInsideMobileNav && mobileNav.classList.contains('active')) {
                mobileNav.classList.remove('active');
                hamburgerMenu.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });

        // Close mobile menu on window resize to desktop size
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                mobileNav.classList.remove('active');
                hamburgerMenu.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    }

    // Fungsi untuk animasi fade-in pada scroll
    const animateOnScrollElements = document.querySelectorAll('.animate-on-scroll');

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-visible');
            }
        });
    }, observerOptions);

    animateOnScrollElements.forEach(element => {
        observer.observe(element);
    });

    // Touch event optimizations
    const touchElements = document.querySelectorAll('button, .produk-card, .category-card, a');

    touchElements.forEach(element => {
        element.addEventListener('touchstart', function() {
            this.classList.add('touch-active');
        });

        element.addEventListener('touchend', function() {
            this.classList.remove('touch-active');
            // Remove class after a short delay to prevent flickering
            setTimeout(() => {
                this.classList.remove('touch-active');
            }, 200);
        });

        // Cleanup class on touch cancel or mouse event
        element.addEventListener('touchcancel', function() {
            this.classList.remove('touch-active');
        });

        element.addEventListener('mouseleave', function() {
            this.classList.remove('touch-active');
        });
    });

    // Prevent double tap zoom on touch devices
    let lastTouchEnd = 0;
    document.addEventListener('touchend', function (event) {
        const now = (new Date()).getTime();
        if (now - lastTouchEnd <= 300) {
            event.preventDefault();
        }
        lastTouchEnd = now;
    }, false);
});
</script> --}}
<script src="{{ asset('js/customer/cust_welcome.js') }}?v=22"></script>
@endsection

@section('footer')
  @include('components.customer.footer.footer')
@endsection
