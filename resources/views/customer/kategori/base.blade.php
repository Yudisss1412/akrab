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

      <div class="kategori-layout">
        <!-- Sidebar Filter -->
        <aside class="sidebar-filter">
          <div class="filter-card">
            <h2>Filter Produk</h2>
            
            <!-- Sub-kategori Filter -->
            <div class="filter-section">
              <div class="filter-header" onclick="toggleFilterSection('subkategori')">
                <h3>Sub-kategori</h3>
                <span class="filter-toggle">+</span>
              </div>
              <div class="filter-content" id="subkategori-content">
                <div class="filter-checkbox-group">
                  <label class="checkbox-label">
                    <input type="checkbox" name="subkategori[]" value="makanan"> Makanan
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="subkategori[]" value="minuman"> Minuman
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="subkategori[]" value="pakaian-pria"> Pakaian Pria
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="subkategori[]" value="pakaian-wanita"> Pakaian Wanita
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="subkategori[]" value="aksesoris"> Aksesoris
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="subkategori[]" value="kerajinan-logam"> Kerajinan Logam
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="subkategori[]" value="kerajinan-kayu"> Kerajinan Kayu
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="subkategori[]" value="tanaman-hias"> Tanaman Hias
                  </label>
                </div>
              </div>
            </div>
            
            <!-- Rentang Harga Filter -->
            <div class="filter-section">
              <div class="filter-header" onclick="toggleFilterSection('harga')">
                <h3>Rentang Harga</h3>
                <span class="filter-toggle">+</span>
              </div>
              <div class="filter-content" id="harga-content">
                <div class="price-range-inputs">
                  <div class="input-group">
                    <label for="min-price">Harga Minimum</label>
                    <input type="number" id="min-price" class="form-control" placeholder="Rp 0">
                  </div>
                  <div class="input-group">
                    <label for="max-price">Harga Maksimum</label>
                    <input type="number" id="max-price" class="form-control" placeholder="Rp 1.000.000">
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Rating Produk Filter -->
            <div class="filter-section">
              <div class="filter-header" onclick="toggleFilterSection('rating')">
                <h3>Rating Produk</h3>
                <span class="filter-toggle">+</span>
              </div>
              <div class="filter-content" id="rating-content">
                <div class="filter-checkbox-group">
                  <label class="checkbox-label">
                    <input type="checkbox" name="rating[]" value="4"> 4 Bintang ke atas
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="rating[]" value="3"> 3 Bintang ke atas
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="rating[]" value="2"> 2 Bintang ke atas
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="rating[]" value="1"> 1 Bintang ke atas
                  </label>
                </div>
              </div>
            </div>
            
            <!-- Lokasi Toko Filter -->
            <div class="filter-section">
              <div class="filter-header" onclick="toggleFilterSection('lokasi')">
                <h3>Lokasi Toko</h3>
                <span class="filter-toggle">+</span>
              </div>
              <div class="filter-content" id="lokasi-content">
                <div class="filter-checkbox-group">
                  <label class="checkbox-label">
                    <input type="checkbox" name="lokasi[]" value="kabupaten-banyuwangi"> Kabupaten Banyuwangi
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="lokasi[]" value="kota-banyuwangi"> Kota Banyuwangi
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="lokasi[]" value="giri"> Giri
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="lokasi[]" value="gambiran"> Gambiran
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="lokasi[]" value="srono"> Srono
                  </label>
                  <label class="checkbox-label">
                    <input type="checkbox" name="lokasi[]" value="muncar"> Muncar
                  </label>
                </div>
              </div>
            </div>
            
            <!-- Sort Filter -->
            <div class="filter-section">
              <div class="filter-header" onclick="toggleFilterSection('sort')">
                <h3>Urutkan</h3>
                <span class="filter-toggle">+</span>
              </div>
              <div class="filter-content" id="sort-content">
                <select id="sort-by" class="form-control">
                  <option value="popular">Paling Populer</option>
                  <option value="newest">Terbaru</option>
                  <option value="price-low">Harga Terendah</option>
                  <option value="price-high">Harga Tertinggi</option>
                </select>
              </div>
            </div>
            
            <!-- Filter Actions -->
            <div class="filter-actions">
              <button id="apply-filter" class="btn btn-primary">Terapkan Filter</button>
              <button id="reset-filter" class="btn btn-secondary">Reset Filter</button>
            </div>
          </div>
        </aside>

        <!-- Main Content (Products Grid) -->
        <div class="main-content">
          <div class="products-header">
            <div class="products-count">
              <p id="product-count">Menampilkan 8 dari 40 produk</p>
            </div>
            <div class="products-sort">
              <select id="sort-by-mobile" class="form-control">
                <option value="popular">Paling Populer</option>
                <option value="newest">Terbaru</option>
                <option value="price-low">Harga Terendah</option>
                <option value="price-high">Harga Tertinggi</option>
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
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <script>
    // Toggle filter sections
    function toggleFilterSection(sectionId) {
      const content = document.getElementById(sectionId + '-content');
      const toggle = content.previousElementSibling.querySelector('.filter-toggle');
      
      if (content.style.display === 'block') {
        content.style.display = 'none';
        toggle.textContent = '+';
      } else {
        content.style.display = 'block';
        toggle.textContent = '-';
      }
    }
    
    // Initialize filter sections - all closed by default
    document.addEventListener('DOMContentLoaded', function() {
      const filterContents = document.querySelectorAll('.filter-content');
      filterContents.forEach(content => {
        content.style.display = 'none';
      });
      
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
      
      // Apply filter button functionality
      document.getElementById('apply-filter').addEventListener('click', function() {
        // Get selected filters
        const minPrice = document.getElementById('min-price').value;
        const maxPrice = document.getElementById('max-price').value;
        const sortBy = document.getElementById('sort-by').value;
        
        // Get selected subcategories
        const subkategoriCheckboxes = document.querySelectorAll('input[name="subkategori[]"]:checked');
        const selectedSubkategori = Array.from(subkategoriCheckboxes).map(cb => cb.value);
        
        // Get selected ratings
        const ratingCheckboxes = document.querySelectorAll('input[name="rating[]"]:checked');
        const selectedRatings = Array.from(ratingCheckboxes).map(cb => cb.value);
        
        // Get selected locations
        const lokasiCheckboxes = document.querySelectorAll('input[name="lokasi[]"]:checked');
        const selectedLocations = Array.from(lokasiCheckboxes).map(cb => cb.value);
        
        console.log('Filters applied:', {
          minPrice,
          maxPrice,
          sortBy,
          subkategori: selectedSubkategori,
          ratings: selectedRatings,
          locations: selectedLocations
        });
        
        // Here you would normally fetch filtered products
        // For demo purposes, we'll show an alert
        alert('Filter berhasil diterapkan!');
      });
      
      // Reset filter button functionality
      document.getElementById('reset-filter').addEventListener('click', function() {
        // Reset all filters
        document.getElementById('min-price').value = '';
        document.getElementById('max-price').value = '';
        document.getElementById('sort-by').value = 'popular';
        
        // Uncheck all checkboxes
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
          checkbox.checked = false;
        });
        
        // Reset mobile sort
        document.getElementById('sort-by-mobile').value = 'popular';
        
        console.log('Filters reset');
        alert('Filter berhasil direset!');
      });
      
      // Mobile sort functionality
      document.getElementById('sort-by-mobile').addEventListener('change', function() {
        document.getElementById('sort-by').value = this.value;
        // Trigger the apply filter function or update products
        console.log('Sort changed to:', this.value);
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
            <img src="\${product.image}" alt="Produk" class="product-image">
            <div class="product-info">
              <h3 class="product-name">\${product.name}</h3>
              <p class="product-description">\${product.description}</p>
              <div class="product-price">\${product.price}</div>
              <button class="btn btn-primary view-product">Pratinjau</button>
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
            alert(\`Anda akan diarahkan ke halaman detail produk untuk \${productName}\`);
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
        const currentPageButton = document.querySelector(\`.page-btn[data-page="\${newPage}"]\`);
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