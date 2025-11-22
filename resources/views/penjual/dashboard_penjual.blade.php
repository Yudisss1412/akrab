<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Dashboard Penjual — AKRAB</title>
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
    
    /* Welcome Banner */
    .welcome-banner {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
    }
    
    .welcome-banner h1 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--ak-primary);
    }
    
    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    
    @media (min-width: 1024px) {
      .stats-grid {
        grid-template-columns: repeat(4, 1fr);
      }
    }
    
    .stat-card {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
      transition: transform 0.2s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-2px);
    }
    
    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: rgba(0, 110, 92, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 0.75rem;
    }
    
    .stat-icon svg {
      width: 24px;
      height: 24px;
      stroke: var(--ak-primary);
      fill: none;
    }
    
    .stat-title {
      font-size: 0.875rem;
      color: var(--ak-muted);
      margin: 0 0 0.25rem 0;
    }
    
    .stat-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--ak-text);
      margin: 0;
    }
    
    /* Main Content Layout */
    .main-content {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }
    
    @media (min-width: 1024px) {
      .main-content {
        grid-template-columns: 2fr 1fr;
      }
    }
    
    .card {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
      margin-bottom: 1.5rem;
    }
    
    .card h2 {
      margin-top: 0;
      margin-bottom: 1rem;
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--ak-primary);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .card h2 svg {
      width: 20px;
      height: 20px;
      stroke: var(--ak-primary);
      fill: none;
    }
    
    /* Action Items */
    .action-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .action-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.75rem 0;
      border-bottom: 1px solid var(--ak-border);
    }
    
    .action-item:last-child {
      border-bottom: none;
    }
    
    .action-link {
      text-decoration: none;
      color: var(--ak-text);
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: color 0.2s ease;
    }
    
    .action-link:hover {
      color: var(--ak-primary);
    }
    
    .action-link svg {
      width: 16px;
      height: 16px;
      stroke: var(--ak-primary);
      fill: none;
    }
    
    .action-count {
      background: var(--ak-primary);
      color: white;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      font-weight: 600;
    }
    
    /* Recent Activity */
    .activity-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .activity-item {
      display: flex;
      align-items: flex-start;
      padding: 0.75rem 0;
      border-bottom: 1px solid var(--ak-border);
    }
    
    .activity-item:last-child {
      border-bottom: none;
    }
    
    .activity-icon {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: rgba(0, 110, 92, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 0.75rem;
      flex-shrink: 0;
    }
    
    .activity-icon svg {
      width: 16px;
      height: 16px;
      stroke: var(--ak-primary);
      fill: none;
    }
    
    .activity-content {
      flex: 1;
    }
    
    .activity-text {
      margin: 0;
      color: var(--ak-text);
      line-height: 1.4;
    }
    
    .activity-time {
      font-size: 0.75rem;
      color: var(--ak-muted);
      margin-top: 0.25rem;
    }
    
    /* Announcements */
    .announcement {
      background: rgba(0, 110, 92, 0.05);
      border-left: 3px solid var(--ak-primary);
      padding: 1rem;
      margin-bottom: 1rem;
      border-radius: 0 var(--ak-radius) var(--ak-radius) 0;
    }
    
    .announcement-title {
      margin: 0 0 0.5rem 0;
      font-weight: 600;
      color: var(--ak-primary);
    }
    
    .announcement-text {
      margin: 0;
      color: var(--ak-text);
      font-size: 0.875rem;
      line-height: 1.4;
    }
    
    /* Quick Links */
    .quick-links {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .quick-link {
      display: block;
      padding: 0.75rem;
      margin-bottom: 0.75rem;
      text-decoration: none;
      color: var(--ak-text);
      background: #f8f9fa;
      border-radius: var(--ak-radius);
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .quick-link:last-child {
      margin-bottom: 0;
    }
    
    .quick-link:hover {
      background: var(--ak-primary);
      color: white;
    }
    
    .quick-link svg {
      width: 18px;
      height: 18px;
      stroke: black;
      fill: none;
      flex-shrink: 0;
    }
    
    .quick-link:hover svg {
      stroke: white;
    }
    
    .link-text {
      font-weight: 500;
    }
  </style>
</head>
<body>
  @include('components.admin_penjual.header')

  <div class="main-layout">
    <div class="content-wrapper">
      <main class="content admin-page-content" role="main">
        <!-- Welcome Banner -->
        <section class="welcome-banner">
          <h1>Selamat Datang, {{ auth()->user()->name }}!</h1>
        </section>

        <!-- Stats Grid -->
        <section class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 8V12L15 15" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <p class="stat-title">Pendapatan Bulan Ini</p>
            <p class="stat-value">Rp 5.450.000</p>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 4V7M9 4V7M6 17L8 15C8.04906 16.1624 8.40074 17.2808 9.01125 18.2198C9.62176 19.1587 10.4646 19.8787 11.4397 20.2928C12.4147 20.707 13.4812 20.797 14.509 20.5509C15.5368 20.3048 16.4838 19.7333 17.2388 18.9032C17.9938 18.0731 18.5249 17.0201 18.7654 15.8668C19.0059 14.7135 18.9459 13.519 18.5916 12.4218C18.2374 11.3246 17.6042 10.3831 16.7654 9.70711C15.9266 9.03113 14.9178 8.6461 13.85 8.6" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M7 8H17C18.1046 8 19 8.89543 19 10V18C19 19.1046 18.1046 20 17 20H7C5.89543 20 5 19.1046 5 18V10C5 8.89543 5.89543 8 7 8Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <p class="stat-title">Pesanan Baru</p>
            <p class="stat-value">8</p>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <p class="stat-title">Produk Dilihat</p>
            <p class="stat-value">1.204</p>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2L12 2Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <p class="stat-title">Rating Toko</p>
            <p class="stat-value">4.8 ⭐</p>
          </div>
        </section>

        <!-- Main Content Area -->
        <div class="main-content">
          <!-- Left Column -->
          <div class="left-column">
            <!-- Urgent Tasks Card -->
            <section class="card">
              <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Pusat Aksi / Tugas Mendesak
              </h2>
              <ul class="action-list">
                <li class="action-item">
                  <a href="{{ route('penjual.pesanan') }}" class="action-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M16 4H8C6.89543 4 6 4.89543 6 6V18C6 19.1046 6.89543 20 8 20H16C17.1046 20 18 19.1046 18 18V6C18 4.89543 17.1046 4 16 4Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M14 8H10" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M14 12H8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M14 16H8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Pesanan Perlu Diproses
                  </a>
                  <span class="action-count" id="pending-orders-count">0</span>
                </li>
                <li class="action-item">
                  <a href="https://wa.me/6289680861370" target="_blank" class="action-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M21 11.5C21.0034 12.8199 20.6951 14.1219 20.1 15.3C19.3944 16.7117 18.3098 17.8992 16.9674 18.7293C15.6251 19.5594 14.0782 19.9994 12.5 20C11.1801 20.0034 9.87812 19.6951 8.7 19.1L3 21L4.9 15.3C4.30493 14.1219 4.02472 12.8199 4 11.5C3.99998 9.92183 4.44002 8.37492 5.27011 7.03258C6.1002 5.69024 7.28825 4.60557 8.7 3.90002C9.87812 3.30495 11.1801 2.99663 12.5 3H13C15.0843 3.11423 17.0572 3.99406 18.527 5.47299C20.0059 6.94276 20.8858 8.91572 21 11V11.5Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Chat Belum Dibalas
                  </a>
                </li>
                <li class="action-item">
                  <a href="{{ route('penjual.komplain.retur') }}" class="action-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M4 19.5V4.5C4 3.4 4.9 2.5 6 2.5H18C19.1 2.5 20 3.4 20 4.5V19.5C20 20.6 19.1 21.5 18 21.5H6C4.9 21.5 4 20.6 4 19.5Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M9 7.5H15" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M9 12.5H15" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M9 17.5H13" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Komplain & Retur
                  </a>
                  <span class="action-count" id="total-urgent-count">0</span>
                </li>
              </ul>
            </section>

            <!-- Recent Activity Card -->
            <section class="card">
              <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M12 8V12L15 15" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Aktivitas Terkini Toko
              </h2>
              <ul class="activity-list">
                <li class="activity-item">
                  <div class="activity-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M16 4H8C6.89543 4 6 4.89543 6 6V18C6 19.1046 6.89543 20 8 20H16C17.1046 20 18 19.1046 18 18V6C18 4.89543 17.1046 4 16 4Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M14 8H10" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M14 12H8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M10 16H14" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </div>
                  <div class="activity-content">
                    <p class="activity-text">Produk "Kaos Polos" baru saja terjual</p>
                    <p class="activity-time">10 menit yang lalu</p>
                  </div>
                </li>
                <li class="activity-item">
                  <div class="activity-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M12 8V12M12 16H12.01" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </div>
                  <div class="activity-content">
                    <p class="activity-text">Anda menerima ulasan bintang 5 dari Budi</p>
                    <p class="activity-time">2 jam yang lalu</p>
                  </div>
                </li>
                <li class="activity-item">
                  <div class="activity-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <circle cx="12" cy="7" r="4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </div>
                  <div class="activity-content">
                    <p class="activity-text">Penjual lain "Toko Serba Ada" telah bergabung</p>
                    <p class="activity-time">5 jam yang lalu</p>
                  </div>
                </li>
                <li class="activity-item">
                  <div class="activity-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M16 4H8C6.89543 4 6 4.89543 6 6V18C6 19.1046 6.89543 20 8 20H16C17.1046 20 18 19.1046 18 18V6C18 4.89543 17.1046 4 16 4Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M14 8H10" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M14 12H8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M10 16H14" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </div>
                  <div class="activity-content">
                    <p class="activity-text">Pesanan dari Siti untuk "Celana Panjang" sedang diproses</p>
                    <p class="activity-time">Hari ini, 09:30</p>
                  </div>
                </li>
              </ul>
            </section>
          </div>

          <!-- Right Column -->
          <div class="right-column">
            <!-- Announcements Card -->
            <section class="card">
              <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M12 8V12L15 15" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Pengumuman dari Admin
              </h2>
              <div class="announcement">
                <h3 class="announcement-title">Pemeliharaan Sistem</h3>
                <p class="announcement-text">Sistem akan menjalani pemeliharaan pada Sabtu, 5 Oktober 2024 pukul 02:00-04:00 WIB.</p>
              </div>
              <div class="announcement">
                <h3 class="announcement-title">Program Promosi Baru</h3>
                <p class="announcement-text">Ikuti program promosi "Hari Pelanggan Nasional" untuk meningkatkan penjualan Anda.</p>
              </div>
            </section>

            <!-- Quick Links Card -->
            <section class="card">
              <h2>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M15.75 2.25H21C21.1989 2.25 21.3897 2.32902 21.5303 2.46967C21.671 2.61032 21.75 2.80109 21.75 3V8.25C21.75 8.44891 21.671 8.63968 21.5303 8.78033C21.3897 8.92098 21.1989 9 21 9C20.8011 9 20.6103 8.92098 20.4697 8.78033C20.329 8.63968 20.25 8.44891 20.25 8.25V4.81L8.03 17.03C7.88782 17.1625 7.69978 17.2346 7.50548 17.2312C7.31118 17.2277 7.12579 17.149 6.98838 17.0116C6.85097 16.8742 6.77225 16.6888 6.76883 16.4945C6.7654 16.3002 6.83752 16.1122 6.97 15.97L19.19 3.75H15.75C15.5511 3.75 15.3603 3.67098 15.2197 3.53033C15.079 3.38968 15 3.19891 15 3C15 2.80109 15.079 2.61032 15.2197 2.46967C15.3603 2.32902 15.5511 2.25 15.75 2.25ZM5.25 6.75C4.85218 6.75 4.47064 6.90804 4.18934 7.18934C3.90804 7.47064 3.75 7.85218 3.75 8.25V18.75C3.75 19.1478 3.90804 19.5294 4.18934 19.8107C4.47064 20.092 4.85218 20.25 5.25 20.25H15.75C16.1478 20.25 16.5294 20.092 16.8107 19.8107C17.092 19.5294 17.25 19.1478 17.25 18.75V10.5C17.25 10.3011 17.329 10.1103 17.4697 9.96967C17.6103 9.82902 17.8011 9.75 18 9.75C18.1989 9.75 18.3897 9.82902 18.5303 9.96967C18.671 10.1103 18.75 10.3011 18.75 10.5V18.75C18.75 19.5456 18.4339 20.3087 17.8713 20.8713C17.3087 21.4339 16.5456 21.75 15.75 21.75H5.25C4.45435 21.75 3.69129 21.4339 3.12868 20.8713C2.56607 20.3087 2.25 19.5456 2.25 18.75V8.25C2.25 7.45435 2.56607 6.69129 3.12868 6.12868C3.69129 5.56607 4.45435 5.25 5.25 5.25H13.5C13.6989 5.25 13.8897 5.32902 14.0303 5.46967C14.171 5.61032 14.25 5.80109 14.25 6C14.25 6.19891 14.171 6.38968 14.0303 6.53033C13.8897 6.67098 13.6989 6.75 13.5 6.75H5.25Z" fill="currentColor"/>
                </svg>
                Pintasan Cepat
              </h2>
              <ul class="quick-links">
                <li>
                  <a href="{{ route('penjual.produk') }}" class="quick-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M6.5 7.5C6.5 7.76522 6.60536 8.01957 6.79289 8.20711C6.98043 8.39464 7.23478 8.5 7.5 8.5C7.76522 8.5 8.01957 8.39464 8.20711 8.20711C8.39464 8.01957 8.5 7.76522 8.5 7.5C8.5 7.23478 8.39464 6.98043 8.20711 6.79289C8.01957 6.60536 7.76522 6.5 7.5 6.5C7.23478 6.5 6.98043 6.60536 6.79289 6.79289C6.60536 6.98043 6.5 7.23478 6.5 7.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M3 6V11.172C3.00011 11.7024 3.2109 12.211 3.586 12.586L11.296 20.296C11.748 20.7479 12.3609 21.0017 13 21.0017C13.6391 21.0017 14.252 20.7479 14.704 20.296L20.296 14.704C20.7479 14.252 21.0017 13.6391 21.0017 13C21.0017 12.3609 20.7479 11.748 20.296 11.296L12.586 3.586C12.211 3.2109 11.7024 3.00011 11.172 3H6C5.20435 3 4.44129 3.31607 3.87868 3.87868C3.31607 4.44129 3 5.20435 3 6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="link-text">Manajemen Produk</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('penjual.pesanan') }}" class="quick-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M3.99987 8.116V4H9.69187V8.116H3.99987ZM4.99987 7.116H8.69187V5H4.99987V7.116ZM7.19387 18C6.50054 18 5.9102 17.7567 5.42287 17.27C4.9362 16.7847 4.69287 16.1947 4.69287 15.5H2.69287V13C2.69287 12.028 3.03287 11.2017 3.71287 10.521C4.39287 9.84033 5.21987 9.5 6.19387 9.5H9.69387V14.5H13.9239L17.6939 9.842V7.308C17.6939 7.128 17.6359 6.98067 17.5199 6.866C17.4045 6.75 17.2572 6.692 17.0779 6.692H14.6929V5.692H17.0779C17.5219 5.692 17.9019 5.85033 18.2179 6.167C18.5339 6.48367 18.6922 6.86367 18.6929 7.307V10.157L14.4629 15.5H9.69287C9.69287 16.1927 9.4502 16.7827 8.96487 17.27C8.47954 17.7573 7.88954 18.0007 7.19487 18M7.19287 17C7.6042 17 7.9572 16.853 8.25187 16.559C8.54587 16.2643 8.69287 15.9113 8.69287 15.5H5.69287C5.69287 15.9113 5.8402 16.2643 6.13487 16.559C6.42887 16.853 6.78154 17 7.19287 17ZM18.8109 18C18.1175 18 17.5272 17.7573 17.0399 17.272C16.5525 16.7867 16.3089 16.1967 16.3089 15.502C16.3089 14.8073 16.5515 14.217 17.0369 13.731C17.5222 13.245 18.1122 13.0013 18.8069 13C19.5015 12.9987 20.0919 13.2417 20.5779 13.729C21.0639 14.2163 21.3072 14.806 21.3079 15.498C21.3085 16.19 21.0659 16.7803 20.5799 17.269C20.0939 17.7577 19.5039 18.0013 18.8099 18M18.8079 17C19.2192 17 19.5719 16.853 19.8659 16.559C20.1605 16.2643 20.3079 15.9113 20.3079 15.5C20.3079 15.0887 20.1605 14.7357 19.8659 14.441C19.5712 14.1463 19.2185 13.9993 18.8079 14C18.3972 14.0007 18.0442 14.1477 17.7489 14.441C17.4549 14.7357 17.3079 15.0887 17.3079 15.5C17.3079 15.9113 17.4549 16.2643 17.7489 16.559C18.0435 16.853 18.3965 17 18.8079 17ZM3.69187 14.5H8.69187V10.5H6.18987C5.5012 10.5 4.91287 10.745 4.42487 11.235C3.93687 11.7243 3.69287 12.3127 3.69287 13L3.69187 14.5Z" fill="currentColor"/>
                    </svg>
                    <span class="link-text">Proses Semua Pesanan</span>
                  </a>
                </li>
                <li>
                  <a href="/penjual/promosi" class="quick-link">
                    <svg width="24" height="24" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M14.158 1.02584C14.2838 1.06763 14.3879 1.15768 14.4474 1.2762C14.5068 1.39472 14.5167 1.532 14.475 1.65784L13.975 3.15784C13.9331 3.28382 13.8428 3.38799 13.7241 3.44744C13.6054 3.50689 13.468 3.51675 13.342 3.47484C13.216 3.43294 13.1118 3.34271 13.0524 3.224C12.9929 3.10529 12.9831 2.96782 13.025 2.84184L13.525 1.34184C13.5458 1.27951 13.5786 1.22189 13.6217 1.17226C13.6648 1.12264 13.7172 1.08199 13.776 1.05265C13.8348 1.0233 13.8988 1.00582 13.9643 1.00123C14.0299 0.996627 14.0957 1.00499 14.158 1.02584ZM17.855 2.85384C17.9014 2.80736 17.9382 2.75218 17.9633 2.69147C17.9884 2.63075 18.0013 2.56569 18.0013 2.49999C18.0012 2.43429 17.9882 2.36925 17.963 2.30857C17.9379 2.24789 17.901 2.19277 17.8545 2.14634C17.808 2.09992 17.7528 2.06311 17.6921 2.03801C17.6314 2.01291 17.5663 2.00002 17.5006 2.00007C17.4349 2.00011 17.3699 2.0131 17.3092 2.03828C17.2485 2.06347 17.1934 2.10036 17.147 2.14684L15.147 4.14684C15.0559 4.24115 15.0055 4.36745 15.0066 4.49855C15.0078 4.62964 15.0604 4.75505 15.1531 4.84775C15.2458 4.94046 15.3712 4.99304 15.5023 4.99418C15.6334 4.99532 15.7597 4.94492 15.854 4.85384L17.855 2.85384ZM7.60698 3.14584C7.74648 2.85208 7.95551 2.59676 8.21596 2.40201C8.47641 2.20726 8.78042 2.07897 9.10164 2.02824C9.42287 1.97751 9.75162 2.00587 10.0594 2.11088C10.3672 2.21589 10.6447 2.39436 10.868 2.63084L17.455 9.61084C17.6748 9.84373 17.8355 10.126 17.9235 10.4339C18.0115 10.7418 18.0243 11.0663 17.9608 11.3802C17.8973 11.6941 17.7594 11.9882 17.5586 12.2376C17.3578 12.4871 17.1 12.6847 16.807 12.8138L12.87 14.5498C13.0159 15.0703 13.0395 15.6175 12.9387 16.1486C12.838 16.6797 12.6158 17.1803 12.2895 17.6113C11.9631 18.0422 11.5415 18.3918 11.0576 18.6327C10.5736 18.8736 10.0405 18.9992 9.49998 18.9998C8.90423 19 8.31831 18.848 7.7977 18.5584C7.27709 18.2688 6.83902 17.8511 6.52498 17.3448L5.32498 17.8738C5.04921 17.9953 4.74326 18.0308 4.447 17.9759C4.15073 17.921 3.87787 17.7781 3.66398 17.5658L2.44198 16.3558C2.22101 16.137 2.07355 15.8549 2.01998 15.5485C1.96641 15.2421 2.00939 14.9267 2.14298 14.6458L7.60698 3.14584ZM7.45298 16.9348C7.78487 17.4086 8.27081 17.7527 8.82797 17.9083C9.38512 18.064 9.97901 18.0216 10.5084 17.7884C11.0378 17.5552 11.4699 17.1456 11.7312 16.6294C11.9924 16.1133 12.0665 15.5225 11.941 14.9578L7.45298 16.9348ZM10.141 3.31784C10.0294 3.19969 9.89068 3.11052 9.73687 3.05805C9.58305 3.00557 9.41877 2.99137 9.25823 3.01669C9.0977 3.042 8.94575 3.10605 8.81555 3.20331C8.68534 3.30057 8.5808 3.42809 8.51098 3.57484L3.04598 15.0748C3.00172 15.1684 2.98757 15.2733 3.00548 15.3752C3.02339 15.4772 3.07248 15.571 3.14598 15.6438L4.36798 16.8548C4.4393 16.9253 4.53016 16.9728 4.62878 16.991C4.7274 17.0091 4.8292 16.9973 4.92098 16.9568L16.403 11.8998C16.5495 11.8354 16.6784 11.7366 16.7788 11.612C16.8793 11.4873 16.9483 11.3404 16.9801 11.1835C17.012 11.0266 17.0057 10.8644 16.9618 10.7105C16.9179 10.5565 16.8377 10.4154 16.728 10.2988L10.141 3.31784ZM17 5.99985C16.8674 5.99985 16.7402 6.05252 16.6464 6.14629C16.5527 6.24006 16.5 6.36724 16.5 6.49985C16.5 6.63245 16.5527 6.75963 16.6464 6.8534C16.7402 6.94717 16.8674 6.99985 17 6.99985H18.5C18.6326 6.99985 18.7598 6.94717 18.8535 6.8534C18.9473 6.75963 19 6.63245 19 6.49985C19 6.36724 18.9473 6.24006 18.8535 6.14629C18.7598 6.05252 18.6326 5.99985 18.5 5.99985H17Z" fill="currentColor"/>
                    </svg>
                    <span class="link-text">Atur Promosi Toko</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('penjual.saldo') }}" class="quick-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M21 11.0026H20.824L19.001 5.66961L3.354 11.0026L3 10.9996M2.5 11.0036H3L14.146 2.09961L16.963 6.04961" stroke="currentColor" stroke-width="2" stroke-linecap="square"/>
                      <path d="M14.5 16C14.5 16.663 14.2366 17.2989 13.7678 17.7678C13.2989 18.2366 12.663 18.5 12 18.5C11.337 18.5 10.7011 18.2366 10.2322 17.7678C9.76339 17.2989 9.5 16.663 9.5 16C9.5 15.337 9.76339 14.7011 10.2322 14.2322C10.7011 13.7634 11.337 13.5 12 13.5C12.663 13.5 13.2989 13.7634 13.7678 14.2322C14.2366 14.7011 14.5 15.337 14.5 16Z" stroke="currentColor" stroke-width="2" stroke-linecap="square"/>
                      <path d="M21.5 11V21H2.5V11H21.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="square"/>
                      <path d="M2.5 11H4.5C4.5 11.5304 4.28929 12.0391 3.91421 12.4142C3.53914 12.7893 3.03043 13 2.5 13V11ZM21.5 11H19.5C19.5 11.5304 19.7107 12.0391 20.0858 12.4142C20.4609 12.7893 20.9696 13 21.5 13V11ZM2.5 21H4.502C4.50226 20.737 4.45066 20.4766 4.35014 20.2336C4.24963 19.9905 4.10217 19.7697 3.91621 19.5838C3.73026 19.3978 3.50946 19.2504 3.26644 19.1499C3.02343 19.0493 2.76298 18.9977 2.5 18.998V21ZM21.5 21H19.5C19.5 20.4696 19.7107 19.9609 20.0858 19.5858C20.4609 19.2107 20.9696 19 21.5 19V21Z" stroke="currentColor" stroke-width="2" stroke-linecap="square"/>
                    </svg>
                    <span class="link-text">Saldo & Penarikan Dana</span>
                  </a>
                </li>
                <li>
                  <a href="{{ route('seller.reviews.index') }}" class="quick-link">
                    <svg width="24" height="24" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="link-text">Lihat Ulasan</span>
                  </a>
                </li>
              </ul>
            </section>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')
  
  <script>
    // Ambil jumlah tugas mendesak secara dinamis
    function fetchUrgentTasks() {
      console.log('Fetching urgent tasks for dashboard penjual...');
      const csrfToken = document.querySelector('meta[name="csrf-token"]');
      const csrfTokenValue = csrfToken ? csrfToken.getAttribute('content') : '';

      fetch('{{ route('penjual.urgent.tasks') }}', {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfTokenValue
        }
      })
      .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.json();
      })
      .then(data => {
        console.log('Received data:', data);
        if (data && data.success && data.urgent_tasks) {
          console.log('Pending orders from API:', data.urgent_tasks.pending_orders);

          // Update pending orders count
          const pendingOrdersElement = document.getElementById('pending-orders-count');
          if (pendingOrdersElement) {
            pendingOrdersElement.textContent = data.urgent_tasks.pending_orders;
            console.log('Updated pending-orders-count to:', data.urgent_tasks.pending_orders);
          }

          // Update unreplied chats count
          const unrepliedChatsElement = document.getElementById('unreplied-chats-count');
          if (unrepliedChatsElement) {
            unrepliedChatsElement.textContent = data.urgent_tasks.unreplied_chats;
            console.log('Updated unreplied-chats-count to:', data.urgent_tasks.unreplied_chats);
          }

          // Update individual counts for reference
          const newComplaintsElement = document.getElementById('new-complaints-count');
          if (newComplaintsElement) {
            newComplaintsElement.textContent = data.urgent_tasks.new_complaints;
            console.log('Updated new-complaints-count to:', data.urgent_tasks.new_complaints);
          }

          const pendingReturnsElement = document.getElementById('pending-returns-count');
          if (pendingReturnsElement) {
            pendingReturnsElement.textContent = data.urgent_tasks.pending_returns;
            console.log('Updated pending-returns-count to:', data.urgent_tasks.pending_returns);
          }

          // Update total urgent count (komplain + retur)
          const totalUrgent = data.urgent_tasks.new_complaints + data.urgent_tasks.pending_returns;
          const totalUrgentElement = document.getElementById('total-urgent-count');
          if (totalUrgentElement) {
            totalUrgentElement.textContent = totalUrgent;
            console.log('Updated total-urgent-count to:', totalUrgent);
          }
        } else {
          console.error('Error fetching urgent tasks:', data.message || 'Invalid response format');
          if(data.message) {
            console.error('Error message:', data.message);
          }
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        // Tampilkan error di UI untuk debugging
        const pendingOrdersElement = document.getElementById('pending-orders-count');
        if (pendingOrdersElement) {
          pendingOrdersElement.textContent = 'Err';
        }
      });
    }

    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOM loaded for dashboard penjual, fetching urgent tasks...');

      // Buat elemen tersembunyi untuk menyimpan data individual
      if (!document.getElementById('new-complaints-count')) {
        const hiddenComplaints = document.createElement('span');
        hiddenComplaints.id = 'new-complaints-count';
        hiddenComplaints.style.display = 'none';
        document.body.appendChild(hiddenComplaints);
      }

      if (!document.getElementById('pending-returns-count')) {
        const hiddenReturns = document.createElement('span');
        hiddenReturns.id = 'pending-returns-count';
        hiddenReturns.style.display = 'none';
        document.body.appendChild(hiddenReturns);
      }

      // Panggil fungsi untuk pertama kali
      fetchUrgentTasks();

      // Refresh setiap 30 detik
      const intervalId = setInterval(fetchUrgentTasks, 30000);
      console.log('Auto-refresh started with interval ID:', intervalId);
    });
  </script>
</body>
</html>
