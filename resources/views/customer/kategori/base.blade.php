@extends('layouts.app')

@section('title', 'Kategori - UMKM AKRAB')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/kategori/kategori.css') }}">
@endpush

@section('content')
  <main class="kategori-page">
    <div class="container">
      <div class="page-header">
        <h1 id="kategori-title">@yield('category-title')</h1>
        <p id="kategori-description">@yield('category-description')</p>
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
        @yield('category-products')
      </div>

      <div class="produk-pagination">
        <button class="page-btn" id="first-page">«</button>
        <button class="page-btn" id="prev-page">‹</button>
        @for ($i = 1; $i <= 5; $i++)
          <button class="page-btn @if($i == 1) active @endif" data-page="{{ $i }}">{{ $i }}</button>
        @endfor
        <button class="page-btn" id="next-page">›</button>
        <button class="page-btn" id="last-page">»</button>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
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
        1: @yield('page-1-products'),
        2: @yield('page-2-products'),
        3: @yield('page-3-products'),
        4: @yield('page-4-products'),
        5: @yield('page-5-products')
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

@section('footer')
  @include('components.customer.footer.footer')
@endsection