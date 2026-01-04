<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Riwayat Penjualan — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/penjual/riwayat_penjualan.css') }}">
</head>
<body>
  @include('components.admin_penjual.header')

  <div class="main-layout">
    <div class="content-wrapper">
      <main class="content admin-page-content" role="main">
        <!-- Page Header -->
        <section class="page-header">
          <h1>
            Riwayat Penjualan untuk {{ auth()->user()->name }}
          </h1>
        </section>

        <!-- Sales Stats -->
        <div class="sales-stats">
          <div class="stat-card">
            <div class="stat-label">Total Penjualan</div>
            <div class="stat-value">{{ number_format($totalSales, 0, ',', '.') }}</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value">{{ $totalTransactions }}</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Pendapatan Bulan Ini</div>
            <div class="stat-value">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Rata-rata Per Transaksi</div>
            <div class="stat-value">Rp {{ number_format($avgPerTransaction, 0, ',', '.') }}</div>
          </div>
        </div>

        <!-- Control Bar -->
        <div class="control-bar">
          <form method="GET" action="{{ route('penjual.riwayat.penjualan') }}" id="filterForm" style="display: flex; flex-wrap: wrap; gap: 1rem; width: 100%; align-items: center;">
            <div class="search-box" style="flex: 1; min-width: 200px;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 10 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <input type="text" name="search" placeholder="Cari riwayat penjualan..." value="{{ request('search') }}">
            </div>
            <div class="date-filter" style="min-width: 180px;">
              <select name="date_filter" onchange="document.getElementById('filterForm').submit();">
                <option value="">Filter Tanggal</option>
                <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="this_week" {{ request('date_filter') == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="this_month" {{ request('date_filter') == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="this_year" {{ request('date_filter') == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
              </select>
            </div>
          </form>
        </div>

        <!-- Sales Container -->
        <section class="sales-container" id="salesContainer">
          @forelse($completedOrders as $order)
          <div class="sale-card">
            <div class="sale-header">
              <div>
                <div class="sale-id">#{{ $order->order_number }}</div>
                <div class="sale-date">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</div>
              </div>
              <div class="customer-name">{{ $order->user->name ?? 'Customer' }}</div>
            </div>
            <div class="sale-content">
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
            <div class="sale-footer">
              <div>
                <span class="status-badge status-completed">Selesai</span>
              </div>
              <div class="action-buttons">
                <a href="{{ route('penjual.pesanan.show', $order->id) }}" class="btn btn-outline">Lihat Detail</a>
              </div>
            </div>
          </div>
          @empty
          <!-- Tidak ada riwayat penjualan -->
          <div class="sale-card">
            <div class="sale-content" style="text-align: center; padding: 2rem;">
              <p>Tidak ada riwayat penjualan ditemukan.</p>
            </div>
          </div>
          @endforelse
        </section>

        <!-- Pagination -->
        <section class="pagination">
          <div class="pagination-info">
            Menampilkan {{ $completedOrders->firstItem() ?? 0 }}-{{ $completedOrders->lastItem() ?? 0 }} dari {{ $completedOrders->total() }} penjualan
          </div>
          <div class="pagination-nav">
            @if ($completedOrders->onFirstPage())
              <button class="pagination-btn" disabled>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M15 19L8 12L15 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            @else
              <a href="{{ $completedOrders->previousPageUrl() }}" class="pagination-btn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M15 19L8 12L15 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </a>
            @endif
            
            @for ($i = 1; $i <= $completedOrders->lastPage(); $i++)
              <a href="{{ $completedOrders->url($i) }}" class="pagination-btn {{ $i == $completedOrders->currentPage() ? 'active' : '' }}">{{ $i }}</a>
            @endfor
            
            @if ($completedOrders->hasMorePages())
              <a href="{{ $completedOrders->nextPageUrl() }}" class="pagination-btn">
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
  </script>
</body>
</html>