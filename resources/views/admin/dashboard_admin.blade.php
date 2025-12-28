@extends('layouts.admin')

@section('title', 'Admin Dashboard - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Dashboard Widget Styles */
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-top: 1rem;
    }

    .widget {
      background: var(--white);
      border-radius: var(--ak-radius);
      border: 1px solid var(--ak-border);
      box-shadow: 0 8px 20px rgba(0,0,0,.05);
      overflow: hidden;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .widget:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 25px rgba(0,0,0,.1);
    }

    .widget-header {
      padding: 1rem;
      border-bottom: 1px solid var(--ak-border);
      background: linear-gradient(135deg, #006E5C 0%, #008d73 100%);
      color: white;
    }

    .widget-title {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 600;
    }

    .widget-icon {
      width: 24px;
      height: 24px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .widget-content {
      padding: 1rem;
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease, padding 0.3s ease;
    }

    .widget-content.expanded {
      max-height: 400px;
      padding: 1rem;
      overflow-y: auto;
    }

    /* Specific height for widgets with charts */
    .widget-content.with-chart {
      max-height: 380px;
    }

    .widget-content.with-chart.expanded {
      max-height: 380px;
      overflow-y: auto;
    }

    .metric-card {
      background: var(--bg);
      border-radius: 8px;
      padding: 0.85rem;
      margin-bottom: 0.85rem;
      border-left: 4px solid #006E5C;
      transition: all 0.2s ease;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .metric-title {
      font-size: 0.95rem;
      color: var(--muted);
      margin-bottom: 0.3rem;
      font-weight: 500;
    }

    .metric-value {
      font-size: 1.35rem;
      font-weight: 700;
      color: var(--text);
      line-height: 1.2;
    }

    .shortcut-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 0.75rem;
    }

    .shortcut-btn {
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 1rem;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s ease;
      color: #006E5C; /* Green color */
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .shortcut-btn:hover {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
    }

    .shortcut-icon {
      width: 24px;
      height: 24px;
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .toggle-icon {
      transition: transform 0.3s ease;
    }

    .toggle-icon.rotated {
      transform: rotate(180deg);
    }

    /* Expanded state for widgets */
    .widget.expanded .toggle-icon {
      transform: rotate(180deg);
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 0.75rem;
      margin-top: 1rem;
    }

    .stat-card {
      background: var(--bg);
      border-radius: 8px;
      padding: 0.8rem;
      text-align: center;
      border: 1px solid var(--border);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .stat-value {
      font-size: 1.3rem;
      font-weight: 700;
      color: #006E5C;
      line-height: 1.2;
    }

    .stat-label {
      font-size: 0.85rem;
      color: var(--muted);
      margin-top: 0.25rem;
    }

    .metric-card a {
      text-decoration: none;
      color: inherit;
    }

    .metric-card a {
      text-decoration: none;
      color: inherit;
      display: block;
    }

    .metric-card-container {
      margin-bottom: 0.75rem;
    }

    .metric-link {
      display: block;
      text-decoration: none;
      color: inherit;
    }

    .metric-link:hover .metric-card {
      background-color: var(--bg);
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .time-filter select {
      background: white;
      border: 1px solid var(--border);
      border-radius: 6px;
      padding: 4px 8px;
      font-size: 0.8rem;
    }

    /* Chart container styles */
    .chart-container {
      position: relative;
      height: 180px;
      margin-top: 15px;
    }

    /* Additional styling for campaign trends chart */
    .campaign-trends-container {
      padding: 10px 0;
    }

    .trend-indicator {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 0.8em;
      margin-left: 8px;
    }

    .trend-up {
      background-color: rgba(40, 167, 69, 0.2);
      color: #28a745;
    }

    .trend-down {
      background-color: rgba(220, 53, 69, 0.2);
      color: #dc3545;
    }

    .stat-card.highlight {
      border-left: 3px solid #006E5C;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .activity-item:hover {
      background: #e9ecef !important;
      transition: background 0.3s ease;
    }

    .metric-card:hover {
      background-color: #e9ecef;
      transform: translateY(-2px);
      transition: all 0.2s ease;
    }

    .widget-header.gradient {
      background: linear-gradient(135deg, #006E5C 0%, #008d73 100%);
    }

    /* Responsive widget styles */
    @media (max-width: 768px) {
      .dashboard-grid {
        grid-template-columns: 1fr; /* Mengubah dari grid auto-fit ke single column untuk mobile */
        gap: 1rem;
        padding: 0.5rem;
      }

      .widget {
        margin: 0.5rem 0;
        box-shadow: 0 4px 12px rgba(0,0,0,.08);
      }

      .widget-header {
        padding: 0.75rem;
      }

      .widget-content {
        padding: 0.75rem;
      }

      .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 0.5rem;
        margin-top: 0.75rem;
      }

      .stat-card {
        padding: 0.5rem;
        margin: 0.25rem 0;
      }

      .stat-value {
        font-size: 1rem;
      }

      .stat-label {
        font-size: 0.7rem;
      }

      .metric-card {
        padding: 0.5rem;
        margin-bottom: 0.5rem;
      }

      .metric-title {
        font-size: 0.8rem;
        margin-bottom: 0.1rem;
      }

      .metric-value {
        font-size: 1rem;
      }

      .shortcut-grid {
        grid-template-columns: 1fr; /* Single column for shortcuts on mobile */
        gap: 0.5rem;
      }

      .shortcut-btn {
        padding: 0.5rem;
        font-size: 0.9rem;
      }

      .main-layout,
      .content-wrapper,
      .admin-page-content {
        padding: 0.5rem;
      }
    }

    @media (max-width: 480px) {
      .dashboard-grid {
        gap: 0.75rem;
        padding: 0.25rem;
      }

      .widget-header {
        padding: 0.5rem;
      }

      .widget-content {
        padding: 0.5rem;
      }

      .stats-grid {
        grid-template-columns: repeat(2, 1fr); /* Dua kolom untuk stat card di layar sangat kecil */
        gap: 0.25rem;
      }

      .stat-value {
        font-size: 0.9rem;
      }

      .stat-label {
        font-size: 0.65rem;
      }

      .metric-title {
        font-size: 0.75rem;
      }

      .metric-value {
        font-size: 0.9rem;
      }

      .time-filter select {
        font-size: 0.7rem;
        padding: 2px 4px;
      }
    }

    /* CSS untuk memastikan layout tidak mengganggu navbar dan footer */
    .main-layout {
      width: 100%;
      margin: 0;
    }

    .content-wrapper {
      width: 100%;
      margin: 0;
      padding: 1rem;
    }

    .admin-page-content {
      width: 100%;
      margin: 0;
      padding: 0;
    }
  </style>
@endpush

@section('content')
  @include('components.admin_penjual.header')
  
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content">
        

        <!-- Dashboard Widgets -->
        <div class="dashboard-grid" style="grid-template-columns: repeat(2, 1fr);">
          <!-- Widget 1: Ecosystem Key Metrics -->
          <div class="widget">
            <div class="widget-header">
              <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <h3 class="widget-title">Metrik Utama Ekosistem</h3>
                <div class="time-filter">
                  <select id="timeRange" style="background: white; border: 1px solid var(--border); border-radius: 6px; padding: 4px 8px; font-size: 0.8rem;">
                    <option value="today">Hari Ini</option>
                    <option value="7days" selected>7 Hari Terakhir</option>
                    <option value="month">Bulan Ini</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="widget-content expanded with-chart">
              <div class="metric-card">
                <div class="metric-title">Gross Merchandise Volume (GMV)</div>
                <div class="metric-value">Rp 2,450,780,000</div>
                <!-- Chart.js chart for GMV -->
                <div class="chart-container">
                  <canvas id="gmvChart"></canvas>
                </div>
              </div>

              <div class="metric-card">
                <div class="metric-title">Pendapatan Platform</div>
                <div class="metric-value">Rp 122,539,000</div>
                <!-- Chart.js chart for Revenue -->
                <div class="chart-container">
                  <canvas id="revenueChart"></canvas>
                </div>
              </div>

              <div style="margin-bottom: 1rem;"></div> <!-- Space between metric card and stats grid -->

              <div class="stats-grid">
                <div class="stat-card">
                  <div class="stat-value">2,340</div>
                  <div class="stat-label">Penjual Aktif</div>
                </div>
                <div class="stat-card">
                  <div class="stat-value">187</div>
                  <div class="stat-label">Penjual Baru</div>
                </div>
                <div class="stat-card">
                  <div class="stat-value">42</div>
                  <div class="stat-label">Penjual Ditangguhkan</div>
                </div>
                <div class="stat-card">
                  <div class="stat-value">24</div>
                  <div class="stat-label">Menunggu Verifikasi</div>
                </div>
                <div class="stat-card">
                  <div class="stat-value">8,950</div>
                  <div class="stat-label">Pembeli Aktif</div>
                </div>
                <div class="stat-card">
                  <div class="stat-value">3,240</div>
                  <div class="stat-label">Total Transaksi</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Widget 2: Real-Time Monitoring -->
          <div class="widget">
            <div class="widget-header">
              <h3 class="widget-title">Pemantauan Real-Time</h3>
            </div>
            <div class="widget-content expanded with-chart">
              <div class="stats-grid">
                <div class="stat-card">
                  <div class="stat-value" id="activeUsersCount">0</div>
                  <div class="stat-label">Pengguna Aktif</div>
                </div>
                <div class="stat-card">
                  <div class="stat-value" id="newOrdersCount">0</div>
                  <div class="stat-label">Pesanan Baru</div>
                </div>
                <div class="stat-card">
                  <div class="stat-value" id="pendingPaymentsCount">0</div>
                  <div class="stat-label">Pembayaran Tertunda</div>
                </div>
              </div>

              <div style="margin-top: 1rem;"></div> <!-- Space between stats grid and metric card -->

              <div class="metric-card">
                <div class="metric-title">Aktivitas Terbaru</div>
                <div id="recentActivitiesList" style="margin-top: 10px;">
                  <!-- Recent activities will be populated by JavaScript -->
                </div>
              </div>
            </div>
          </div>

          <!-- Widget 3: Quick Shortcuts -->
          <div class="widget">
            <div class="widget-header">
              <h3 class="widget-title">Pintasan Cepat</h3>
            </div>
            <div class="widget-content expanded">
              <div class="shortcut-grid">
                <a href="{{ route('sellers.index') }}" class="shortcut-btn">
                  <div class="shortcut-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M20 21V19C20 16.24 16.42 14 12 14C7.58 14 4 16.24 4 19V21" stroke="currentColor" stroke-width="2"/>
                      <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                    </svg>
                  </div>
                  <div>Manajemen Akun</div>
                </a>

                <a href="{{ route('admin.produk.index') }}" class="shortcut-btn">
                  <div class="shortcut-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M5 21C4.44772 21 4 20.5523 4 20V4C4 3.44772 4.44772 3 5 3H19C19.5523 3 20 3.44772 20 4V20C20 20.5523 19.5523 21 19 21H5Z" stroke="currentColor" stroke-width="2"/>
                      <path d="M4 8H20" stroke="currentColor" stroke-width="2"/>
                      <path d="M9 13H15" stroke="currentColor" stroke-width="2"/>
                    </svg>
                  </div>
                  <div>Manajemen Produk</div>
                </a>


              </div>
            </div>
          </div>

          <!-- Widget 5: Management & Moderation Feed -->
          <div class="widget">
            <div class="widget-header">
              <h3 class="widget-title">Manajemen & Moderation</h3>
            </div>
            <div class="widget-content expanded">
              <div class="metric-card-container">
                <a href="{{ route('reports.violations') }}" class="metric-link">
                  <div class="metric-card">
                    <div class="metric-title">Laporan Pelanggaran Penjual</div>
                    <div class="metric-value">24</div>
                  </div>
                </a>
              </div>

              <div class="metric-card-container">
                <a href="{{ route('support.tickets') }}" class="metric-link">
                  <div class="metric-card">
                    <div class="metric-title">Tiket Bantuan (Support Tickets)</div>
                    <div class="metric-value">42</div>
                  </div>
                </a>
              </div>

              <div class="metric-card-container">
                <a href="{{ route('withdrawal.requests') }}" class="metric-link">
                  <div class="metric-card">
                    <div class="metric-title">Permintaan Penarikan Dana</div>
                    <div class="metric-value">18</div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <script>
    // Time filter functionality
    document.getElementById('timeRange').addEventListener('change', function() {
      const selectedRange = this.value;
      
      // Update metrics based on selected time range
      updateMetrics(selectedRange);
      
      // Update charts based on selected time range
      updateCharts(selectedRange);
    });
    
    // Fetch initial dashboard data
    document.addEventListener('DOMContentLoaded', function() {
      // Load initial data
      updateMetrics('7days');
      updateCharts('7days');
    });
    
    async function updateMetrics(range) {
      try {
        // Show loading state
        showLoadingState();
        
        // Fetch data from API
        const response = await fetch(`/api/admin/dashboard/stats?range=${range}`);
        const result = await response.json();
        
        if (result.success) {
          const data = result.data;
          
          // Update GMV
          document.querySelector('.metric-card:nth-child(1) .metric-value').textContent = formatCurrency(data.metrics.gmv);
          
          // Update Revenue
          document.querySelector('.metric-card:nth-child(2) .metric-value').textContent = formatCurrency(data.metrics.revenue);
          
          // Update growth percentage
          const growthElement = document.querySelector('.metric-card:nth-child(1) .metric-title');
          if (growthElement) {
            // Add growth indicator
            const growthIndicator = data.metrics.growth_percentage >= 0 ? '↗' : '↘';
            growthElement.innerHTML = `Gross Merchandise Volume (GMV) <span style="float: right; color: ${data.metrics.growth_percentage >= 0 ? 'green' : 'red'}">${growthIndicator} ${Math.abs(data.metrics.growth_percentage)}%</span>`;
          }
          
          // Update the stat cards
          const statCards = document.querySelectorAll('.stat-card');
          if (statCards.length >= 6) {
            statCards[0].querySelector('.stat-value').textContent = data.seller_stats.total;
            statCards[1].querySelector('.stat-value').textContent = data.seller_stats.new;
            statCards[2].querySelector('.stat-value').textContent = data.moderation_counts.violations_reported;
            statCards[3].querySelector('.stat-value').textContent = data.moderation_counts.pending_verifications;
            statCards[4].querySelector('.stat-value').textContent = data.user_stats.active_users;
            statCards[5].querySelector('.stat-value').textContent = data.metrics.total_orders;
          }
          
          // Hide loading state
          hideLoadingState();
        } else {
          console.error('Failed to load dashboard data:', result.message);
          hideLoadingState();
        }
      } catch (error) {
        console.error('Error loading dashboard data:', error);
        hideLoadingState();
        // Fallback to static data
        fallbackToStaticData(range);
      }
    }
    
    function updateCharts(range) {
      // This will be implemented later when we have real chart data
      console.log('Updating charts for range:', range);
    }
    
    function showLoadingState() {
      // Add loading indicators to metric values
      const metricValues = document.querySelectorAll('.metric-value');
      metricValues.forEach(value => {
        value.innerHTML = '<span class="loading-spinner">Loading...</span>';
      });
    }
    
    function hideLoadingState() {
      // Remove loading indicators
      const loadingSpinners = document.querySelectorAll('.loading-spinner');
      loadingSpinners.forEach(spinner => {
        spinner.parentElement.innerHTML = spinner.parentElement.innerHTML.replace(/<span class="loading-spinner">Loading...<\/span>/g, '');
      });
    }
    
    function fallbackToStaticData(range) {
      // Sample data for different time ranges
      const metricsData = {
        'today': {
          gmv: 'Rp 120,500,000',
          revenue: 'Rp 6,025,000',
          activeSellers: '120',
          newSellers: '5',
          suspendedSellers: '2',
          pendingVerification: '1',
          activeBuyers: '450',
          totalTransactions: '150',
          growthPercentage: '12.5'
        },
        '7days': {
          gmv: 'Rp 840,750,000',
          revenue: 'Rp 42,037,500',
          activeSellers: '850',
          newSellers: '35',
          suspendedSellers: '15',
          pendingVerification: '8',
          activeBuyers: '3,200',
          totalTransactions: '1,100',
          growthPercentage: '8.3'
        },
        'month': {
          gmv: 'Rp 2,450,780,000',
          revenue: 'Rp 122,539,000',
          activeSellers: '2,340',
          newSellers: '187',
          suspendedSellers: '42',
          pendingVerification: '24',
          activeBuyers: '8,950',
          totalTransactions: '3,240',
          growthPercentage: '5.7'
        }
      };

      const data = metricsData[range];

      // Update values in the dashboard
      document.querySelector('.metric-card:nth-child(1) .metric-value').textContent = data.gmv;
      document.querySelector('.metric-card:nth-child(2) .metric-value').textContent = data.revenue;

      // Update growth percentage
      const growthElement = document.querySelector('.metric-card:nth-child(1) .metric-title');
      if (growthElement) {
        growthElement.innerHTML = `Gross Merchandise Volume (GMV) <span style="float: right; color: green">↗ ${data.growthPercentage}%</span>`;
      }

      // Update the stat cards
      const statCards = document.querySelectorAll('.stat-card');
      if (statCards.length >= 6) {
        statCards[0].querySelector('.stat-value').textContent = data.activeSellers;
        statCards[1].querySelector('.stat-value').textContent = data.newSellers;
        statCards[2].querySelector('.stat-value').textContent = data.suspendedSellers;
        statCards[3].querySelector('.stat-value').textContent = data.pendingVerification;
        statCards[4].querySelector('.stat-value').textContent = data.activeBuyers;
        statCards[5].querySelector('.stat-value').textContent = data.totalTransactions;
      }
    }

    // Update metrics function to use API data
    async function updateMetrics(range) {
      try {
        // Show loading state
        showLoadingState();

        // Fetch data from API
        const response = await fetch(`/api/admin/dashboard/stats?range=${range}`);
        const result = await response.json();

        if (result.success) {
          const data = result.data;

          // Debug: log the data to console
          console.log('Dashboard data received:', data);

          // Update GMV
          document.querySelector('.metric-card:nth-child(1) .metric-value').textContent = formatCurrency(data.metrics.gmv);

          // Update Revenue
          document.querySelector('.metric-card:nth-child(2) .metric-value').textContent = formatCurrency(data.metrics.revenue);

          // Update growth percentage
          const growthElement = document.querySelector('.metric-card:nth-child(1) .metric-title');
          if (growthElement) {
            // Add growth indicator
            const growthIndicator = data.metrics.growth_percentage >= 0 ? '↗' : '↘';
            growthElement.innerHTML = `Gross Merchandise Volume (GMV) <span style="float: right; color: ${data.metrics.growth_percentage >= 0 ? 'green' : 'red'}">${growthIndicator} ${Math.abs(data.metrics.growth_percentage)}%</span>`;
          }

          // Update the stat cards
          const statCards = document.querySelectorAll('.stat-card');
          if (statCards.length >= 6) {
            statCards[0].querySelector('.stat-value').textContent = data.seller_stats.total;
            statCards[1].querySelector('.stat-value').textContent = data.seller_stats.new;
            statCards[2].querySelector('.stat-value').textContent = data.moderation_counts.violations_reported;
            statCards[3].querySelector('.stat-value').textContent = data.moderation_counts.pending_verifications;
            statCards[4].querySelector('.stat-value').textContent = data.user_stats.active_users;
            statCards[5].querySelector('.stat-value').textContent = data.metrics.total_orders;
          }

          // Hide loading state
          hideLoadingState();
        } else {
          console.error('Failed to load dashboard data:', result.message);
          hideLoadingState();
          // Fallback to static data
          fallbackToStaticData(range);
        }
      } catch (error) {
        console.error('Error loading dashboard data:', error);
        hideLoadingState();
        // Fallback to static data
        fallbackToStaticData(range);
      }
    }
    
    function formatCurrency(amount) {
      // Convert to Indonesian Rupiah format
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(amount);
    }

    // Initialize Chart.js charts
    let gmvChart, revenueChart;

    function initCharts() {
      const gmvCtx = document.getElementById('gmvChart').getContext('2d');
      const revenueCtx = document.getElementById('revenueChart').getContext('2d');

      // Destroy existing charts if they exist
      if (gmvChart) gmvChart.destroy();
      if (revenueChart) revenueChart.destroy();

      // Sample data - this will be replaced with actual API data
      const chartData = {
        'today': {
          labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '23:59'],
          gmv: [30, 28, 32, 29, 35, 33, 30],
          revenue: [15, 14, 16, 14, 18, 16, 15]
        },
        '7days': {
          labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
          gmv: [20, 22, 18, 25, 30, 28, 32],
          revenue: [10, 11, 9, 13, 15, 14, 16]
        },
        'month': {
          labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
          gmv: [100, 120, 110, 140],
          revenue: [50, 60, 55, 70]
        }
      };

      const currentRange = document.getElementById('timeRange').value;
      const data = chartData[currentRange];

      // GMV Chart
      gmvChart = new Chart(gmvCtx, {
        type: 'line',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'GMV (Rp)',
            data: data.gmv,
            borderColor: '#006E5C',
            backgroundColor: 'rgba(0, 110, 92, 0.1)',
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              display: false // Hide y-axis labels to save space
            },
            x: {
              display: true
            }
          }
        }
      });

      // Revenue Chart
      revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Pendapatan (Rp)',
            data: data.revenue,
            borderColor: '#006E5C',
            backgroundColor: 'rgba(0, 110, 92, 0.1)',
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              display: false // Hide y-axis labels to save space
            },
            x: {
              display: true
            }
          }
        }
      });
    }

    function updateCharts(range) {
      // Update the charts with new data based on selected range
      initCharts();

      // Update metrics with new data based on selected range
      updateMetrics(range);
    }


    // Initialize with the default selection
    document.addEventListener('DOMContentLoaded', function() {
      initCharts();
      updateMetrics('7days');  // This will fetch and display dynamic data

      // Start real-time updates
      startRealTimeUpdates();
    });

    // Real-time updates functionality
    let realTimeUpdateInterval;

    function startRealTimeUpdates() {
      // Initial load
      updateRealTimeStats();

      // Set up interval to update every 30 seconds
      realTimeUpdateInterval = setInterval(() => {
        updateRealTimeStats();
      }, 30000);
    }

    async function updateRealTimeStats() {
      try {
        const response = await fetch('/api/admin/dashboard/realtime');
        const result = await response.json();

        if (result.success) {
          const data = result.data;

          // Update real-time metrics
          document.getElementById('activeUsersCount').textContent = data.active_users;
          document.getElementById('newOrdersCount').textContent = data.new_orders;
          document.getElementById('pendingPaymentsCount').textContent = data.pending_payments;

          // Update recent activities list
          updateRecentActivitiesList(data.recent_activities);
        } else {
          console.error('Failed to load real-time data:', result.message);
        }
      } catch (error) {
        console.error('Error fetching real-time data:', error);
      }
    }

    function updateRecentActivitiesList(activities) {
      const activitiesContainer = document.getElementById('recentActivitiesList');

      if (!activities || activities.length === 0) {
        activitiesContainer.innerHTML = '<div class="no-activities">Tidak ada aktivitas terbaru</div>';
        return;
      }

      // Generate HTML for each activity
      const activitiesHTML = activities.map(activity => {
        let icon = '🛒'; // default icon
        if (activity.type === 'payment') icon = '💰';

        return `
          <div class="activity-item" style="display: flex; align-items: center; margin-bottom: 8px; padding: 10px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px;">
            <div style="margin-right: 12px; font-size: 1.3em;">${icon}</div>
            <div style="flex: 1;">
              <div style="font-weight: 500; font-size: 0.95em; margin-bottom: 2px;">${activity.message}</div>
              <div style="font-size: 0.8em; color: var(--muted); margin-bottom: 2px;">${activity.time}</div>
              ${activity.formatted_amount ? `<div style="font-weight: 600; color: #006E5C; font-size: 0.95em;">${activity.formatted_amount}</div>` : ''}
            </div>
          </div>
        `;
      }).join('');

      activitiesContainer.innerHTML = activitiesHTML;
    }

    // Clean up interval when page is unloaded
    window.addEventListener('beforeunload', function() {
      if (realTimeUpdateInterval) {
        clearInterval(realTimeUpdateInterval);
      }
    });
  </script>
@endsection