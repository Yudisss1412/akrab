@extends('layouts.admin')

@section('title', 'Admin Dashboard - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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