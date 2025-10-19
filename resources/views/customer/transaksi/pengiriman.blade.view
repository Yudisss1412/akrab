@extends('layouts.app')

@section('title', 'Pengiriman')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/transaksi/pengiriman.css') }}"/>
@endpush

@section('content')
  <main class="pengiriman-page">
    <div class="container">
      <div class="page-header">
        <h1>Pengiriman</h1>
        <div class="progress-steps">
          <div class="step">
            <div class="step-number">1</div>
            <span class="step-label">Alamat</span>
          </div>
          <div class="step active">
            <div class="step-number">2</div>
            <span class="step-label">Pengiriman</span>
          </div>
          <div class="step">
            <div class="step-number">3</div>
            <span class="step-label">Pembayaran</span>
          </div>
        </div>
      </div>

      <div class="pengiriman-content">
        <div class="main-content">
          <!-- Alamat Pengiriman -->
          <section>
            <h2>Alamat Pengiriman</h2>
            @if(isset($order) && $order)
              <div class="shipping-info">
                <div class="info-row">
                  <span class="label">Nama Penerima</span>
                  <span class="value">{{ $order->shipping_address->recipient_name }}</span>
                </div>
                <div class="info-row">
                  <span class="label">Alamat Lengkap</span>
                  <span class="value">{{ $order->shipping_address->full_address }}</span>
                </div>
                <div class="info-row">
                  <span class="label">Kontak</span>
                  <span class="value">{{ $order->shipping_address->phone }}</span>
                </div>
              </div>
            @else
              <p>Informasi pengiriman tidak ditemukan.</p>
            @endif
          </section>

          <!-- Metode Pengiriman -->
          <section>
            <h2>Metode Pengiriman</h2>
            <div class="shipping-options">
              <div class="shipping-option @if(isset($order) && $order->shipping_courier == 'reguler') selected @endif" data-shipping-method="reguler" data-shipping-cost="15000">
                <div class="option-header">
                  <div class="option-info">
                    <h3>Reguler</h3>
                    <p>3-5 hari kerja</p>
                  </div>
                  <div class="option-price">Rp15.000</div>
                </div>
                <p class="option-description">Layanan pengiriman reguler dengan asuransi barang.</p>
              </div>

              <div class="shipping-option @if(isset($order) && $order->shipping_courier == 'express') selected @endif" data-shipping-method="express" data-shipping-cost="25000">
                <div class="option-header">
                  <div class="option-info">
                    <h3>Kilat</h3>
                    <p>1-2 hari kerja</p>
                  </div>
                  <div class="option-price">Rp25.000</div>
                </div>
                <p class="option-description">Layanan pengiriman cepat dengan prioritas pengemasan.</p>
              </div>

              <div class="shipping-option" data-shipping-method="same_day" data-shipping-cost="50000">
                <div class="option-header">
                  <div class="option-info">
                    <h3>Same Day</h3>
                    <p>Hari ini juga</p>
                  </div>
                  <div class="option-price">Rp50.000</div>
                </div>
                <p class="option-description">Pengiriman dalam hari yang sama dengan kurir khusus.</p>
              </div>
            </div>
          </section>
        </div>

        <div class="sidebar">
          <!-- Ringkasan Pembayaran -->
          <section class="payment-summary">
            <h2>Ringkasan Pembayaran</h2>
            @if(isset($order) && $order)
              <div class="summary-details">
                @if($order->items->count() > 0)
                  <div class="summary-row">
                    <span>Subtotal ({{ $order->items->sum('quantity') }} produk)</span>
                    <span class="subtotal-amount">Rp {{ number_format($order->sub_total, 0, ',', '.') }}</span>
                  </div>
                @endif
                <div class="summary-row">
                  <span>Ongkos Kirim</span>
                  <span class="shipping-cost-amount">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                  <span>Asuransi Pengiriman</span>
                  <span>Rp {{ number_format(1500, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row discount">
                  <span>Diskon</span>
                  <span>-Rp {{ number_format(0, 0, ',', '.') }}</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row total">
                  <span>Total</span>
                  <span class="total-amount">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
              </div>
            @else
              <p>Data pesanan tidak ditemukan.</p>
            @endif

            <button class="btn btn-primary btn-lanjut-pembayaran">
              Lanjut ke Pembayaran
            </button>
          </section>
        </div>
      </div>
      
      <!-- Review Section - only show if order is delivered -->
      @if(isset($order) && $order && $order->status === 'delivered')
      <div class="review-section">
        <h2 class="section-title">Beri Ulasan untuk Pesanan Ini</h2>
        <div class="products-to-review">
          @foreach($order->items as $item)
            @php
              // Check if user has already reviewed this product in this order
              $existingReview = \App\Models\Review::where('user_id', auth()->id())
                                                   ->where('product_id', $item->product_id)
                                                   ->where('order_id', $order->id)
                                                   ->first();
            @endphp
            
            @if(!$existingReview)
            <div class="product-review-card">
              <div class="product-info">
                <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : asset('src/placeholder_produk.png') }}" alt="{{ $item->product->name }}">
                <div class="product-details">
                  <h3>{{ $item->product->name }}</h3>
                  <p class="shop-name">Toko: {{ $item->product->seller->name ?? 'Toko Tidak Diketahui' }}</p>
                </div>
              </div>
              <a href="{{ route('ulasan.create', $item->id) }}" class="btn btn-primary">Beri Ulasan</a>
            </div>
            @endif
          @endforeach
        </div>
      </div>
      @endif
    </div>
  </main>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Shipping option selection
      const shippingOptions = document.querySelectorAll('.shipping-option');
      const orderNumber = @json($order->order_number ?? null);
      
      shippingOptions.forEach(option => {
        option.addEventListener('click', function() {
          shippingOptions.forEach(opt => opt.classList.remove('selected'));
          this.classList.add('selected');
          
          // Get the selected shipping method and cost
          const shippingMethod = this.getAttribute('data-shipping-method');
          const shippingCost = parseInt(this.getAttribute('data-shipping-cost'));
          
          // Update the summary with new shipping cost and total
          updateShippingSummary(shippingMethod, shippingCost);
        });
      });

      // Function to update shipping summary via AJAX
      function updateShippingSummary(shippingMethod, shippingCost) {
        if (!orderNumber) {
          console.error('Order number not found');
          return;
        }
        
        // Show loading indicator
        const totalAmountElement = document.querySelector('.total-amount');
        if (totalAmountElement) {
          totalAmountElement.innerHTML = 'Memperbarui...';
        }
        
        // Send AJAX request to update shipping method
        fetch('{{ route("cust.pengiriman.update") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            order_number: orderNumber,
            shipping_method: shippingMethod
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Update the summary with new values
            updateSummaryDisplay(data.order);
          } else {
            console.error('Error updating shipping method:', data.message);
            // Revert to previous state if there was an error
            alert('Terjadi kesalahan: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat memperbarui metode pengiriman');
        });
      }
      
      // Function to update the payment summary display
      function updateSummaryDisplay(order) {
        const subtotalElement = document.querySelector('.subtotal-amount');
        const shippingCostElement = document.querySelector('.shipping-cost-amount');
        const totalElement = document.querySelector('.total-amount');
        
        if (subtotalElement) {
          subtotalElement.textContent = 'Rp ' + formatCurrency(order.sub_total);
        }
        
        if (shippingCostElement) {
          shippingCostElement.textContent = 'Rp ' + formatCurrency(order.shipping_cost);
        }
        
        if (totalElement) {
          totalElement.textContent = 'Rp ' + formatCurrency(order.total_amount);
        }
      }
      
      // Currency formatting function for Indonesian Rupiah format (without currency symbol to avoid duplication)
      // Format: 15.000 (with dots for thousands separator, commas are for decimals)
      function formatCurrency(amount) {
        // Format with thousands separator using dots (.) 
        return new Intl.NumberFormat('id-ID', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }).format(amount).replace('Rp', '').trim();
      }

      // Lanjut ke pembayaran button
      const paymentBtn = document.querySelector('.btn-lanjut-pembayaran');
      if (paymentBtn) {
        paymentBtn.addEventListener('click', function() {
          // Redirect to payment page
          window.location.href = '{{ route("cust.pembayaran") }}';
        });
      }
    });
  </script>
@endpush