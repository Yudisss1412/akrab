<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Manajemen Produk — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/penjual/manajemen_produk.css') }}">
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