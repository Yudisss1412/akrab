<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Manajemen Promosi — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/penjual/manajemen-promosi.css') }}">
  <style>
    /* Gaya tambahan untuk overlay dan modal */
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1040;
      align-items: center;
      justify-content: center;
    }

    .modal-overlay.active {
      display: flex;
    }

    .modal-content-custom {
      background: white;
      border-radius: var(--ak-radius);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      border: 1px solid var(--ak-border);
      max-width: 600px;
      width: 90%;
      max-height: 90vh;
      overflow: hidden;
      transform: scale(0.7);
      opacity: 0;
      transition: transform 0.3s ease, opacity 0.3s ease;
      position: relative;
    }

    .modal-overlay.active .modal-content-custom {
      transform: scale(1);
      opacity: 1;
    }

    .modal-header-custom {
      background: var(--ak-white);
      border-bottom: 1px solid var(--ak-border);
      padding: 1rem 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .modal-title-custom {
      margin: 0;
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--ak-text);
    }

    .modal-close-btn {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--ak-muted);
      padding: 0;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-body-custom {
      padding: 1.5rem;
    }
    
    body {
      background-color: #f8f9fa;
    }
    
    /* Custom styles for action buttons */
    .action-btn {
      transition: all 0.3s ease; /* Smooth transition for all properties */
      border: 1px solid #dee2e6 !important; /* Default border color */
      background-color: #f8f9fa !important; /* Light background */
    }
    
    /* Default icon colors - using higher specificity */
    .action-btn.btn-edit svg,
    .action-btn.btn-deactivate svg {
      color: #198754 !important; /* Bootstrap success green */
      fill: #198754 !important; /* For SVG fill property */
    }
    
    .action-btn.btn-delete svg {
      color: #dc3545 !important; /* Bootstrap danger red */
      fill: #dc3545 !important; /* For SVG fill property */
    }
    
    /* Hover effects - using higher specificity */
    .action-btn.btn-edit:hover,
    .action-btn.btn-deactivate:hover {
      background-color: #198754 !important; /* Bootstrap success green */
      border-color: #198754 !important;
    }
    
    .action-btn.btn-edit:hover svg,
    .action-btn.btn-deactivate:hover svg {
      color: white !important;
      fill: white !important;
    }
    
    .action-btn.btn-delete:hover {
      background-color: #dc3545 !important; /* Bootstrap danger red */
      border-color: #dc3545 !important;
    }
    
    .action-btn.btn-delete:hover svg {
      color: white !important;
      fill: white !important;
    }
    
    /* Ensure icons are properly aligned and sized */
    .action-btn svg {
      width: 16px;
      height: 16px;
    }
    
    /* Special handling for trash SVG which has different dimensions */
    .action-btn.btn-delete svg:not([width="33"]) {
      width: 16px;
      height: 16px;
    }
    
    /* Override any conflicting styles */
    .action-btn svg.bi {
      color: inherit !important;
      fill: inherit !important;
    }
    
    /* Ensure all SVG elements use currentColor for consistent behavior */
    .action-btn svg * {
      fill: currentColor !important;
    }
  </style>
</head>
<body>
  @include('components.admin_penjual.header')
  
  <div class="main-layout pt-5 pb-5">
    <div class="content-wrapper">
      <!-- Header Halaman -->
      <div class="page-header my-5 py-5 mb-5" style="margin-top: 2rem; margin-bottom: 3rem;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <h1 class="page-title mb-0">Manajemen Promosi untuk {{ auth()->user()->name }}</h1>
          <button class="btn btn-primary" id="openPromotionModalBtn">
            <i class="bi bi-plus-lg"></i> Buat Promosi Baru
          </button>
        </div>
      </div>

      <!-- Modal Overlay dan Konten -->
      <div class="modal-overlay" id="promotionModalOverlay">
        <div class="modal-content-custom">
          <div class="modal-header-custom">
            <h5 class="modal-title-custom">Pilih Jenis Promosi</h5>
            <button type="button" class="modal-close-btn" id="closeModalBtn">&times;</button>
          </div>
          <div class="modal-body-custom">
            <div class="promotion-types">
              <div class="promotion-type-card">
                <i class="bi bi-tag-fill text-primary mb-3"></i>
                <h5>Diskon Produk</h5>
                <p class="text-muted">Berikan diskon pada produk tertentu</p>
                <a class="btn btn-outline-primary" href="{{ route('penjual.promosi.diskon') }}">Pilih</a>
              </div>
              <div class="promotion-type-card">
                <i class="bi bi-ticket-perforated text-success mb-3"></i>
                <h5>Voucher Toko</h5>
                <p class="text-muted">Buat voucher untuk pelanggan dengan syarat tertentu</p>
                <a class="btn btn-outline-success" href="{{ route('penjual.promosi.voucher') }}">Pilih</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Navigasi Tab -->
      <div class="tabs-navigation mb-0">
        <div class="tabs-container" id="promotionTabs" role="tablist">
          <button class="tab-button tab-active" id="all-tab" type="button" role="tab" data-tab="all">
            <i class="bi bi-grid-fill"></i>
            Semua Promosi
          </button>
          <button class="tab-button tab-inactive" id="discount-tab" type="button" role="tab" data-tab="discount">
            <i class="bi bi-tag-fill"></i>
            Diskon Produk
          </button>
          <button class="tab-button tab-inactive" id="voucher-tab" type="button" role="tab" data-tab="voucher">
            <i class="bi bi-ticket-perforated-fill"></i>
            Voucher Toko
          </button>
        </div>
      </div>

      <!-- Panel Konten Tab -->
      <div id="promotionTabContent">
        <!-- Tab Semua Promosi -->
        <div class="tab-panel d-none" id="all" role="tabpanel">
          <div class="card">
            <div class="card-body p-0 pb-4">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Nama Promosi</th>
                      <th>Tipe</th>
                      <th>Produk yang Berlaku</th>
                      <th>Periode Berlangsung</th>
                      <th>Status</th>
                      <th class="text-end">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Voucher Promotions -->
                    @foreach($vouchers as $voucher)
                    <tr>
                      <td>{{ $voucher->name }}</td>
                      <td>Voucher</td>
                      <td>-</td>
                      <td>{{ \Carbon\Carbon::parse($voucher->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($voucher->end_date)->format('d M Y') }}</td>
                      <td>
                        <span class="status-badge 
                          @if($voucher->status === 'active') status-completed 
                          @elseif($voucher->status === 'inactive') status-upcoming 
                          @else status-expired @endif">
                          {{ ucfirst($voucher->status) }}
                        </span>
                      </td>
                      <td>
                        <div class="action-buttons d-flex justify-content-end gap-2">
                          <a href="{{ route('penjual.promosi.edit', ['id' => $voucher->id]) }}" class="action-btn btn-edit rounded-circle bg-light p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                              <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                            </svg>
                          </a>
                          <button class="action-btn btn-deactivate rounded-circle bg-light p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Nonaktifkan" onclick="confirmDeactivate({{ $voucher->id }})">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M7.29314 7.99965L3.14614 3.85366C3.09966 3.80717 3.06278 3.75198 3.03762 3.69124C3.01246 3.6305 2.99951 3.5654 2.99951 3.49966C2.99951 3.43391 3.01246 3.36881 3.03762 3.30807C3.06278 3.24733 3.09966 3.19214 3.14614 3.14565C3.19263 3.09917 3.24782 3.06229 3.30856 3.03713C3.3693 3.01197 3.4344 2.99902 3.50014 2.99902C3.56589 2.99902 3.63099 3.01197 3.69173 3.03713C3.75247 3.06229 3.80766 3.09917 3.85414 3.14565L8.00014 7.29265L12.1461 3.14565C12.24 3.05177 12.3674 2.99902 12.5001 2.99902C12.6329 2.99902 12.7603 3.05177 12.8541 3.14565C12.948 3.23954 13.0008 3.36688 13.0008 3.49966C13.0008 3.63243 12.948 3.75977 12.8541 3.85366L8.70714 7.99965L12.8541 12.1457C12.948 12.2395 13.0008 12.3669 13.0008 12.4997C13.0008 12.6324 12.948 12.7598 12.8541 12.8537C12.7603 12.9475 12.6329 13.0003 12.5001 13.0003C12.3674 13.0003 12.24 12.9475 12.1461 12.8537L8.00014 8.70665L3.85414 12.8537C3.76026 12.9475 3.63292 13.0003 3.50014 13.0003C3.36737 13.0003 3.24003 12.9475 3.14614 12.8537C3.05226 12.7598 2.99951 12.6324 2.99951 12.4997C2.99951 12.3669 3.05226 12.2395 3.14614 12.1457L7.29314 7.99965Z" fill="currentColor"/>
                            </svg>
                          </button>
                          <button class="action-btn btn-delete rounded-circle bg-light p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Hapus" onclick="confirmDelete({{ $voucher->id }})">
                            <svg width="33" height="33" viewBox="0 0 33 33" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M5.5 9.625H27.5M13.75 15.125V23.375M19.25 15.125V23.375M6.875 9.625L8.25 26.125C8.25 26.8543 8.53973 27.5538 9.05546 28.0695C9.57118 28.5853 10.2707 28.875 11 28.875H22C22.7293 28.875 23.4288 28.5853 23.9445 28.0695C24.4603 27.5538 24.75 26.8543 24.75 26.125L26.125 9.625M12.375 9.625V5.5C12.375 5.13533 12.5199 4.78559 12.7777 4.52773C13.0356 4.26987 13.3853 4.125 13.75 4.125H19.25C19.6147 4.125 19.9644 4.26987 20.2223 4.52773C20.4801 4.78559 20.625 5.13533 20.625 5.5V9.625" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                          </button>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                    
                    <!-- Product Discount Promotions -->
                    @foreach($productDiscounts as $productPromotion)
                    <tr>
                      <td>{{ $productPromotion->product->name }} - Diskon</td>
                      <td>Diskon Produk</td>
                      <td>{{ $productPromotion->product->name }}</td>
                      <td>{{ \Carbon\Carbon::parse($productPromotion->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($productPromotion->end_date)->format('d M Y') }}</td>
                      <td>
                        <span class="status-badge 
                          @if($productPromotion->status === 'active') status-completed 
                          @elseif($productPromotion->status === 'inactive') status-upcoming 
                          @else status-expired @endif">
                          {{ ucfirst($productPromotion->status) }}
                        </span>
                      </td>
                      <td>
                        <div class="action-buttons d-flex justify-content-end gap-2">
                          <a href="{{ route('penjual.promosi.edit', ['id' => $productPromotion->id]) }}" class="action-btn btn-edit rounded-circle bg-light p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                              <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                            </svg>
                          </a>
                          <button class="action-btn btn-deactivate rounded-circle bg-light p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Nonaktifkan" onclick="confirmDeactivate({{ $productPromotion->id }})">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M7.29314 7.99965L3.14614 3.85366C3.09966 3.80717 3.06278 3.75198 3.03762 3.69124C3.01246 3.6305 2.99951 3.5654 2.99951 3.49966C2.99951 3.43391 3.01246 3.36881 3.03762 3.30807C3.06278 3.24733 3.09966 3.19214 3.14614 3.14565C3.19263 3.09917 3.24782 3.06229 3.30856 3.03713C3.3693 3.01197 3.4344 2.99902 3.50014 2.99902C3.56589 2.99902 3.63099 3.01197 3.69173 3.03713C3.75247 3.06229 3.80766 3.09917 3.85414 3.14565L8.00014 7.29265L12.1461 3.14565C12.24 3.05177 12.3674 2.99902 12.5001 2.99902C12.6329 2.99902 12.7603 3.05177 12.8541 3.14565C12.948 3.23954 13.0008 3.36688 13.0008 3.49966C13.0008 3.63243 12.948 3.75977 12.8541 3.85366L8.70714 7.99965L12.8541 12.1457C12.948 12.2395 13.0008 12.3669 13.0008 12.4997C13.0008 12.6324 12.948 12.7598 12.8541 12.8537C12.7603 12.9475 12.6329 13.0003 12.5001 13.0003C12.3674 13.0003 12.24 12.9475 12.1461 12.8537L8.00014 8.70665L3.85414 12.8537C3.76026 12.9475 3.63292 13.0003 3.50014 13.0003C3.36737 13.0003 3.24003 12.9475 3.14614 12.8537C3.05226 12.7598 2.99951 12.6324 2.99951 12.4997C2.99951 12.3669 3.05226 12.2395 3.14614 12.1457L7.29314 7.99965Z" fill="currentColor"/>
                            </svg>
                          </button>
                          <button class="action-btn btn-delete rounded-circle bg-light p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Hapus" onclick="confirmDelete({{ $productPromotion->id }})">
                            <svg width="33" height="33" viewBox="0 0 33 33" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M5.5 9.625H27.5M13.75 15.125V23.375M19.25 15.125V23.375M6.875 9.625L8.25 26.125C8.25 26.8543 8.53973 27.5538 9.05546 28.0695C9.57118 28.5853 10.2707 28.875 11 28.875H22C22.7293 28.875 23.4288 28.5853 23.9445 28.0695C24.4603 27.5538 24.75 26.8543 24.75 26.125L26.125 9.625M12.375 9.625V5.5C12.375 5.13533 12.5199 4.78559 12.7777 4.52773C13.0356 4.26987 13.3853 4.125 13.75 4.125H19.25C19.6147 4.125 19.9644 4.26987 20.2223 4.52773C20.4801 4.78559 20.625 5.13533 20.625 5.5V9.625" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                          </button>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab Diskon Produk -->
        <div class="tab-panel d-none" id="discount" role="tabpanel">
          <div class="card">
            <div class="card-body p-0 pb-4">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Nama Promosi</th>
                      <th>Tipe</th>
                      <th>Produk yang Berlaku</th>
                      <th>Periode Berlangsung</th>
                      <th>Status</th>
                      <th class="text-end">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($productDiscounts as $productPromotion)
                    <tr>
                      <td>{{ $productPromotion->product->name }} - Diskon</td>
                      <td>Diskon Produk</td>
                      <td>{{ $productPromotion->product->name }}</td>
                      <td>{{ \Carbon\Carbon::parse($productPromotion->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($productPromotion->end_date)->format('d M Y') }}</td>
                      <td>
                        <span class="status-badge 
                          @if($productPromotion->status === 'active') status-completed 
                          @elseif($productPromotion->status === 'inactive') status-upcoming 
                          @else status-expired @endif">
                          {{ ucfirst($productPromotion->status) }}
                        </span>
                      </td>
                      <td>
                        <div class="action-buttons d-flex justify-content-end gap-2">
                          <a href="{{ route('penjual.promosi.edit', ['id' => $productPromotion->id]) }}" class="action-btn btn-edit rounded-circle bg-light p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                              <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                            </svg>
                          </a>
                          <button class="action-btn btn-deactivate rounded-circle bg-light p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Nonaktifkan" onclick="confirmDeactivate({{ $productPromotion->id }})">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M7.29314 7.99965L3.14614 3.85366C3.09966 3.80717 3.06278 3.75198 3.03762 3.69124C3.01246 3.6305 2.99951 3.5654 2.99951 3.49966C2.99951 3.43391 3.01246 3.36881 3.03762 3.30807C3.06278 3.24733 3.09966 3.19214 3.14614 3.14565C3.19263 3.09917 3.24782 3.06229 3.30856 3.03713C3.3693 3.01197 3.4344 2.99902 3.50014 2.99902C3.56589 2.99902 3.63099 3.01197 3.69173 3.03713C3.75247 3.06229 3.80766 3.09917 3.85414 3.14565L8.00014 7.29265L12.1461 3.14565C12.24 3.05177 12.3674 2.99902 12.5001 2.99902C12.6329 2.99902 12.7603 3.05177 12.8541 3.14565C12.948 3.23954 13.0008 3.36688 13.0008 3.49966C13.0008 3.63243 12.948 3.75977 12.8541 3.85366L8.70714 7.99965L12.8541 12.1457C12.948 12.2395 13.0008 12.3669 13.0008 12.4997C13.0008 12.6324 12.948 12.7598 12.8541 12.8537C12.7603 12.9475 12.6329 13.0003 12.5001 13.0003C12.3674 13.0003 12.24 12.9475 12.1461 12.8537L8.00014 8.70665L3.85414 12.8537C3.76026 12.9475 3.63292 13.0003 3.50014 13.0003C3.36737 13.0003 3.24003 12.9475 3.14614 12.8537C3.05226 12.7598 2.99951 12.6324 2.99951 12.4997C2.99951 12.3669 3.05226 12.2395 3.14614 12.1457L7.29314 7.99965Z" fill="currentColor"/>
                            </svg>
                          </button>
                          <button class="action-btn btn-delete rounded-circle bg-light p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Hapus" onclick="confirmDelete({{ $productPromotion->id }})">
                            <svg width="33" height="33" viewBox="0 0 33 33" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M5.5 9.625H27.5M13.75 15.125V23.375M19.25 15.125V23.375M6.875 9.625L8.25 26.125C8.25 26.8543 8.53973 27.5538 9.05546 28.0695C9.57118 28.5853 10.2707 28.875 11 28.875H22C22.7293 28.875 23.4288 28.5853 23.9445 28.0695C24.4603 27.5538 24.75 26.8543 24.75 26.125L26.125 9.625M12.375 9.625V5.5C12.375 5.13533 12.5199 4.78559 12.7777 4.52773C13.0356 4.26987 13.3853 4.125 13.75 4.125H19.25C19.6147 4.125 19.9644 4.26987 20.2223 4.52773C20.4801 4.78559 20.625 5.13533 20.625 5.5V9.625" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                          </button>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab Voucher Toko -->
        <div class="tab-panel d-none" id="voucher" role="tabpanel">
          <div class="card">
            <div class="card-body p-0 pb-4">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Nama / Kode Voucher</th>
                      <th>Tipe</th>
                      <th>Minimum Pembelian</th>
                      <th>Kuota</th>
                      <th>Periode Berlangsung</th>
                      <th>Status</th>
                      <th class="text-end">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($vouchers as $voucher)
                    <tr>
                      <td>{{ $voucher->name }}<br><small class="text-muted">{{ $voucher->code }}</small></td>
                      <td>
                        @if($voucher->type === 'free_shipping') Voucher Pengiriman
                        @elseif($voucher->type === 'percentage') Voucher Diskon Persen
                        @else Voucher Diskon Tetap
                        @endif
                      </td>
                      <td>Rp {{ number_format($voucher->min_order_amount, 0, ',', '.') }}</td>
                      <td>{{ $voucher->used_count }}/{{ $voucher->usage_limit ?: '∞' }}</td>
                      <td>{{ \Carbon\Carbon::parse($voucher->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($voucher->end_date)->format('d M Y') }}</td>
                      <td>
                        <span class="status-badge 
                          @if($voucher->status === 'active') status-completed 
                          @elseif($voucher->status === 'inactive') status-upcoming 
                          @else status-expired @endif">
                          {{ ucfirst($voucher->status) }}
                        </span>
                      </td>
                      <td>
                        <div class="action-buttons d-flex justify-content-end gap-2">
                          <a href="{{ route('penjual.promosi.edit', ['id' => $voucher->id]) }}" class="action-btn btn-edit rounded-circle bg-light p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                              <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                            </svg>
                          </a>
                          <button class="action-btn btn-deactivate rounded-circle bg-light p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Nonaktifkan" onclick="confirmDeactivate({{ $voucher->id }})">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M7.29314 7.99965L3.14614 3.85366C3.09966 3.80717 3.06278 3.75198 3.03762 3.69124C3.01246 3.6305 2.99951 3.5654 2.99951 3.49966C2.99951 3.43391 3.01246 3.36881 3.03762 3.30807C3.06278 3.24733 3.09966 3.19214 3.14614 3.14565C3.19263 3.09917 3.24782 3.06229 3.30856 3.03713C3.3693 3.01197 3.4344 2.99902 3.50014 2.99902C3.56589 2.99902 3.63099 3.01197 3.69173 3.03713C3.75247 3.06229 3.80766 3.09917 3.85414 3.14565L8.00014 7.29265L12.1461 3.14565C12.24 3.05177 12.3674 2.99902 12.5001 2.99902C12.6329 2.99902 12.7603 3.05177 12.8541 3.14565C12.948 3.23954 13.0008 3.36688 13.0008 3.49966C13.0008 3.63243 12.948 3.75977 12.8541 3.85366L8.70714 7.99965L12.8541 12.1457C12.948 12.2395 13.0008 12.3669 13.0008 12.4997C13.0008 12.6324 12.948 12.7598 12.8541 12.8537C12.7603 12.9475 12.6329 13.0003 12.5001 13.0003C12.3674 13.0003 12.24 12.9475 12.1461 12.8537L8.00014 8.70665L3.85414 12.8537C3.76026 12.9475 3.63292 13.0003 3.50014 13.0003C3.36737 13.0003 3.24003 12.9475 3.14614 12.8537C3.05226 12.7598 2.99951 12.6324 2.99951 12.4997C2.99951 12.3669 3.05226 12.2395 3.14614 12.1457L7.29314 7.99965Z" fill="currentColor"/>
                            </svg>
                          </button>
                          <button class="action-btn btn-delete rounded-circle bg-light p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Hapus" onclick="confirmDelete({{ $voucher->id }})">
                            <svg width="33" height="33" viewBox="0 0 33 33" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path d="M5.5 9.625H27.5M13.75 15.125V23.375M19.25 15.125V23.375M6.875 9.625L8.25 26.125C8.25 26.8543 8.53973 27.5538 9.05546 28.0695C9.57118 28.5853 10.2707 28.875 11 28.875H22C22.7293 28.875 23.4288 28.5853 23.9445 28.0695C24.4603 27.5538 24.75 26.8543 24.75 26.125L26.125 9.625M12.375 9.625V5.5C12.375 5.13533 12.5199 4.78559 12.7777 4.52773C13.0356 4.26987 13.3853 4.125 13.75 4.125H19.25C19.6147 4.125 19.9644 4.26987 20.2223 4.52773C20.4801 4.78559 20.625 5.13533 20.625 5.5V9.625" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                          </button>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  </div>
  </div>
  <div class="mt-5 pt-4" style="margin-top: 4rem; padding-top: 2rem;">
  @include('components.admin_penjual.footer')
  
  <script>
    // Fungsionalitas modal dan tab dari awal (from scratch)
    document.addEventListener('DOMContentLoaded', function() {
      // Fungsionalitas Modal
      const modalOverlay = document.getElementById('promotionModalOverlay');
      const openModalBtn = document.getElementById('openPromotionModalBtn');
      const closeModalBtn = document.getElementById('closeModalBtn');
      
      // Fungsi untuk membuka modal
      function openModal() {
        modalOverlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Mencegah scrolling saat modal terbuka
      }
      
      // Fungsi untuk menutup modal
      function closeModal() {
        modalOverlay.classList.remove('active');
        document.body.style.overflow = ''; // Kembalikan scrolling
      }
      
      // Event listener untuk membuka modal
      openModalBtn.addEventListener('click', openModal);
      
      // Event listener untuk menutup modal dengan tombol X
      closeModalBtn.addEventListener('click', closeModal);
      
      // Event listener untuk menutup modal dengan klik di overlay
      modalOverlay.addEventListener('click', function(event) {
        if (event.target === modalOverlay) {
          closeModal();
        }
      });
      
      // Event listener untuk menutup modal dengan tombol Escape
      document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modalOverlay.classList.contains('active')) {
          closeModal();
        }
      });
      
      // Fungsionalitas tab
      const tabButtons = document.querySelectorAll('.tab-button');
      const tabPanels = document.querySelectorAll('.tab-panel');
      
      // Fungsi untuk mengatur tab aktif
      function setActiveTab(tabId) {
        // Reset semua tombol tab
        tabButtons.forEach(button => {
          button.classList.remove('tab-active');
          button.classList.add('tab-inactive');
        });
        
        // Sembunyikan semua panel
        tabPanels.forEach(panel => {
          panel.classList.remove('active');
          panel.classList.add('d-none');
        });
        
        // Aktifkan tombol yang diklik
        const activeButton = document.querySelector(`[data-tab="${tabId}"]`);
        if (activeButton) {
          activeButton.classList.remove('tab-inactive');
          activeButton.classList.add('tab-active');
        }
        
        // Tampilkan panel yang sesuai
        const activePanel = document.getElementById(tabId);
        if (activePanel) {
          activePanel.classList.remove('d-none');
          activePanel.classList.add('active');
        }
        
        // Update URL hash
        history.pushState(null, null, `#${tabId}`);
      }
      
      // Set default tab aktif saat halaman dimuat
      const hash = window.location.hash.replace('#', '');
      if (hash && document.getElementById(hash)) {
        setActiveTab(hash);
      } else {
        setActiveTab('all'); // Default ke 'Semua Promosi'
      }
      
      // Tambahkan event listener ke semua tombol tab
      tabButtons.forEach(button => {
        button.addEventListener('click', function() {
          const tabId = this.getAttribute('data-tab');
          setActiveTab(tabId);
        });
      });
      
      // Fungsi konfirmasi untuk menonaktifkan promosi
      function confirmDeactivate(promosiId) {
        if (confirm('Apakah Anda yakin ingin menonaktifkan promosi ini?')) {
          // Kirim permintaan ke endpoint nonaktifkan
          fetch(`/penjual/promosi/${promosiId}/nonaktifkan`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          .then(response => {
            if (response.ok) {
              window.location.reload();
            } else {
              alert('Gagal menonaktifkan promosi. Silakan coba lagi.');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
          });
        }
      }
      
      // Fungsi konfirmasi untuk menghapus promosi
      function confirmDelete(promosiId) {
        if (confirm('Apakah Anda yakin ingin menghapus promosi ini secara permanen? Data yang dihapus tidak dapat dikembalikan.')) {
          // Kirim permintaan ke endpoint delete
          fetch(`/penjual/promosi/${promosiId}`, {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          .then(response => {
            if (response.ok) {
              window.location.reload();
            } else {
              alert('Gagal menghapus promosi. Silakan coba lagi.');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
          });
        }
      }
    });
  </script>
</body>
</html>
