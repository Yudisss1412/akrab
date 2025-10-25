@extends('layouts.app')

@section('title', 'Buat Voucher Toko Baru')

@section('header')
  @include('components.penjual.header_compact')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/penjual/manajemen-promosi.css') }}">
  <style>
    /* Styling tambahan untuk formulir voucher */
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
    
    .max-discount-group {
      display: none; /* Sembunyikan secara default */
    }
    
    .max-discount-group.show {
      display: block; /* Tampilkan saat tipe voucher persentase */
    }
    
    .input-group {
      display: flex;
      gap: 0.5rem;
    }
    
    .input-group .form-control {
      flex: 1;
    }
    
    .input-group .btn {
      flex-shrink: 0;
    }
  </style>
@endpush

@section('content')
  <main class="create-voucher">
    <div class="container-fluid">
      <!-- Header Halaman -->
      <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <a href="{{ route('penjual.promosi') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
          </a>
          <h1 class="page-title mb-0">Buat Voucher Toko Baru</h1>
        </div>
      </div>
      
      <!-- Formulir Voucher -->
      <form id="createVoucherForm" action="{{ route('penjual.promosi.voucher.store') }}" method="POST">
        @csrf
        <!-- Bagian Informasi Dasar -->
        <div class="form-card">
          <h5 class="card-title mb-3">Informasi Dasar</h5>
          <div class="form-group">
            <label for="voucherName" class="form-label">Nama Voucher</label>
            <input type="text" id="voucherName" name="name" class="form-control" placeholder="Contoh: Voucher Hemat Akhir Tahun">
          </div>
          
          <div class="form-group">
            <label for="voucherCode" class="form-label">Kode Voucher</label>
            <div class="input-group">
              <input type="text" id="voucherCode" name="code" class="form-control" placeholder="Contoh: HEMAT2024">
              <button type="button" class="btn btn-outline-secondary" id="generateCodeBtn">Buat Kode Acak</button>
            </div>
          </div>
        </div>
        
        <!-- Bagian Pengaturan Voucher -->
        <div class="form-card">
          <h5 class="card-title mb-3">Pengaturan Voucher</h5>
          <div class="form-group">
            <label class="form-label">Tipe Voucher</label>
            <div class="radio-group">
              <div class="radio-option">
                <input type="radio" id="typePercentage" name="type" value="percentage" checked>
                <label for="typePercentage">Persentase (%)</label>
              </div>
              <div class="radio-option">
                <input type="radio" id="typeFixed" name="type" value="fixed">
                <label for="typeFixed">Potongan Harga Tetap (Rp)</label>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="discountValue" class="form-label">Besar Diskon/Potongan</label>
            <input type="number" id="discountValue" name="discount_value" class="form-control" placeholder="Masukkan besar diskon">
          </div>
          
          <div class="form-group max-discount-group" id="maxDiscountGroup">
            <label for="maxDiscount" class="form-label">Maksimum Potongan (Rp)</label>
            <input type="number" id="maxDiscount" name="max_discount_amount" class="form-control" placeholder="Masukkan maksimum potongan">
          </div>
        </div>
        
        <!-- Bagian Syarat & Ketentuan -->
        <div class="form-card">
          <h5 class="card-title mb-3">Syarat & Ketentuan</h5>
          <div class="form-group">
            <label for="minPurchase" class="form-label">Minimum Pembelian (Rp)</label>
            <input type="number" id="minPurchase" name="min_order_amount" class="form-control" placeholder="Rp 0">
          </div>
          
          <div class="form-group">
            <label for="quota" class="form-label">Kuota Penggunaan</label>
            <input type="number" id="quota" name="usage_limit" class="form-control" placeholder="Jumlah total voucher yang tersedia">
          </div>
        </div>
        
        <!-- Bagian Periode Berlaku -->
        <div class="form-card">
          <h5 class="card-title mb-3">Periode Berlaku</h5>
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
            <button type="submit" class="btn btn-primary">Simpan Voucher</button>
          </div>
        </div>
      </form>
    </div>
  </main>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Event listener untuk radio button tipe voucher
      const discountTypeRadios = document.querySelectorAll('input[name="type"]');
      const maxDiscountGroup = document.getElementById('maxDiscountGroup');
      
      // Fungsi untuk mengatur tampilan maksimum potongan
      function toggleMaxDiscountField() {
        const selectedType = document.querySelector('input[name="type"]:checked').value;
        if (selectedType === 'percentage') {
          maxDiscountGroup.classList.add('show');
        } else {
          maxDiscountGroup.classList.remove('show');
        }
      }
      
      // Event listener untuk setiap radio button
      discountTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleMaxDiscountField);
      });
      
      // Panggil fungsi untuk menentukan tampilan awal
      toggleMaxDiscountField();
      
      // Fungsi untuk membuat kode voucher acak
      document.getElementById('generateCodeBtn').addEventListener('click', function() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < 8; i++) {
          code += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        document.getElementById('voucherCode').value = code;
      });
      
      // Event listener untuk form
      const form = document.getElementById('createVoucherForm');
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validasi sederhana
        const voucherName = document.getElementById('voucherName').value;
        const voucherCode = document.getElementById('voucherCode').value;
        const discountValue = document.getElementById('discountValue').value;
        
        if (!voucherName) {
          alert('Harap masukkan Nama Voucher');
          return;
        }
        
        if (!voucherCode) {
          alert('Harap masukkan Kode Voucher');
          return;
        }
        
        if (!discountValue) {
          alert('Harap masukkan Besar Diskon/Potongan');
          return;
        }
        
        if (!document.getElementById('startDate').value || !document.getElementById('endDate').value) {
          alert('Harap atur periode voucher');
          return;
        }
        
        // Submit form
        form.submit();
      });
    });
  </script>
@endpush
