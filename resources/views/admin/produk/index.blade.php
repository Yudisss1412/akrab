@extends('layouts.admin')

@section('title', 'Manajemen Produk - AKRAB')

@include('components.admin_penjual.header')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <style>
    /* Enhanced styles for product management page */
    :root {
      --primary: #006E5C;
      --primary-light: #a8d5c9;
      --secondary: #6c757d;
      --success: #28a745;
      --danger: #dc3545;
      --warning: #ffc107;
      --info: #17a2b8;
      --light: #f8f9fa;
      --dark: #343a40;
      --white: #ffffff;
      --gray: #6c757d;
      --border: #dee2e6;
      --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
      --border-radius: 0.375rem;
      --transition: all 0.3s ease;
    }
    
    body {
      background-color: #f5f7fa;
      font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
    }
    
    .main-content {
      padding-top: 2rem;
      padding-bottom: 2rem;
    }
    
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      padding: 1.5rem 0;
    }
    
    .page-title {
      margin: 0;
      font-size: 1.75rem;
      font-weight: 700;
      color: var(--primary);
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .page-title i {
      color: var(--primary-light);
    }
    
    .filter-panel {
      background: var(--white);
      border-radius: var(--border-radius);
      border: none;
      box-shadow: var(--box-shadow);
      margin-bottom: 1.5rem;
      overflow: hidden;
      transition: var(--transition);
    }
    
    .filter-panel:hover {
      box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
    }
    
    .filter-header {
      padding: 1.25rem 1.5rem;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      color: white;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .filter-title {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .filter-content {
      padding: 1.5rem;
    }
    
    .form-label {
      font-weight: 600;
      color: #495057;
    }
    
    .form-control, .form-select {
      border: 1px solid var(--border);
      border-radius: var(--border-radius);
      padding: 0.65rem 0.75rem;
      transition: var(--transition);
    }
    
    .form-control:focus, .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.2rem rgba(0, 110, 92, 0.25);
      outline: 0;
    }
    
    .btn {
      border-radius: var(--border-radius);
      padding: 0.5rem 1rem;
      font-weight: 600;
      transition: var(--transition);
      border: none;
    }
    
    .btn-primary {
      background: var(--primary);
      border: 1px solid var(--primary);
    }
    
    .btn-primary:hover {
      background: #005a4a;
      border-color: #005a4a;
      transform: translateY(-2px);
    }
    
    .btn-secondary {
      background: var(--secondary);
      border: 1px solid var(--secondary);
    }
    
    .btn-secondary:hover {
      background: #5a6268;
      border-color: #545b62;
      transform: translateY(-2px);
    }
    
    .table-container {
      background: var(--white);
      border-radius: var(--border-radius);
      border: none;
      box-shadow: var(--box-shadow);
      overflow: hidden;
      transition: var(--transition);
    }
    
    .table-container:hover {
      box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
    }
    
    .table-header {
      padding: 1.25rem 1.5rem;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      color: white;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .table-title {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .table-content {
      padding: 1.5rem;
    }
    
    .table {
      margin-bottom: 0;
    }
    
    .table th {
      background-color: #f8f9fa;
      color: #495057;
      font-weight: 600;
      padding: 0.9rem 0.75rem;
      border-top: none;
    }
    
    .table td {
      padding: 0.9rem 0.75rem;
      vertical-align: middle;
    }
    
    .table-hover tbody tr:hover {
      background-color: rgba(0, 110, 92, 0.05);
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
      background-color: rgba(0, 0, 0, 0.02);
    }
    
    .badge {
      padding: 0.4em 0.7em;
      font-size: 0.8em;
      font-weight: 500;
      border-radius: 50rem;
    }
    
    .btn-group .btn {
      margin-right: 0.25rem;
    }
    
    .btn-group .btn:last-child {
      margin-right: 0;
    }
    
    .pagination {
      margin-bottom: 0;
    }
    
    .pagination .page-link {
      color: var(--primary);
      border-radius: var(--border-radius);
      margin: 0 0.1rem;
    }
    
    .pagination .page-item.active .page-link {
      background-color: var(--primary);
      border-color: var(--primary);
    }
    
    .card {
      box-shadow: var(--box-shadow);
      border: none;
      border-radius: var(--border-radius);
      transition: var(--transition);
    }
    
    .table-responsive {
      border-radius: var(--border-radius);
    }
    
    /* Product thumbnail styling */
    .product-thumbnail {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 4px;
      border: 1px solid #dee2e6;
    }
    
    /* Badge styling for different statuses */
    .badge-pending {
      background-color: #ffc107;
      color: #212529;
    }
    
    .badge-active {
      background-color: #28a745;
      color: white;
    }
    
    .badge-suspended {
      background-color: #dc3545;
      color: white;
    }
    
    .badge-rejected {
      background-color: #6c757d;
      color: white;
    }
    
    /* Tab styling */
    .nav-tabs .nav-link {
      border: 1px solid transparent;
      border-bottom: 3px solid transparent;
    }
    
    .nav-tabs .nav-link.active {
      border-color: var(--primary) var(--primary) transparent;
      background-color: white;
      color: var(--primary);
      border-bottom: 3px solid var(--primary);
    }
    
    .tab-content {
      margin-top: 1rem;
    }
    
    /* Category management layout */
    .category-layout {
      display: flex;
      gap: 1.5rem;
    }
    
    .category-panel {
      flex: 1;
      background: var(--white);
      border-radius: var(--border-radius);
      border: 1px solid var(--border);
      box-shadow: var(--box-shadow);
      padding: 1.5rem;
      min-height: 400px;
    }
    
    .category-panel h3 {
      color: var(--primary);
      border-bottom: 2px solid var(--primary-light);
      padding-bottom: 0.5rem;
      margin-top: 0;
    }
    
    .category-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .category-list li {
      padding: 0.75rem;
      border-bottom: 1px solid var(--border);
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .category-list li:hover {
      background-color: #f8f9fa;
    }
    
    .category-list li.active {
      background-color: var(--primary-light);
      color: white;
    }
    
    .subcategory-list {
      margin-left: 1rem;
    }
    
    /* Action button styling */
    .btn-square {
      width: 32px;
      height: 32px;
      padding: 0.25rem 0.5rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    
    .btn-view {
      background-color: #17a2b8;
      color: white;
    }
    
    .btn-view:hover {
      background-color: #138496;
      color: white;
    }
    
    .btn-edit {
      background-color: #007bff;
      color: white;
    }
    
    .btn-edit:hover {
      background-color: #0069d9;
      color: white;
    }
    
    .btn-approve {
      background-color: #28a745;
      color: white;
    }
    
    .btn-approve:hover {
      background-color: #218838;
      color: white;
    }
    
    .btn-reject {
      background-color: #dc3545;
      color: white;
    }
    
    .btn-reject:hover {
      background-color: #c82333;
      color: white;
    }
    
    .btn-suspend {
      background-color: #ffc107;
      color: #212529;
    }
    
    .btn-suspend:hover {
      background-color: #e0a800;
      color: #212529;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
      .category-layout {
        flex-direction: column;
      }
      
      .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }
    }
  </style>
@endpush

@section('content')
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content main-content">
          <div class="page-header">
            <h2 class="page-title">Manajemen Produk</h2>
          </div>

          <!-- Tab Navigation -->
          <ul class="nav nav-tabs" id="productManagementTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link {{ (isset($tab) && $tab === 'reviews') || (isset($tab) && $tab === 'categories') ? '' : 'active' }}" id="products-tab" data-bs-toggle="tab" data-bs-target="#products-tab-pane" type="button" role="tab">Produk</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link {{ (isset($tab) && $tab === 'categories') ? 'active' : '' }}" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories-tab-pane" type="button" role="tab">Kategori & Atribut</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link {{ (isset($tab) && $tab === 'reviews') ? 'active' : '' }}" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-tab-pane" type="button" role="tab">Ulasan Produk</button>
            </li>
          </ul>

          <!-- Tab Content -->
          <div class="tab-content" id="productManagementTabsContent">
            <!-- Products Tab -->
            <div class="tab-pane fade {{ (isset($tab) && $tab === 'reviews') || (isset($tab) && $tab === 'categories') ? '' : 'show active' }}" id="products-tab-pane" role="tabpanel">
              <div class="mt-3">
                <!-- Filter Panel - Horizontal Layout -->
                <div class="filter-panel">
                  <div class="filter-header">
                    <h3 class="filter-title"><i class="fas fa-filter"></i> Filter dan Pencarian</h3>
                  </div>
                  <div class="filter-content">
                    <form id="productFilterForm" method="GET" action="{{ route('admin.produk.index') }}">
                      <div class="row g-2">
                        <div class="col-md-3 mb-2">
                          <label for="search" class="form-label">Cari Produk</label>
                          <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nama Produk atau SKU">
                        </div>
                        <div class="col-md-3 mb-2">
                          <label for="seller_filter" class="form-label">Filter berdasarkan Penjual</label>
                          <select class="form-select" id="seller_filter" name="seller_id">
                            <option value="">Semua Penjual</option>
                            @if(isset($sellers))
                              @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" {{ request('seller_id') == $seller->id ? 'selected' : '' }}>{{ $seller->store_name }}</option>
                              @endforeach
                            @endif
                          </select>
                        </div>
                        <div class="col-md-2 mb-2">
                          <label for="status_filter" class="form-label">Status Produk</label>
                          <select class="form-select" id="status_filter" name="status">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                          </select>
                        </div>
                        <div class="col-md-2 mb-2">
                          <label for="category_filter" class="form-label">Kategori</label>
                          <select class="form-select" id="category_filter" name="category">
                            <option value="">Semua Kategori</option>
                            @if(isset($categories))
                              @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                              @endforeach
                            @endif
                          </select>
                        </div>
                        <div class="col-md-2 mb-2">
                          <label for="subcategory_filter" class="form-label">Subkategori</label>
                          <select class="form-select" id="subcategory_filter" name="subcategory">
                            <option value="">Semua Subkategori</option>
                            @if(isset($subcategories) && request('category'))
                              @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}" {{ request('subcategory') == $subcategory->id ? 'selected' : '' }}>{{ $subcategory->name }}</option>
                              @endforeach
                            @endif
                          </select>
                        </div>
                        <div class="col-md-2 mb-2 d-flex align-items-end">
                          <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Cari</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                
                <!-- Products Table -->
                <div class="table-container">
                  <div class="table-header">
                    <h3 class="table-title">Daftar Produk</h3>
                  </div>
                  <div class="table-content">
                    <div class="table-responsive">
                      <table class="table table-striped table-hover">
                        <thead>
                          <tr>
                            <th scope="col">Info Produk</th>
                            <th scope="col">Nama Toko</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Harga dan Stok</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          @forelse($products ?? collect() as $product)
                          <tr id="product-row-{{ $product->id }}">
                            <td>
                              <div>
                                <div><strong>{{ $product->name }}</strong></div>
                                <small class="text-muted">SKU: {{ $product->sku ?? 'N/A' }}</small>
                              </div>
                            </td>
                            <td>
                              <a href="{{ route('sellers.show', $product->seller_id) }}" class="text-primary">{{ $product->seller_name ?? 'N/A' }}</a>
                            </td>
                            <td>
                              <div>{{ $product->category->name ?? 'N/A' }}</div>
                              <small class="text-muted">{{ $product->subcategory ?? 'N/A' }}</small>
                            </td>
                            <td>
                              <div><strong>Rp {{ number_format($product->price, 0, ',', '.') }}</strong></div>
                              <small class="text-muted">Stok: {{ $product->stock }}</small>
                            </td>
                            <td>
                              @php
                                $statusClass = '';
                                $statusText = '';
                                switch($product->status) {
                                  case 'pending':
                                    $statusClass = 'badge-pending';
                                    $statusText = 'Menunggu Persetujuan';
                                    break;
                                  case 'active':
                                    $statusClass = 'badge-active';
                                    $statusText = 'Aktif';
                                    break;
                                  case 'suspended':
                                    $statusClass = 'badge-suspended';
                                    $statusText = 'Ditangguhkan';
                                    break;
                                  case 'rejected':
                                    $statusClass = 'badge-rejected';
                                    $statusText = 'Ditolak';
                                    break;
                                  default:
                                    $statusClass = 'badge-secondary';
                                    $statusText = 'Tidak Dikenal';
                                }
                              @endphp
                              <span class="badge {{ $statusClass }} d-block">{{ $statusText }}</span>
                            </td>
                            <td>
                              <div class="btn-group" role="group">
                                <a href="{{ route('halaman.produk') }}?product={{ $product->id }}" class="btn btn-sm btn-view" title="Lihat di Situs">
                                  <i class="fas fa-external-link-alt"></i>
                                </a>
                                <a href="{{ route('penjual.produk.edit', $product->id) }}" class="btn btn-sm btn-edit" title="Edit">
                                  <i class="fas fa-edit"></i>
                                </a>
                                @if($product->status === 'pending')
                                  <button type="button" class="btn btn-sm btn-approve" title="Setujui" onclick="approveProduct({{ $product->id }})">
                                    <i class="fas fa-check"></i>
                                  </button>
                                  <button type="button" class="btn btn-sm btn-reject" title="Tolak" onclick="rejectProduct({{ $product->id }})">
                                    <i class="fas fa-times"></i>
                                  </button>
                                @elseif($product->status === 'active')
                                  <button type="button" class="btn btn-sm btn-suspend" title="Tangguhkan" onclick="suspendProduct({{ $product->id }})">
                                    <i class="fas fa-pause"></i>
                                  </button>
                                @else
                                  <button type="button" class="btn btn-sm btn-approve" title="Setujui" onclick="approveProduct({{ $product->id }})">
                                    <i class="fas fa-check"></i>
                                  </button>
                                @endif
                              </div>
                            </td>
                          </tr>
                          @empty
                          <tr>
                            <td colspan="6" class="text-center py-4">Tidak ada data produk ditemukan.</td>
                          </tr>
                          @endforelse
                        </tbody>
                      </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if(isset($products) && $products->count() > 0)
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                      Menampilkan {{ $products->firstItem() }} sampai {{ $products->lastItem() }} dari {{ $products->total() }} produk
                    </div>
                    <div>
                      <nav aria-label="Navigasi produk">
                        <ul class="pagination">
                          @if ($products->onFirstPage())
                            <li class="page-item disabled">
                              <span class="page-link">&laquo;&laquo; Sebelumnya</span>
                            </li>
                          @else
                            <li class="page-item">
                              <a class="page-link" href="{{ $products->previousPageUrl() }}{{ request('tab') ? '&tab=products' : '' }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;&laquo; Sebelumnya</span>
                              </a>
                            </li>
                          @endif

                          @for ($i = max(1, $products->currentPage() - 2); $i <= min($products->lastPage(), $products->currentPage() + 2); $i++)
                            @if ($i == $products->currentPage())
                              <li class="page-item active"><span class="page-link" style="color: white;">{{ $i }}</span></li>
                            @else
                              <li class="page-item"><a class="page-link" href="{{ $products->url($i) }}{{ request('tab') ? '&tab=products' : '' }}">{{ $i }}</a></li>
                            @endif
                          @endfor

                          @if ($products->hasMorePages())
                            <li class="page-item">
                              <a class="page-link" href="{{ $products->nextPageUrl() }}{{ request('tab') ? '&tab=products' : '' }}" aria-label="Next">
                                <span aria-hidden="true">Berikutnya &raquo;&raquo;</span>
                              </a>
                            </li>
                          @else
                            <li class="page-item disabled">
                              <span class="page-link">Berikutnya &raquo;&raquo;</span>
                            </li>
                          @endif
                        </ul>
                      </nav>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>

            <!-- Categories & Attributes Tab -->
            <div class="tab-pane fade {{ (isset($tab) && $tab === 'categories') ? 'show active' : '' }}" id="categories-tab-pane" role="tabpanel">
              <div class="mt-3">
                <div class="category-layout">
                  <div class="category-panel">
                    <h3>Kategori Utama</h3>
                    <div class="d-flex justify-content-between mb-3">
                      <button class="btn btn-primary" onclick="showAddCategoryModal()">Tambah Kategori</button>
                    </div>
                    <ul class="category-list">
                      @if(isset($mainCategories) && $mainCategories->count() > 0)
                        @foreach($mainCategories as $index => $category)
                          <li class="{{ $index === 0 ? 'active' : '' }}" data-id="{{ $category->id }}" onclick="selectCategory({{ $category->id }})" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                            <span>{{ $category->name }}</span>
                            <div>
                              <button class="btn btn-sm btn-outline-primary me-1" onclick="event.stopPropagation(); editCategory({{ $category->id }})">Edit</button>
                              <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); deleteCategory({{ $category->id }})">Hapus</button>
                            </div>
                          </li>
                        @endforeach
                      @else
                        <li class="text-center text-muted">Tidak ada kategori ditemukan</li>
                      @endif
                    </ul>
                  </div>
                  
                  <div class="category-panel">
                    <h3>Subkategori: <span id="selected-category-name">Pilih Kategori</span></h3>
                    <div class="d-flex justify-content-between mb-3">
                      <button class="btn btn-primary" onclick="showAddSubcategoryModal()">Tambah Subkategori</button>
                    </div>
                    <ul class="category-list subcategory-list" id="subcategory-list">
                      <!-- Subkategori akan dimuat secara dinamis -->
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- Reviews Tab -->
            <div class="tab-pane fade {{ (isset($tab) && $tab === 'reviews') ? 'show active' : '' }}" id="reviews-tab-pane" role="tabpanel">
              <div class="mt-3">
                <!-- Filter Panel for Reviews -->
                <div class="filter-panel">
                  <div class="filter-header">
                    <h3 class="filter-title"><i class="fas fa-filter"></i> Filter Ulasan</h3>
                  </div>
                  <div class="filter-content">
                    <form id="reviewFilterForm" method="GET" action="{{ route('admin.produk.index') }}?tab=reviews">
                      <div class="row g-2">
                        <div class="col-md-3 mb-2">
                          <label for="review_status_filter" class="form-label">Status Ulasan</label>
                          <select class="form-select" id="review_status_filter" name="review_status">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('review_status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                            <option value="approved" {{ request('review_status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ request('review_status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                          </select>
                        </div>
                        <div class="col-md-3 mb-2">
                          <label for="rating_filter" class="form-label">Filter berdasarkan Rating</label>
                          <select class="form-select" id="rating_filter" name="rating">
                            <option value="">Semua Rating</option>
                            <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Bintang</option>
                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Bintang</option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Bintang</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Bintang</option>
                            <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Bintang</option>
                          </select>
                        </div>
                        <div class="col-md-4 mb-2">
                          <label for="review_search" class="form-label">Cari berdasarkan</label>
                          <input type="text" class="form-control" id="review_search" name="review_search" value="{{ request('review_search') }}" placeholder="Isi ulasan, nama produk, atau nama pengguna">
                        </div>
                        <div class="col-md-2 mb-2 d-flex align-items-end">
                          <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Cari</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                
                <!-- Reviews Table -->
                <div class="table-container">
                  <div class="table-header">
                    <h3 class="table-title">Daftar Ulasan Produk</h3>
                  </div>
                  <div class="table-content">
                    <div class="table-responsive">
                      <table class="table table-striped table-hover">
                        <thead>
                          <tr>
                            <th scope="col">Ulasan</th>
                            <th scope="col">Info Produk</th>
                            <th scope="col">Info Pengguna</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          @forelse($reviews ?? collect() as $review)
                          <tr id="review-row-{{ $review->id }}">
                            <td>
                              <div>{!! preg_replace('/\(\d+\)/', '', $review->review_text) !!}</div>
                              <div>
                                <span class="badge bg-warning text-dark">
                                  <i class="fas fa-star"></i> {{ $review->rating }}
                                </span>
                              </div>
                            </td>
                            <td>
                              <span class="text-primary">{{ $review->product->name ?? 'Produk Tidak Ditemukan' }}</span>
                            </td>
                            <td>
                              <div><strong>{{ $review->user->name ?? 'User Tidak Ditemukan' }}</strong></div>
                              <small class="text-muted">{{ $review->user->email ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $review->created_at->format('d M Y') }}</td>
                            <td>
                              @if($review->approved_at)
                                <span class="badge badge-active d-block">Disetujui</span>
                              @elseif($review->rejected_at)
                                <span class="badge badge-rejected d-block">Ditolak</span>
                              @else
                                <span class="badge badge-pending d-block">Menunggu Persetujuan</span>
                              @endif
                            </td>
                            <td>
                              <div class="btn-group" role="group">
                                @if(!$review->approved_at && !$review->rejected_at)
                                  <button type="button" class="btn btn-sm btn-approve" title="Setujui" onclick="approveReview({{ $review->id }})">
                                    <i class="fas fa-check"></i>
                                  </button>
                                  <button type="button" class="btn btn-sm btn-danger" title="Tolak" onclick="rejectReview({{ $review->id }})">
                                    <i class="fas fa-times"></i>
                                  </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-danger" title="Hapus" onclick="deleteReview({{ $review->id }})">
                                  <i class="fas fa-trash"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" title="Tandai sebagai Spam" onclick="markAsSpam({{ $review->id }})">
                                  <i class="fas fa-ban"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                          @empty
                          <tr>
                            <td colspan="6" class="text-center py-4">Tidak ada data ulasan produk ditemukan.</td>
                          </tr>
                          @endforelse
                        </tbody>
                      </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if(isset($reviews) && $reviews->count() > 0)
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                      Menampilkan {{ $reviews->firstItem() }} sampai {{ $reviews->lastItem() }} dari {{ $reviews->total() }} ulasan
                    </div>
                    <div>
                      <nav aria-label="Navigasi ulasan">
                        <ul class="pagination">
                          @if ($reviews->onFirstPage())
                            <li class="page-item disabled">
                              <span class="page-link">«« Sebelumnya</span>
                            </li>
                          @else
                            <li class="page-item">
                              <a class="page-link" href="{{ $reviews->previousPageUrl() }}&tab=reviews" aria-label="Previous">
                                <span aria-hidden="true">«« Sebelumnya</span>
                              </a>
                            </li>
                          @endif

                          @for ($i = max(1, $reviews->currentPage() - 2); $i <= min($reviews->lastPage(), $reviews->currentPage() + 2); $i++)
                            @if ($i == $reviews->currentPage())
                              <li class="page-item active"><span class="page-link" style="color: white;">{{ $i }}</span></li>
                            @else
                              <li class="page-item"><a class="page-link" href="{{ $reviews->url($i) }}&tab=reviews">{{ $i }}</a></li>
                            @endif
                          @endfor

                          @if ($reviews->hasMorePages())
                            <li class="page-item">
                              <a class="page-link" href="{{ $reviews->nextPageUrl() }}&tab=reviews" aria-label="Next">
                                <span aria-hidden="true">Berikutnya »»</span>
                              </a>
                            </li>
                          @else
                            <li class="page-item disabled">
                              <span class="page-link">Berikutnya »»</span>
                            </li>
                          @endif
                        </ul>
                      </nav>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <!-- JavaScript for Tabs and Interactions -->
  <script>
    // Helper function to safely get CSRF token
    function getCSRFToken() {
      var csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
      return csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';
    }

    // Functions for product actions - defined in global scope to be accessible from HTML onclick attributes

    function approveProduct(productId) {
      if (confirm('Apakah Anda yakin ingin menyetujui produk ini?')) {
        var url = '/admin/produk/' + productId + '/approve';
        fetch(url, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
          }
        })
        .then(function(response) {
          return response.json();
        })
        .then(function(data) {
          if (data.success) {
            alert(data.message);
            location.reload();
          } else {
            alert('Gagal menyetujui produk: ' + data.message);
          }
        })
        .catch(function(error) {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat menyetujui produk');
        });
      }
    }
    
    function rejectProduct(productId) {
      if (confirm('Apakah Anda yakin ingin menolak produk ini?')) {
        var url = '/admin/produk/' + productId + '/reject';
        fetch(url, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
          }
        })
        .then(function(response) {
          return response.json();
        })
        .then(function(data) {
          if (data.success) {
            alert(data.message);
            location.reload();
          } else {
            alert('Gagal menolak produk: ' + data.message);
          }
        })
        .catch(function(error) {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat menolak produk');
        });
      }
    }
    
    function suspendProduct(productId) {
      if (confirm('Apakah Anda yakin ingin menangguhkan produk ini?')) {
        var url = '/admin/produk/' + productId + '/suspend';
        fetch(url, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
          }
        })
        .then(function(response) {
          return response.json();
        })
        .then(function(data) {
          if (data.success) {
            alert(data.message);
            location.reload();
          } else {
            alert('Gagal menangguhkan produk: ' + data.message);
          }
        })
        .catch(function(error) {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat menangguhkan produk');
        });
      }
    }
    
    // Functions for review actions
    function approveReview(reviewId) {
      if (confirm('Apakah Anda yakin ingin menyetujui ulasan ini?')) {
        var url = '/admin/reviews/' + reviewId + '/approve';
        fetch(url, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
          }
        })
        .then(function(response) {
          return response.json();
        })
        .then(function(data) {
          if (data.success) {
            alert(data.message);
            location.reload();
          } else {
            alert('Gagal menyetujui ulasan: ' + data.message);
          }
        })
        .catch(function(error) {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat menyetujui ulasan');
        });
      }
    }
    
    function rejectReview(reviewId) {
      if (confirm('Apakah Anda yakin ingin menolak ulasan ini?')) {
        var url = '/admin/reviews/' + reviewId + '/reject';
        fetch(url, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
          }
        })
        .then(function(response) {
          return response.json();
        })
        .then(function(data) {
          if (data.success) {
            alert(data.message);
            location.reload();
          } else {
            alert('Gagal menolak ulasan: ' + data.message);
          }
        })
        .catch(function(error) {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat menolak ulasan');
        });
      }
    }
    
    function deleteReview(reviewId) {
      if (confirm('Apakah Anda yakin ingin menghapus ulasan ini?')) {
        var url = '/admin/reviews/' + reviewId;
        fetch(url, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
          }
        })
        .then(function(response) {
          return response.json();
        })
        .then(function(data) {
          if (data.success) {
            alert(data.message);
            location.reload();
          } else {
            alert('Gagal menghapus ulasan: ' + data.message);
          }
        })
        .catch(function(error) {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat menghapus ulasan');
        });
      }
    }
    
    function markAsSpam(reviewId) {
      alert('Fitur ini akan segera hadir: Tandai ulasan sebagai spam');
    }
    
    // Functions for category management
    function selectCategory(categoryId) {
      console.log('selectCategory called with ID:', categoryId);
      
      // Update URL dengan selected_category
      var urlParams = new URLSearchParams(window.location.search);
      urlParams.set('selected_category', categoryId);
      urlParams.set('tab', 'categories');
      var newUrl = window.location.pathname + '?' + urlParams.toString();
      
      // Update the URL without reloading the page
      history.pushState({}, '', newUrl);
      
      // Remove active class from all categories
      var allCategories = document.querySelectorAll('#categories-tab-pane .category-list li');
      allCategories.forEach(function(li) {
        li.classList.remove('active');
      });
      
      // Add active class to clicked category
      var clickedElement = document.querySelector('#categories-tab-pane .category-list li[data-id="' + categoryId + '"]');
      if (clickedElement) {
          clickedElement.classList.add('active');
          console.log('Active class added to category ID:', categoryId);
      }
      
      // Load subcategories for the selected category
      console.log('About to load subcategories for category ID:', categoryId);
      loadSubcategories(categoryId);
    }
    
    // Load subcategories when category is selected
    function loadSubcategories(categoryId) {
      console.log('loadSubcategories called with ID:', categoryId); // Debug log
      
      if (!categoryId) {
        console.log('No categoryId provided, clearing subcategory list');
        document.getElementById('selected-category-name').textContent = 'Pilih Kategori';
        document.getElementById('subcategory-list').innerHTML = '';
        return;
      }

      // Update category name
      var selectedCategory = document.querySelector('#categories-tab-pane .category-list li[data-id="' + categoryId + '"]');
      document.getElementById('selected-category-name').textContent = selectedCategory ? selectedCategory.textContent : 'Kategori Terpilih';
      console.log('Updated selected category name to:', selectedCategory ? selectedCategory.textContent : 'Kategori Terpilih');

      // Load subcategories via AJAX
      console.log('Fetching subcategories from:', '/api/categories/' + categoryId + '/subcategories');
      var fetchUrl = '/api/categories/' + categoryId + '/subcategories';
      fetch(fetchUrl)
        .then(function(response) {
          console.log('API response status:', response.status);
          return response.json();
        })
        .then(function(data) {
          console.log('Received subcategory data:', data);
          var subcategoryList = document.getElementById('subcategory-list');
          subcategoryList.innerHTML = '';

          if (data.subcategories && data.subcategories.length > 0) {
            data.subcategories.forEach(function(subcategory) {
              var li = document.createElement('li');
              var innerHTML = '<span>' + subcategory.name + '</span>';
              innerHTML += '<div>';
              innerHTML += '<button class="btn btn-sm btn-primary me-1" onclick="editSubcategory(' + subcategory.id + ')">Edit</button>';
              innerHTML += '<button class="btn btn-sm btn-outline-danger" onclick="deleteSubcategory(' + subcategory.id + ')">Hapus</button>';
              innerHTML += '</div>';
              li.innerHTML = innerHTML;
              li.style.display = 'flex';
              li.style.justifyContent = 'space-between';
              li.style.alignItems = 'center';
              subcategoryList.appendChild(li);
            });
            console.log('Subcategories loaded successfully:', data.subcategories.length, 'subcategories found');
          } else {
            var li = document.createElement('li');
            li.textContent = 'Tidak ada subkategori';
            li.style.fontStyle = 'italic';
            li.style.color = '#6c757d';
            subcategoryList.appendChild(li);
            console.log('No subcategories found for category ID:', categoryId);
          }
        })
        .catch(function(error) {
          console.error('Error loading subcategories:', error);
        });
    }

    // Placeholder functions for subcategory management
    function showAddSubcategoryModal() {
      // Get active category ID
      var activeCategory = document.querySelector('#categories-tab-pane .category-list li.active');
      if (!activeCategory) {
        alert('Silakan pilih kategori terlebih dahulu');
        return;
      }

      var categoryId = activeCategory.getAttribute('data-id');

      // Buat modal sederhana untuk menambah subkategori
      var modalHtml = '<div id="addSubcategoryModal" class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); z-index: 1050; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; overflow-y: auto; padding: 5% 0;">';
      modalHtml += '<div class="modal-dialog" style="margin: 0 auto; max-width: 500px;">';
      modalHtml += '<div class="modal-content">';
      modalHtml += '<div class="modal-header">';
      modalHtml += '<h5 class="modal-title">Tambah Subkategori Baru</h5>';
      modalHtml += '<button type="button" class="btn-close" onclick="closeModal(\'addSubcategoryModal\')">&times;</button>';
      modalHtml += '</div>';
      modalHtml += '<div class="modal-body">';
      modalHtml += '<form id="addSubcategoryForm">';
      modalHtml += '<div class="mb-3">';
      modalHtml += '<label for="subcategoryName" class="form-label">Nama Subkategori</label>';
      modalHtml += '<input type="text" class="form-control" id="subcategoryName" name="name" required maxlength="255">';
      modalHtml += '</div>';
      modalHtml += '<input type="hidden" id="categoryId" name="category_id" value="' + categoryId + '">';
      modalHtml += '<div class="modal-footer">';
      modalHtml += '<button type="button" class="btn btn-secondary" onclick="closeModal(\'addSubcategoryModal\')">Batal</button>';
      modalHtml += '<button type="submit" class="btn btn-primary">Simpan</button>';
      modalHtml += '</div>';
      modalHtml += '</form>';
      modalHtml += '</div>';
      modalHtml += '</div>';
      modalHtml += '</div>';

      // Tambahkan ke body
      document.body.insertAdjacentHTML('beforeend', modalHtml);

      // Tambahkan event listener ke form (hanya jika element sudah dibuat)
      var addSubcategoryForm = document.getElementById('addSubcategoryForm');
      if (addSubcategoryForm) {
        addSubcategoryForm.addEventListener('submit', function(e) {
          e.preventDefault();

          var subcategoryName = document.getElementById('subcategoryName').value.trim();
          var categoryId = document.getElementById('categoryId').value;

          if (subcategoryName === '') {
            alert('Nama subkategori tidak boleh kosong!');
            return;
          }

          // Disable tombol submit untuk mencegah double submission
          var submitButton = document.querySelector('#addSubcategoryForm button[type="submit"]');
          if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = 'Menyimpan...';
          }

          // Kirim ke server
          fetch('/api/subcategories', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': getCSRFToken()
            },
            body: JSON.stringify({
              name: subcategoryName,
              category_id: categoryId
            })
          })
          .then(function(response) {
            return response.json();
          })
          .then(function(data) {
            // Kembalikan tombol ke kondisi awal
            if (submitButton) {
              submitButton.disabled = false;
              submitButton.innerHTML = 'Simpan';
            }

            if (data.success) {
              alert(data.message);
              closeModal('addSubcategoryModal');
              // Refresh subkategori list tanpa reload halaman
              loadSubcategories(categoryId);
            } else {
              alert('Gagal menambah subkategori: ' + (data.message || 'Data tidak valid'));
            }
          })
          .catch(function(error) {
            console.error('Error:', error);
            // Kembalikan tombol ke kondisi awal
            if (submitButton) {
              submitButton.disabled = false;
              submitButton.innerHTML = 'Simpan';
            }
            alert('Terjadi kesalahan saat menambah subkategori: ' + error.message);
          });
        });
      }
    }

    function editSubcategory(id) {
      // Fetch subcategory data
      fetch('/api/subcategories/' + id)
        .then(function(response) {
          return response.json();
        })
        .then(function(data) {
          if (data.success) {
            var subcategory = data.subcategory;

            // Buat modal sederhana untuk mengedit subkategori
            var modalHtml = '<div id="editSubcategoryModal" class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); z-index: 1050; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; overflow-y: auto; padding: 5% 0;">';
            modalHtml += '<div class="modal-dialog" style="margin: 0 auto; max-width: 500px;">';
            modalHtml += '<div class="modal-content">';
            modalHtml += '<div class="modal-header">';
            modalHtml += '<h5 class="modal-title">Edit Subkategori</h5>';
            modalHtml += '<button type="button" class="btn-close" onclick="closeModal(\'editSubcategoryModal\')">&times;</button>';
            modalHtml += '</div>';
            modalHtml += '<div class="modal-body">';
            modalHtml += '<form id="editSubcategoryForm">';
            modalHtml += '<div class="mb-3">';
            modalHtml += '<label for="editSubcategoryName" class="form-label">Nama Subkategori</label>';
            modalHtml += '<input type="text" class="form-control" id="editSubcategoryName" name="name" value="' + (subcategory.name || '') + '" required maxlength="255">';
            modalHtml += '</div>';
            modalHtml += '<input type="hidden" id="editSubcategoryId" name="id" value="' + id + '">';
            modalHtml += '<div class="modal-footer">';
            modalHtml += '<button type="button" class="btn btn-secondary" onclick="closeModal(\'editSubcategoryModal\')">Batal</button>';
            modalHtml += '<button type="submit" class="btn btn-primary">Simpan Perubahan</button>';
            modalHtml += '</div>';
            modalHtml += '</form>';
            modalHtml += '</div>';
            modalHtml += '</div>';
            modalHtml += '</div>';

            // Tambahkan ke body
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Tambahkan event listener ke form (hanya jika element sudah dibuat)
            var editSubcategoryForm = document.getElementById('editSubcategoryForm');
            if (editSubcategoryForm) {
              editSubcategoryForm.addEventListener('submit', function(e) {
                e.preventDefault();

                var subcategoryName = document.getElementById('editSubcategoryName').value.trim();
                var categoryId = subcategory.category_id; // Use the original category ID

                if (subcategoryName === '') {
                  alert('Nama subkategori tidak boleh kosong!');
                  return;
                }

                // Disable tombol submit untuk mencegah double submission
                var submitButton = document.querySelector('#editSubcategoryForm button[type="submit"]');
                if (submitButton) {
                  submitButton.disabled = true;
                  submitButton.innerHTML = 'Menyimpan...';
                }

                // Kirim ke server
                fetch('/api/subcategories/' + id, {
                  method: 'PUT',
                  headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken()
                  },
                  body: JSON.stringify({
                    name: subcategoryName
                  })
                })
                .then(function(response) {
                  return response.json();
                })
                .then(function(data) {
                  // Kembalikan tombol ke kondisi awal
                  if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Simpan Perubahan';
                  }

                  if (data.success) {
                    alert(data.message);
                    closeModal('editSubcategoryModal');
                    // Refresh subkategori list tanpa reload halaman
                    loadSubcategories(categoryId);
                  } else {
                    alert('Gagal mengedit subkategori: ' + (data.message || 'Data tidak valid'));
                  }
                })
                .catch(function(error) {
                  console.error('Error:', error);
                  // Kembalikan tombol ke kondisi awal
                  if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Simpan Perubahan';
                  }
                  alert('Terjadi kesalahan saat mengedit subkategori: ' + error.message);
                });
              });
            }
          } else {
            alert('Gagal memuat data subkategori: ' + (data.message || 'Data tidak ditemukan'));
          }
        })
        .catch(function(error) {
          console.error('Error fetching subcategory:', error);
          alert('Terjadi kesalahan saat memuat data subkategori');
        });
    }

    function deleteSubcategory(id) {
      // Ambil informasi jumlah produk terlebih dahulu sebelum menghapus
      var fetchUrl = '/api/subcategories/' + id + '/product-count';
      fetch(fetchUrl)
        .then(function(response) {
          return response.json();
        })
        .then(function(data) {
          var productCount = data.product_count || 0;
          var message = 'Apakah Anda yakin ingin menghapus subkategori ini?\\n';
          message += 'Subkategori ini sedang digunakan oleh ' + productCount + ' produk.\\n\\n';
          
          if (productCount > 0) {
            message += 'Peringatan: Produk yang terkait dengan subkategori ini akan tetap ada,\\n';
            message += 'namun tidak akan memiliki subkategori setelah penghapusan.\\n';
            message += 'Anda yakin ingin melanjutkan?';
          } else {
            message += 'Subkategori ini tidak memiliki produk terkait.\\n';
            message += 'Anda yakin ingin melanjutkan?';
          }
          
          if (confirm(message)) {
            var deleteUrl = '/api/subcategories/' + id;
            fetch(deleteUrl, {
              method: 'DELETE',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken()
              }
            })
            .then(function(response) {
              return response.json();
            })
            .then(function(data) {
              if (data.success) {
                alert(data.message);
                // Reload subcategories
                var activeCategoryId = document.querySelector('#categories-tab-pane .category-list li.active')?.getAttribute('data-id');
                if (activeCategoryId) {
                  loadSubcategories(activeCategoryId);
                }
              } else {
                alert('Gagal menghapus subkategori: ' + data.message);
              }
            })
            .catch(function(error) {
              console.error('Error deleting subcategory:', error);
              alert('Terjadi kesalahan saat menghapus subkategori');
            });
          }
        })
        .catch(function(error) {
          console.error('Error checking product count:', error);
          alert('Gagal memeriksa jumlah produk');
        });
    }

    function showAddCategoryModal() {
      // Buat modal sederhana
      var modalHtml = '<div id="addCategoryModal" class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); z-index: 1050; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; overflow-y: auto; padding: 5% 0;">';
      modalHtml += '<div class="modal-dialog" style="margin: 0 auto;">';
      modalHtml += '<div class="modal-content">';
      modalHtml += '<div class="modal-header">';
      modalHtml += '<h5 class="modal-title">Tambah Kategori Baru</h5>';
      modalHtml += '<button type="button" class="btn-close" onclick="closeModal(\'addCategoryModal\')">&times;</button>';
      modalHtml += '</div>';
      modalHtml += '<div class="modal-body">';
      modalHtml += '<form id="addCategoryForm">';
      modalHtml += '<div class="mb-3">';
      modalHtml += '<label for="categoryName" class="form-label">Nama Kategori</label>';
      modalHtml += '<input type="text" class="form-control" id="categoryName" name="name" required maxlength="255">';
      modalHtml += '</div>';
      modalHtml += '<div class="mb-3">';
      modalHtml += '<label for="categoryDescription" class="form-label">Deskripsi Kategori</label>';
      modalHtml += '<textarea class="form-control" id="categoryDescription" name="description" rows="3" maxlength="500"></textarea>';
      modalHtml += '</div>';
      modalHtml += '<div class="text-end">';
      modalHtml += '<button type="button" class="btn btn-secondary" onclick="closeModal(\'addCategoryModal\')">Batal</button>';
      modalHtml += '<button type="submit" class="btn btn-primary">Simpan</button>';
      modalHtml += '</div>';
      modalHtml += '</form>';
      modalHtml += '</div>';
      modalHtml += '</div>';
      modalHtml += '</div>';
      modalHtml += '</div>';
      
      // Tambahkan ke body
      document.body.insertAdjacentHTML('beforeend', modalHtml);
      
      // Tambahkan event listener ke form (hanya jika element sudah dibuat)
      var addCategoryForm = document.getElementById('addCategoryForm');
      if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', function(e) {
          e.preventDefault();

          var categoryName = document.getElementById('categoryName').value.trim();
          var categoryDescription = document.getElementById('categoryDescription').value.trim();

          if (categoryName === '') {
            alert('Nama kategori tidak boleh kosong!');
            return;
          }

          // Disable tombol submit untuk mencegah double submission
          var submitButton = document.querySelector('#addCategoryForm button[type="submit"]');
          if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = 'Menyimpan...';
          }

          // Kirim ke server
          fetch('/api/categories', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': getCSRFToken()
            },
            body: JSON.stringify({
              name: categoryName,
              description: categoryDescription
            })
          })
          .then(function(response) {
            return response.json();
          })
          .then(function(data) {
            // Kembalikan tombol ke kondisi awal
            if (submitButton) {
              submitButton.disabled = false;
              submitButton.innerHTML = 'Simpan';
            }

            if (data.success) {
              alert(data.message);
              closeModal('addCategoryModal');
              // Refresh kategori list tanpa reload halaman
              location.reload();
            } else {
              alert('Gagal menambah kategori: ' + (data.message || 'Data tidak valid'));
            }
          })
          .catch(function(error) {
            console.error('Error:', error);
            // Kembalikan tombol ke kondisi awal
            if (submitButton) {
              submitButton.disabled = false;
              submitButton.innerHTML = 'Simpan';
            }
            alert('Terjadi kesalahan saat menambah kategori: ' + error.message);
          });
        });
      }
    }

    function closeModal(modalId) {
      var modal = document.getElementById(modalId);
      if (modal) {
        modal.remove();
      }
    }

    function deleteCategory(id) {
      // Cek dulu jumlah produk di kategori ini
      fetch('/api/categories/' + id + '/product-count')
        .then(function(response) {
          return response.json();
        })
        .then(function(data) {
          var productCount = data.product_count;
          var message = 'Apakah Anda yakin ingin menghapus kategori ini?\\n';
          message += 'Kategori ini sedang digunakan oleh ' + productCount + ' produk.\\n\\n';
          
          if (productCount > 0) {
            message += 'Peringatan: Produk yang terkait dengan kategori ini akan tetap ada,\\n';
            message += 'namun tidak akan memiliki kategori setelah penghapusan.\\n';
            message += 'Anda yakin ingin melanjutkan?';
          } else {
            message += 'Kategori ini tidak memiliki produk terkait.\\n';
            message += 'Anda yakin ingin melanjutkan?';
          }
          
          if (confirm(message)) {
            // Kirim request hapus
            fetch('/api/categories/' + id, {
              method: 'DELETE',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken()
              }
            })
            .then(function(response) {
              return response.json();
            })
            .then(function(data) {
              if (data.success) {
                alert(data.message);
                // Refresh halaman
                location.reload();
              } else {
                alert('Gagal menghapus kategori: ' + data.message);
              }
            })
            .catch(function(error) {
              console.error('Error:', error);
              alert('Terjadi kesalahan saat menghapus kategori');
            });
          }
        })
        .catch(function(error) {
          console.error('Error checking product count:', error);
          alert('Gagal memeriksa jumlah produk');
        });
    }

    function editCategory(id) {
      // Fetch category data
      fetch('/api/categories/' + id)
        .then(function(response) {
          return response.json();
        })
        .then(function(data) {
          if (data.success) {
            var category = data.category;

            // Buat modal sederhana untuk mengedit kategori
            var modalHtml = '<div id="editCategoryModal" class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); z-index: 1050; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; overflow-y: auto; padding: 5% 0;">';
            modalHtml += '<div class="modal-dialog" style="margin: 0 auto; max-width: 500px;">';
            modalHtml += '<div class="modal-content">';
            modalHtml += '<div class="modal-header">';
            modalHtml += '<h5 class="modal-title">Edit Kategori</h5>';
            modalHtml += '<button type="button" class="btn-close" onclick="closeModal(\'editCategoryModal\')">&times;</button>';
            modalHtml += '</div>';
            modalHtml += '<div class="modal-body">';
            modalHtml += '<form id="editCategoryForm">';
            modalHtml += '<div class="mb-3">';
            modalHtml += '<label for="editCategoryName" class="form-label">Nama Kategori</label>';
            modalHtml += '<input type="text" class="form-control" id="editCategoryName" name="name" value="' + (category.name || '') + '" required maxlength="255">';
            modalHtml += '</div>';
            modalHtml += '<div class="mb-3">';
            modalHtml += '<label for="editCategoryDescription" class="form-label">Deskripsi Kategori</label>';
            modalHtml += '<textarea class="form-control" id="editCategoryDescription" name="description" rows="3" maxlength="500">' + (category.description || '') + '</textarea>';
            modalHtml += '</div>';
            modalHtml += '<input type="hidden" id="editCategoryId" name="id" value="' + id + '">';
            modalHtml += '<div class="modal-footer">';
            modalHtml += '<button type="button" class="btn btn-secondary" onclick="closeModal(\'editCategoryModal\')">Batal</button>';
            modalHtml += '<button type="submit" class="btn btn-primary">Simpan Perubahan</button>';
            modalHtml += '</div>';
            modalHtml += '</form>';
            modalHtml += '</div>';
            modalHtml += '</div>';
            modalHtml += '</div>';

            // Tambahkan ke body
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Tambahkan event listener ke form (hanya jika element sudah dibuat)
            var editCategoryForm = document.getElementById('editCategoryForm');
            if (editCategoryForm) {
              editCategoryForm.addEventListener('submit', function(e) {
                e.preventDefault();

                var categoryName = document.getElementById('editCategoryName').value.trim();
                var categoryDescription = document.getElementById('editCategoryDescription').value.trim();

                if (categoryName === '') {
                  alert('Nama kategori tidak boleh kosong!');
                  return;
                }

                // Disable tombol submit untuk mencegah double submission
                var submitButton = document.querySelector('#editCategoryForm button[type="submit"]');
                if (submitButton) {
                  submitButton.disabled = true;
                  submitButton.innerHTML = 'Menyimpan...';
                }

                // Kirim ke server
                fetch('/api/categories/' + id, {
                  method: 'PUT', // Using PUT method for update
                  headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken()
                  },
                  body: JSON.stringify({
                    name: categoryName,
                    description: categoryDescription
                  })
                })
                .then(function(response) {
                  return response.json();
                })
                .then(function(data) {
                  // Kembalikan tombol ke kondisi awal
                  if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Simpan Perubahan';
                  }

                  if (data.success) {
                    alert(data.message);
                    closeModal('editCategoryModal');
                    // Refresh halaman untuk memperbarui daftar kategori
                    location.reload();
                  } else {
                    alert('Gagal mengedit kategori: ' + (data.message || 'Data tidak valid'));
                  }
                })
                .catch(function(error) {
                  console.error('Error:', error);
                  // Kembalikan tombol ke kondisi awal
                  if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Simpan Perubahan';
                  }
                  alert('Terjadi kesalahan saat mengedit kategori: ' + error.message);
                });
              });
            }
          } else {
            alert('Gagal memuat data kategori: ' + (data.message || 'Data tidak ditemukan'));
          }
        })
        .catch(function(error) {
          console.error('Error fetching category:', error);
          alert('Terjadi kesalahan saat memuat data kategori');
        });
    }

    // Handle tab changes and filtering - inside DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function() {
      // Get the active tab from URL or default to products tab
      var urlParams = new URLSearchParams(window.location.search);
      var activeTabFromUrl = urlParams.get('tab');
      
      // Set the active tab when page loads
      if (activeTabFromUrl) {
        var activeTabBtn = document.getElementById(activeTabFromUrl + '-tab');
        var activeTabPane = document.getElementById(activeTabFromUrl + '-tab-pane');
        
        if (activeTabBtn && activeTabPane) {
          // Remove active class from all tabs and panes
          document.querySelectorAll('#productManagementTabs .nav-link').forEach(function(tab) {
            tab.classList.remove('active');
          });
          document.querySelectorAll('.tab-pane').forEach(function(pane) {
            pane.classList.remove('show');
            pane.classList.remove('active');
          });
          
          // Add active class to the desired tab and pane
          activeTabBtn.classList.add('active');
          activeTabPane.classList.add('show');
          activeTabPane.classList.add('active');
        }
      }
      
      var tabTriggerList = [].slice.call(document.querySelectorAll('#productManagementTabs button[data-bs-toggle="tab"]'));
      tabTriggerList.forEach(function(tabTrigger) {
        tabTrigger.addEventListener('shown.bs.tab', function(event) {
          // Update URL with current tab when tab changes
          var currentTab = event.target.id.replace('-tab', '');
          var urlParams = new URLSearchParams(window.location.search);
          urlParams.set('tab', currentTab);
          
          // Update the URL without reloading the page
          var newUrl = window.location.pathname + '?' + urlParams.toString();
          history.pushState({}, '', newUrl);
        });
      });
      
      // Auto-submit product filter form when any filter changes
      var productFilterForm = document.getElementById('productFilterForm');
      
      if (productFilterForm) {
        // Prevent default form submission
        productFilterForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          // Build URL with query parameters
          var formData = new FormData(productFilterForm);
          var params = new URLSearchParams();
          
          for (var pair of formData.entries()) {
            var key = pair[0];
            var value = pair[1];
            if (value.trim() !== '') { // Only add non-empty values
              params.append(key, value);
            }
          }
          
          // Preserve the current active tab in the URL
          var currentActiveTab = document.querySelector('#productManagementTabs .nav-link.active').id.replace('-tab', '');
          params.set('tab', currentActiveTab);
          
          // Get the current URL without query parameters
          var newUrl = window.location.pathname;
          
          // Add parameters if there are any
          if (params.toString()) {
            newUrl += '?' + params.toString();
          }
          
          // Navigate to the new URL with parameters
          window.location.href = newUrl;
        });
        
        // Add event listeners to product filter inputs for auto-submit
        var productFilterInputs = productFilterForm.querySelectorAll('input, select');
        
        productFilterInputs.forEach(function(input) {
          // Handle change events for select and other inputs
          input.addEventListener('change', function() {
            // Submit the form when any filter changes
            productFilterForm.dispatchEvent(new Event('submit'));
          });
          
          // Handle input events for text inputs (for real-time search)
          if (input.type === 'text') {
            var timeout;
            input.addEventListener('input', function() {
              clearTimeout(timeout);
              timeout = setTimeout(function() {
                // Submit the form after user stops typing
                productFilterForm.dispatchEvent(new Event('submit'));
              }, 500); // Delay for 500ms after user stops typing
            });
          }
        });
      }
      
      // Auto-submit review filter form when any filter changes
      var reviewFilterForm = document.getElementById('reviewFilterForm');
      
      if (reviewFilterForm) {
        // Prevent default form submission
        reviewFilterForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          // Build URL with query parameters
          var formData = new FormData(reviewFilterForm);
          var params = new URLSearchParams();
          
          for (var pair of formData.entries()) {
            var key = pair[0];
            var value = pair[1];
            if (value.trim() !== '') { // Only add non-empty values
              params.append(key, value);
            }
          }
          
          // Preserve the current active tab in the URL
          params.set('tab', 'reviews');
          
          // Get the current URL without query parameters
          var newUrl = window.location.pathname;
          
          // Add parameters if there are any
          if (params.toString()) {
            newUrl += '?' + params.toString();
          }
          
          // Navigate to the new URL with parameters
          window.location.href = newUrl;
        });
        
        // Add event listeners to review filter inputs for auto-submit
        var reviewFilterInputs = reviewFilterForm.querySelectorAll('input, select');
        
        reviewFilterInputs.forEach(function(input) {
          // Handle change events for select and other inputs
          input.addEventListener('change', function() {
            // Submit the form when any filter changes
            reviewFilterForm.dispatchEvent(new Event('submit'));
          });
          
          // Handle input events for text inputs (for real-time search)
          if (input.type === 'text') {
            var timeout;
            input.addEventListener('input', function() {
              clearTimeout(timeout);
              timeout = setTimeout(function() {
                // Submit the form after user stops typing
                reviewFilterForm.dispatchEvent(new Event('submit'));
              }, 500); // Delay for 500ms after user stops typing
            });
          }
        });
      }

      // Simulate category click when user clicks on category in the filter panel
      document.getElementById('category_filter').addEventListener('change', function() {
        var categoryId = this.value;
        if (categoryId) {
          // Simulate click on the corresponding category in the category panel
          var categoryItem = document.querySelector('#categories-tab-pane .category-list li[data-id="' + categoryId + '"]');
          if (categoryItem) {
            categoryItem.click();
          } else {
            // If not found in panel, just load subcategories
            loadSubcategories(categoryId);
          }
        } else {
          loadSubcategories(null);
        }
      });
    });
  </script>
  
@endsection