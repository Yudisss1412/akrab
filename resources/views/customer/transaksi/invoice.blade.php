@extends('layouts.app')

@section('title', 'Invoice')

@section('header')
  @include('components.header_compact')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/transaksi/invoice.css') }}">
@endpush

@section('content')
  <main class="invoice-page">
    <div class="container">
      <header class="invoice-header">
        <div class="brand">
          <img src="{{ asset('src/Logo_UMKM.png') }}" alt="AKRAB" class="logo">
          <div>
            <h1>Invoice</h1>
            <p class="invoice-id">#INV-2023-001</p>
          </div>
        </div>
        <div class="actions">
          <button id="btnPrint" class="btn btn-primary" type="button" aria-label="Cetak invoice">
            Cetak
          </button>
          <a href="{{ route('profil.pembeli') }}" class="btn" aria-label="Kembali ke Profil">Kembali</a>
        </div>
      </header>

      <div class="invoice-content">
        <section class="invoice-details">
          <div class="detail-row">
            <span>Tanggal Pemesanan:</span>
            <span>15 Juni 2023, 14:30</span>
          </div>
          <div class="detail-row">
            <span>Status Pembayaran:</span>
            <span class="status paid">Sudah Dibayar</span>
          </div>
          <div class="detail-row">
            <span>Metode Pembayaran:</span>
            <span>Transfer Bank - BCA</span>
          </div>
        </section>

        <div class="parties">
          <div class="party">
            <h3>Dikirim ke:</h3>
            <p><strong>Andi Prasetyo</strong><br>
            Jl. Merdeka No. 123<br>
            Surabaya, Jawa Timur 60123<br>
            0812-3456-7890</p>
          </div>

          <div class="party">
            <h3>Dikirim dari:</h3>
            <p><strong>Toko Grosir Elektronik</strong><br>
            Jl. Teknologi No. 45<br>
            Jakarta Selatan 12345<br>
            grosir.elektronik@email.com</p>
          </div>
        </div>

        <section class="order-items">
          <h3>Detail Pesanan</h3>
          <div class="items-table">
            <div class="table-header">
              <div>Produk</div>
              <div class="text-right">Harga</div>
              <div class="text-center">Jumlah</div>
              <div class="text-right">Total</div>
            </div>
            <div class="table-body">
              <div class="table-row">
                <div class="item-info">
                  <img src="https://picsum.photos/seed/charger/64/64" alt="Charger" class="item-image">
                  <div>
                    <h4>Fast Charging Adapter 20W USB-C</h4>
                    <p class="item-meta">Putih · Garansi Resmi</p>
                  </div>
                </div>
                <div class="text-right">Rp45.000</div>
                <div class="text-center">1</div>
                <div class="text-right">Rp45.000</div>
              </div>
              <div class="table-row">
                <div class="item-info">
                  <img src="https://picsum.photos/seed/cable/64/64" alt="Cable" class="item-image">
                  <div>
                    <h4>USB-C to Lightning Cable 1m</h4>
                    <p class="item-meta">Hitam · Kompatibel iOS</p>
                  </div>
                </div>
                <div class="text-right">Rp29.000</div>
                <div class="text-center">2</div>
                <div class="text-right">Rp58.000</div>
              </div>
            </div>
          </div>
        </section>

        <section class="payment-summary">
          <div class="summary-row">
            <span>Subtotal (2 produk)</span>
            <span>Rp103.000</span>
          </div>
          <div class="summary-row">
            <span>Ongkos Kirim</span>
            <span>Rp9.000</span>
          </div>
          <div class="summary-row">
            <span>Asuransi Pengiriman</span>
            <span>Rp1.500</span>
          </div>
          <div class="summary-row discount">
            <span>Diskon Voucher</span>
            <span>-Rp5.000</span>
          </div>
          <div class="summary-divider"></div>
          <div class="summary-row total">
            <span>Total Pembayaran</span>
            <span class="total-amount">Rp108.500</span>
          </div>
        </section>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Print button
      const printBtn = document.getElementById('btnPrint');
      if (printBtn) {
        printBtn.addEventListener('click', function() {
          window.print();
        });
      }
    });
  </script>
@endpush