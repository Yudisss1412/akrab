<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Manajemen Produk — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <style>
    :root {
      --ak-primary: #006E5C;
      --ak-primary-light: #a8d5c9;
      --ak-white: #FFFFFF;
      --ak-background: #f0fdfa;
      --ak-text: #1D232A;
      --ak-muted: #6b7280;
      --ak-border: #E5E7EB;
      --ak-radius: 12px;
      --ak-space: 16px;
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      color: var(--ak-text);
      background: var(--ak-background);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .main-layout {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .content-wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: 0 1.5rem;
    }

    /* Welcome Banner */
    .page-header {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
    }

    .page-header h1 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--ak-primary);
    }

    /* Control Bar */
    .control-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      background: var(--ak-white);
      padding: 1rem;
      border-radius: var(--ak-radius);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
    }

    .control-bar-left {
      display: flex;
      gap: 1rem;
    }

    .btn {
      border: 1px solid transparent;
      border-radius: var(--ak-radius);
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }

    .btn-primary {
      background: var(--ak-primary);
      color: white;
    }

    .btn-primary:hover {
      background: #005a4a;
    }

    .search {
      position: relative;
      display: flex;
      align-items: center;
    }

    .search input {
      padding: 0.5rem 0.75rem 0.5rem 2.5rem;
      border: 1px solid var(--ak-border);
      border-radius: var(--ak-radius);
      font-size: 0.875rem;
      width: 250px;
    }

    .search svg {
      position: absolute;
      left: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      width: 18px;
      height: 18px;
      color: var(--ak-muted);
    }

    /* Table */
    .table-card {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
      margin-bottom: 1.5rem;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
    }

    .table th {
      text-align: left;
      padding: 0.75rem 1rem;
      font-size: 0.875rem;
      color: var(--ak-muted);
      font-weight: 600;
      border-bottom: 1px solid var(--ak-border);
    }

    .table td {
      padding: 0.75rem 1rem;
      font-size: 0.875rem;
      border-bottom: 1px solid var(--ak-border);
    }

    .table tr:last-child td {
      border-bottom: none;
    }

    .product-cell {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .product-thumb {
      width: 40px;
      height: 40px;
      border-radius: 6px;
      object-fit: cover;
      background: #f3f4f6;
    }

    .status-badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 1rem;
      font-size: 0.75rem;
      font-weight: 500;
    }

    .status-active {
      background: rgba(34, 197, 94, 0.1);
      color: #16a34a;
    }

    .status-pending {
      background: rgba(234, 179, 8, 0.1);
      color: #ca8a04;
    }

    .status-suspended {
      background: rgba(239, 68, 68, 0.1);
      color: #dc2626;
    }

    .action-buttons {
      display: flex;
      gap: 0.5rem;
    }

    .action-btn {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      border: 1px solid var(--ak-border);
      background: var(--ak-white);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .action-btn:hover {
      background: var(--ak-primary);
      color: white;
      border-color: var(--ak-primary);
    }

    .action-btn svg {
      width: 16px;
      height: 16px;
    }

    /* Pagination */
    .pagination {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1rem;
    }

    .pagination-info {
      color: var(--ak-muted);
      font-size: 0.875rem;
    }

    .pagination-nav {
      display: flex;
      gap: 0.5rem;
    }

    .pagination-btn {
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--ak-border);
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      font-size: 0.875rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .pagination-btn:hover {
      background: var(--ak-primary);
      color: white;
      border-color: var(--ak-primary);
    }

    .pagination-btn.active {
      background: var(--ak-primary);
      color: white;
      border-color: var(--ak-primary);
    }

    .pagination-separator {
      display: flex;
      align-items: center;
      margin: 0 0.5rem;
    }

    /* Modal */
    .modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }

    .modal.active {
      opacity: 1;
      visibility: visible;
    }

    .modal-content {
      background-color: var(--ak-white);
      border-radius: var(--ak-radius);
      width: 100%;
      max-width: 500px;
      max-height: 90vh;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      transform: translateY(-20px);
      transition: transform 0.3s ease;
      display: flex;
      flex-direction: column;
    }

    .modal.active .modal-content {
      transform: translateY(0);
    }

    .modal-header {
      padding: 1.5rem;
      border-bottom: 1px solid var(--ak-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-shrink: 0;
    }

    .modal-title {
      margin: 0;
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--ak-primary);
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--ak-muted);
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      transition: all 0.2s ease;
    }

    .modal-close:hover {
      background-color: #f3f4f6;
      color: var(--ak-text);
    }

    .modal-body {
      padding: 1.5rem;
      overflow-y: auto;
      flex: 1;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-size: 0.875rem;
      color: var(--ak-text);
      font-weight: 500;
    }

    .form-control {
      width: 100%;
      padding: 0.5rem 0.75rem;
      border: 1px solid var(--ak-border);
      border-radius: var(--ak-radius);
      font-size: 0.875rem;
      transition: border-color 0.2s ease;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--ak-primary);
    }

    .form-row {
      display: flex;
      gap: 1rem;
    }

    .form-row .form-group {
      flex: 1;
    }

    .modal-footer {
      padding: 1rem 1.5rem;
      border-top: 1px solid var(--ak-border);
      display: flex;
      justify-content: flex-end;
      gap: 0.5rem;
    }

    /* Image Upload */
    .image-upload {
      border: 2px dashed var(--ak-border);
      border-radius: var(--ak-radius);
      padding: 1.5rem;
      text-align: center;
      cursor: pointer;
      transition: border-color 0.2s ease;
      margin-bottom: 1rem;
    }

    .image-upload:hover {
      border-color: var(--ak-primary);
    }

    .image-upload p {
      margin: 0;
      color: var(--ak-muted);
      font-size: 0.875rem;
    }

    .image-upload input[type="file"] {
      display: none;
    }

    .image-preview {
      width: 100%;
      height: 150px;
      border-radius: var(--ak-radius);
      background-color: #f9fafb;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      margin-top: 1rem;
    }

    .image-preview img {
      max-width: 100%;
      max-height: 100%;
      object-fit: cover;
    }

    /* Ensure modal appears above header and footer */
    .ak-navbar,
    .ak-footer {
      position: relative;
      z-index: 10;
    }

    .modal {
      z-index: 1000;
    }

    .loading {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(255,255,255,.3);
      border-radius: 50%;
      border-top-color: #fff;
      animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 768px) {
      .control-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
      }

      .control-bar-left {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        align-items: center; /* Center align items */
      }

      .btn-primary {
        align-self: center; /* Center align the button */
      }

      .form-control {
        width: 100% !important;
        max-width: 300px; /* Limit width but allow to shrink */
        min-width: 0;
      }

      .search {
        width: 100%;
        max-width: 300px; /* Match search to form control width */
        align-self: center;
      }

      .search input {
        width: 100%;
      }

      .table-card {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      .table {
        min-width: 650px;
        width: 100%;
      }

      /* Improve table cell readability on mobile */
      .table th,
      .table td {
        white-space: nowrap;
      }

      .pagination {
        flex-direction: column;
        gap: 1rem;
      }

      .pagination-nav {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        justify-content: flex-start;
        padding: 0.5rem 0;
        margin: 0 auto;
      }

      .pagination-btn {
        min-width: 32px;
      }
    }
  </style>
  <script>
    // Modal functionality
    document.addEventListener('DOMContentLoaded', function() {
      const modal = document.getElementById('productModal');
      const openModalBtn = document.querySelector('.btn-primary');
      const closeModalBtn = document.querySelector('.modal-close');
      const modalBackdrop = modal.querySelector('.modal');

      openModalBtn.addEventListener('click', function() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
      });

      closeModalBtn.addEventListener('click', function() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
      });

      modalBackdrop.addEventListener('click', function(e) {
        if (e.target === modalBackdrop) {
          modal.classList.remove('active');
          document.body.style.overflow = '';
        }
      });

      // Close modal with Escape key
      document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('productModal');
        if (e.key === 'Escape' && modal.classList.contains('active')) {
          modal.classList.remove('active');
          document.body.style.overflow = '';
        }
      });

      // Function to handle adding/updating products via AJAX
      document.getElementById('saveProductBtn').addEventListener('click', function() {
        const saveBtn = document.getElementById('saveProductBtn');
        const originalText = saveBtn.textContent;

        // Show loading state
        saveBtn.innerHTML = '<div class="loading"></div>';
        saveBtn.disabled = true;

        // Get form data
        const formData = new FormData();
        formData.append('name', document.getElementById('productName').value);
        formData.append('description', document.getElementById('productDescription').value);
        formData.append('price', document.getElementById('productPrice').value);
        formData.append('category_id', document.getElementById('productCategory').value);
        formData.append('stock', document.getElementById('productStock').value);
        formData.append('weight', document.getElementById('productWeight').value);

        // Get all selected images
        const imageInput = document.getElementById('productImage');
        for (let i = 0; i < imageInput.files.length; i++) {
          formData.append('images[]', imageInput.files[i]);
        }

        // Add variants if needed (simplified for now)
        const isEdit = document.querySelector('.modal-title').textContent.includes('Edit');
        const productId = document.querySelector('.modal-title').dataset.productId;

        fetch(isEdit ? `/penjual/produk/${productId}` : '/penjual/produk', {
          method: isEdit ? 'PUT' : 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(data.message);
            location.reload(); // Reload the page to show updated products
          } else {
            alert('Error: ' + (data.message || 'Unknown error occurred'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error occurred while saving product');
        })
        .finally(() => {
          // Restore button state
          saveBtn.textContent = originalText;
          saveBtn.disabled = false;
        });
      });

      // Image preview functionality
      const imageUploadArea = document.getElementById('imageUploadArea');
      const imageInput = document.getElementById('productImage');
      const imagePreview = document.getElementById('imagePreview');

      imageUploadArea.addEventListener('click', function() {
        imageInput.click();
      });

      imageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
          const reader = new FileReader();

          reader.onload = function(e) {
            imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
          }

          reader.readAsDataURL(this.files[0]);
        }
      });

      // Drag and drop functionality
      imageUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = 'var(--ak-primary)';
      });

      imageUploadArea.addEventListener('dragleave', function() {
        this.style.borderColor = 'var(--ak-border)';
      });

      imageUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = 'var(--ak-border)';

        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
          imageInput.files = e.dataTransfer.files;

          const reader = new FileReader();
          reader.onload = function(e) {
            imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
          }

          reader.readAsDataURL(e.dataTransfer.files[0]);
        }
      });
    });

    // Function to handle editing a product
    function editProduct(productId, productName, productPrice, productStock, productCategory, productDescription, productWeight) {
      document.querySelector('.modal-title').textContent = 'Edit Produk';
      document.querySelector('.modal-title').dataset.productId = productId;
      document.getElementById('productName').value = productName;
      document.getElementById('productPrice').value = productPrice;
      document.getElementById('productStock').value = productStock;
      document.getElementById('productCategory').value = productCategory;
      document.getElementById('productDescription').value = productDescription;
      document.getElementById('productWeight').value = productWeight;
      document.getElementById('saveProductBtn').textContent = 'Update Produk';

      const modal = document.getElementById('productModal');
      modal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    // Variables to store current product being deleted
    let currentDeleteProductId = null;
    let currentDeleteProductName = '';

    // Function to handle deleting a product
    function deleteProduct(productId, productName) {
      currentDeleteProductId = productId;
      currentDeleteProductName = productName;

      // Set product name in modal
      document.getElementById('productNameToDelete').textContent = productName;

      // Show modal
      const deleteModal = document.getElementById('deleteModal');
      deleteModal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    // Function to close delete modal
    function closeDeleteModal() {
      const deleteModal = document.getElementById('deleteModal');
      deleteModal.classList.remove('active');
      document.body.style.overflow = '';
      currentDeleteProductId = null;
      currentDeleteProductName = '';
    }

    // Function to confirm deletion
    function confirmDelete() {
      if (currentDeleteProductId) {
        fetch(`/penjual/produk/${currentDeleteProductId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            closeDeleteModal();
            alert(data.message);
            location.reload(); // Reload the page to reflect changes
          } else {
            alert('Error: ' + (data.message || 'Unknown error occurred'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error occurred while deleting product');
        });
      }
    }

    // Initialize delete modal event listeners
    document.addEventListener('DOMContentLoaded', function() {
      const deleteModal = document.getElementById('deleteModal');
      const deleteModalBackdrop = deleteModal;

      // Close modal with backdrop click
      deleteModalBackdrop.addEventListener('click', function(e) {
        if (e.target === deleteModalBackdrop) {
          closeDeleteModal();
        }
      });

      // Close modal with Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && deleteModal.classList.contains('active')) {
          closeDeleteModal();
        }
      });
    });

    // Function to update URL with filters
    function updateFilters() {
        const url = new URL(window.location);
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const statusFilter = document.getElementById('statusFilter');

        const searchTerm = searchInput ? searchInput.value.trim() : '';
        const category = categoryFilter ? categoryFilter.value : '';
        const status = statusFilter ? statusFilter.value : '';

        // Clear existing params
        url.searchParams.delete('search');
        url.searchParams.delete('category');
        url.searchParams.delete('status');
        url.searchParams.delete('page');

        // Set new params
        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        }
        if (category) {
            url.searchParams.set('category', category);
        }
        if (status) {
            url.searchParams.set('status', status);
        }

        window.location = url.toString();
    }

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const statusFilter = document.getElementById('statusFilter');
        let searchTimeout;

        // Add event listeners only if elements exist
        if (searchInput) {
            // Search input handler
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(updateFilters, 500); // Debounce for 500ms
            });

            // Enter key to trigger search immediately
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimeout);
                    updateFilters();
                }
            });
        }

        // Filter change handlers - only add if elements exist
        if (categoryFilter) {
            categoryFilter.addEventListener('change', function() {
                updateFilters();
            });
        }

        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                updateFilters();
            });
        }
    });
  </script>

  <!-- Modal Konfirmasi Hapus Produk -->
  <div class="modal" id="deleteModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Konfirmasi Hapus Produk</h3>
        <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus produk "<span id="productNameToDelete"></span>"?</p>
        <p class="text-muted">Tindakan ini tidak dapat dibatalkan.</p>
      </div>
      <div class="modal-footer">
        <button class="btn" onclick="closeDeleteModal()" style="background: var(--ak-border); color: var(--ak-text);">Batal</button>
        <button class="btn btn-primary" onclick="confirmDelete()">Hapus Produk</button>
      </div>
    </div>
  </div>

  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
  @include('components.admin_penjual.header')

  <div class="main-layout">
    <div class="content-wrapper">
      <main class="content admin-page-content" role="main">
        <!-- Page Header -->
        <section class="page-header">
          <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="{{ route('seller.dashboard') }}" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; color: inherit;">
              <i class="fas fa-arrow-left" style="font-size: 1.2rem;"></i>
            </a>
            <h1 style="margin: 0; font-size: 1.5rem; display: inline-block;">Manajemen Produk untuk {{ auth()->user()->name }}</h1>
          </div>
        </section>

        <!-- Control Bar -->
        <div class="control-bar">
          <div class="control-bar-left">
            <a href="{{ route('penjual.produk.create') }}" class="btn btn-primary">+ Tambah Produk</a>

            <!-- Filter Kategori -->
            <select class="form-control" id="categoryFilter" style="width: 200px; margin-left: 1rem;">
              <option value="">Semua Kategori</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>

            <!-- Filter Status -->
            <select class="form-control" id="statusFilter" style="width: 150px; margin-left: 1rem;">
              <option value="">Semua Status</option>
              <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
              <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
              <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
              <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
              <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
          </div>

          <div class="search">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <input type="text" placeholder="Cari produk..." id="searchInput" value="{{ request('search') }}">
          </div>
        </div>

        <!-- Product Table -->
        <section class="table-card">
          <table class="table">
            <thead>
              <tr>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($products as $product)
              <tr>
                <td>
                  <div class="product-cell">
                    @if($product->images->first())
                      <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="product-thumb">
                    @else
                      <img src="https://placehold.co/40x40" alt="{{ $product->name }}" class="product-thumb">
                    @endif
                    <span>{{ $product->name }}</span>
                  </div>
                </td>
                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td>{{ $product->category ? $product->category->name : 'N/A' }}</td>
                <td>{{ $product->stock }}</td>
                <td>
                  <span class="status-badge
                    @if($product->status === 'aktif') status-active
                    @elseif($product->status === 'pending') status-pending
                    @else status-suspended @endif">
                    {{ ucfirst($product->status) }}
                  </span>
                </td>
                <td>
                  <div class="action-buttons">
                    <a href="{{ route('penjual.produk.edit', $product->id) }}" class="action-btn" title="Edit">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.44 7.875L15.12 6.555C14.7313 6.16629 14.2054 5.9536 13.6557 5.9536C13.106 5.9536 12.5801 6.16629 12.1914 6.555L4.25137 14.495C4.10967 14.6367 4.00687 14.812 3.95154 15.0075L3.00137 18.755C2.92658 19.0539 3.01586 19.3738 3.23538 19.5933C3.4549 19.8128 3.7748 19.9021 4.07367 19.8273L7.8212 18.8771C8.0167 18.8218 8.19202 18.719 8.33372 18.5773L16.2737 10.6373C16.6624 10.2486 16.8751 9.72269 16.8751 9.17297C16.8751 8.62325 16.6624 8.09734 16.2737 7.70863L16.44 7.875ZM17.34 6.975L16.02 5.655C15.6313 5.26629 15.1054 5.0536 14.5557 5.0536C14.006 5.0536 13.4801 5.26629 13.0914 5.655L5.15137 13.595C4.76266 13.9837 4.54998 14.5096 4.54998 15.0593C4.54998 15.609 4.76266 16.1349 5.15137 16.5236L6.62137 18.005L10.3714 16.5236L11.8414 15.0593L17.34 9.575C17.7287 9.18629 17.9414 8.66038 17.9414 8.11066C17.9414 7.56094 17.7287 7.03503 17.34 6.64632V6.975Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </a>
                    <button class="action-btn" title="Hapus" onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 7H20M10 11V17M14 11V17M5 7L6 19C6 20.1046 6.89543 21 8 21H16C17.1046 21 18 20.1046 18 19L19 7M9 7V4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" style="text-align: center;">Tidak ada produk ditemukan</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </section>

        <!-- Pagination -->
        <section class="pagination">
          <div class="pagination-info">
            Menampilkan {{ $products->firstItem() }}-{{ $products->lastItem() }} dari {{ $products->total() }} produk
          </div>
          <div class="pagination-nav">
            @if ($products->onFirstPage())
              <button class="pagination-btn" disabled>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M15 19L8 12L15 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            @else
              <a href="{{ $products->previousPageUrl() }}" class="pagination-btn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M15 19L8 12L15 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </a>
            @endif

            @for ($i = 1; $i <= $products->lastPage(); $i++)
              <a href="{{ $products->url($i) }}" class="pagination-btn {{ $i == $products->currentPage() ? 'active' : '' }}">{{ $i }}</a>
            @endfor

            @if ($products->hasMorePages())
              <a href="{{ $products->nextPageUrl() }}" class="pagination-btn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M9 5L16 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </a>
            @else
              <button class="pagination-btn" disabled>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M9 5L16 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            @endif
          </div>
        </section>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')
</body>
</html>