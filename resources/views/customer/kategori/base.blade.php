@extends('layouts.app')

@section('title', 'Kategori - UMKM AKRAB')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/kategori/kategori.css') }}">
  <style>
    /* ========== Modal Produk ========== */
    .modal-detail-produk {
      position: fixed;
      inset: 0;
      width: 100vw;
      height: 100vh;
      z-index: 9999;
      display: none;
      align-items: center;
      justify-content: center;
      background: rgba(0, 0, 0, 0.5);
      overflow-y: auto;
    }

    .modal-content-new {
      max-width: 420px;
      width: 95%; /* Wider on mobile */
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

    .modal-title-row {
      padding: 1.2rem 1.5rem 0.5rem 1.5rem;
      font-size: 1.25rem;
      font-weight: 600;
      color: #333;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: white;
      position: sticky;
      top: 0;
      z-index: 10;
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
      background: #fafbfc;
      padding: 0 0 1.2rem 0;
      flex-shrink: 0; /* Prevents the image section from shrinking */
    }

    .modal-img-main {
      width: 90%;
      max-width: 220px;
      height: auto;
      object-fit: cover;
      border-radius: 16px;
      background: #e5e5e5;
      margin: 0.5rem auto 0.7rem auto; /* Center the image */
    }

    .modal-thumbs-new {
      display: flex;
      gap: 0.6rem;
      margin-bottom: 0.5rem;
      justify-content: center; /* Center thumbs */
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

    .modal-body-scrollable {
      flex: 1;
      overflow-y: auto;
      padding: 0 1.5rem 1.1rem 1.5rem;
    }

    .modal-price-new {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--primary-color-dark);
      margin: 0 0 0.6rem 0;
    }

    .modal-desc-box-new {
      background: #f2fbf7;
      border-radius: 15px;
      padding: 1rem 1.3rem 0.8rem 1.3rem;
      margin: 0 0 1.1rem 0;
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

    .modal-actions {
      display: flex;
      flex-direction: column; /* Stack buttons on mobile */
      gap: 0.8rem;
      padding: 0 1.5rem 1.2rem 1.5rem;
      width: 100%;
    }

    .modal-btn {
      flex: 1;
      border: none;
      padding: 0.8rem 0; /* Increase padding for better touch target */
      font-size: 1.07rem;
      font-weight: 600;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.15s, transform 0.12s;
      width: 100%;
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

    /* Responsive modal for mobile */
    @media (max-width: 576px) {
      .modal-content-new {
        width: 98%;
        margin: 10px;
        max-height: 95vh;
      }

      .modal-title-row {
        padding: 1rem 1.2rem 0.3rem 1.2rem;
        font-size: 1.1rem;
      }

      .modal-img-main {
        max-width: 180px;
        margin: 0.5rem auto 0.5rem auto;
      }

      .modal-body-scrollable {
        padding: 0 1.2rem 0.8rem 1.2rem;
      }

      .modal-price-new {
        font-size: 1.2rem;
      }

      .modal-desc-box-new {
        padding: 0.8rem 1rem 0.6rem 1rem;
      }

      .modal-actions {
        padding: 0 1.2rem 1rem 1.2rem;
        gap: 0.6rem;
      }

      .modal-btn {
        padding: 0.7rem 0;
        font-size: 1rem;
      }
    }
  </style>

  <!-- Produk Detail Modal HTML -->
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
@endpush

@section('content')
  <main class="kategori-page">
    <div class="container">
      <div class="page-header">
        <h1 id="kategori-title">{{ $categoryTitle }}</h1>
        <p id="kategori-description">{{ $categoryDescription }}</p>
      </div>

      <!-- Mobile Filter Toggle Button -->
      <div class="mobile-filter-toggle-container">
        <button id="mobile-filter-toggle" class="btn btn-primary mobile-filter-btn">
          <span class="filter-icon">filtrasi</span>
        </button>
      </div>

      <div class="kategori-layout">
        <!-- Collapsible Sidebar Filter -->
        <aside class="sidebar-filter" id="sidebar-filter">
          <div class="filter-card">
            <div class="filter-header-top">
              <h2>Filter Produk</h2>
              <button id="close-sidebar-btn" class="close-sidebar-btn">&times;</button>
            </div>

            <!-- Sub-kategori Filter -->
            <div class="filter-section">
              <div class="filter-header">
                <h3>Sub-kategori</h3>
              </div>
              <div class="filter-content" id="subkategori-content">
                <div class="filter-checkbox-group">
                  @if(isset($subcategories) && $subcategories->count() > 0)
                    @foreach($subcategories as $subcategory)
                      <label class="checkbox-label">
                        <input type="checkbox" name="subkategori[]" value="{{ \Illuminate\Support\Str::slug($subcategory->name) }}"> {{ $subcategory->name }}
                      </label>
                    @endforeach
                  @else
                    <p class="no-subcategories">Tidak ada subkategori untuk kategori ini</p>
                  @endif
                </div>
              </div>
            </div>

            <!-- Rentang Harga Filter -->
            <div class="filter-section">
              <div class="filter-header">
                <h3>Rentang Harga</h3>
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
              <div class="filter-header">
                <h3>Rating Produk</h3>
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
              <div class="filter-header">
                <h3>Lokasi Toko</h3>
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
              <div class="filter-header">
                <h3>Urutkan</h3>
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

        <!-- Overlay untuk saat sidebar muncul di mobile -->
        <div id="sidebar-overlay" class="sidebar-overlay"></div>

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
            @section('category-products')
              <!-- Tampilkan produk halaman pertama secara default -->
              @if(isset($page_1_products) && count($page_1_products) > 0)
                @foreach($page_1_products as $product)
                  <div class="product-card">
                    <img class="product-image" src="{{ $product['image'] }}" alt="{{ $product['name'] }}" onerror="this.onerror=null; this.src='{{ asset('src/placeholder_produk.png') }}';">
                    <div class="product-info">
                      <h3 class="product-name">{{ $product['name'] }}</h3>
                      <p class="product-description">{{ Str::limit($product['description'], 100) }}</p>
                      <div class="product-price">{{ $product['price'] }}</div>
                      <button class="btn btn-primary view-product" data-product-id="{{ $product['id'] }}">Pratinjau</button>
                    </div>
                  </div>
                @endforeach
              @else
                <p class="no-products-message">Tidak ada produk dalam kategori ini.</p>
              @endif
            @show
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
    // Function to get asset URL
    function asset(path) {
      return '{{ asset("") }}' + path;
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
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

        // Check if at least one filter is selected
        const hasFilter = minPrice || maxPrice || selectedSubkategori.length > 0 ||
                         selectedRatings.length > 0 || selectedLocations.length > 0 ||
                         (sortBy && sortBy !== 'popular');

        if (!hasFilter) {
          showNotification('Pilih minimal satu filter sebelum menekan Terapkan Filter', 'warning');
          return;
        }

        console.log('Filters applied:', {
          minPrice,
          maxPrice,
          sortBy,
          subkategori: selectedSubkategori,
          ratings: selectedRatings,
          locations: selectedLocations
        });

        // Build query parameters for API call
        const params = new URLSearchParams();

        if (minPrice) params.append('min_price', minPrice);
        if (maxPrice) params.append('max_price', maxPrice);
        if (sortBy && sortBy !== 'popular') params.append('sort', sortBy);
        if (selectedSubkategori.length > 0) {
          selectedSubkategori.forEach(sub => params.append('subkategori[]', sub));
        }
        if (selectedRatings.length > 0) {
          selectedRatings.forEach(rating => params.append('rating[]', rating));
        }
        if (selectedLocations.length > 0) {
          selectedLocations.forEach(location => params.append('lokasi[]', location));
        }

        // Get the current category from the URL
        const currentPath = window.location.pathname;
        let apiUrl = '/api/products/filter'; // Default API endpoint

        // If we're on a specific category page, include it in the API call
        if (currentPath.includes('/kategori/')) {
          const categoryMatch = currentPath.split('/kategori/')[1];
          if (categoryMatch) {
            params.append('kategori', categoryMatch);
            apiUrl = '/api/products/filter';
          }
        }

        // Add other filters that might be relevant
        // Apply the filter by making an API call and updating the product list
        applyProductFilters(apiUrl, params);

        // Reset to page 1 after applying filters
        updateActivePage(1);
        currentPage = 1;
      });

      // Reset filter button functionality
      document.getElementById('reset-filter').addEventListener('click', function() {
        // Reset all filter inputs
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

        // Reload original products (without any filters)
        resetProductFilters();
      });

      // Mobile sort functionality
      document.getElementById('sort-by-mobile').addEventListener('change', function() {
        document.getElementById('sort-by').value = this.value;
        // Trigger the apply filter function or update products
        console.log('Sort changed to:', this.value);
      });
    });

      // Function to apply product filters via API
      function applyProductFilters(apiUrl, params) {
        fetch(apiUrl + '?' + params.toString(), {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Update the product grid with filtered results
            const productsContainer = document.getElementById('products-container');
            if (productsContainer) {
              productsContainer.innerHTML = '';

              if (data.products && data.products.length > 0) {
                // Display filtered products
                data.products.forEach(product => {
                  const productCard = document.createElement('div');
                  productCard.className = 'product-card';
                  productCard.setAttribute('data-product-id', product.id);

                  // Create elements individually to avoid issues with special characters
                  const img = document.createElement('img');
                  img.src = product.image || asset('src/placeholder_produk.png');
                  img.alt = product.name || 'Produk';
                  img.className = 'product-image';
                  // Handle image load error
                  img.onerror = function() {
                    this.onerror = null; // Prevent infinite loop
                    this.src = asset('src/placeholder_produk.png'); // Fallback to default placeholder
                  };

                  const infoDiv = document.createElement('div');
                  infoDiv.className = 'product-info';

                  const nameH3 = document.createElement('h3');
                  nameH3.className = 'product-name';
                  // Basic sanitization for product name to prevent syntax errors
                  const sanitizedName = (product.name || 'Nama Produk').toString().replace(/[&<>"']/g, '');
                  nameH3.textContent = sanitizedName.substring(0, 200);

                  const descP = document.createElement('p');
                  descP.className = 'product-description';
                  // Basic sanitization for product description to prevent syntax errors
                  const sanitizedDesc = (product.description || 'Deskripsi produk').toString().replace(/[&<>"']/g, '');
                  descP.textContent = sanitizedDesc.substring(0, 500);

                  const priceDiv = document.createElement('div');
                  priceDiv.className = 'product-price';
                  // Basic sanitization for price to prevent syntax errors
                  const sanitizedPrice = (product.price || 'Rp 0').toString().replace(/[&<>"']/g, '');
                  priceDiv.textContent = sanitizedPrice.substring(0, 50);

                  const button = document.createElement('button');
                  button.className = 'btn btn-primary view-product';
                  button.setAttribute('data-product-id', product.id);
                  button.textContent = 'Pratinjau';

                  // Append elements in order
                  infoDiv.appendChild(nameH3);
                  infoDiv.appendChild(descP);
                  infoDiv.appendChild(priceDiv);
                  infoDiv.appendChild(button);
                  productCard.appendChild(img);
                  productCard.appendChild(infoDiv);

                  productsContainer.appendChild(productCard);
                });

                // Update product count
                const productCount = document.getElementById('product-count');
                if (productCount) {
                  productCount.textContent = 'Menampilkan ' + data.products.length + ' dari ' + data.total + ' produk';
                }
              } else {
                // Show no results message
                productsContainer.innerHTML = '';
                const noProductsDiv = document.createElement('div');
                noProductsDiv.className = 'no-products-message';
                const noProductsP = document.createElement('p');
                noProductsP.textContent = 'Maaf, tidak ada produk yang sesuai dengan filter Anda.';
                noProductsDiv.appendChild(noProductsP);
                productsContainer.appendChild(noProductsDiv);
              }
            }
          } else {
            console.error('Error applying filters:', data.message);
            showNotification(data.message || 'Terjadi kesalahan saat menerapkan filter', 'error');
          }
        })
        .catch(error => {
          console.error('Error applying filters:', error);
          showNotification('Terjadi kesalahan saat menerapkan filter', 'error');
        });
      }

      // Reset filter button functionality
      document.getElementById('reset-filter').addEventListener('click', function() {
        // Reset all filter inputs
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

        // Reload original products (without any filters)
        resetProductFilters();
      });

      // Function to reset and reload original products
      function resetProductFilters() {
        // Reset all filter inputs first
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

        // Get the current category from the URL to reload original products
        const currentPath = window.location.pathname;
        let apiUrl = '/api/products/filter'; // Default API endpoint
        const params = new URLSearchParams();

        // If we're on a specific category page, include it to get original category products
        if (currentPath.includes('/kategori/')) {
          const categoryMatch = currentPath.split('/kategori/')[1];
          if (categoryMatch) {
            params.append('kategori', categoryMatch);
            apiUrl = '/api/products/filter';
          }
        }

        // Call API to get original products without filters
        applyProductFilters(apiUrl, params);

        // Reset to page 1 after resetting filters
        updateActivePage(1);
        currentPage = 1;
        showNotification('Filter berhasil direset', 'success');
      }

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

      // Sample products data for different pages (now unused due to direct API calls)
      const productsData = {
        1: [],
        2: [],
        3: [],
        4: [],
        5: []
      };

      // Function to render products for a specific page
      function renderProducts(page) {
        // Reload products with current filters applied
        const params = new URLSearchParams();
        params.append('page', page);

        const currentPath = window.location.pathname;
        let apiUrl = '/api/products/filter';

        if (currentPath.includes('/kategori/')) {
          const categoryMatch = currentPath.split('/kategori/')[1];
          if (categoryMatch) {
            params.append('kategori', categoryMatch);
          }
        }

        // Apply filters if any - check if filters are active
        const minPrice = document.getElementById('min-price').value;
        const maxPrice = document.getElementById('max-price').value;
        const sortBy = document.getElementById('sort-by').value;

        if (minPrice) params.append('min_price', minPrice);
        if (maxPrice) params.append('max_price', maxPrice);
        if (sortBy && sortBy !== 'popular') params.append('sort', sortBy);

        // Get selected subcategories
        const subkategoriCheckboxes = document.querySelectorAll('input[name="subkategori[]"]:checked');
        const selectedSubkategori = Array.from(subkategoriCheckboxes).map(cb => cb.value);
        selectedSubkategori.forEach(sub => params.append('subkategori[]', sub));

        // Get selected ratings
        const ratingCheckboxes = document.querySelectorAll('input[name="rating[]"]:checked');
        const selectedRatings = Array.from(ratingCheckboxes).map(cb => cb.value);
        selectedRatings.forEach(rating => params.append('rating[]', rating));

        // Get selected locations
        const lokasiCheckboxes = document.querySelectorAll('input[name="lokasi[]"]:checked');
        const selectedLocations = Array.from(lokasiCheckboxes).map(cb => cb.value);
        selectedLocations.forEach(location => params.append('lokasi[]', location));

        fetch(apiUrl + '?' + params.toString(), {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const productsContainer = document.getElementById('products-container');
            if (productsContainer) {
              productsContainer.innerHTML = '';

              if (data.products && data.products.length > 0) {
                // Display products from API response
                data.products.forEach(product => {
                  const productCard = document.createElement('div');
                  productCard.className = 'product-card';
                  // Add data-product-id attribute to the product card to make the modal work
                  productCard.setAttribute('data-product-id', product.id || 1);

                  // Create elements individually to avoid issues with special characters
                  const img = document.createElement('img');
                  img.src = product.image || asset('src/placeholder_produk.png');
                  img.alt = product.name || 'Produk';
                  img.className = 'product-image';
                  // Handle image load error
                  img.onerror = function() {
                    this.onerror = null; // Prevent infinite loop
                    this.src = asset('src/placeholder_produk.png'); // Fallback to default placeholder
                  };

                  const infoDiv = document.createElement('div');
                  infoDiv.className = 'product-info';

                  const nameH3 = document.createElement('h3');
                  nameH3.className = 'product-name';
                  // Basic sanitization for product name to prevent syntax errors
                  const sanitizedName = (product.name || 'Nama Produk').toString().replace(/[&<>"']/g, '');
                  nameH3.textContent = sanitizedName.substring(0, 200);

                  const descP = document.createElement('p');
                  descP.className = 'product-description';
                  // Basic sanitization for product description to prevent syntax errors
                  const sanitizedDesc = (product.description || 'Deskripsi produk').toString().replace(/[&<>"']/g, '');
                  descP.textContent = sanitizedDesc.substring(0, 500);

                  const priceDiv = document.createElement('div');
                  priceDiv.className = 'product-price';
                  // Basic sanitization for price to prevent syntax errors
                  const sanitizedPrice = (product.price || 'Rp 0').toString().replace(/[&<>"']/g, '');
                  priceDiv.textContent = sanitizedPrice.substring(0, 50);

                  const button = document.createElement('button');
                  button.className = 'btn btn-primary view-product';
                  button.setAttribute('data-product-id', product.id || 1);
                  button.textContent = 'Pratinjau';

                  // Append elements in order
                  infoDiv.appendChild(nameH3);
                  infoDiv.appendChild(descP);
                  infoDiv.appendChild(priceDiv);
                  infoDiv.appendChild(button);
                  productCard.appendChild(img);
                  productCard.appendChild(infoDiv);

                  productsContainer.appendChild(productCard);
                });

                // Update product count
                const productCount = document.getElementById('product-count');
                if (productCount) {
                  productCount.textContent = 'Menampilkan ' + data.products.length + ' dari ' + data.total + ' produk';
                }
              } else {
                // Show no results message
                productsContainer.innerHTML = '';
                const noProductsDiv = document.createElement('div');
                noProductsDiv.className = 'no-products-message';
                const noProductsP = document.createElement('p');
                noProductsP.textContent = 'Maaf, tidak ada produk yang sesuai dengan filter Anda.';
                noProductsDiv.appendChild(noProductsP);
                productsContainer.appendChild(noProductsDiv);
              }
            }
          } else {
            console.error('Error loading products for page:', data.message);
          }
        })
        .catch(error => {
          console.error('Error loading products for page:', error);
          showNotification('Terjadi kesalahan saat memuat produk', 'error');
        });
      }

      // Update active page button
      function updateActivePage(newPage) {
        // Remove active class from all page buttons
        pageButtons.forEach(button => {
          button.classList.remove('active');
        });

        // Add active class to current page button
        const currentPageButton = document.querySelector('.page-btn[data-page="' + newPage + '"]');
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

      // Initialize products on page load
      // Check if products are already loaded from server, if not then load via API
      window.addEventListener('load', function() {
        const productsContainer = document.getElementById('products-container');
        const initialProducts = productsContainer.querySelectorAll('.product-card');

        // If no products were loaded from server, load them via API
        if (initialProducts.length === 0) {
          // Load the first page of products via API
          renderProducts(1);
        } else {
          // Products are already loaded from server, so set the current page to 1
          // and update the active page button to reflect page 1 as active
          currentPage = 1;
          updateActivePage(1);

          // Update product count based on the initial products loaded from server
          const productCountElement = document.getElementById('product-count');
          if (productCountElement) {
            productCountElement.textContent = 'Menampilkan ' + initialProducts.length + ' dari ' + initialProducts.length + ' produk';
          }
        }
      });

      // Function to show product detail modal
      function showModalDetail(name, image, price, description) {
        // Create modal elements if they don't exist
        let modal = document.getElementById('product-detail-modal');
        if (!modal) {
          // Create modal overlay
          const overlay = document.createElement('div');
          overlay.id = 'product-detail-modal';
          overlay.className = 'modal-overlay';
          overlay.style.position = 'fixed';
          overlay.style.top = '0';
          overlay.style.left = '0';
          overlay.style.width = '100%';
          overlay.style.height = '100%';
          overlay.style.background = 'rgba(0,0,0,0.7)';
          overlay.style.display = 'flex';
          overlay.style.justifyContent = 'center';
          overlay.style.alignItems = 'center';
          overlay.style.zIndex = '10000';
          overlay.style.opacity = '0';
          overlay.style.transition = 'opacity 0.3s ease';

          // Create modal container
          const container = document.createElement('div');
          container.className = 'modal-container';
          container.style.background = '#fff';
          container.style.borderRadius = '8px';
          container.style.width = '90%';
          container.style.maxWidth = '600px';
          container.style.maxHeight = '90vh';
          container.style.overflowY = 'auto';
          container.style.position = 'relative';
          container.style.opacity = '0';
          container.style.transform = 'scale(0.8)';
          container.style.transition = 'all 0.3s ease';

          // Create close button
          const closeBtn = document.createElement('button');
          closeBtn.innerHTML = '&times;';
          closeBtn.style.position = 'absolute';
          closeBtn.style.top = '10px';
          closeBtn.style.right = '15px';
          closeBtn.style.background = 'none';
          closeBtn.style.border = 'none';
          closeBtn.style.fontSize = '24px';
          closeBtn.style.cursor = 'pointer';
          closeBtn.style.zIndex = '10';
          closeBtn.onclick = () => hideModal();

          // Create modal content
          const content = document.createElement('div');
          content.className = 'modal-content';
          content.style.padding = '20px';

          // Create modal structure using DOM methods to avoid issues with special characters
          const modalImageDiv = document.createElement('div');
          modalImageDiv.className = 'modal-product-image';
          modalImageDiv.style.cssText = 'text-align: center; margin-bottom: 15px;';

          const modalImage = document.createElement('img');
          modalImage.id = 'modal-product-image';
          modalImage.alt = '';
          modalImage.style.cssText = 'max-width: 100%; max-height: 300px; border-radius: 4px;';
          modalImageDiv.appendChild(modalImage);

          const modalName = document.createElement('h3');
          modalName.id = 'modal-product-name';
          modalName.style.cssText = 'margin: 10px 0;';

          const modalPrice = document.createElement('div');
          modalPrice.id = 'modal-product-price';
          modalPrice.style.cssText = 'font-size: 1.5em; color: #e74c3c; font-weight: bold; margin: 10px 0;';

          const modalDescription = document.createElement('p');
          modalDescription.id = 'modal-product-description';
          modalDescription.style.cssText = 'color: #555; margin: 15px 0; line-height: 1.5;';

          const modalActions = document.createElement('div');
          modalActions.className = 'modal-actions';
          modalActions.style.cssText = 'display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;';

          const viewDetailBtn = document.createElement('button');
          viewDetailBtn.id = 'modal-view-detail-btn';
          viewDetailBtn.className = 'btn btn-primary';
          viewDetailBtn.style.cssText = 'padding: 8px 16px; border-radius: 4px;';
          viewDetailBtn.textContent = 'Lihat Detail';

          const addCartBtn = document.createElement('button');
          addCartBtn.id = 'modal-add-cart-btn';
          addCartBtn.className = 'btn btn-outline';
          addCartBtn.style.cssText = 'padding: 8px 16px; border-radius: 4px;';
          addCartBtn.textContent = 'Tambah ke Keranjang';

          modalActions.appendChild(viewDetailBtn);
          modalActions.appendChild(addCartBtn);

          content.appendChild(modalImageDiv);
          content.appendChild(modalName);
          content.appendChild(modalPrice);
          content.appendChild(modalDescription);
          content.appendChild(modalActions);

          container.appendChild(closeBtn);
          container.appendChild(content);
          overlay.appendChild(container);
          document.body.appendChild(overlay);

          modal = overlay;

          // Add event listener to close modal when clicking on overlay
          modal.addEventListener('click', function(e) {
            if (e.target === modal) {
              hideModal();
            }
          });

          // Add close button functionality
          document.getElementById('modal-view-detail-btn').addEventListener('click', function() {
            alert('Menuju halaman detail produk');
            hideModal();
            // In a real implementation, this would redirect to the product detail page
            // window.location.href = '/produk_detail/' + productId;
          });

          document.getElementById('modal-add-cart-btn').addEventListener('click', function() {
            alert('Produk ditambahkan ke keranjang');
            hideModal();
            // In a real implementation, this would add product to cart
          });
        }

        // Set modal content
        document.getElementById('modal-product-image').src = image;
        document.getElementById('modal-product-name').textContent = name;
        document.getElementById('modal-product-price').textContent = price;
        document.getElementById('modal-product-description').textContent = description;

        // Show modal
        modal.style.display = 'flex';
        setTimeout(() => {
          modal.style.opacity = '1';
          modal.querySelector('.modal-container').style.opacity = '1';
          modal.querySelector('.modal-container').style.transform = 'scale(1)';
        }, 10);

        // Add Escape key listener
        document.addEventListener('keydown', handleEscKey, true);
      }

      function hideModal() {
        const modal = document.getElementById('product-detail-modal');
        if (modal) {
          modal.style.opacity = '0';
          modal.querySelector('.modal-container').style.opacity = '0';
          modal.querySelector('.modal-container').style.transform = 'scale(0.8)';

          setTimeout(() => {
            modal.style.display = 'none';
          }, 300);
        }
        document.removeEventListener('keydown', handleEscKey, true);
      }

      function handleEscKey(e) {
        if (e.key === 'Escape') {
          hideModal();
        }
      }
    });
  </script>

  <!-- Modal Produk -->
  <div class="modal-detail-produk" id="modal-detail-produk" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-content-new">
        <div class="modal-title-row">
            <span id="modal-product"></span>
            <span class="close" id="modal-close-btn">&times;</span>
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

  <script>
    // ----- Modal Handling for Categories -----
    const modal = document.getElementById('modal-detail-produk');
    const modalImg = document.getElementById('modal-img');
    const modalProduct = document.getElementById('modal-product');
    const modalPrice = document.getElementById('modal-price');
    const modalDesc = document.getElementById('modal-desc');
    const modalSpecs = document.getElementById('modal-specs');
    const modalThumbs = document.getElementById('modal-thumbs');
    const modalAddCart = document.getElementById('modal-addcart-btn');
    const modalLihatDetail = document.getElementById('modal-lihatdetail-btn');
    const modalCloseBtn = document.getElementById('modal-close-btn');

    let currentProduk = null;

    // Buka modal via event delegation
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('view-product')) {
        const productId = e.target.getAttribute('data-product-id') || e.target.closest('.product-card').getAttribute('data-product-id') || 1;
        openProdukModal(null, productId);
      }
    });

    // Tutup dengan klik di area overlay
    if (modal) {
      modal.addEventListener('mousedown', function(e) {
        if (e.target === modal) closeProdukModal();
      });
    }

    // Tutup dengan tombol close (jika ditampilkan di CSS)
    if (modalCloseBtn) {
      modalCloseBtn.addEventListener('click', closeProdukModal);
    }

    // Tutup dengan tombol Esc
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeProdukModal();
    });

    function openProdukModal(idx, productId) {
      if (!modal) return;

      // Try to find product data in global allProductData first (since we're using dummy data)
      if (typeof allProductData !== 'undefined' && allProductData.length > 0) {
        const product = allProductData.find(p => p.id == productId);

        if (product) {
          // Use product data from global variable
          currentProduk = product;

          // Sanitize product data for modal
          const sanitizedName = (product.name || '').toString().replace(/[&<>"']/g, '');
          const sanitizedPrice = (product.price || '').toString().replace(/[&<>"']/g, '');
          const sanitizedDesc = (product.description || '').toString().replace(/[&<>"']/g, '');

          modalProduct.textContent = sanitizedName.substring(0, 200);
          modalImg.src = product.image;
          modalImg.alt = sanitizedName.substring(0, 100);
          modalPrice.textContent = sanitizedPrice.substring(0, 50);
          modalDesc.textContent = sanitizedDesc.substring(0, 500);

          // Specifications
          modalSpecs.innerHTML = '';
          const specs = product.specifications || product.spesifikasi || product.specs || [];

          if (specs.length > 0) {
            // Handle both object/array formats
            if (Array.isArray(specs)) {
              specs.forEach(spec => {
                const li = document.createElement('li');
                if (typeof spec === 'object' && spec.key && spec.value) {
                  // If it's an object with key-value pairs
                  const strong = document.createElement('strong');
                  // Sanitize key and value to prevent syntax errors
                  const sanitizedKey = spec.key.toString().replace(/[&<>"']/g, '').substring(0, 100);
                  const sanitizedValue = spec.value.toString().replace(/[&<>"']/g, '').substring(0, 200);
                  strong.textContent = sanitizedKey + ':';
                  li.appendChild(strong);
                  li.appendChild(document.createTextNode(' ' + sanitizedValue));
                } else if (typeof spec === 'string' && spec.includes(':')) {
                  // If it's a string with colon separator
                  const [key, ...valueParts] = spec.split(':');
                  const value = valueParts.join(':');
                  const strong = document.createElement('strong');
                  // Sanitize key and value to prevent syntax errors
                  const sanitizedKey = key.trim().replace(/[&<>"']/g, '').substring(0, 100);
                  const sanitizedValue = value.trim().replace(/[&<>"']/g, '').substring(0, 200);
                  strong.textContent = sanitizedKey + ':';
                  li.appendChild(strong);
                  li.appendChild(document.createTextNode(' ' + sanitizedValue));
                } else {
                  // Just the spec as text
                  const sanitizedSpec = spec.toString().replace(/[&<>"']/g, '').substring(0, 200);
                  li.textContent = sanitizedSpec;
                }
                modalSpecs.appendChild(li);
              });
            } else if (typeof specs === 'object') {
              // Handle object format: { key: value }
              Object.entries(specs).forEach(([key, value]) => {
                const li = document.createElement('li');
                const strong = document.createElement('strong');
                // Sanitize key and value to prevent syntax errors
                const sanitizedKey = key.toString().replace(/[&<>"']/g, '').substring(0, 100);
                const sanitizedValue = value.toString().replace(/[&<>"']/g, '').substring(0, 200);
                strong.textContent = sanitizedKey + ':';
                li.appendChild(strong);
                li.appendChild(document.createTextNode(' ' + sanitizedValue));
                modalSpecs.appendChild(li);
              });
            }
          } else {
            // Add default specifications if none provided
            const li = document.createElement('li');
            const defaultSpecDesc = (product.description || 'Spesifikasi tidak tersedia').toString().replace(/[&<>"']/g, '');
            li.textContent = defaultSpecDesc.substring(0, 200);
            modalSpecs.appendChild(li);
          }

          // Thumbnails - using main image for now, but could be extended with multiple images
          modalThumbs.innerHTML = '';
          const thumb = document.createElement('img');
          thumb.src = product.image;
          const thumbAlt = (product.name || 'Thumbnail').toString().replace(/[&<>"']/g, '');
          thumb.alt = thumbAlt.substring(0, 100);
          thumb.classList.add('active');
          thumb.onclick = () => {
            modalImg.src = product.image;
            [...modalThumbs.children].forEach(img => img.classList.remove('active'));
            thumb.classList.add('active');
          };
          modalThumbs.appendChild(thumb);

          // Store product ID in the add to cart button for easy access
          if (modalAddCart) {
            modalAddCart.setAttribute('data-product-id', productId);
          }

          modal.style.display = 'flex';
          document.body.style.overflow = 'hidden';
          return; // Exit early if product found in global data
        }
      }

      // If product not found in global data, fetch from API as fallback
      fetch('/api/products/' + productId)
        .then(response => response.json())
        .then(produk => {
          currentProduk = produk;

          // Sanitize product data from API for modal
          const apiProductName = (produk.name || '').toString().replace(/[&<>"']/g, '');
          const apiProductPrice = (produk.price || '').toString().replace(/[&<>"']/g, '');
          const apiProductDesc = (produk.description || '').toString().replace(/[&<>"']/g, '');

          modalProduct.textContent = apiProductName.substring(0, 200);
          modalImg.src = produk.image;
          modalImg.alt = apiProductName.substring(0, 100);
          modalPrice.textContent = apiProductPrice.substring(0, 50);
          modalDesc.textContent = apiProductDesc.substring(0, 500);

          // Specifications
          // Add store name as first specification
          const storeLi = document.createElement('li');
          storeLi.innerHTML = '<strong>Toko:</strong> <a href="/toko/' + encodeURIComponent(produk.seller?.name || produk.seller?.store_name || produk.seller_id || 'toko-tidak-ditemukan') + '">' + (produk.seller?.name || produk.seller?.store_name || 'Toko Umum') + '</a>';
          modalSpecs.appendChild(storeLi);

          (produk.specifications || produk.spesifikasi || []).forEach(spec => {
            const sanitizedSpec = spec.toString().replace(/[&<>"']/g, '');
            const li = document.createElement('li');
            li.textContent = sanitizedSpec.substring(0, 200);
            modalSpecs.appendChild(li);
          });

          // Thumbnails - for now using single image, but could be extended
          modalThumbs.innerHTML = '';
          const thumb = document.createElement('img');
          thumb.src = produk.image;
          const thumbAlt = (produk.name || 'Thumbnail').toString().replace(/[&<>"']/g, '');
          thumb.alt = thumbAlt.substring(0, 100);
          thumb.classList.add('active');
          thumb.onclick = () => {
            modalImg.src = produk.image;
            [...modalThumbs.children].forEach(img => img.classList.remove('active'));
            thumb.classList.add('active');
          };
          modalThumbs.appendChild(thumb);

          // Store product ID in the add to cart button for easy access
          if (modalAddCart) {
            modalAddCart.setAttribute('data-product-id', productId);
          }

          modal.style.display = 'flex';
          document.body.style.overflow = 'hidden';
        })
        .catch(error => {
          console.error('Error loading product details:', error);

          // Attempt to find product data in the visible DOM as fallback
          const productCard = document.querySelector('[data-product-id="' + productId + '"]');
          if (productCard) {
            const productName = productCard.querySelector('.product-name')?.textContent;
            const productImage = productCard.querySelector('.product-image')?.src;
            const productDesc = productCard.querySelector('.product-description')?.textContent;
            const productPrice = productCard.querySelector('.product-price')?.textContent;
            const productStore = productCard.closest('.main-content') ?
                                productCard.querySelector('.product-toko a')?.textContent ||
                                productCard.querySelector('.toko-link')?.textContent || 'Toko Umum' : 'Toko Umum';

            if (productName && productImage && productPrice) {
              // Sanitize the data from DOM as fallback
              const fallbackName = productName.toString().replace(/[&<>"']/g, '');
              const fallbackPrice = productPrice.toString().replace(/[&<>"']/g, '');
              const fallbackDesc = (productDesc || 'Deskripsi produk tidak tersedia').toString().replace(/[&<>"']/g, '');
              const fallbackStore = productStore.toString().replace(/[&<>"']/g, '');

              // Use the data from the DOM
              modalProduct.textContent = fallbackName.substring(0, 200);

              // Clear and rebuild specifications list
              modalSpecs.innerHTML = '';
              const storeLinkLi = document.createElement('li');
              storeLinkLi.innerHTML = '<strong>Toko:</strong> <a href="/toko/' + encodeURIComponent(fallbackStore) + '">' + fallbackStore + '</a>';
              modalSpecs.appendChild(storeLinkLi);

              modalImg.src = productImage;
              modalImg.alt = fallbackName.substring(0, 100);
              modalPrice.textContent = fallbackPrice.substring(0, 50);
              modalDesc.textContent = fallbackDesc.substring(0, 500);

              const li = document.createElement('li');
              const fallbackSpec = (productDesc || 'Spesifikasi tidak tersedia').toString().replace(/[&<>"']/g, '');
              li.textContent = fallbackSpec.substring(0, 200);
              modalSpecs.appendChild(li);

              modalThumbs.innerHTML = '';
              const thumb = document.createElement('img');
              thumb.src = productImage;
              const thumbAlt = fallbackName.substring(0, 100);
              thumb.alt = thumbAlt;
              thumb.classList.add('active');
              thumb.onclick = () => {
                modalImg.src = productImage;
                [...modalThumbs.children].forEach(img => img.classList.remove('active'));
                thumb.classList.add('active');
              };
              modalThumbs.appendChild(thumb);

              // Store product ID in the add to cart button for easy access
              if (modalAddCart) {
                modalAddCart.setAttribute('data-product-id', productId);
              }

              modal.style.display = 'flex';
              document.body.style.overflow = 'hidden';
              return; // Exit after using fallback data
            }
          }

          // Final fallback to dummy data if product not found at all
          const produk = {
            name: "Produk Tidak Ditemukan",
            seller: { name: "Toko Umum" },
            image: asset('src/product_1.png'),
            price: "Rp 0",
            description: "Produk tidak ditemukan di sistem.",
            specifications: []
          };

          currentProduk = produk;
          const dummyName = produk.name.toString().replace(/[&<>"']/g, '');
          const dummyStore = (produk.seller?.name || produk.seller?.store_name || 'Toko Umum').toString().replace(/[&<>"']/g, '');
          const dummyPrice = produk.price.toString().replace(/[&<>"']/g, '');
          const dummyDesc = produk.description.toString().replace(/[&<>"']/g, '');

          modalProduct.textContent = dummyName.substring(0, 200);

          // Add store link for dummy data
          const dummyStoreLi = document.createElement('li');
          dummyStoreLi.innerHTML = '<strong>Toko:</strong> <a href="/toko/' + encodeURIComponent(dummyStore) + '">' + dummyStore + '</a>';
          modalSpecs.innerHTML = '';
          modalSpecs.appendChild(dummyStoreLi);

          modalImg.src = produk.image;
          modalImg.alt = dummyName.substring(0, 100);
          modalPrice.textContent = dummyPrice.substring(0, 50);
          modalDesc.textContent = dummyDesc.substring(0, 500);

          const dummySpecLi = document.createElement('li');
          dummySpecLi.textContent = 'Spesifikasi tidak tersedia';
          modalSpecs.appendChild(dummySpecLi);

          modal.style.display = 'flex';
          document.body.style.overflow = 'hidden';
        });
    }

    function closeProdukModal() {
      if (!modal) return;
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }

    // Add to cart functionality
    if (modalAddCart) {
      modalAddCart.onclick = function() {
        const productId = this.getAttribute('data-product-id');
        addToCart(productId);
      };
    }

    if (modalLihatDetail) {
      modalLihatDetail.onclick = () => {
        if (currentProduk && currentProduk.id) {
          window.location.href = '/produk_detail/' + currentProduk.id;
        } else {
          alert('Menuju halaman detail produk (demo)');
        }
      };
    }

    // Function to add item to cart
    async function addToCart(productId) {
      if (!productId) {
        showNotification('Produk tidak ditemukan', 'error');
        return;
      }

      // Get CSRF token from the meta tag in the layout
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      if (!csrfToken) {
        showNotification('Terjadi masalah dengan keamanan, silakan refresh halaman', 'error');
        return;
      }

      try {
        const response = await fetch('/cart/add', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest' // Important for Laravel to recognize AJAX requests
          },
          body: JSON.stringify({
            product_id: productId,
            quantity: 1 // Default quantity
          })
        });

        const result = await response.json();

        if (response.ok && result.success) {
          showNotification(result.message || 'Produk berhasil ditambahkan ke keranjang', 'success');

          // Update cart count in header via API
          if (window.updateCartCount) {
            window.updateCartCount();
          }
        } else {
          showNotification(result.message || 'Gagal menambahkan ke keranjang', 'error');
        }
      } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
      }
    }

    // Toggle sidebar filter functionality
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('sidebar-filter');
      const toggleBtn = document.getElementById('mobile-filter-toggle');
      const closeBtn = document.getElementById('close-sidebar-btn');
      const overlay = document.getElementById('sidebar-overlay');

      // Function to open sidebar
      function openSidebar() {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
      }

      // Function to close sidebar
      function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = ''; // Re-enable background scrolling
      }

      // Event listeners for toggle functionality
      if (toggleBtn) {
        toggleBtn.addEventListener('click', openSidebar);
      }

      if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
      }

      if (overlay) {
        overlay.addEventListener('click', closeSidebar);
      }

      // Also close sidebar when clicking outside of it
      document.addEventListener('click', function(e) {
        if (sidebar.classList.contains('active') &&
            !sidebar.contains(e.target) &&
            e.target !== toggleBtn) {
          closeSidebar();
        }
      });
    });

    function showNotification(message, type = 'info') {
      // Sanitize the message to prevent syntax errors
      let sanitizedMessage = (message || '').toString().replace(/[&<>"']/g, '');
      sanitizedMessage = sanitizedMessage.substring(0, 500); // Limit length

      // Buat elemen notifikasi
      const notification = document.createElement('div');
      const sanitizedType = (type || 'info').toString().replace(/[&<>"']/g, '').substring(0, 20);
      notification.className = 'notification notification-' + sanitizedType;
      notification.textContent = sanitizedMessage;

      // Gaya dasar untuk notifikasi
      Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '12px 20px',
        borderRadius: '6px',
        color: '#fff',
        backgroundColor: type === 'error' ? '#dc3545' : type === 'success' ? '#28a745' : '#007bff',
        zIndex: '9999',
        boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
        fontSize: '14px',
        maxWidth: '400px',
        wordWrap: 'break-word'
      });

      // Tambahkan ke body
      document.body.appendChild(notification);

      // Hapus notifikasi setelah 3 detik
      setTimeout(() => {
        if (notification.parentNode) {
          notification.parentNode.removeChild(notification);
        }
      }, 3000);
    }
  </script>

  <!-- Subcategory filter script -->
  <script src="{{ asset('js/customer/kategori/subcategory-filter.js') }}"></script>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection
