@extends('layouts.app')

@section('title', 'Kategori - UMKM AKRAB')

@section('header')
  @include('components.header_compact')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/kategori/kategori.css') }}">
@endpush

@section('content')
  <main class="kategori-page">
    <div class="container">
      <div class="page-header">
        <h1 id="kategori-title">Kategori</h1>
        <p id="kategori-description">Temukan berbagai produk menarik dalam kategori ini</p>
      </div>

      <div class="filter-bar">
        <div class="filter-group">
          <label for="sort-by">Urutkan:</label>
          <select id="sort-by" class="form-control">
            <option value="popular">Paling Populer</option>
            <option value="newest">Terbaru</option>
            <option value="price-low">Harga Terendah</option>
            <option value="price-high">Harga Tertinggi</option>
          </select>
        </div>
        <div class="filter-group">
          <label for="filter-by">Filter:</label>
          <select id="filter-by" class="form-control">
            <option value="all">Semua Produk</option>
            <option value="promo">Sedang Promo</option>
            <option value="new">Produk Baru</option>
          </select>
        </div>
      </div>

      <div class="products-grid" id="products-container">
        <!-- Products will be loaded here dynamically -->
        <!-- Page 1 -->
        <div class="product-card">
          <img src="https://picsum.photos/seed/product1/300/300" alt="Produk" class="product-image">
          <div class="product-info">
            <h3 class="product-name">Produk Kuliner 1</h3>
            <p class="product-description">Deskripsi singkat produk kuliner yang lezat dan berkualitas tinggi</p>
            <div class="product-price">Rp50.000</div>
            <button class="btn btn-primary view-product">Lihat Produk</button>
          </div>
        </div>
        
        <div class="product-card">
          <img src="https://picsum.photos/seed/product2/300/300" alt="Produk" class="product-image">
          <div class="product-info">
            <h3 class="product-name">Produk Fashion 1</h3>
            <p class="product-description">Deskripsi singkat produk fashion yang stylish dan nyaman</p>
            <div class="product-price">Rp120.000</div>
            <button class="btn btn-primary view-product">Lihat Produk</button>
          </div>
        </div>
        
        <div class="product-card">
          <img src="https://picsum.photos/seed/product3/300/300" alt="Produk" class="product-image">
          <div class="product-info">
            <h3 class="product-name">Produk Kerajinan 1</h3>
            <p class="product-description">Deskripsi singkat produk kerajinan tangan yang unik dan artistik</p>
            <div class="product-price">Rp85.000</div>
            <button class="btn btn-primary view-product">Lihat Produk</button>
          </div>
        </div>
        
        <div class="product-card">
          <img src="https://picsum.photos/seed/product4/300/300" alt="Produk" class="product-image">
          <div class="product-info">
            <h3 class="product-name">Produk Berkebun 1</h3>
            <p class="product-description">Deskripsi singkat produk berkebun yang alami dan berkualitas</p>
            <div class="product-price">Rp45.000</div>
            <button class="btn btn-primary view-product">Lihat Produk</button>
          </div>
        </div>
        
        <div class="product-card">
          <img src="https://picsum.photos/seed/product5/300/300" alt="Produk" class="product-image">
          <div class="product-info">
            <h3 class="product-name">Produk Kesehatan 1</h3>
            <p class="product-description">Deskripsi singkat produk kesehatan yang alami dan aman</p>
            <div class="product-price">Rp75.000</div>
            <button class="btn btn-primary view-product">Lihat Produk</button>
          </div>
        </div>
        
        <div class="product-card">
          <img src="https://picsum.photos/seed/product6/300/300" alt="Produk" class="product-image">
          <div class="product-info">
            <h3 class="product-name">Produk Mainan 1</h3>
            <p class="product-description">Deskripsi singkat produk mainan yang menyenangkan dan edukatif</p>
            <div class="product-price">Rp35.000</div>
            <button class="btn btn-primary view-product">Lihat Produk</button>
          </div>
        </div>
        
        <div class="product-card">
          <img src="https://picsum.photos/seed/product7/300/300" alt="Produk" class="product-image">
          <div class="product-info">
            <h3 class="product-name">Produk Hampers 1</h3>
            <p class="product-description">Deskripsi singkat produk hampers yang elegan dan lengkap</p>
            <div class="product-price">Rp250.000</div>
            <button class="btn btn-primary view-product">Lihat Produk</button>
          </div>
        </div>
        
        <div class="product-card">
          <img src="https://picsum.photos/seed/product8/300/300" alt="Produk" class="product-image">
          <div class="product-info">
            <h3 class="product-name">Produk Kuliner 2</h3>
            <p class="product-description">Deskripsi singkat produk kuliner yang lezat dan berkualitas tinggi</p>
            <div class="product-price">Rp65.000</div>
            <button class="btn btn-primary view-product">Lihat Produk</button>
          </div>
        </div>
      </div>

      <div class="produk-pagination">
        <button class="page-btn" id="first-page">«</button>
        <button class="page-btn" id="prev-page">‹</button>
        <button class="page-btn active" data-page="1">1</button>
        <button class="page-btn" data-page="2">2</button>
        <button class="page-btn" data-page="3">3</button>
        <button class="page-btn" data-page="4">4</button>
        <button class="page-btn" data-page="5">5</button>
        <button class="page-btn" id="next-page">›</button>
        <button class="page-btn" id="last-page">»</button>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Get category from URL parameter
      const urlParams = new URLSearchParams(window.location.search);
      const category = urlParams.get('kategori');
      
      // Update page title and description based on category
      if (category) {
        const titleElement = document.getElementById('kategori-title');
        const descElement = document.getElementById('kategori-description');
        
        // Map category codes to display names
        const categoryMap = {
          'kuliner': 'Kuliner',
          'fashion': 'Fashion',
          'kerajinan': 'Kerajinan Tangan',
          'berkebun': 'Produk Berkebun',
          'kesehatan': 'Produk Kesehatan',
          'mainan': 'Mainan',
          'hampers': 'Hampers'
        };
        
        const displayName = categoryMap[category] || category;
        titleElement.textContent = displayName;
        descElement.textContent = `Temukan berbagai produk ${displayName.toLowerCase()} menarik dari UMKM lokal`;
      }
      
      // Add to cart functionality
      const viewProductButtons = document.querySelectorAll('.view-product');
      viewProductButtons.forEach(button => {
        button.addEventListener('click', function() {
          const productCard = this.closest('.product-card');
          const productName = productCard.querySelector('.product-name').textContent;
          alert(`Anda akan diarahkan ke halaman detail produk untuk ${productName}`);
          // Here you would normally redirect to the product detail page
          // window.location.href = '/produk-detail?product=' + encodeURIComponent(productName);
        });
      });
      
      // Pagination functionality
      const pageButtons = document.querySelectorAll('.page-btn:not(#prev-page):not(#next-page):not(#first-page):not(#last-page)');
      const prevButton = document.getElementById('prev-page');
      const nextButton = document.getElementById('next-page');
      const firstButton = document.getElementById('first-page');
      const lastButton = document.getElementById('last-page');
      let currentPage = 1;
      const totalPages = 5;
      
      // Sample products data for different pages
      const productsData = {
        1: [
          {name: "Produk Kuliner 1", description: "Deskripsi singkat produk kuliner yang lezat dan berkualitas tinggi", price: "Rp50.000", image: "https://picsum.photos/seed/product1/300/300"},
          {name: "Produk Fashion 1", description: "Deskripsi singkat produk fashion yang stylish dan nyaman", price: "Rp120.000", image: "https://picsum.photos/seed/product2/300/300"},
          {name: "Produk Kerajinan 1", description: "Deskripsi singkat produk kerajinan tangan yang unik dan artistik", price: "Rp85.000", image: "https://picsum.photos/seed/product3/300/300"},
          {name: "Produk Berkebun 1", description: "Deskripsi singkat produk berkebun yang alami dan berkualitas", price: "Rp45.000", image: "https://picsum.photos/seed/product4/300/300"},
          {name: "Produk Kesehatan 1", description: "Deskripsi singkat produk kesehatan yang alami dan aman", price: "Rp75.000", image: "https://picsum.photos/seed/product5/300/300"},
          {name: "Produk Mainan 1", description: "Deskripsi singkat produk mainan yang menyenangkan dan edukatif", price: "Rp35.000", image: "https://picsum.photos/seed/product6/300/300"},
          {name: "Produk Hampers 1", description: "Deskripsi singkat produk hampers yang elegan dan lengkap", price: "Rp250.000", image: "https://picsum.photos/seed/product7/300/300"},
          {name: "Produk Kuliner 2", description: "Deskripsi singkat produk kuliner yang lezat dan berkualitas tinggi", price: "Rp65.000", image: "https://picsum.photos/seed/product8/300/300"}
        ],
        2: [
          {name: "Produk Fashion 2", description: "Deskripsi singkat produk fashion yang stylish dan nyaman", price: "Rp110.000", image: "https://picsum.photos/seed/product9/300/300"},
          {name: "Produk Kerajinan 2", description: "Deskripsi singkat produk kerajinan tangan yang unik dan artistik", price: "Rp95.000", image: "https://picsum.photos/seed/product10/300/300"},
          {name: "Produk Berkebun 2", description: "Deskripsi singkat produk berkebun yang alami dan berkualitas", price: "Rp55.000", image: "https://picsum.photos/seed/product11/300/300"},
          {name: "Produk Kesehatan 2", description: "Deskripsi singkat produk kesehatan yang alami dan aman", price: "Rp80.000", image: "https://picsum.photos/seed/product12/300/300"},
          {name: "Produk Mainan 2", description: "Deskripsi singkat produk mainan yang menyenangkan dan edukatif", price: "Rp40.000", image: "https://picsum.photos/seed/product13/300/300"},
          {name: "Produk Hampers 2", description: "Deskripsi singkat produk hampers yang elegan dan lengkap", price: "Rp275.000", image: "https://picsum.photos/seed/product14/300/300"},
          {name: "Produk Kuliner 3", description: "Deskripsi singkat produk kuliner yang lezat dan berkualitas tinggi", price: "Rp55.000", image: "https://picsum.photos/seed/product15/300/300"},
          {name: "Produk Fashion 3", description: "Deskripsi singkat produk fashion yang stylish dan nyaman", price: "Rp130.000", image: "https://picsum.photos/seed/product16/300/300"}
        ],
        3: [
          {name: "Produk Kerajinan 3", description: "Deskripsi singkat produk kerajinan tangan yang unik dan artistik", price: "Rp75.000", image: "https://picsum.photos/seed/product17/300/300"},
          {name: "Produk Berkebun 3", description: "Deskripsi singkat produk berkebun yang alami dan berkualitas", price: "Rp60.000", image: "https://picsum.photos/seed/product18/300/300"},
          {name: "Produk Kesehatan 3", description: "Deskripsi singkat produk kesehatan yang alami dan aman", price: "Rp90.000", image: "https://picsum.photos/seed/product19/300/300"},
          {name: "Produk Mainan 3", description: "Deskripsi singkat produk mainan yang menyenangkan dan edukatif", price: "Rp45.000", image: "https://picsum.photos/seed/product20/300/300"},
          {name: "Produk Hampers 3", description: "Deskripsi singkat produk hampers yang elegan dan lengkap", price: "Rp300.000", image: "https://picsum.photos/seed/product21/300/300"},
          {name: "Produk Kuliner 4", description: "Deskripsi singkat produk kuliner yang lezat dan berkualitas tinggi", price: "Rp70.000", image: "https://picsum.photos/seed/product22/300/300"},
          {name: "Produk Fashion 4", description: "Deskripsi singkat produk fashion yang stylish dan nyaman", price: "Rp140.000", image: "https://picsum.photos/seed/product23/300/300"},
          {name: "Produk Kerajinan 4", description: "Deskripsi singkat produk kerajinan tangan yang unik dan artistik", price: "Rp105.000", image: "https://picsum.photos/seed/product24/300/300"}
        ],
        4: [
          {name: "Produk Berkebun 4", description: "Deskripsi singkat produk berkebun yang alami dan berkualitas", price: "Rp65.000", image: "https://picsum.photos/seed/product25/300/300"},
          {name: "Produk Kesehatan 4", description: "Deskripsi singkat produk kesehatan yang alami dan aman", price: "Rp95.000", image: "https://picsum.photos/seed/product26/300/300"},
          {name: "Produk Mainan 4", description: "Deskripsi singkat produk mainan yang menyenangkan dan edukatif", price: "Rp50.000", image: "https://picsum.photos/seed/product27/300/300"},
          {name: "Produk Hampers 4", description: "Deskripsi singkat produk hampers yang elegan dan lengkap", price: "Rp325.000", image: "https://picsum.photos/seed/product28/300/300"},
          {name: "Produk Kuliner 5", description: "Deskripsi singkat produk kuliner yang lezat dan berkualitas tinggi", price: "Rp75.000", image: "https://picsum.photos/seed/product29/300/300"},
          {name: "Produk Fashion 5", description: "Deskripsi singkat produk fashion yang stylish dan nyaman", price: "Rp150.000", image: "https://picsum.photos/seed/product30/300/300"},
          {name: "Produk Kerajinan 5", description: "Deskripsi singkat produk kerajinan tangan yang unik dan artistik", price: "Rp115.000", image: "https://picsum.photos/seed/product31/300/300"},
          {name: "Produk Berkebun 5", description: "Deskripsi singkat produk berkebun yang alami dan berkualitas", price: "Rp70.000", image: "https://picsum.photos/seed/product32/300/300"}
        ],
        5: [
          {name: "Produk Kesehatan 5", description: "Deskripsi singkat produk kesehatan yang alami dan aman", price: "Rp100.000", image: "https://picsum.photos/seed/product33/300/300"},
          {name: "Produk Mainan 5", description: "Deskripsi singkat produk mainan yang menyenangkan dan edukatif", price: "Rp55.000", image: "https://picsum.photos/seed/product34/300/300"},
          {name: "Produk Hampers 5", description: "Deskripsi singkat produk hampers yang elegan dan lengkap", price: "Rp350.000", image: "https://picsum.photos/seed/product35/300/300"},
          {name: "Produk Kuliner 6", description: "Deskripsi singkat produk kuliner yang lezat dan berkualitas tinggi", price: "Rp80.000", image: "https://picsum.photos/seed/product36/300/300"},
          {name: "Produk Fashion 6", description: "Deskripsi singkat produk fashion yang stylish dan nyaman", price: "Rp160.000", image: "https://picsum.photos/seed/product37/300/300"},
          {name: "Produk Kerajinan 6", description: "Deskripsi singkat produk kerajinan tangan yang unik dan artistik", price: "Rp125.000", image: "https://picsum.photos/seed/product38/300/300"},
          {name: "Produk Berkebun 6", description: "Deskripsi singkat produk berkebun yang alami dan berkualitas", price: "Rp75.000", image: "https://picsum.photos/seed/product39/300/300"},
          {name: "Produk Kesehatan 6", description: "Deskripsi singkat produk kesehatan yang alami dan aman", price: "Rp105.000", image: "https://picsum.photos/seed/product40/300/300"}
        ]
      };
      
      // Function to render products for a specific page
      function renderProducts(page) {
        const productsContainer = document.getElementById('products-container');
        const products = productsData[page] || productsData[1];
        
        // Clear current products
        productsContainer.innerHTML = '';
        
        // Add products for this page
        products.forEach(product => {
          const productCard = document.createElement('div');
          productCard.className = 'product-card';
          productCard.innerHTML = `
            <img src="${product.image}" alt="Produk" class="product-image">
            <div class="product-info">
              <h3 class="product-name">${product.name}</h3>
              <p class="product-description">${product.description}</p>
              <div class="product-price">${product.price}</div>
              <button class="btn btn-primary view-product">Lihat Produk</button>
            </div>
          `;
          productsContainer.appendChild(productCard);
        });
        
        // Reattach event listeners to new view product buttons
        const newViewProductButtons = productsContainer.querySelectorAll('.view-product');
        newViewProductButtons.forEach(button => {
          button.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            const productName = productCard.querySelector('.product-name').textContent;
            alert(`Anda akan diarahkan ke halaman detail produk untuk ${productName}`);
            // Here you would normally redirect to the product detail page
            // window.location.href = '/produk-detail?product=' + encodeURIComponent(productName);
          });
        });
      }
      
      // Update active page button
      function updateActivePage(newPage) {
        // Remove active class from all page buttons
        pageButtons.forEach(button => {
          button.classList.remove('active');
        });
        
        // Add active class to current page button
        const currentPageButton = document.querySelector(`.page-btn[data-page="${newPage}"]`);
        if (currentPageButton) {
          currentPageButton.classList.add('active');
        }
        
        currentPage = newPage;
      }
      
      // Page button click handlers
      pageButtons.forEach(button => {
        button.addEventListener('click', function() {
          const page = parseInt(this.getAttribute('data-page'));
          if (page && page !== currentPage) {
            updateActivePage(page);
            renderProducts(page);
          }
        });
      });
      
      // Previous button handler
      prevButton.addEventListener('click', function() {
        if (currentPage > 1) {
          updateActivePage(currentPage - 1);
          renderProducts(currentPage);
        }
      });
      
      // Next button handler
      nextButton.addEventListener('click', function() {
        if (currentPage < totalPages) {
          updateActivePage(currentPage + 1);
          renderProducts(currentPage);
        }
      });
      
      // First page button handler
      firstButton.addEventListener('click', function() {
        if (currentPage > 1) {
          updateActivePage(1);
          renderProducts(1);
        }
      });
      
      // Last page button handler
      lastButton.addEventListener('click', function() {
        if (currentPage < totalPages) {
          updateActivePage(totalPages);
          renderProducts(totalPages);
        }
      });
    });
  </script>
@endpush