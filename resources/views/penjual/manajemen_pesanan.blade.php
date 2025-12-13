<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Manajemen Pesanan — AKRAB</title>
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
    
    /* Tab Navigation */
    .tab-nav {
      display: flex;
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 0.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
      overflow-x: auto;
    }
    
    .tab-item {
      flex: 1;
      text-align: center;
      padding: 0.75rem 1rem;
      cursor: pointer;
      border-radius: 8px;
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--ak-text);
      white-space: nowrap;
    }
    
    .tab-item.active {
      background: var(--ak-primary);
      color: white;
    }
    
    .tab-item .badge {
      background: var(--ak-primary-light);
      color: var(--ak-primary);
      border-radius: 50%;
      padding: 0.25rem 0.5rem;
      font-size: 0.75rem;
      margin-left: 0.25rem;
    }
    
    .tab-item.active .badge {
      background: rgba(255, 255, 255, 0.3);
      color: white;
    }
    
    /* Control Bar */
    .control-bar {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      background: var(--ak-white);
      padding: 1rem;
      border-radius: var(--ak-radius);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
      flex-wrap: wrap;
    }

    .control-bar form {
      display: flex;
      gap: 1rem;
      width: 100%;
      min-width: 0; /* Allow flex items to shrink below content size */
      flex-wrap: wrap;
    }

    .search-box {
      flex: 1;
      min-width: 150px;
      position: relative;
    }

    .search-box input {
      width: 100%;
      padding: 0.5rem 0.75rem 0.5rem 2.5rem;
      border: 1px solid var(--ak-border);
      border-radius: var(--ak-radius);
      font-size: 0.875rem;
      min-width: 0; /* Allow input to shrink */
    }

    .search-box svg {
      position: absolute;
      left: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      width: 18px;
      height: 18px;
      color: var(--ak-muted);
    }

    .date-filter, .bulk-action {
      min-width: 140px;
      flex: 1;
      min-width: 0; /* Allow flex items to shrink below content size */
    }

    .date-filter select,
    .bulk-action select {
      width: 100%;
      padding: 0.5rem 0.75rem;
      border: 1px solid var(--ak-border);
      border-radius: var(--ak-radius);
      font-size: 0.875rem;
      background-color: var(--ak-white);
      min-width: 0; /* Allow select to shrink */
    }
    
    /* Order Cards */
    .orders-container {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    
    .order-card {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
      overflow: hidden;
    }
    
    .order-header {
      padding: 1rem;
      border-bottom: 1px solid var(--ak-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .order-id {
      font-weight: 600;
      color: var(--ak-primary);
    }
    
    .order-date {
      color: var(--ak-muted);
      font-size: 0.875rem;
    }
    
    .customer-name {
      font-weight: 500;
    }
    
    .order-content {
      padding: 1rem;
      border-bottom: 1px solid var(--ak-border);
    }
    
    .product-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.5rem 0;
    }
    
    .product-thumb {
      width: 50px;
      height: 50px;
      border-radius: 6px;
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
    
    .product-quantity {
      color: var(--ak-muted);
      font-size: 0.875rem;
    }
    
    .order-footer {
      padding: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .order-total {
      font-weight: 600;
      color: var(--ak-primary);
    }
    
    .action-buttons {
      display: flex;
      gap: 0.5rem;
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
    
    /* Pagination */
    .pagination {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1.5rem;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .pagination-info {
      color: var(--ak-muted);
      font-size: 0.875rem;
      flex: 1;
    }

    .pagination-nav {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
      justify-content: center;
    }

    .pagination-btn {
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--ak-border);
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      font-size: 0.875rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .pagination-btn:hover {
      background: var(--ak-primary);
      color: white;
      border-color: var(--ak-primary);
    }

    .pagination-btn.active {
      background: var(--ak-primary);
      color: white;
      border-color: var(--ak-primary);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .control-bar {
        flex-direction: column;
      }

      .date-filter, .bulk-action {
        width: 100%;
      }

      .tab-nav {
        justify-content: flex-start;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      .tab-item {
        min-width: 120px;
        flex: 0 0 auto;
      }

      .search-box {
        min-width: auto;
        width: 100%;
      }

      .pagination {
        flex-direction: column;
        text-align: center;
      }

      .pagination-nav {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding: 0.5rem 0;
        justify-content: flex-start;
      }

      .pagination-btn {
        min-width: 32px;
      }

      /* Ensure any remaining overflow is handled with horizontal scrolling */
      .control-bar {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
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
                  @if($order->status === 'pending_payment') status-pending-payment
                  @elseif($order->status === 'processing') status-processing
                  @elseif($order->status === 'shipping') status-shipping
                  @elseif($order->status === 'completed') status-completed
                  @elseif($order->status === 'cancelled') status-cancelled
                  @else status-pending-payment @endif">
                  {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
              </div>
              <div class="action-buttons">
                <a href="{{ route('penjual.pesanan.show', $order->id) }}" class="btn btn-outline">Lihat Detail</a>
                @if($order->status === 'pending_payment')
                  <button class="btn btn-primary" onclick="updateOrderStatus({{ $order->id }}, 'processing')">Konfirmasi Pembayaran</button>
                @elseif($order->status === 'processing')
                  <button class="btn btn-primary" onclick="updateOrderStatus({{ $order->id }}, 'shipping')">Proses Pesanan</button>
                @elseif($order->status === 'shipping')
                  <button class="btn btn-primary">Lacak Pengiriman</button>
                @endif
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

    // Panggil fungsi update saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
      // Panggil update setelah DOM selesai dimuat
      updateOrderStatusCounts();

      // Refresh jumlah status setiap 30 detik
      setInterval(updateOrderStatusCounts, 30000);
    });
  </script>
</body>
</html>