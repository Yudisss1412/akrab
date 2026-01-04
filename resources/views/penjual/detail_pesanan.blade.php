<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Detail Pesanan — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/penjual/detail-pesanan.css') }}">
  <link rel="stylesheet" href="{{ asset('css/penjual/detail_pesanan.css') }}">
</head>
<body>
  @include('components.admin_penjual.header')

  <div class="main-layout">
    <div class="content-wrapper">
      <main class="content admin-page-content" role="main">
        <!-- Page Header -->
        <section class="page-header">
          <h1>
            <a href="{{ route('penjual.pesanan') }}" class="btn btn-sm btn-back">
              <i class="fas fa-arrow-left"></i>
            </a>
            Detail Pesanan
          </h1>
        </section>

        <!-- Card 1: Informasi Pesanan -->
        <section class="card">
          <div class="card-header">
            <h2 class="card-title">Informasi Pesanan</h2>
            <span class="status-badge
              @if($order->status === 'pending_payment') status-pending-payment
              @elseif($order->status === 'processing') status-processing
              @elseif($order->status === 'shipping') status-shipping
              @elseif($order->status === 'completed') status-completed
              @elseif($order->status === 'cancelled') status-cancelled
              @else status-pending-payment @endif">
              {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>
          </div>
          <div class="card-content">
            <div class="info-row">
              <div class="info-label">Nomor Pesanan</div>
              <div class="info-value">#{{ $order->order_number }}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Tanggal Pesanan</div>
              <div class="info-value">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</div>
            </div>
          </div>
        </section>

        <!-- Card 2: Informasi Pembeli -->
        <section class="card">
          <div class="card-header">
            <h2 class="card-title">Informasi Pembeli</h2>
          </div>
          <div class="card-content">
            <div class="info-row">
              <div class="info-label">Nama</div>
              <div class="info-value">{{ $order->user->name }}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Email</div>
              <div class="info-value">{{ $order->user->email }}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Nomor Telepon</div>
              <div class="info-value">{{ $order->user->phone ?? 'Tidak disediakan' }}</div>
            </div>
          </div>
        </section>

        <!-- Card 3: Alamat Pengiriman -->
        @if($order->shipping_address)
        <section class="card">
          <div class="card-header">
            <h2 class="card-title">Alamat Pengiriman</h2>
          </div>
          <div class="card-content">
            <div class="info-row">
              <div class="info-label">Nama Penerima</div>
              <div class="info-value">{{ $order->shipping_address->name }}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Telepon</div>
              <div class="info-value">{{ $order->shipping_address->phone }}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Alamat</div>
              <div class="info-value">{{ $order->shipping_address->address }}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Kota</div>
              <div class="info-value">{{ $order->shipping_address->city }}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Provinsi</div>
              <div class="info-value">{{ $order->shipping_address->province }}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Kode Pos</div>
              <div class="info-value">{{ $order->shipping_address->postal_code }}</div>
            </div>
          </div>
        </section>
        @endif

        <!-- Card 4: Produk -->
        <section class="card">
          <div class="card-header">
            <h2 class="card-title">Produk</h2>
          </div>
          <div class="card-content">
            <div class="product-items">
              @foreach($order->items as $item)
              <div class="product-item">
                <img src="{{ $item->product->main_image ? asset('storage/' . $item->product->main_image) : asset('src/placeholder_produk.png') }}" alt="{{ $item->product->name }}" class="product-thumb">
                <div class="product-info">
                  <div class="product-name">{{ $item->product->name }}</div>
                  @if($item->variant)
                  <div class="product-variant">{{ $item->variant->name }} - {{ $item->variant->value }}</div>
                  @endif
                  <div class="product-price">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                  <div class="product-quantity">Jumlah: {{ $item->quantity }}</div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </section>

        <!-- Card 5: Ringkasan Pesanan -->
        <section class="card">
          <div class="card-header">
            <h2 class="card-title">Ringkasan Pesanan</h2>
          </div>
          <div class="card-content">
            <div class="order-summary">
              <div class="summary-row">
                <div class="summary-label">Subtotal</div>
                <div class="summary-value">Rp {{ number_format($order->items->sum('subtotal'), 0, ',', '.') }}</div>
              </div>
              <div class="summary-row">
                <div class="summary-label">Ongkos Kirim</div>
                <div class="summary-value">Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</div>
              </div>
              <div class="summary-row">
                <div class="summary-label">Diskon</div>
                <div class="summary-value">- Rp {{ number_format($order->discount_amount ?? 0, 0, ',', '.') }}</div>
              </div>
              <div class="summary-row summary-total">
                <div class="summary-label">Total</div>
                <div class="summary-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
              </div>
            </div>
          </div>
        </section>

        <!-- Card 6: Informasi Pembayaran -->
        @if($order->payment)
        <section class="card">
          <div class="card-header">
            <h2 class="card-title">Informasi Pembayaran</h2>
          </div>
          <div class="card-content">
            <div class="info-row">
              <div class="info-label">Metode Pembayaran</div>
              <div class="info-value">{{ $order->payment->payment_method ?? 'Tidak ditemukan' }}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Status Pembayaran</div>
              <div class="info-value">{{ ucfirst(str_replace('_', ' ', $order->payment->status ?? 'unknown')) }}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Tanggal Pembayaran</div>
              <div class="info-value">{{ $order->payment->paid_at ? \Carbon\Carbon::parse($order->payment->paid_at)->format('d M Y, H:i') : 'Belum dibayar' }}</div>
            </div>
          </div>
        </section>
        @endif

        <!-- Card 7: Aksi -->
        <section class="card">
          <div class="card-header">
            <h2 class="card-title">Aksi Pesanan</h2>
          </div>
          <div class="card-content">
            <div class="action-buttons">
              @if($order->status === 'shipped' && $order->tracking_number)
                <div class="tracking-info">
                  <p><strong>Nomor Resi:</strong> {{ $order->tracking_number }}</p>
                  @if($order->shipping_courier)
                    <p><strong>Jenis Layanan (Otomatis):</strong> {{ $order->shipping_courier }}</p>
                  @endif
                  @if($order->shipping_carrier)
                    <p><strong>Nama Ekspedisi:</strong> {{ $order->shipping_carrier }}</p>
                  @endif
                </div>
              @else
                <a href="{{ route('penjual.pesanan') }}" class="btn btn-back">Kembali ke Daftar Pesanan</a>
              @endif
              <div class="action-buttons-right">
                @if($order->status === 'pending')
                  <button class="btn btn-primary" onclick="updateOrderStatus({{ $order->id }}, 'confirmed')">Konfirmasi Pembayaran</button>
                @elseif($order->status === 'confirmed')
                  <!-- Button to trigger the modal for entering tracking number -->
                  <button class="btn btn-primary" onclick="showShippingModal({{ $order->id }})">Proses Pengiriman</button>
                @elseif($order->status === 'shipped')
                  @if($order->tracking_number)
                    <button class="btn btn-primary" onclick="console.log('Reloading page...'); location.reload();">Perbarui Status</button>
                  @else
                    <button class="btn btn-primary" onclick="showShippingModal({{ $order->id }})">Masukkan Nomor Resi</button>
                  @endif
                @elseif($order->status === 'delivered')
                  <button class="btn btn-outline" disabled>Selesai</button>
                @elseif($order->status === 'cancelled')
                  <button class="btn btn-outline" disabled>Dibatalkan</button>
                @endif
              </div>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <script>
    // Array to store callback functions
    const confirmCallbacks = [];

    // Function to add a callback and return its index
    function addConfirmCallback(callback) {
      const index = confirmCallbacks.length;
      confirmCallbacks[index] = callback;
      return index;
    }

    // Function to execute a callback by index
    function executeConfirmCallback(index) {
      if (confirmCallbacks[index] && typeof confirmCallbacks[index] === 'function') {
        confirmCallbacks[index]();
        // Clean up to prevent memory leaks
        delete confirmCallbacks[index];
      }
    }

    // Function to update order status
    function updateOrderStatus(orderId, newStatus) {
      if (confirm('Apakah Anda yakin ingin mengubah status pesanan ini?')) {
        fetch(`/penjual/pesanan/${orderId}/status`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            status: newStatus
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(data.message);
            // Refresh page to show updated status
            location.reload();
          } else {
            alert('Gagal mengubah status: ' + (data.message || 'Unknown error'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat mengubah status pesanan');
        });
      }
    }

    // Function to show shipping modal
    function showShippingModal(orderId) {
      // Create modal HTML
      const modalHtml = `
        <div id="shippingModal" class="modal" style="display: block; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; padding: 50px; box-sizing: border-box;">
          <div class="modal-content" style="background: white; padding: 20px; border-radius: 8px; max-width: 500px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
              <h3 style="margin: 0;">Masukkan Informasi Pengiriman</h3>
              <span onclick="closeShippingModal()" style="font-size: 24px; cursor: pointer;">&times;</span>
            </div>
            <div class="modal-body">
              <form id="shippingForm">
                <div class="form-group" style="margin-bottom: 15px;">
                  <label for="trackingNumber" style="display: block; margin-bottom: 5px;">Nomor Resi:</label>
                  <input type="text" id="trackingNumber" name="tracking_number" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                  <label for="shippingCourier" style="display: block; margin-bottom: 5px;">Jenis Layanan (Otomatis):</label>
                  <input type="text" id="shippingCourier" name="shipping_courier" value="{{ $order->shipping_courier }}" readonly style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background-color: #f5f5f5;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                  <label for="shippingCarrier" style="display: block; margin-bottom: 5px;">Nama Ekspedisi:</label>
                  <input type="text" id="shippingCarrier" name="shipping_carrier" placeholder="Contoh: JNE, J&T, SiCepat" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div class="form-actions" style="margin-top: 20px; text-align: right;">
                  <button type="button" onclick="closeShippingModal()" class="btn btn-outline" style="margin-right: 10px;">Batal</button>
                  <button type="submit" class="btn btn-primary">Kirim Barang</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      `;

      // Add modal to body
      document.body.insertAdjacentHTML('beforeend', modalHtml);

      // Add event listener to form
      document.getElementById('shippingForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const trackingNumber = document.getElementById('trackingNumber').value;
        const shippingCourier = document.getElementById('shippingCourier').value;
        const shippingCarrier = document.getElementById('shippingCarrier').value;

        updateShippingStatus(orderId, trackingNumber, shippingCourier, shippingCarrier);
      });
    }

    // Function to close shipping modal
    function closeShippingModal() {
      const modal = document.getElementById('shippingModal');
      if (modal) {
        modal.remove();
      }
    }

    // Function to show confirmation modal
    function showConfirmationModal(title, message, onConfirm, onCancel = null) {
      // Store the confirm and cancel callbacks and get their indices
      const confirmIndex = addConfirmCallback(onConfirm);
      const cancelIndex = onCancel ? addConfirmCallback(onCancel) : null;

      // Create modal HTML with consistent styling
      const modalHtml = `
        <div id="confirmationModal" class="modal" style="display: block; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; padding: 50px; box-sizing: border-box;">
          <div class="modal-content" style="background: white; padding: 20px; border-radius: 8px; max-width: 400px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
              <h3 style="margin: 0; color: var(--ak-primary, #006E5C);">${title}</h3>
              <span onclick="closeConfirmationModal()" style="font-size: 24px; cursor: pointer; color: #666;">&times;</span>
            </div>
            <div class="modal-body" style="margin-bottom: 20px;">
              <p style="margin: 0 0 15px 0;">${message}</p>
            </div>
            <div class="modal-footer" style="text-align: right;">
              <button type="button" onclick="closeConfirmationModal(); ${cancelIndex !== null ? `executeConfirmCallback(${cancelIndex});` : ''}" class="btn btn-outline" style="margin-right: 10px; padding: 8px 16px;">Batal</button>
              <button type="button" onclick="executeConfirmCallback(${confirmIndex}); closeConfirmationModal();" class="btn btn-primary" style="padding: 8px 16px;">Ya, Lanjutkan</button>
            </div>
          </div>
        </div>
      `;

      // Add modal to body
      document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    // Function to close confirmation modal
    function closeConfirmationModal() {
      const modal = document.getElementById('confirmationModal');
      if (modal) {
        modal.remove();
      }
    }

    // Function to update shipping status with tracking number
    function updateShippingStatus(orderId, trackingNumber, shippingCourier, shippingCarrier) {
      // Get CSRF token value using multiple methods to ensure it's found
      let csrfToken = null;

      // Method 1: Try to get from meta tag
      const metaToken = document.querySelector('meta[name="csrf-token"]');
      if (metaToken) {
        csrfToken = metaToken.getAttribute('content');
      }

      // Method 2: If not found in meta tag, try to get from data attribute in body or other element
      if (!csrfToken) {
        const bodyToken = document.body.getAttribute('data-csrf-token');
        if (bodyToken) {
          csrfToken = bodyToken;
        }
      }

      // Method 3: If still not found, try to get from a hidden input
      if (!csrfToken) {
        const inputToken = document.querySelector('input[name="_token"]');
        if (inputToken) {
          csrfToken = inputToken.value;
        }
      }

      // Method 4: If still not found, try to get from window object (if set by Laravel)
      if (!csrfToken && window.Laravel && window.Laravel.csrfToken) {
        csrfToken = window.Laravel.csrfToken;
      }

      if (!csrfToken) {
        alert('CSRF token tidak ditemukan. Harap refresh halaman.');
        return;
      }

      // Show confirmation modal instead of browser confirm
      showConfirmationModal(
        'Konfirmasi Pengiriman',
        'Apakah Anda yakin ingin mengirimkan pesanan ini dengan nomor resi: ' + trackingNumber + '?',
        function() {
          // This function is called when user confirms
          fetch(`/penjual/pesanan/${orderId}/shipping`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
              tracking_number: trackingNumber,
              shipping_courier: shippingCourier,
              shipping_carrier: shippingCarrier
            })
          })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert(data.message);
              closeShippingModal();
              // Refresh page to show updated status
              location.reload();
            } else {
              alert('Gagal mengirimkan pesanan: ' + (data.message || 'Unknown error'));
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirimkan pesanan');
          });
        }
      );
    }
  </script>
</body>
</html>