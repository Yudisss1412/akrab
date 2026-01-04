<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Manajemen Pesanan — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/penjual/manajemen_pesanan.css') }}">
</head>
<body>
  @include('components.admin_penjual.header')

  <div class="main-layout">
    <div class="content-wrapper">
      <main class="content admin-page-content" role="main">
        <!-- Page Header -->
        <section class="page-header">
          <h1>
            Manajemen Pesanan untuk {{ auth()->user()->name }}
          </h1>
        </section>

        <!-- Tab Navigation -->
        <div class="tab-nav">
          <div class="tab-item active" data-status="all">Semua</div>
          <div class="tab-item" data-status="pending_payment">Belum Dibayar <span class="badge">{{ $statusData['pending_payment'] ?? 0 }}</span></div>
          <div class="tab-item" data-status="processing">Perlu Diproses <span class="badge">{{ $statusData['processing'] ?? 0 }}</span></div>
          <div class="tab-item" data-status="shipping">Sedang Dikirim <span class="badge">{{ $statusData['shipping'] ?? 0 }}</span></div>
          <div class="tab-item" data-status="completed">Selesai <span class="badge">{{ $statusData['completed'] ?? 0 }}</span></div>
          <div class="tab-item" data-status="cancelled">Dibatalkan <span class="badge">{{ $statusData['cancelled'] ?? 0 }}</span></div>
        </div>

        <!-- Control Bar -->
        <div class="control-bar">
          <form method="GET" action="{{ route('penjual.pesanan') }}" id="filterForm" style="display: flex; gap: 1rem; width: 100%;">
            <div class="search-box" style="flex: 1;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <input type="text" name="search" placeholder="Cari pesanan..." value="{{ request('search') }}">
            </div>
            <div class="date-filter" style="min-width: 200px;">
              <select name="date_filter" onchange="document.getElementById('filterForm').submit();">
                <option value="">Filter Tanggal</option>
                <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="this_week" {{ request('date_filter') == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="this_month" {{ request('date_filter') == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="this_year" {{ request('date_filter') == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
              </select>
            </div>
            <div class="bulk-action" style="min-width: 200px;">
              <select name="status" onchange="document.getElementById('filterForm').submit();">
                <option value="">Semua Status</option>
                <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>Belum Dibayar</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Perlu Diproses</option>
                <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Sedang Dikirim</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
              </select>
            </div>
          </form>
        </div>

        <!-- Orders Container -->
        <section class="orders-container" id="ordersContainer">
          @forelse($orders as $order)
          <div class="order-card" id="order-{{ $order->id }}" data-status="{{ $order->status }}">
            <div class="order-header">
              <div>
                <div class="order-id">#{{ $order->order_number }}</div>
                <div class="order-date">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</div>
              </div>
              <div class="customer-name">{{ $order->user->name ?? 'Customer' }}</div>
            </div>
            <div class="order-content">
              @foreach($order->items as $item)
              <div class="product-item">
                <img src="{{ $item->product->main_image ? asset('storage/' . $item->product->main_image) : asset('src/placeholder_produk.png') }}" alt="{{ $item->product->name }}" class="product-thumb">
                <div class="product-info">
                  <div class="product-name">{{ $item->product->name }}</div>
                  <div class="product-quantity">Jumlah: {{ $item->quantity }}</div>
                </div>
              </div>
              @endforeach
            </div>
            <div class="order-footer">
              <div>
                <span class="status-badge
                  @if($order->status === 'pending') status-pending-payment
                  @elseif($order->status === 'confirmed') status-processing
                  @elseif($order->status === 'shipped') status-shipping
                  @elseif($order->status === 'delivered') status-completed
                  @elseif($order->status === 'cancelled') status-cancelled
                  @else status-pending-payment @endif">
                  {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
              </div>
              <div class="action-buttons">
                <a href="{{ route('penjual.pesanan.show', $order->id) }}" class="btn btn-outline">Lihat Detail</a>
                @if($order->status === 'shipped' && $order->tracking_number)
                  <div class="tracking-info">
                    <p><small><strong>Resi:</strong> {{ $order->tracking_number }}</small></p>
                  </div>
                @else
                  <a href="{{ route('penjual.pesanan') }}" class="btn btn-back">Kembali</a>
                @endif
                <div class="action-buttons-right">
                  @if($order->status === 'pending')
                    <button class="btn btn-primary" onclick="updateOrderStatus({{ $order->id }}, 'confirmed')">Konfirmasi Pembayaran</button>
                  @elseif($order->status === 'confirmed')
                    <button class="btn btn-primary" onclick="showShippingModal({{ $order->id }})">Proses Pengiriman</button>
                  @elseif($order->status === 'shipped')
                    @if($order->tracking_number)
                      <button class="btn btn-primary" onclick="console.log('Reloading page...'); location.reload();">Perbarui</button>
                    @else
                      <button class="btn btn-primary" onclick="showShippingModal({{ $order->id }})">Masukkan Resi</button>
                    @endif
                  @endif
                </div>
              </div>
            </div>
          </div>
          @empty
          <div class="text-center p-4">
            <p>Tidak ada pesanan ditemukan</p>
          </div>
          @endforelse
        </section>

        <!-- Pagination -->
        <section class="pagination">
          <div class="pagination-info">
            Menampilkan {{ $orders->firstItem() ?? 0 }}-{{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} pesanan
          </div>
          <div class="pagination-nav">
            @if ($orders->onFirstPage())
              <button class="pagination-btn" disabled>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M15 19L8 12L15 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            @else
              <a href="{{ $orders->previousPageUrl() }}" class="pagination-btn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M15 19L8 12L15 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </a>
            @endif
            
            @for ($i = 1; $i <= $orders->lastPage(); $i++)
              <a href="{{ $orders->url($i) }}" class="pagination-btn {{ $i == $orders->currentPage() ? 'active' : '' }}">{{ $i }}</a>
            @endfor
            
            @if ($orders->hasMorePages())
              <a href="{{ $orders->nextPageUrl() }}" class="pagination-btn">
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
  
  <script>
    // Tab navigation functionality
    document.addEventListener('DOMContentLoaded', function() {
      const tabItems = document.querySelectorAll('.tab-item');

      // Function to handle tab click - this will navigate to the appropriate URL
      function handleTabClick(tabElement) {
        // Remove active class from all tabs
        tabItems.forEach(t => t.classList.remove('active'));

        // Add active class to clicked tab
        tabElement.classList.add('active');

        // Get the status filter
        const status = tabElement.getAttribute('data-status');

        // Navigate to the appropriate URL based on the tab clicked
        let newUrl;
        if (status === 'all') {
          // Remove status parameter to show all orders
          const currentUrl = new URL(window.location);
          currentUrl.searchParams.delete('status');
          newUrl = currentUrl.toString();
        } else {
          // Add status parameter to filter orders
          const currentUrl = new URL(window.location);
          currentUrl.searchParams.set('status', status);
          newUrl = currentUrl.toString();
        }

        // Navigate to the new URL
        window.location.href = newUrl;
      }

      tabItems.forEach(tab => {
        tab.addEventListener('click', function() {
          handleTabClick(this);
        });
      });

      // Check URL parameters to activate the correct tab on page load
      const urlParams = new URLSearchParams(window.location.search);
      const urlStatus = urlParams.get('status');

      if (urlStatus) {
        const targetTab = document.querySelector(`.tab-item[data-status="${urlStatus}"]`);
        if (targetTab) {
          // Update tab to be active
          tabItems.forEach(t => t.classList.remove('active'));
          targetTab.classList.add('active');
        }
      }
    });

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
            // Update status badge in the UI without page reload
            const statusBadge = document.querySelector(`#order-${orderId} .status-badge`);
            if (statusBadge) {
              statusBadge.textContent = getStatusText(data.order.status);
              statusBadge.className = 'status-badge ' + getStatusClass(data.order.status);
            }

            // Update status in dropdown if present
            const statusSelect = document.querySelector(`#order-${orderId} .status-select`);
            if (statusSelect) {
              statusSelect.value = data.order.status;
            }

            // Update timestamp if present
            const updatedAtElement = document.querySelector(`#order-${orderId} .updated-at`);
            if (updatedAtElement) {
              updatedAtElement.textContent = new Date().toLocaleString('id-ID');
            }

            // Refresh status counts to update the tab badges
            updateOrderStatusCounts();

            alert(data.message);
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

    // Helper functions to convert status to readable text and appropriate CSS class
    function getStatusText(status) {
      const statusMap = {
        'pending_payment': 'Menunggu Pembayaran',
        'processing': 'Diproses',
        'shipping': 'Dikirim',
        'completed': 'Selesai',
        'cancelled': 'Dibatalkan'
      };
      return statusMap[status] || status;
    }

    function getStatusClass(status) {
      const classMap = {
        'pending_payment': 'status-badge-pending',
        'processing': 'status-badge-processing',
        'shipping': 'status-badge-shipping',
        'completed': 'status-badge-completed',
        'cancelled': 'status-badge-cancelled'
      };
      return classMap[status] || 'status-badge-default';
    }

    // Fungsi untuk mengupdate jumlah status pesanan secara dinamis
    function updateOrderStatusCounts() {
      const csrfToken = document.querySelector('meta[name="csrf-token"]');
      const csrfTokenValue = csrfToken ? csrfToken.getAttribute('content') : '';

      fetch('{{ route('penjual.order.status.counts') }}', {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfTokenValue
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          console.log('Updating order status counts:', data.status_counts);
          // Update badge untuk setiap status
          for (const [status, count] of Object.entries(data.status_counts)) {
            const badgeElement = document.querySelector(`.tab-item[data-status="${status}"] .badge`);
            if (badgeElement) {
              console.log(`Updating ${status} badge to ${count}`);
              badgeElement.textContent = count;
            } else {
              console.log(`Badge element not found for status: ${status}`);
            }
          }
        } else {
          console.error('API call failed:', data.message);
        }
      })
      .catch(error => {
        console.error('Error fetching order status counts:', error);
      });
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

    // Define a namespace object to avoid conflicts
    const ShippingModalNS = {
      // Array to store callback functions
      confirmCallbacks: [],

      // Function to add a callback and return its index
      addConfirmCallback: function(callback) {
        const index = this.confirmCallbacks.length;
        this.confirmCallbacks[index] = callback;
        return index;
      },

      // Function to execute a callback by index
      executeConfirmCallback: function(index) {
        if (this.confirmCallbacks[index] && typeof this.confirmCallbacks[index] === 'function') {
          this.confirmCallbacks[index]();
          // Clean up to prevent memory leaks
          delete this.confirmCallbacks[index];
        }
      },

      // Function to close shipping modal
      closeShippingModal: function() {
        const modal = document.getElementById('shippingModal');
        if (modal) {
          modal.remove();
        }
      },

      // Function to show confirmation modal
      showConfirmationModal: function(title, message, onConfirm, onCancel = null) {
        // Store the confirm and cancel callbacks and get their indices
        const confirmIndex = this.addConfirmCallback(onConfirm);
        const cancelIndex = onCancel ? this.addConfirmCallback(onCancel) : null;

        // Create modal HTML with consistent styling
        const modalHtml = `
          <div id="confirmationModal" class="modal" style="display: block; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; padding: 50px; box-sizing: border-box;">
            <div class="modal-content" style="background: white; padding: 20px; border-radius: 8px; max-width: 400px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
              <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0; color: var(--ak-primary, #006E5C);">${title}</h3>
                <span onclick="ShippingModalNS.closeConfirmationModal()" style="font-size: 24px; cursor: pointer; color: #666;">&times;</span>
              </div>
              <div class="modal-body" style="margin-bottom: 20px;">
                <p style="margin: 0 0 15px 0;">${message}</p>
              </div>
              <div class="modal-footer" style="text-align: right;">
                <button type="button" onclick="ShippingModalNS.closeConfirmationModal(); ${cancelIndex !== null ? `ShippingModalNS.executeConfirmCallback(${cancelIndex});` : ''}" class="btn btn-outline" style="margin-right: 10px; padding: 8px 16px;">Batal</button>
                <button type="button" onclick="ShippingModalNS.executeConfirmCallback(${confirmIndex}); ShippingModalNS.closeConfirmationModal();" class="btn btn-primary" style="padding: 8px 16px;">Ya, Lanjutkan</button>
              </div>
            </div>
          </div>
        `;

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
      },

      // Function to close confirmation modal
      closeConfirmationModal: function() {
        const modal = document.getElementById('confirmationModal');
        if (modal) {
          modal.remove();
        }
      },

      // Function to update shipping status with tracking number
      updateShippingStatus: function(orderId, trackingNumber, shippingCourier, shippingCarrier) {
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
        this.showConfirmationModal(
          'Konfirmasi Pengiriman',
          'Apakah Anda yakin ingin mengirimkan pesanan ini dengan nomor resi: ' + trackingNumber + '?',
          function() {
            // This function is called when user confirms
            // Prepare the data to send
            const requestData = {
              tracking_number: trackingNumber,
            };

            // Only add shipping_carrier if it has a value
            if (shippingCarrier.trim() !== '') {
              requestData.shipping_carrier = shippingCarrier;
            }

            // Only add shipping_courier if it has a value
            if (shippingCourier.trim() !== '' && shippingCourier !== 'akan diisi otomatis') {
              requestData.shipping_courier = shippingCourier;
            }

            fetch(`/penjual/pesanan/${orderId}/shipping`, {
              method: 'PUT',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
              },
              body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                alert(data.message);
                ShippingModalNS.closeShippingModal();
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
    };

    // Override the existing showShippingModal function to ensure it has 3 fields
    function showShippingModal(orderId) {
      // Create modal HTML with 3 fields
      const modalHtml = `
        <div id="shippingModal" class="modal" style="display: block; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; padding: 50px; box-sizing: border-box;">
          <div class="modal-content" style="background: white; padding: 20px; border-radius: 8px; max-width: 500px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
              <h3 style="margin: 0;">Masukkan Informasi Pengiriman</h3>
              <span onclick="ShippingModalNS.closeShippingModal()" style="font-size: 24px; cursor: pointer;">&times;</span>
            </div>
            <div class="modal-body">
              <form id="shippingForm">
                <div class="form-group" style="margin-bottom: 15px;">
                  <label for="trackingNumber" style="display: block; margin-bottom: 5px;">Nomor Resi:</label>
                  <input type="text" id="trackingNumber" name="tracking_number" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                  <label for="shippingCourier" style="display: block; margin-bottom: 5px;">Jenis Layanan (Otomatis):</label>
                  <input type="text" id="shippingCourier" name="shipping_courier" readonly style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background-color: #f5f5f5;" value="akan diisi otomatis" title="Nilai ini akan diisi otomatis dari pilihan customer saat checkout">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                  <label for="shippingCarrier" style="display: block; margin-bottom: 5px;">Nama Ekspedisi:</label>
                  <input type="text" id="shippingCarrier" name="shipping_carrier" placeholder="Contoh: JNE, J&T, SiCepat" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>
                <div class="form-actions" style="margin-top: 20px; text-align: right;">
                  <button type="button" onclick="ShippingModalNS.closeShippingModal()" class="btn btn-outline" style="margin-right: 10px;">Batal</button>
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
        // For shipping_courier, we'll send an empty value and let the backend handle it
        // by retrieving the original value from the database
        const shippingCourier = ''; // This will be handled by backend
        const shippingCarrier = document.getElementById('shippingCarrier').value;

        ShippingModalNS.updateShippingStatus(orderId, trackingNumber, shippingCourier, shippingCarrier);
      });
    }

    // Panggil fungsi update saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
      // Panggil update setelah DOM selesai dimuat
      if (typeof updateOrderStatusCounts === 'function') {
        updateOrderStatusCounts();
      }

      // Refresh jumlah status setiap 30 detik
      setInterval(function() {
        if (typeof updateOrderStatusCounts === 'function') {
          updateOrderStatusCounts();
        }
      }, 30000);
    });
  </script>
</body>
</html>