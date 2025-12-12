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
          <div class="step active">
            <span class="step-number">1</span>
            <span class="step-label">Alamat</span>
          </div>
          <div class="step active">
            <span class="step-number">2</span>
            <span class="step-label">Pengiriman</span>
          </div>
          <div class="step">
            <span class="step-number">3</span>
            <span class="step-label">Pembayaran</span>
          </div>
        </div>
        <!-- Mobile Step Indicator -->
        <div class="mobile-step-indicator">
          <span>Pengiriman • Langkah 2 dari 3</span>
        </div>
      </div>

      <div class="pengiriman-content">
        <div class="pengiriman-left">
          <!-- Alamat Pengiriman -->
          <section class="alamat-section">
            <div class="section-header">
              <h2>Alamat Pengiriman</h2>
              <button type="button" class="btn-edit" id="editAlamatBtn">
                <i class="bi bi-pencil"></i>
              </button>
            </div>

            <div class="alamat-card" id="alamatCard">
              <div class="alamat-content">
                <div class="primary-badge">Utama</div>
                @if(isset($order) && $order && $order->shipping_address)
                  <h3 id="alamatNama">{{ $order->shipping_address->recipient_name }}</h3>
                  <div id="alamatDetail">
                    <span class="alamat-line">{{ $order->shipping_address->full_address }}</span><br />
                    <span class="alamat-line">{{ $order->shipping_address->district }}, {{ $order->shipping_address->city }}</span><br />
                    <span class="alamat-line">{{ $order->shipping_address->province }}</span>
                  </div>
                  <p class="alamat-phone" id="alamatPhone">{{ $order->shipping_address->phone }}</p>
                @else
                  <h3 id="alamatNama">Alamat tidak ditemukan</h3>
                  <div id="alamatDetail">
                    <span class="alamat-line">Alamat pengiriman tidak ditemukan</span>
                  </div>
                  <p class="alamat-phone" id="alamatPhone">-</p>
                @endif
              </div>
            </div>

            <!-- Form untuk alamat pengiriman -->
            <div class="alamat-form" id="alamatFormSection" style="display: none;">
              <div class="form-group field">
                <input type="text" id="recipient_name" name="recipient_name"
                       value="{{ old('recipient_name', $order->shipping_address->recipient_name ?? '') }}" required placeholder=" " />
                <label for="recipient_name">Nama Penerima</label>
              </div>

              <div class="form-group field">
                <input type="tel" id="phone" name="phone"
                       value="{{ old('phone', $order->shipping_address->phone ?? '') }}" required placeholder=" " />
                <label for="phone">Nomor Telepon</label>
              </div>

              <div class="form-group field">
                <input type="text" id="province" name="province"
                       value="{{ old('province', $order->shipping_address->province ?? '') }}" required placeholder=" " />
                <label for="province">Provinsi</label>
              </div>

              <div class="form-group field">
                <input type="text" id="city" name="city"
                       value="{{ old('city', $order->shipping_address->city ?? '') }}" required placeholder=" " />
                <label for="city">Kota/Kabupaten</label>
              </div>

              <div class="form-group field">
                <input type="text" id="district" name="district"
                       value="{{ old('district', $order->shipping_address->district ?? '') }}" required placeholder=" " />
                <label for="district">Kecamatan</label>
              </div>

              <div class="form-group field">
                <input type="text" id="ward" name="ward"
                       value="{{ old('ward', $order->shipping_address->ward ?? '') }}" required placeholder=" " />
                <label for="ward">Kelurahan</label>
              </div>

              <div class="form-group field">
                <textarea id="full_address" name="full_address" rows="3" required placeholder=" ">{{ old('full_address', $order->shipping_address->full_address ?? '') }}</textarea>
                <label for="full_address">Alamat Lengkap</label>
              </div>
            </div>
          </section>

          <!-- Metode Pengiriman -->
          <section class="pengiriman-section">
            <div class="section-header">
              <h2>Metode Pengiriman</h2>
            </div>

            <div class="pengiriman-content">
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
            </div>
          </section>
        </div>

        <div class="pengiriman-right">
          <!-- Ringkasan Belanja -->
          <section class="ringkasan-belanja">
            <div class="section-header">
              <h3>Ringkasan Belanja</h3>
              <button type="button" class="btn-ghost" id="toggleRingkasan">
                <span class="toggle-text">Lihat Rincian</span>
                <i class="bi bi-chevron-down chevron-icon"></i>
              </button>
            </div>

            <div class="ringkasan-content" id="ringkasanDetails">
              @if(isset($order) && $order && $order->items->count() > 0)
                @foreach($order->items as $item)
                  <div class="produk-preview">
                    <img src="{{ asset($item->product->main_image ?? 'src/default-product.png') }}" alt="{{ $item->product->name ?? 'Produk' }}" />
                    <div class="produk-info">
                      <h4>{{ $item->product->name ?? 'Produk tidak ditemukan' }}</h4>
                      <p class="produk-harga">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                    </div>
                    <span class="produk-qty">x{{ $item->quantity }}</span>
                  </div>
                @endforeach
              @else
                <p>Keranjang Anda kosong.</p>
              @endif
            </div>

            <div class="total-section">
              <div class="total-row">
                <span>Total Belanja</span>
                @if(isset($order) && $order)
                  <span>Rp {{ number_format($order->sub_total, 0, ',', '.') }}</span>
                @else
                  <span>Rp 0</span>
                @endif
              </div>

              <a href="{{ route('cust.pembayaran') }}" class="btn btn-primary btn-lanjut-pembayaran">
                Lanjut ke Pembayaran
              </a>
            </div>
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
        // Define order number from PHP
        @php
          $actualOrder = $order ?? $latestOrder ?? null;
        @endphp
        const orderNumber = @json($actualOrder ? $actualOrder->order_number : null);

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

      // Edit alamat functionality
      const editBtn = document.getElementById('editAlamatBtn');
      const alamatCard = document.getElementById('alamatCard');
      const alamatFormSection = document.getElementById('alamatFormSection');

      if (editBtn) {
        // Gunakan state untuk melacak mode tombol
        let isEditMode = false;

        editBtn.addEventListener('click', function() {
          if (!isEditMode) {
            // Mode edit: tampilkan form, sembunyikan card
            alamatCard.style.display = 'none';
            alamatFormSection.style.display = 'block';
            editBtn.innerHTML = '<i class="bi bi-check"></i>';
            isEditMode = true;
          } else {
            // Mode simpan: validasi dan kirim ke server untuk update temporary data
            const recipientName = document.getElementById('recipient_name').value;
            const phone = document.getElementById('phone').value;
            const province = document.getElementById('province').value;
            const city = document.getElementById('city').value;
            const district = document.getElementById('district').value;
            const ward = document.getElementById('ward').value;
            const fullAddress = document.getElementById('full_address').value;

            if (!recipientName || !phone || !province || !city || !district || !ward || !fullAddress) {
              alert('Mohon lengkapi semua field alamat');
              return;
            }

            // Simpan alamat hanya di sisi klien (tidak dikirim ke server)
            // Karena sistem kita hanya untuk sementara di sesi checkout ini
            document.getElementById('alamatNama').textContent = recipientName || 'Nama tidak tersedia';
            document.getElementById('alamatDetail').innerHTML = fullAddress ? fullAddress.replace(/,/g, '<br>') : 'Alamat tidak lengkap';
            document.getElementById('alamatPhone').textContent = phone || 'Nomor telepon tidak tersedia';

            // Kembali ke tampilan card
            alamatCard.style.display = 'block';
            alamatFormSection.style.display = 'none';
            editBtn.innerHTML = '<i class="bi bi-pencil"></i>';
            isEditMode = false;

            // Tampilkan notifikasi sukses
            alert('Alamat berhasil diperbarui untuk checkout saat ini');
          }
        });
      }

      // Toggle ringkasan belanja section
      const toggleBtn = document.getElementById('toggleRingkasan');
      const ringkasanDetails = document.getElementById('ringkasanDetails');
      const toggleText = toggleBtn.querySelector('.toggle-text');
      const chevronIcon = toggleBtn.querySelector('.chevron-icon');

      if (toggleBtn && ringkasanDetails) {
        toggleBtn.addEventListener('click', function() {
          ringkasanDetails.classList.toggle('show');

          if (ringkasanDetails.classList.contains('show')) {
            ringkasanDetails.style.display = 'block';
            toggleText.textContent = 'Sembunyikan Rincian';
            chevronIcon.classList.add('rotated');
          } else {
            ringkasanDetails.style.display = 'none';
            toggleText.textContent = 'Lihat Rincian';
            chevronIcon.classList.remove('rotated');
          }
        });
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