@extends('layouts.admin')

@section('title', 'Admin Dashboard - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
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
      background: linear-gradient(135deg, #006E5C 0%, #a8d5c9 100%);
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
      max-height: 1000px;
      padding: 1rem;
    }
    
    .metric-card {
      background: var(--bg);
      border-radius: 8px;
      padding: 0.75rem;
      margin-bottom: 0.75rem;
      border-left: 3px solid var(--primary);
      transition: background-color 0.2s ease;
    }
    
    .metric-title {
      font-size: 0.9rem;
      color: var(--muted);
      margin-bottom: 0.25rem;
    }
    
    .metric-value {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--text);
    }
    
    .shortcut-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 0.75rem;
    }
    
    .shortcut-btn {
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 0.75rem;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s ease;
      color: #006E5C; /* Green color */
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
      padding: 0.75rem;
      text-align: center;
      border: 1px solid var(--border);
    }
    
    .stat-value {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--primary);
    }
    
    .stat-label {
      font-size: 0.8rem;
      color: var(--muted);
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
    
    
  </style>
@endpush

@section('content')
  @include('components.admin_penjual.header')
  
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content">
        

        <!-- Dashboard Widgets -->
        <div class="dashboard-grid">
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
            <div class="widget-content expanded">
              <div class="metric-card">
                <div class="metric-title">Gross Merchandise Volume (GMV)</div>
                <div class="metric-value">Rp 2,450,780,000</div>
                <!-- Sparkline chart for GMV -->
                <div style="height: 40px; margin-top: 10px;">
                  <svg width="100%" height="40" viewBox="0 0 300 40">
                    <polyline 
                      fill="none" 
                      stroke="#a8d5c9" 
                      stroke-width="2" 
                      points="0,30 30,25 60,20 90,22 120,18 150,15 180,12 210,10 240,15 270,8 300,5" 
                    />
                    <polyline 
                      fill="none" 
                      stroke="#006E5C" 
                      stroke-width="3" 
                      points="0,30 30,25 60,20 90,22 120,18 150,15 180,12 210,10 240,15 270,8 300,5" 
                    />
                  </svg>
                </div>
              </div>
              
              <div class="metric-card">
                <div class="metric-title">Pendapatan Platform</div>
                <div class="metric-value">Rp 122,539,000</div>
                <!-- Sparkline chart for Revenue -->
                <div style="height: 40px; margin-top: 10px;">
                  <svg width="100%" height="40" viewBox="0 0 300 40">
                    <polyline 
                      fill="none" 
                      stroke="#a8d5c9" 
                      stroke-width="2" 
                      points="0,35 30,32 60,30 90,28 120,25 150,22 180,20 210,18 240,15 270,12 300,10" 
                    />
                    <polyline 
                      fill="none" 
                      stroke="#006E5C" 
                      stroke-width="3" 
                      points="0,35 30,32 60,30 90,28 120,25 150,22 180,20 210,18 240,15 270,12 300,10" 
                    />
                  </svg>
                </div>
              </div>
              
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
          
          <!-- Widget 2: Management & Moderation Feed -->
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
            statCards[2].querySelector('.stat-value').textContent = '42'; // Violations - dummy data
            statCards[3].querySelector('.stat-value').textContent = '24'; // Pending verifications - dummy data
            statCards[4].querySelector('.stat-value').textContent = '8,950'; // Active buyers - dummy data
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
    
    function formatCurrency(amount) {
      // Convert to Indonesian Rupiah format
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(amount);
    }
    
    function updateCharts(range) {
      // Sample chart data for different time ranges
      const chartData = {
        'today': {
          gmv: [30, 28, 32, 29, 35, 33, 30], // 7 points for 24 hours
          revenue: [15, 14, 16, 14, 18, 16, 15]
        },
        '7days': {
          gmv: [20, 22, 18, 25, 30, 28, 32], // 7 points for 7 days
          revenue: [10, 11, 9, 13, 15, 14, 16]
        },
        'month': {
          gmv: [10, 12, 11, 15, 18, 16, 14, 17, 19, 22, 20, 24, 26, 23, 25, 28, 26, 30, 28, 32, 30, 33, 31, 35, 33, 36, 34, 38, 36, 40], // 30 points for 30 days
          revenue: [5, 6, 5, 7, 9, 8, 7, 8, 9, 11, 10, 12, 13, 11, 12, 14, 13, 15, 14, 16, 15, 17, 16, 18, 17, 19, 18, 20, 19, 21]
        }
      };
      
      const data = chartData[range];
      
      // Update GMV chart
      const gmvPoints = data.gmv.map((value, index) => {
        const x = (index / (data.gmv.length - 1)) * 300;
        const y = 40 - (value / Math.max(...data.gmv)) * 35;
        return `${x},${y}`;
      }).join(' ');
      
      // Update Revenue chart
      const revenuePoints = data.revenue.map((value, index) => {
        const x = (index / (data.revenue.length - 1)) * 300;
        const y = 40 - (value / Math.max(...data.revenue)) * 35;
        return `${x},${y}`;
      }).join(' ');
      
      // Update the chart SVGs
      const svgs = document.querySelectorAll('svg');
      if (svgs.length >= 2) {
        const gmvPolylines = svgs[0].querySelectorAll('polyline');
        const revenuePolylines = svgs[1].querySelectorAll('polyline');
        
        if (gmvPolylines.length >= 2) {
          gmvPolylines[1].setAttribute('points', gmvPoints);
        }
        
        if (revenuePolylines.length >= 2) {
          revenuePolylines[1].setAttribute('points', revenuePoints);
        }
      }
    }
    
    // Initialize with the default selection
    document.addEventListener('DOMContentLoaded', function() {
      updateMetrics('7days');
      updateCharts('7days');
    });
  </script>
@endsection