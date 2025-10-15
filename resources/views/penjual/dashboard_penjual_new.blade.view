<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Dashboard Penjual — AKRAB</title>
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
    
    .quick-link:hover svg {
      stroke: white;
    }
    
    .quick-link svg {
      width: 18px;
      height: 18px;
      stroke: var(--ak-primary);
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
          <h1>Selamat Datang, Shoppy.gg!</h1>
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
                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
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
                  <a href="#" class="action-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M16 4H8C6.89543 4 6 4.89543 6 6V18C6 19.1046 6.89543 20 8 20H16C17.1046 20 18 19.1046 18 18V6C18 4.89543 17.1046 4 16 4Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M14 8H10" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M14 12H8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M14 16H8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Pesanan Perlu Diproses
                  </a>
                  <span class="action-count">5</span>
                </li>
                <li class="action-item">
                  <a href="#" class="action-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M21 11.5C21.0034 12.8199 20.6951 14.1219 20.1 15.3C19.3944 16.7117 18.3098 17.8992 16.9674 18.7293C15.6251 19.5594 14.0782 19.9994 12.5 20C11.1801 20.0034 9.87812 19.6951 8.7 19.1L3 21L4.9 15.3C4.30493 14.1219 4.02472 12.8199 4 11.5C3.99998 9.92183 4.44002 8.37492 5.27011 7.03258C6.1002 5.69024 7.28825 4.60557 8.7 3.90002C9.87812 3.30495 11.1801 2.99663 12.5 3H13C15.0843 3.11423 17.0572 3.99406 18.527 5.47299C20.0059 6.94276 20.8858 8.91572 21 11V11.5Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Chat Belum Dibalas
                  </a>
                  <span class="action-count">3</span>
                </li>
                <li class="action-item">
                  <a href="#" class="action-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M4 19.5V4.5C4 3.4 4.9 2.5 6 2.5H18C19.1 2.5 20 3.4 20 4.5V19.5C20 20.6 19.1 21.5 18 21.5H6C4.9 21.5 4 20.6 4 19.5Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M9 7.5H15" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M9 12.5H15" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M9 17.5H13" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Komplain & Retur Baru
                  </a>
                  <span class="action-count">1</span>
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
                      <path d="M8 12H12M12H16M12 16V12V16ZM12 12L8 8L16 8L12 12Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
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
                  <path d="M13 3H12C10.8954 3 10 3.89543 10 5V11C10 12.1046 10.89543 13 12 13H13C14.1046 13 15 12.1046 15 11V5C15 3.89543 14.1046 3 13 3Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M13 15H12C10.8954 15 10 15.8954 10 17V21C10 21.5523 10.4477 22 11 22H13C13.5523 22 14 21.5523 14 21V17C14 15.8954 13.1046 15 12 15Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M6 7H5C3.89543 7 3 7.89543 3 9V17C3 18.1046 3.89543 19 5 19H6C7.10457 19 8 18.1046 8 17V9C8 7.89543 7.10457 7 6 7Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M20 7H19C17.8954 7 17 7.89543 17 9V12C17 13.1046 17.8954 14 19 14H20C21.1046 14 22 13.1046 22 12V9C22 7.89543 21.1046 7 20 7Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Pintasan Cepat
              </h2>
              <ul class="quick-links">
                <li>
                  <a href="#" class="quick-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M20 12H4M4 12L8 8M4 12L8 16" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M4 20H20C20.5304 20 21.0391 19.7893 21.4142 19.4142C21.7893 19.0391 22 18.5304 22 18V6C22 5.46957 21.7893 4.96086 21.4142 4.58579C21.0391 4.21071 20.5304 4 20 4H4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="link-text">Manajemen Produk</span>
                  </a>
                </li>
                <li>
                  <a href="#" class="quick-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M16 4H8C6.89543 4 6 4.89543 6 6V18C6 19.1046 6.89543 20 8 20H16C17.1046 20 18 19.1046 18 18V6C18 4.89543 17.1046 4 16 4Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M14 8H10" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M14 12H8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M14 16H8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="link-text">Proses Semua Pesanan</span>
                  </a>
                </li>
                <li>
                  <a href="/penjual/promosi" class="quick-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M14 12L10 8M14 12L10 16M14 12H20M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="link-text">Atur Promosi Toko</span>
                  </a>
                </li>
                <li>
                  <a href="#" class="quick-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12 8V12L15 15" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="link-text">Saldo & Penarikan Dana</span>
                  </a>
                </li>
                <li>
                  <a href="#" class="quick-link">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M8 12H12M12H16M12 16V12V16ZM12 12L8 8L16 8L12 12Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
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
</body>
</html>