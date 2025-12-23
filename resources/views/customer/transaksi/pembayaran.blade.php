@extends('layouts.app')

@section('title', 'Pembayaran')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/transaksi/pembayaran.css') }}"/>
@endpush

@section('content')
  <main class="pembayaran-page">
    <div class="container">
      <div class="page-header">
        <h1>Pembayaran</h1>
        <div class="progress-steps">
          <div class="step active">
            <span class="step-number">1</span>
            <span class="step-label">Alamat</span>
          </div>
          <div class="step active">
            <span class="step-number">2</span>
            <span class="step-label">Pengiriman</span>
          </div>
          <div class="step active">
            <span class="step-number">3</span>
            <span class="step-label">Pembayaran</span>
          </div>
        </div>
        <!-- Mobile Step Indicator -->
        <div class="mobile-step-indicator">
          <span>Pembayaran • Langkah 3 dari 3</span>
        </div>
      </div>

      <div class="pembayaran-content">
        <div class="main-content-left">
          <!-- Ringkasan Pesanan -->
          <section class="order-summary">
            <h2>Ringkasan Pesanan</h2>
            <div class="order-items">
              @if(isset($order) && $order && $order->items->count() > 0)
                @foreach($order->items as $item)
                  <div class="order-item">
                    <img src="{{ asset($item->product->main_image ?? 'src/default-product.png') }}" alt="{{ $item->product->name ?? 'Produk' }}" class="item-image">
                    <div class="item-details">
                      <h3 class="item-name">{{ $item->product->name ?? 'Produk tidak ditemukan' }}</h3>
                      @if($item->variant)
                        <p class="item-variant">{{ $item->variant->name }}</p>
                      @endif
                      <p class="item-price">Rp{{ number_format($item->unit_price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                    </div>
                    <div class="item-total">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</div>
                  </div>
                @endforeach
              @else
                <p>Detail pesanan tidak ditemukan.</p>
              @endif
            </div>
          </section>

          <!-- Detail Pengiriman -->
          <section class="shipping-details">
            <h2>Detail Pengiriman</h2>
            <div class="shipping-info">
              @if(isset($order) && $order && $order->shipping_address)
                <div class="info-row">
                  <span class="label">Alamat Pengiriman</span>
                  <span class="value">{{ $order->shipping_address->recipient_name }}, {{ $order->shipping_address->full_address }}, {{ $order->shipping_address->phone }}</span>
                </div>
                <div class="info-row">
                  <span class="label">Metode Pengiriman</span>
                  <span class="value">
                    @if($order->shipping_courier == 'reguler') 
                      Reguler (3-5 hari kerja) 
                    @elseif($order->shipping_courier == 'express') 
                      Express (1-2 hari kerja) 
                    @else 
                      {{ ucfirst($order->shipping_courier) }} 
                    @endif
                  </span>
                </div>
              @else
                <p>Informasi pengiriman tidak ditemukan.</p>
              @endif
            </div>
          </section>

          <!-- Metode Pembayaran -->
          <section class="payment-methods">
            <h2>Metode Pembayaran</h2>
            <div class="payment-options">
              <div class="payment-option">
                <input type="radio" id="bankTransfer" name="paymentMethod" value="bank_transfer" checked>
                <label for="bankTransfer" class="option-content">
                  <div class="option-header">
                    <div class="option-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M3 7V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7Z" stroke="#006E5C" stroke-width="2"/>
                        <path d="M3 10H21" stroke="#006E5C" stroke-width="2"/>
                        <path d="M8 14H10" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                    </div>
                    <div class="option-text">
                      <h3>Transfer Bank</h3>
                      <p>Bayar via rekening bank</p>
                    </div>
                  </div>
                </label>
              </div>

              <div class="payment-option">
                <input type="radio" id="eWallet" name="paymentMethod" value="e_wallet">
                <label for="eWallet" class="option-content">
                  <div class="option-header">
                    <div class="option-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="9" stroke="#006E5C" stroke-width="2"/>
                        <path d="M12 8V12L15 14" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                    </div>
                    <div class="option-text">
                      <h3>Dompet Digital</h3>
                      <p>OVO, GoPay, DANA, dll.</p>
                    </div>
                  </div>
                </label>
              </div>

              <div class="payment-option">
                <input type="radio" id="cod" name="paymentMethod" value="cod">
                <label for="cod" class="option-content">
                  <div class="option-header">
                    <div class="option-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#006E5C" stroke-width="2"/>
                        <path d="M12 16V12" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                        <path d="M12 8H12.01" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                    </div>
                    <div class="option-text">
                      <h3>Cash on Delivery</h3>
                      <p>Bayar saat barang diterima</p>
                    </div>
                  </div>
                </label>
              </div>

              <div class="payment-option">
                <input type="radio" id="midtrans" name="paymentMethod" value="midtrans">
                <label for="midtrans" class="option-content">
                  <div class="option-header">
                    <div class="option-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 2C6.477 2 2 6.477 2 12C2 17.523 6.477 22 12 22C17.523 22 22 17.523 22 12C22 6.477 17.523 2 12 2Z" stroke="#006E5C" stroke-width="2"/>
                        <path d="M12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16Z" stroke="#006E5C" stroke-width="2"/>
                        <path d="M12 6V8" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                        <path d="M12 16V18" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                        <path d="M6 12H8" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                        <path d="M16 12H18" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                    </div>
                    <div class="option-text">
                      <h3>Midtrans</h3>
                      <p>Bayar via Midtrans (Kartu Kredit, Debit, e-Wallet, Transfer Bank)</p>
                    </div>
                  </div>
                </label>
              </div>
            </div>
          </section>
        </div>

        <div class="payment-summary-column">
          <!-- Ringkasan Pembayaran -->
          <section class="payment-summary-card">
            <h2>Ringkasan Pembayaran</h2>
            <div class="summary-details">
              @if(isset($order) && $order)
                <div class="summary-row">
                  <span>Subtotal ({{ $order->items->sum('quantity') }} produk)</span>
                  <span>Rp{{ number_format($order->sub_total, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                  <span>Ongkos Kirim</span>
                  <span>Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                  <span>Asuransi Pengiriman</span>
                  <span>Rp1.500</span>
                </div>
                <div class="summary-row discount">
                  <span>Diskon</span>
                  <span>-Rp{{ number_format(0, 0, ',', '.') }}</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row total">
                  <span>Total</span>
                  <span class="total-amount">Rp{{ number_format($order->total_amount + 1500, 0, ',', '.') }}</span>
                </div>
              @else
                <p>Data pembayaran tidak ditemukan.</p>
              @endif
            </div>

            <button class="btn btn-primary btn-proses-pembayaran">
              Proses Pembayaran
            </button>
          </section>
        </div>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Payment method selection
      const paymentOptions = document.querySelectorAll('.payment-option');
      paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
          const radio = this.querySelector('input[type="radio"]');
          radio.checked = true;
        });
      });

      // Proses pembayaran button
      const payBtn = document.querySelector('.btn-proses-pembayaran');
      if (payBtn) {
        payBtn.addEventListener('click', function() {
          // Disable button to prevent double submission
          payBtn.disabled = true;
          payBtn.innerHTML = 'Memproses...';

          // Get selected payment method
          const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked').value;

          // Prepare the data to send
          const formData = new FormData();
          formData.append('payment_method', selectedMethod);
          @if(isset($order) && $order)
            formData.append('order_number', '{{ $order->order_number }}');
          @endif
          formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

          // Make AJAX request to process payment
          fetch('{{ route("payment.process") }}', {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Redirect to appropriate payment confirmation page based on selected method
              const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
              const orderNumber = data.order_number;
              window.location.href = '{{ route("payment.confirmation") }}?order=' + orderNumber + '&method=' + selectedMethod;
            } else {
              // Show error message
              alert(data.message || 'Terjadi kesalahan saat memproses pembayaran.');
              // Re-enable the button
              payBtn.disabled = false;
              payBtn.innerHTML = 'Proses Pembayaran';
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
            // Re-enable the button
            payBtn.disabled = false;
            payBtn.innerHTML = 'Proses Pembayaran';
          });
        });
      }

      // Show sticky checkout bar on mobile
      function showStickyCheckoutBar() {
        const stickyBar = document.querySelector('.sticky-checkout-bar');
        if (stickyBar) {
          // Get window width
          if (window.innerWidth < 768) {
            // Show the sticky bar
            stickyBar.style.display = 'flex';
          } else {
            // Hide the sticky bar on desktop
            stickyBar.style.display = 'none';
          }
        }
      }

      // Call on load
      showStickyCheckoutBar();

      // Call on resize
      window.addEventListener('resize', showStickyCheckoutBar);

      // Add responsive utility for better mobile experience
      function adjustLayoutForMobile() {
        const container = document.querySelector('.pembayaran-content');
        if (window.innerWidth < 768) {
          // On mobile, adjust layout if needed
          container.style.alignItems = 'stretch';
        } else {
          // Reset for desktop
          container.style.alignItems = '';
        }
      }

      // Call initial layout adjustment
      adjustLayoutForMobile();

      // Listen for resize events to adjust layout
      window.addEventListener('resize', adjustLayoutForMobile);
    });
  </script>
@endpush