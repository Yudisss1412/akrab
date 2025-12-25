<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Detail Pesanan — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
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

    .tracking-info {
      background: #f8f9fa;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 15px;
      border: 1px solid #e9ecef;
      font-size: 0.9rem;
    }

    .tracking-info p {
      margin: 4px 0;
      line-height: 1.4;
    }

    .tracking-info strong {
      display: inline-block;
      min-width: 100px;
    }

    .action-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }

    .action-buttons .btn {
      margin-bottom: 5px;
      font-size: 0.9rem;
      padding: 8px 12px;
    }

    .action-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }

    .action-buttons .btn-back {
      flex: 0 0 auto;
    }

    .action-buttons-right {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
      flex: 0 0 auto;
      margin-left: auto; /* Mendorong ke kanan */
    }

    .action-buttons .btn-primary,
    .action-buttons .btn-outline {
      flex: 0 0 auto;
    }

    .tracking-info {
      flex: 0 0 auto;
      min-width: 250px; /* Memberi ruang cukup untuk info tracking */
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

    /* Page Header */
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

    /* Card Styles */
    .card {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
      overflow: hidden;
      margin-bottom: 1.5rem;
    }

    .card-header {
      padding: 1rem 1.5rem;
      border-bottom: 1px solid var(--ak-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .card-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--ak-text);
      margin: 0;
    }

    .card-content {
      padding: 1.5rem;
    }

    /* Info Row */
    .info-row {
      display: flex;
      margin-bottom: 0.75rem;
    }

    .info-label {
      width: 150px;
      font-weight: 500;
      color: var(--ak-text);
    }

    .info-value {
      flex: 1;
      color: var(--ak-muted);
    }

    /* Product Items */
    .product-items {
      margin-top: 1rem;
    }

    .product-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem 0;
      border-bottom: 1px solid var(--ak-border);
    }

    .product-item:last-child {
      border-bottom: none;
    }

    .product-thumb {
      width: 80px;
      height: 80px;
      border-radius: var(--ak-radius);
      object-fit: cover;
      background: #f3f4f6;
    }

    .product-info {
      flex: 1;
    }

    .product-name {
      font-weight: 500;
      margin-bottom: 0.25rem;
    }

    .product-variant {
      color: var(--ak-muted);
      font-size: 0.875rem;
      margin-bottom: 0.25rem;
    }

    .product-price {
      color: var(--ak-primary);
      font-weight: 600;
    }

    .product-quantity {
      color: var(--ak-muted);
      font-size: 0.875rem;
    }

    /* Order Summary */
    .order-summary {
      margin-top: 1rem;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0.5rem;
    }

    .summary-label {
      color: var(--ak-muted);
    }

    .summary-value {
      font-weight: 500;
      text-align: right;
    }

    .summary-total {
      border-top: 1px solid var(--ak-border);
      padding-top: 0.5rem;
      margin-top: 0.5rem;
      font-weight: 600;
      font-size: 1.1rem;
    }

    /* Shipping Info */
    .shipping-info {
      margin-top: 1rem;
    }

    /* Status Badge */
    .status-badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 1rem;
      font-size: 0.75rem;
      font-weight: 500;
    }

    .status-pending-payment {
      background: rgba(245, 158, 11, 0.1);
      color: #d97706;
    }

    .status-processing {
      background: rgba(59, 130, 246, 0.1);
      color: #2563eb;
    }

    .status-shipping {
      background: rgba(139, 69, 193, 0.1);
      color: #8b45c1;
    }

    .status-completed {
      background: rgba(34, 197, 94, 0.1);
      color: #16a34a;
    }

    .status-cancelled {
      background: rgba(239, 68, 68, 0.1);
      color: #dc2626;
    }

    /* Action Buttons */
    .action-buttons {
      display: flex;
      gap: 0.5rem;
      margin-top: 1rem;
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

    .btn-outline {
      border: 1px solid var(--ak-primary);
      color: var(--ak-primary);
      background: transparent;
    }

    .btn-outline:hover {
      background: var(--ak-primary);
      color: white;
    }

    .btn-primary {
      background: var(--ak-primary);
      color: white;
    }

    .btn-primary:hover {
      background: #005a4a;
    }

    .btn-back {
      border: 1px solid var(--ak-border);
      color: var(--ak-text);
      background: var(--ak-white);
    }

    .btn-back:hover {
      background: var(--ak-muted);
      color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .info-row {
        flex-direction: column;
      }

      .info-label {
        width: auto;
        margin-bottom: 0.25rem;
      }

      .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }
    }
  </style>
</head>
<body>
  @include('components.admin_penjual.header')

  <div class="main-layout">
    <div class="content-wrapper">
      <main class="content admin-page-content" role="main">
        <!-- Page Header -->
        <section class="page-header">
          <h1>
            <a href="{{ route('penjual.pesanan') }}" class="btn btn-sm" style="margin-right: 1rem; vertical-align: middle; background: var(--ak-primary); color: white; border: none; width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
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
                    <p><strong>Kurir:</strong> {{ $order->shipping_courier }}</p>
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
                    <button class="btn btn-primary" onclick="location.reload()">Perbarui Status</button>
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
                  <label for="shippingCourier" style="display: block; margin-bottom: 5px;">Kurir Pengiriman:</label>
                  <input type="text" id="shippingCourier" name="shipping_courier" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
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

        updateShippingStatus(orderId, trackingNumber, shippingCourier);
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
    function updateShippingStatus(orderId, trackingNumber, shippingCourier) {
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
              shipping_courier: shippingCourier
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