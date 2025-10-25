@extends('layouts.app')

@section('title', 'Buat Diskon Produk Baru')

@section('header')
  @include('components.penjual.header_compact')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/penjual/manajemen-promosi.css') }}">
  <style>
    /* Styling tambahan untuk formulir diskon */
    .form-card {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }
    
    .form-group {
      margin-bottom: 1rem;
    }
    
    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--ak-text);
    }
    
    .form-control {
      width: 100%;
      padding: 0.5rem 0.75rem;
      border: 1px solid var(--ak-border);
      border-radius: var(--ak-radius);
      font-size: 0.875rem;
      transition: border-color 0.2s;
    }
    
    .form-control:focus {
      outline: none;
      border-color: var(--ak-primary);
      box-shadow: 0 0 0 3px rgba(0, 110, 92, 0.1);
    }
    
    .radio-group {
      display: flex;
      gap: 1rem;
      margin-top: 0.5rem;
    }
    
    .radio-option {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .product-search-container {
      position: relative;
    }
    
    .selected-products {
      margin-top: 1rem;
      max-height: 200px;
      overflow-y: auto;
      border: 1px solid var(--ak-border);
      border-radius: var(--ak-radius);
      padding: 0.75rem;
    }
    
    .selected-product-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem;
      border-bottom: 1px solid var(--ak-border);
    }
    
    .selected-product-item:last-child {
      border-bottom: none;
    }
    
    .remove-product-btn {
      background: var(--ak-danger);
      color: white;
      border: none;
      border-radius: 4px;
      padding: 0.25rem 0.5rem;
      cursor: pointer;
      font-size: 0.75rem;
    }
    
    .product-suggestions {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: white;
      border: 1px solid var(--ak-border);
      border-radius: var(--ak-radius);
      max-height: 200px;
      overflow-y: auto;
      z-index: 1000;
      display: none;
    }
    
    .product-suggestion-item {
      padding: 0.75rem;
      cursor: pointer;
      border-bottom: 1px solid var(--ak-border);
    }
    
    .product-suggestion-item:last-child {
      border-bottom: none;
    }
    
    .product-suggestion-item:hover {
      background-color: var(--ak-background);
    }
    
    .action-buttons {
      display: flex;
      gap: 1rem;
      justify-content: flex-end;
      padding-top: 1rem;
      border-top: 1px solid var(--ak-border);
    }
    
    .btn-secondary {
      background: #6c757d;
      color: white;
      border-color: #6c757d;
    }
    
    .btn-secondary:hover {
      background: #5a6268;
      border-color: #545b62;
    }
  </style>
@endpush

@section('content')
  <main class="create-discount">
    <div class="container-fluid">
      <!-- Header Halaman -->
      <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <a href="{{ route('penjual.promosi') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
          </a>
          <h1 class="page-title mb-0">Buat Diskon Produk Baru</h1>
        </div>
      </div>
      
      <!-- Formulir Diskon -->
      <form id="createDiscountForm" action="{{ route('penjual.promosi.diskon.store') }}" method="POST">
        @csrf
        <!-- Bagian Informasi Dasar -->
        <div class="form-card">
          <h5 class="card-title mb-3">Informasi Dasar</h5>
          <div class="form-group">
            <label for="discountName" class="form-label">Nama Promosi</label>
            <input type="text" id="discountName" name="name" class="form-control" placeholder="Contoh: Diskon Akhir Tahun">
          </div>
        </div>
        
        <!-- Bagian Pengaturan Diskon -->
        <div class="form-card">
          <h5 class="card-title mb-3">Pengaturan Diskon</h5>
          <div class="form-group">
            <label class="form-label">Tipe Diskon</label>
            <div class="radio-group">
              <div class="radio-option">
                <input type="radio" id="typePercentage" name="type" value="percentage" checked>
                <label for="typePercentage">Persentase (%)</label>
              </div>
              <div class="radio-option">
                <input type="radio" id="typeFixed" name="type" value="fixed_amount">
                <label for="typeFixed">Potongan Harga Tetap (Rp)</label>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="discountValue" class="form-label">Besar Diskon</label>
            <input type="number" id="discountValue" name="discount_value" class="form-control" placeholder="Masukkan besar diskon">
          </div>
        </div>
        
        <!-- Bagian Pemilihan Produk -->
        <div class="form-card">
          <h5 class="card-title mb-3">Pemilihan Produk</h5>
          <div class="form-group">
            <label for="productSearch" class="form-label">Pilih Produk yang akan Didiskon</label>
            <div class="product-search-container">
              <input type="text" id="productSearch" class="form-control" placeholder="Cari di Akrab...">
              <div class="product-suggestions" id="productSuggestions"></div>
            </div>
            
            <div class="selected-products" id="selectedProductsContainer">
              <p class="text-muted mb-0">Belum ada produk yang dipilih</p>
            </div>
          </div>
        </div>
        
        <!-- Bagian Periode Promosi -->
        <div class="form-card">
          <h5 class="card-title mb-3">Periode Promosi</h5>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="startDate" class="form-label">Tanggal & Waktu Mulai</label>
                <input type="datetime-local" id="startDate" name="start_date" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="endDate" class="form-label">Tanggal & Waktu Selesai</label>
                <input type="datetime-local" id="endDate" name="end_date" class="form-control">
              </div>
            </div>
          </div>
        </div>
        
        <!-- Bagian Tombol Aksi -->
        <div class="form-card">
          <div class="action-buttons">
            <a href="{{ route('penjual.promosi') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Diskon</button>
          </div>
        </div>
      </form>
      

    </div>
  </main>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Produk dummy untuk pencarian
      const products = [
        { id: 1, name: 'Kaos Polos Premium', category: 'Pakaian' },
        { id: 2, name: 'Celana Jeans Levis', category: 'Pakaian' },
        { id: 3, name: 'Sepatu Olahraga Nike', category: 'Aksesoris' },
        { id: 4, name: 'Jam Tangan Pria', category: 'Aksesoris' },
        { id: 5, name: 'Kemeja Formal', category: 'Pakaian' },
        { id: 6, name: 'Tas Ransel Laptop', category: 'Aksesoris' },
        { id: 7, name: 'Topi Baseball', category: 'Aksesoris' },
        { id: 8, name: 'Sweater Rajut', category: 'Pakaian' }
      ];
      
      // Fungsi pencarian produk
      const productSearchInput = document.getElementById('productSearch');
      const productSuggestions = document.getElementById('productSuggestions');
      const selectedProductsContainer = document.getElementById('selectedProductsContainer');
      
      // Produk yang telah dipilih
      let selectedProducts = [];
      
      // Fungsi untuk menampilkan saran produk
      function showProductSuggestions(searchTerm) {
        // Filter produk berdasarkan nama
        const filteredProducts = products.filter(product => 
          product.name.toLowerCase().includes(searchTerm.toLowerCase())
        );
        
        // Kosongkan saran sebelumnya
        productSuggestions.innerHTML = '';
        
        // Tampilkan saran produk
        if (filteredProducts.length > 0) {
          filteredProducts.forEach(product => {
            const suggestionItem = document.createElement('div');
            suggestionItem.className = 'product-suggestion-item';
            suggestionItem.textContent = product.name;
            suggestionItem.dataset.productId = product.id;
            
            suggestionItem.addEventListener('click', function() {
              addProductToSelection(product);
              productSearchInput.value = '';
              productSuggestions.style.display = 'none';
            });
            
            productSuggestions.appendChild(suggestionItem);
          });
          
          productSuggestions.style.display = 'block';
        } else {
          productSuggestions.style.display = 'none';
        }
      }
      
      // Fungsi untuk menambahkan produk ke daftar yang dipilih
      function addProductToSelection(product) {
        // Cek apakah produk sudah dipilih sebelumnya
        if (!selectedProducts.some(p => p.id === product.id)) {
          selectedProducts.push(product);
          updateSelectedProductsDisplay();
        }
      }
      
      // Fungsi untuk menghapus produk dari daftar yang dipilih
      function removeProductFromSelection(productId) {
        selectedProducts = selectedProducts.filter(product => product.id !== productId);
        updateSelectedProductsDisplay();
      }
      
      // Fungsi untuk memperbarui tampilan produk yang dipilih
      function updateSelectedProductsDisplay() {
        selectedProductsContainer.innerHTML = '';
        
        if (selectedProducts.length === 0) {
          selectedProductsContainer.innerHTML = '<p class="text-muted mb-0">Belum ada produk yang dipilih</p>';
        } else {
          selectedProducts.forEach(product => {
            const productItem = document.createElement('div');
            productItem.className = 'selected-product-item';
            productItem.innerHTML = `
              <span>${product.name}</span>
              <button type="button" class="remove-product-btn" data-product-id="${product.id}">Hapus</button>
            `;
            
            selectedProductsContainer.appendChild(productItem);
          });
          
          // Tambahkan event listener untuk tombol hapus
          document.querySelectorAll('.remove-product-btn').forEach(button => {
            button.addEventListener('click', function() {
              const productId = parseInt(this.getAttribute('data-product-id'));
              removeProductFromSelection(productId);
            });
          });
        }
      }
      
      // Event listener untuk input pencarian produk
      productSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        if (searchTerm) {
          showProductSuggestions(searchTerm);
        } else {
          productSuggestions.style.display = 'none';
        }
      });
      
      // Event listener untuk menyembunyikan saran saat input kehilangan fokus
      productSearchInput.addEventListener('blur', function() {
        // Memberi sedikit delay agar bisa klik saran
        setTimeout(() => {
          productSuggestions.style.display = 'none';
        }, 200);
      });
      
      // Event listener untuk form
      const form = document.getElementById('createDiscountForm');
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validasi sederhana
        const discountName = document.getElementById('discountName').value;
        const discountValue = document.getElementById('discountValue').value;
        
        if (!discountName) {
          alert('Harap masukkan Nama Promosi');
          return;
        }
        
        if (!discountValue) {
          alert('Harap masukkan Besar Diskon');
          return;
        }
        
        if (selectedProducts.length === 0) {
          alert('Harap pilih setidaknya satu produk');
          return;
        }
        
        if (!document.getElementById('startDate').value || !document.getElementById('endDate').value) {
          alert('Harap atur periode promosi');
          return;
        }
        
        // Tambahkan produk yang dipilih ke form sebagai input tersembunyi
        selectedProducts.forEach(product => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'product_ids[]';
          input.value = product.id;
          form.appendChild(input);
        });
        
        // Submit form
        form.submit();
      });
    });
  </script>
@endpush
