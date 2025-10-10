<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Manajemen Pesanan — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
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
    
    .search-box {
      flex: 1;
      min-width: 250px;
      position: relative;
    }
    
    .search-box input {
      width: 100%;
      padding: 0.5rem 0.75rem 0.5rem 2.5rem;
      border: 1px solid var(--ak-border);
      border-radius: var(--ak-radius);
      font-size: 0.875rem;
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
      width: 200px;
    }
    
    .date-filter select, 
    .bulk-action select {
      width: 100%;
      padding: 0.5rem 0.75rem;
      border: 1px solid var(--ak-border);
      border-radius: var(--ak-radius);
      font-size: 0.875rem;
      background-color: var(--ak-white);
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
    }
    
    .pagination-info {
      color: var(--ak-muted);
      font-size: 0.875rem;
    }
    
    .pagination-nav {
      display: flex;
      gap: 0.5rem;
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
      }
      
      .tab-item {
        min-width: 120px;
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
          <h1>Manajemen Pesanan</h1>
        </section>

        <!-- Tab Navigation -->
        <div class="tab-nav">
          <div class="tab-item active" data-status="all">Semua</div>
          <div class="tab-item" data-status="pending_payment">Belum Dibayar <span class="badge">3</span></div>
          <div class="tab-item" data-status="processing">Perlu Diproses <span class="badge">5</span></div>
          <div class="tab-item" data-status="shipping">Sedang Dikirim <span class="badge">2</span></div>
          <div class="tab-item" data-status="completed">Selesai <span class="badge">42</span></div>
          <div class="tab-item" data-status="cancelled">Dibatalkan <span class="badge">1</span></div>
        </div>

        <!-- Control Bar -->
        <div class="control-bar">
          <div class="search-box">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <input type="text" placeholder="Cari berdasarkan ID Pesanan, produk, atau nama pembeli">
          </div>
          <div class="date-filter">
            <select>
              <option>Filter Tanggal</option>
              <option>Hari Ini</option>
              <option>Minggu Ini</option>
              <option>Bulan Ini</option>
              <option>Tahun Ini</option>
              <option>Custom</option>
            </select>
          </div>
          <div class="bulk-action">
            <select>
              <option>Aksi Massal</option>
              <option>Tandai sebagai diproses</option>
              <option>Cetak label</option>
              <option>Batalkan pesanan</option>
            </select>
          </div>
        </div>

        <!-- Orders Container -->
        <section class="orders-container" id="ordersContainer">
          <!-- Order Card 1 - Pending Payment -->
          <div class="order-card" data-status="pending_payment">
            <div class="order-header">
              <div>
                <div class="order-id">#ORD-202301001</div>
                <div class="order-date">15 Okt 2024, 14:30</div>
              </div>
              <div class="customer-name">Budi Santoso</div>
            </div>
            <div class="order-content">
              <div class="product-item">
                <img src="https://placehold.co/50x50" alt="Produk" class="product-thumb">
                <div class="product-info">
                  <div class="product-name">Kaos Polos Premium</div>
                  <div class="product-quantity">Jumlah: 2</div>
              </div>
              </div>
            </div>
            <div class="order-footer">
              <div>
                <span class="status-badge status-pending-payment">Belum Dibayar</span>
              </div>
              <div class="action-buttons">
                <a href="{{ route('orders.show', ['order' => 'ORD-202301001']) }}" class="btn btn-outline">Lihat Detail</a>
                <button class="btn btn-primary">Konfirmasi Pembayaran</button>
              </div>
            </div>
          </div>

          <!-- Order Card 2 - Processing -->
          <div class="order-card" data-status="processing">
            <div class="order-header">
              <div>
                <div class="order-id">#ORD-202301002</div>
                <div class="order-date">16 Okt 2024, 09:15</div>
              </div>
              <div class="customer-name">Siti Aminah</div>
            </div>
            <div class="order-content">
              <div class="product-item">
                <img src="https://placehold.co/50x50" alt="Produk" class="product-thumb">
                <div class="product-info">
                  <div class="product-name">Celana Jeans Premium</div>
                  <div class="product-quantity">Jumlah: 1</div>
                </div>
              </div>
              <div class="product-item">
                <img src="https://placehold.co/50x50" alt="Produk" class="product-thumb">
                <div class="product-info">
                  <div class="product-name">Topi Baseball Trendy</div>
                  <div class="product-quantity">Jumlah: 1</div>
                </div>
              </div>
            </div>
            <div class="order-footer">
              <div>
                <span class="status-badge status-processing">Perlu Diproses</span>
              </div>
              <div class="action-buttons">
                <a href="{{ route('orders.show', ['order' => 'ORD-202301002']) }}" class="btn btn-outline">Lihat Detail</a>
                <button class="btn btn-primary">Proses Pesanan</button>
              </div>
            </div>
          </div>

          <!-- Order Card 3 - Shipping -->
          <div class="order-card" data-status="shipping">
            <div class="order-header">
              <div>
                <div class="order-id">#ORD-202301003</div>
                <div class="order-date">16 Okt 2024, 15:45</div>
              </div>
              <div class="customer-name">Ahmad Fauzi</div>
            </div>
            <div class="order-content">
              <div class="product-item">
                <img src="https://placehold.co/50x50" alt="Produk" class="product-thumb">
                <div class="product-info">
                  <div class="product-name">Sepatu Sneakers Casual</div>
                  <div class="product-quantity">Jumlah: 1</div>
                </div>
              </div>
            </div>
            <div class="order-footer">
              <div>
                <span class="status-badge status-shipping">Sedang Dikirim</span>
              </div>
              <div class="action-buttons">
                <a href="{{ route('orders.show', ['order' => 'ORD-202301003']) }}" class="btn btn-outline">Lihat Detail</a>
                <button class="btn btn-primary">Lacak Pengiriman</button>
              </div>
            </div>
          </div>

          <!-- Order Card 4 - Completed -->
          <div class="order-card" data-status="completed">
            <div class="order-header">
              <div>
                <div class="order-id">#ORD-202301004</div>
                <div class="order-date">17 Okt 2024, 10:30</div>
              </div>
              <div class="customer-name">Dewi Lestari</div>
            </div>
            <div class="order-content">
              <div class="product-item">
                <img src="https://placehold.co/50x50" alt="Produk" class="product-thumb">
                <div class="product-info">
                  <div class="product-name">Jam Tangan Digital</div>
                  <div class="product-quantity">Jumlah: 1</div>
                </div>
              </div>
            </div>
            <div class="order-footer">
              <div>
                <span class="status-badge status-completed">Selesai</span>
              </div>
              <div class="action-buttons">
                <a href="{{ route('orders.show', ['order' => 'ORD-202301004']) }}" class="btn btn-outline">Lihat Detail</a>
              </div>
            </div>
          </div>

          <!-- Order Card 5 - Cancelled -->
          <div class="order-card" data-status="cancelled">
            <div class="order-header">
              <div>
                <div class="order-id">#ORD-202301005</div>
                <div class="order-date">18 Okt 2024, 11:15</div>
              </div>
              <div class="customer-name">Rina Kartika</div>
            </div>
            <div class="order-content">
              <div class="product-item">
                <img src="https://placehold.co/50x50" alt="Produk" class="product-thumb">
                <div class="product-info">
                  <div class="product-name">Dompet Kulit Asli</div>
                  <div class="product-quantity">Jumlah: 1</div>
                </div>
              </div>
            </div>
            <div class="order-footer">
              <div>
                <span class="status-badge status-cancelled">Dibatalkan</span>
              </div>
              <div class="action-buttons">
                <a href="{{ route('orders.show', ['order' => 'ORD-202301005']) }}" class="btn btn-outline">Lihat Detail</a>
              </div>
            </div>
          </div>
        </section>

        <!-- Pagination -->
        <section class="pagination">
          <div class="pagination-info">
            Menampilkan 1-5 dari 50 pesanan
          </div>
          <div class="pagination-nav">
            <button class="pagination-btn">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 19L8 12L15 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
            <button class="pagination-btn active">1</button>
            <button class="pagination-btn">2</button>
            <button class="pagination-btn">3</button>
            <div class="pagination-separator">...</div>
            <button class="pagination-btn">5</button>
            <button class="pagination-btn">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 5L16 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
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
      const orderCards = document.querySelectorAll('.order-card');
      
      tabItems.forEach(tab => {
        tab.addEventListener('click', function() {
          // Remove active class from all tabs
          tabItems.forEach(t => t.classList.remove('active'));
          
          // Add active class to clicked tab
          this.classList.add('active');
          
          // Get the status filter
          const status = this.getAttribute('data-status');
          
          // Filter orders based on status
          orderCards.forEach(card => {
            if (status === 'all') {
              card.style.display = 'block';
            } else {
              const cardStatus = card.getAttribute('data-status');
              if (cardStatus === status) {
                card.style.display = 'block';
              } else {
                card.style.display = 'none';
              }
            }
          });
        });
      });
    });
  </script>
</body>
</html>