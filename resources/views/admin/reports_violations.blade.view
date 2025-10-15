@extends('layouts.admin')

@section('title', 'Laporan Pelanggaran - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <style>
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }
    
    .filters {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
    }
    
    .filter-group {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }
    
    .filter-group label {
      font-size: 0.9rem;
      color: var(--muted);
    }
    
    .filter-group input,
    .filter-group select {
      padding: 0.5rem;
      border: 1px solid var(--border);
      border-radius: 6px;
      background: white;
    }
    
    .table-container {
      background: white;
      border-radius: var(--ak-radius);
      border: 1px solid var(--ak-border);
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,.05);
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    th, td {
      padding: 0.75rem 1rem;
      text-align: left;
      border-bottom: 1px solid var(--ak-border);
    }
    
    th {
      background: var(--bg);
      font-weight: 600;
      color: var(--text);
      position: sticky;
      top: 0;
    }
    
    tr:last-child td {
      border-bottom: none;
    }
    
    tr:hover {
      background-color: var(--bg);
    }
    
    .sort-indicator {
      display: inline-block;
      margin-left: 0.25rem;
      font-size: 0.8rem;
      opacity: 0.5;
    }
    
    .sort-asc .sort-indicator::after {
      content: " ↑";
      opacity: 1;
    }
    
    .sort-desc .sort-indicator::after {
      content: " ↓";
      opacity: 1;
    }
    
    .status-badge {
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      font-size: 0.8rem;
      font-weight: 500;
    }
    
    .status-pending {
      background-color: #fffbeb;
      color: #f59e0b;
    }
    
    .status-verified {
      background-color: #ecfdf5;
      color: #10b981;
    }
    
    .status-resolved {
      background-color: #eff6ff;
      color: #3b82f6;
    }
    
    .status-rejected {
      background-color: #fef2f2;
      color: #ef4444;
    }
    
    .pagination {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1.5rem;
    }
    
    .pagination-info {
      color: var(--muted);
      font-size: 0.9rem;
    }
    
    .pagination-controls {
      display: flex;
      gap: 0.5rem;
    }
    
    .page-btn {
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid var(--border);
      background: var(--white);
      cursor: pointer;
      font-size: 0.9rem;
    }
    
    .page-btn.active {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
    }
    
    .page-btn:disabled {
      opacity: 0.4;
      cursor: not-allowed;
    }
    
    .page-btn:hover:not(:disabled):not(.active) {
      background-color: var(--bg);
    }
    
    .action-buttons {
      display: flex;
      gap: 0.5rem;
    }
    
    .btn-action {
      padding: 0.25rem 0.5rem;
      border: 1px solid var(--border);
      border-radius: 4px;
      background: white;
      cursor: pointer;
      font-size: 0.8rem;
    }
    
    .btn-view {
      color: var(--primary);
    }
    
    .btn-resolve {
      color: #10b981;
    }
    
    .btn-reject {
      color: #ef4444;
    }
    
    .modern-date-input {
      padding: 0.5rem 0.75rem;
      border: 1px solid var(--border);
      border-radius: 8px;
      background: white;
      font-size: 0.9rem;
      color: var(--text);
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
      cursor: pointer;
    }
    
    .modern-date-input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 110, 92, 0.1);
    }
    
    /* Modern date picker styling */
    .modern-date-input::-webkit-calendar-picker-indicator {
      cursor: pointer;
      padding: 4px;
      border-radius: 4px;
      transition: background-color 0.2s ease;
    }
    
    .modern-date-input::-webkit-calendar-picker-indicator:hover {
      background-color: var(--bg);
    }
    
    /* Styling for the date picker dropdown */
    .modern-date-input::-webkit-datetime-edit {
      padding: 2px 4px;
    }
    
    /* Custom date picker appearance (where supported) */
    .modern-date-input::-webkit-inner-spin-button,
    .modern-date-input::-webkit-calendar-picker-indicator {
      -webkit-appearance: none;
      appearance: none;
    }
    
    /* Fallback for Firefox */
    .modern-date-input {
      -webkit-appearance: none;
      -moz-appearance: textfield;
    }
  </style>
@endpush

@section('content')
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content">
        <div class="page-header">
          <h1>Laporan Pelanggaran</h1>
          <button class="btn btn-primary">Ekspor Laporan</button>
        </div>
        
        <div class="filters">
          <div class="filter-group">
            <label for="searchFilter">Cari</label>
            <input type="text" id="searchFilter" placeholder="Cari di Akrab...">
          </div>
          
          <div class="filter-group">
            <label for="dateFilter">Tanggal</label>
            <input type="date" id="dateFilter">
          </div>
          
          <div class="filter-group">
            <label for="violationTypeFilter">Jenis Pelanggaran</label>
            <select id="violationTypeFilter">
              <option value="">Semua Jenis</option>
              <option value="product">Produk Palsu</option>
              <option value="content">Konten Tidak Pantas</option>
              <option value="scam">Penipuan</option>
              <option value="copyright">Pelanggaran Hak Cipta</option>
              <option value="other">Lainnya</option>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="statusFilter">Status</label>
            <select id="statusFilter">
              <option value="">Semua Status</option>
              <option value="pending">Pending</option>
              <option value="verified">Terverifikasi</option>
              <option value="resolved">Diselesaikan</option>
              <option value="rejected">Ditolak</option>
            </select>
          </div>
        </div>
        
        <div class="table-container">
          <table id="violationsTable">
            <thead>
              <tr>
                <th class="sortable" data-column="date">Tanggal <span class="sort-indicator"></span></th>
                <th class="sortable" data-column="violationType">Jenis Pelanggaran <span class="sort-indicator"></span></th>
                <th class="sortable" data-column="seller">Nama Penjual <span class="sort-indicator"></span></th>
                <th>Produk Terkait</th>
                <th>Pelapor</th>
                <th class="sortable" data-column="status">Status <span class="sort-indicator"></span></th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>2023-06-15</td>
                <td>Produk Palsu</td>
                <td>Toko Sejahtera</td>
                <td>Kaos Original Branded</td>
                <td>Budi Santoso</td>
                <td><span class="status-badge status-pending">Pending</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-06-15-001']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-06-14</td>
                <td>Konten Tidak Pantas</td>
                <td>Warung Online</td>
                <td>Stiker Lucu</td>
                <td>Siti Aminah</td>
                <td><span class="status-badge status-verified">Terverifikasi</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-06-14-002']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-06-13</td>
                <td>Penipuan</td>
                <td>Elektronik Murah</td>
                <td>Handphone Second</td>
                <td>Andi Pratama</td>
                <td><span class="status-badge status-resolved">Diselesaikan</span></td>
                <td class="action-buttons">
                  <button class="btn-action btn-view">Lihat</button>
                </td>
              </tr>
              <tr>
                <td>2023-06-12</td>
                <td>Produk Palsu</td>
                <td>Fashion Kita</td>
                <td>Sepatu Branded</td>
                <td>Rina Kartika</td>
                <td><span class="status-badge status-pending">Pending</span></td>
                <td class="action-buttons">
                  <button class="btn-action btn-view">Lihat</button>
                  <button class="btn-action btn-reject">Tolak</button>
                </td>
              </tr>
              <tr>
                <td>2023-06-11</td>
                <td>Pelanggaran Hak Cipta</td>
                <td>Buku Seru</td>
                <td>Novel Best Seller</td>
                <td>Dedi Kusnadi</td>
                <td><span class="status-badge status-rejected">Ditolak</span></td>
                <td class="action-buttons">
                  <button class="btn-action btn-view">Lihat</button>
                </td>
              </tr>
              <tr>
                <td>2023-06-10</td>
                <td>Konten Tidak Pantas</td>
                <td>Hadiah Spesial</td>
                <td>Kartu Ucapan Dewasa</td>
                <td>Lisa Nuraini</td>
                <td><span class="status-badge status-pending">Pending</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-06-10-006']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-06-09</td>
                <td>Penipuan</td>
                <td>Aksesoris Modis</td>
                <td>Cincin Emas Palsu</td>
                <td>Fajar Nugroho</td>
                <td><span class="status-badge status-verified">Terverifikasi</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-06-09-007']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-06-08</td>
                <td>Produk Palsu</td>
                <td>Olahraga Store</td>
                <td>Jaket Branded</td>
                <td>Rini Susanti</td>
                <td><span class="status-badge status-resolved">Diselesaikan</span></td>
                <td class="action-buttons">
                  <button class="btn-action btn-view">Lihat</button>
                </td>
              </tr>
              <tr>
                <td>2023-06-07</td>
                <td>Produk Palsu</td>
                <td>Toko Modern</td>
                <td>Dompet Kulit Asli</td>
                <td>Wawan Kurniawan</td>
                <td><span class="status-badge status-pending">Pending</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-06-07-009']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-06-06</td>
                <td>Konten Tidak Pantas</td>
                <td>Seni Kita</td>
                <td>Lukisan Abstrak</td>
                <td>Maya Fitriani</td>
                <td><span class="status-badge status-verified">Terverifikasi</span></td>
                <td class="action-buttons">
                  <button class="btn-action btn-view">Lihat</button>
                  <button class="btn-action btn-reject">Tolak</button>
                </td>
              </tr>
              <tr>
                <td>2023-06-05</td>
                <td>Penipuan</td>
                <td>Makanan Sehat</td>
                <td>Suplemen Diet</td>
                <td>Rizki Prasetya</td>
                <td><span class="status-badge status-resolved">Diselesaikan</span></td>
                <td class="action-buttons">
                  <button class="btn-action btn-view">Lihat</button>
                </td>
              </tr>
              <tr>
                <td>2023-06-04</td>
                <td>Produk Palsu</td>
                <td>Perhiasan Emas</td>
                <td>Gelang Berlian</td>
                <td>Desi Marlina</td>
                <td><span class="status-badge status-pending">Pending</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-06-04-012']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-06-03</td>
                <td>Pelanggaran Hak Cipta</td>
                <td>Fotocopy Center</td>
                <td>Buku Pelajaran</td>
                <td>Ahmad Supriyadi</td>
                <td><span class="status-badge status-rejected">Ditolak</span></td>
                <td class="action-buttons">
                  <button class="btn-action btn-view">Lihat</button>
                </td>
              </tr>
              <tr>
                <td>2023-06-02</td>
                <td>Konten Tidak Pantas</td>
                <td>Hadiah Unik</td>
                <td>Boneka Lucu</td>
                <td>Intan Permata</td>
                <td><span class="status-badge status-pending">Pending</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-06-02-014']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-06-01</td>
                <td>Penipuan</td>
                <td>Toko Elektronik</td>
                <td>Speaker Bluetooth</td>
                <td>Heri Susanto</td>
                <td><span class="status-badge status-verified">Terverifikasi</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-06-01-015']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-31</td>
                <td>Produk Palsu</td>
                <td>Busana Muslim</td>
                <td>Koko Branded</td>
                <td>Sari Puspita</td>
                <td><span class="status-badge status-resolved">Diselesaikan</span></td>
                <td class="action-buttons">
                  <button class="btn-action btn-view">Lihat</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-30</td>
                <td>Produk Palsu</td>
                <td>Tas Import</td>
                <td>Tas Branded</td>
                <td>Adi Prasetyo</td>
                <td><span class="status-badge status-pending">Pending</span></td>
                <td class="action-buttons">
                  <button class="btn-action btn-view">Lihat</button>
                  <button class="btn-action btn-reject">Tolak</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-29</td>
                <td>Konten Tidak Pantas</td>
                <td>Hiburan Online</td>
                <td>Stiker Lucu</td>
                <td>Yeni Anggraini</td>
                <td><span class="status-badge status-verified">Terverifikasi</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-05-29-017']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-28</td>
                <td>Penipuan</td>
                <td>Perhiasan Murah</td>
                <td>Cincin Emas</td>
                <td>Beni Hidayat</td>
                <td><span class="status-badge status-resolved">Diselesaikan</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-05-28-018']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-27</td>
                <td>Produk Palsu</td>
                <td>Kosmetik Cantik</td>
                <td>Bedak Branded</td>
                <td>Lia Oktaviani</td>
                <td><span class="status-badge status-pending">Pending</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-06-15-001']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-26</td>
                <td>Konten Tidak Pantas</td>
                <td>Kado Spesial</td>
                <td>Boneka Lucu</td>
                <td>Susi Lestari</td>
                <td><span class="status-badge status-pending">Pending</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-05-26-020']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-25</td>
                <td>Penipuan</td>
                <td>Elektronik Baru</td>
                <td>Power Bank</td>
                <td>Tommy Prasetya</td>
                <td><span class="status-badge status-verified">Terverifikasi</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-05-25-021']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-reject">Tolak</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-24</td>
                <td>Produk Palsu</td>
                <td>Tas Kulit Asli</td>
                <td>Tas Kulit</td>
                <td>Dina Kusuma</td>
                <td><span class="status-badge status-resolved">Diselesaikan</span></td>
                <td class="action-buttons">
                  <button class="btn-action btn-view">Lihat</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-23</td>
                <td>Produk Palsu</td>
                <td>Parfum Original</td>
                <td>Parfum</td>
                <td>Joko Widodo</td>
                <td><span class="status-badge status-pending">Pending</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-06-15-001']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-22</td>
                <td>Konten Tidak Pantas</td>
                <td>Hadiah Dewasa</td>
                <td>Kartu Ucapan</td>
                <td>Ani Pratiwi</td>
                <td><span class="status-badge status-verified">Terverifikasi</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-05-22-023']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-21</td>
                <td>Penipuan</td>
                <td>Peralatan Masak</td>
                <td>Wajan Anti Lengket</td>
                <td>Bambang Subekti</td>
                <td><span class="status-badge status-resolved">Diselesaikan</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-05-21-024']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-view">Lihat</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-20</td>
                <td>Pelanggaran Hak Cipta</td>
                <td>CD Musik</td>
                <td>Album Terbaru</td>
                <td>Restu Adi</td>
                <td><span class="status-badge status-rejected">Ditolak</span></td>
                <td class="action-buttons">
                  <button class="btn-action btn-view">Lihat</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-19</td>
                <td>Produk Palsu</td>
                <td>Kacamata Branded</td>
                <td>Kacamata</td>
                <td>Fitri Lestari</td>
                <td><span class="status-badge status-pending">Pending</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-06-15-001']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-18</td>
                <td>Konten Tidak Pantas</td>
                <td>Stiker Gaul</td>
                <td>Stiker Mobil</td>
                <td>Taufik Hidayat</td>
                <td><span class="status-badge status-verified">Terverifikasi</span></td>
                <td class="action-buttons">
                  <button class="btn-action btn-view">Lihat</button>
                  <button class="btn-action btn-reject">Tolak</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-17</td>
                <td>Penipuan</td>
                <td>Obat Herbal</td>
                <td>Suplemen</td>
                <td>Sari Dewi</td>
                <td><span class="status-badge status-resolved">Diselesaikan</span></td>
                <td class="action-buttons">
                  <button class="btn-action btn-view">Lihat</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-16</td>
                <td>Produk Palsu</td>
                <td>Perhiasan Asli</td>
                <td>Kalung Berlian</td>
                <td>Agus Santoso</td>
                <td><span class="status-badge status-pending">Pending</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-05-16-026']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
              <tr>
                <td>2023-05-15</td>
                <td>Konten Tidak Pantas</td>
                <td>Boneka Lucu</td>
                <td>Boneka Anime</td>
                <td>Maya Sari</td>
                <td><span class="status-badge status-verified">Terverifikasi</span></td>
                <td class="action-buttons">
                  <a href="{{ route('reports.violations.detail', ['id' => 'VR-2023-05-15-027']) }}" class="btn-action btn-view">Lihat</a>
                  <button class="btn-action btn-resolve">Selesaikan</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div class="pagination">
          <div class="pagination-info" id="paginationInfo">Menampilkan 1-10 dari 32 laporan</div>
          <div class="pagination-controls">
            <button class="page-btn" id="firstBtn" disabled>&lt;&lt; Pertama</button>
            <button class="page-btn" id="prevBtn" disabled>‹ Sebelumnya</button>
            <button class="page-btn active" data-page="1" style="display: inline-block;">1</button>
            <button class="page-btn" data-page="2" style="display: inline-block;">2</button>
            <button class="page-btn" data-page="3" style="display: inline-block;">3</button>
            <button class="page-btn" data-page="4" style="display: inline-block;">4</button>
            <button class="page-btn" data-page="5" style="display: inline-block;">5</button>
            <button class="page-btn" id="nextBtn">Berikutnya ›</button>
            <button class="page-btn" id="lastBtn" disabled>Terakhir &gt;&gt;</button>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <script>
    // Sorting functionality
    const sortableHeaders = document.querySelectorAll('.sortable');
    let currentSort = { column: 'date', direction: 'asc' };
    
    sortableHeaders.forEach(header => {
      header.addEventListener('click', () => {
        const column = header.dataset.column;
        
        // Determine sort direction
        if (currentSort.column === column) {
          currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
          currentSort.column = column;
          currentSort.direction = 'asc';
        }
        
        // Update sort indicators
        document.querySelectorAll('.sortable').forEach(h => {
          h.classList.remove('sort-asc', 'sort-desc');
        });
        
        header.classList.add(currentSort.direction === 'asc' ? 'sort-asc' : 'sort-desc');
        
        // Sort the table
        sortTable(column, currentSort.direction);
      });
    });
    
    function sortTable(column, direction) {
      const table = document.getElementById('violationsTable');
      const tbody = table.querySelector('tbody');
      const rows = Array.from(tbody.querySelectorAll('tr'));
      
      rows.sort((a, b) => {
        let aVal = a.cells[getColumnIndex(column)].textContent.trim();
        let bVal = b.cells[getColumnIndex(column)].textContent.trim();
        
        // Handle date sorting
        if (column === 'date') {
          // Convert date format for proper sorting (YYYY-MM-DD)
          const aDate = parseDate(aVal);
          const bDate = parseDate(bVal);
          
          if (direction === 'asc') {
            return aDate - bDate;
          } else {
            return bDate - aDate;
          }
        }
        
        // Handle text sorting
        if (direction === 'asc') {
          return aVal.localeCompare(bVal, undefined, { numeric: true });
        } else {
          return bVal.localeCompare(aVal, undefined, { numeric: true });
        }
      });
      
      // Re-append sorted rows
      rows.forEach(row => tbody.appendChild(row));
    }
    
    function getColumnIndex(column) {
      // Map column names to their cell index positions
      const columnMap = {
        'date': 0,
        'violationType': 1,
        'seller': 2,
        'status': 5 // Status is in the 6th column (0-indexed: 5)
      };
      return columnMap[column];
    }
    
    // Helper function to parse date from DD/MM/YYYY or YYYY-MM-DD format
    function parseDate(dateString) {
      // If the date is in YYYY-MM-DD format
      if (dateString.includes('-')) {
        return new Date(dateString);
      }
      // If the date is in DD/MM/YYYY format
      const [day, month, year] = dateString.split('/');
      return new Date(year, month - 1, day);
    }
    
    // Filtering and search functionality
    const filters = {
      search: document.getElementById('searchFilter'),
      date: document.getElementById('dateFilter'),
      violationType: document.getElementById('violationTypeFilter'),
      status: document.getElementById('statusFilter')
    };
    
    Object.values(filters).forEach(filter => {
      filter.addEventListener('input', applyFilters);
    });
    
    function applyFilters() {
      const rows = document.querySelectorAll('#violationsTable tbody tr');
      const filterValues = {
        search: filters.search.value.toLowerCase(),
        date: filters.date.value,
        violationType: filters.violationType.value,
        status: filters.status.value
      };
      
      rows.forEach(row => {
        const cells = row.cells;
        let matches = true;
        
        // Search filter (applies to all columns)
        if (filterValues.search) {
          let searchMatch = false;
          
          // Check all text cells for the search term
          for (let i = 0; i < cells.length - 1; i++) { // Exclude the last column (actions)
            if (cells[i].textContent.toLowerCase().includes(filterValues.search)) {
              searchMatch = true;
              break;
            }
          }
          
          if (!searchMatch) matches = false;
        }
        
        // Date filter - exact match
        if (filterValues.date && matches) {
          if (cells[0].textContent.trim() !== formatDate(new Date(filterValues.date))) {
            matches = false;
          }
        }
        
        // Violation type filter - exact match with the option values
        if (filterValues.violationType && matches) {
          let violationMatch = false;
          
          if (filterValues.violationType === 'product' && cells[1].textContent.includes('Produk Palsu')) violationMatch = true;
          if (filterValues.violationType === 'content' && cells[1].textContent.includes('Konten Tidak Pantas')) violationMatch = true;
          if (filterValues.violationType === 'scam' && cells[1].textContent.includes('Penipuan')) violationMatch = true;
          if (filterValues.violationType === 'copyright' && cells[1].textContent.includes('Pelanggaran Hak Cipta')) violationMatch = true;
          if (filterValues.violationType === 'other' && cells[1].textContent.includes('Lainnya')) violationMatch = true;
          
          if (!violationMatch) matches = false;
        }
        
        // Status filter - exact match with text content
        if (filterValues.status && matches) {
          const statusCell = cells[5].textContent.trim().toLowerCase();
          let statusMatch = false;
          
          if (filterValues.status === 'pending' && statusCell.includes('pending')) statusMatch = true;
          if (filterValues.status === 'verified' && statusCell.includes('terverifikasi')) statusMatch = true;
          if (filterValues.status === 'resolved' && statusCell.includes('diselesaikan')) statusMatch = true;
          if (filterValues.status === 'rejected' && statusCell.includes('ditolak')) statusMatch = true;
          
          if (!statusMatch) matches = false;
        }
        
        row.style.display = matches ? '' : 'none';
      });
      
      // After filtering, reset to first page and update pagination
      currentPage = 1;
      showPage(currentPage);
    }
    
    // Helper function to format date as YYYY-MM-DD
    function formatDate(date) {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    }
    
    // Pagination setup
    let currentPage = 1;
    const rowsPerPage = 10;
    const allRows = document.querySelectorAll('#violationsTable tbody tr');
    const totalPages = Math.ceil(allRows.length / rowsPerPage);
    
    function showPage(page) {
      // First, get all visible rows (not hidden by filters)
      const visibleRows = Array.from(allRows).filter(row => {
        return row.style.display === '' || row.style.display === 'table-row';
      });
      
      const totalPagesForVisible = Math.ceil(visibleRows.length / rowsPerPage);
      
      // Calculate visible range
      const start = (page - 1) * rowsPerPage;
      const end = start + rowsPerPage;
      
      // Hide all rows first
      allRows.forEach(row => {
        row.style.display = 'none';
      });
      
      // Show only the rows that are both visible (not filtered) and in the current page
      visibleRows.forEach((row, index) => {
        if (index >= start && index < end) {
          row.style.display = '';
        }
      });
      
      // Update pagination info
      const currentCount = Math.min(rowsPerPage, visibleRows.length - start);
      const startNum = visibleRows.length > 0 ? Math.min(start + 1, visibleRows.length) : 0;
      const endNum = visibleRows.length > 0 ? Math.min(end, visibleRows.length) : 0;
      
      document.getElementById('paginationInfo').textContent = 
        `Menampilkan ${startNum}-${endNum} dari ${visibleRows.length} laporan`;
      
      // Update active page button
      document.querySelectorAll('.page-btn[data-page]').forEach(btn => {
        btn.style.display = 'inline-block'; // Show all page buttons
        btn.classList.toggle('active', parseInt(btn.dataset.page) === page);
      });
      
      // Update prev/next button states
      document.getElementById('prevBtn').disabled = page === 1 || visibleRows.length <= rowsPerPage;
      document.getElementById('nextBtn').disabled = page >= totalPagesForVisible || visibleRows.length <= rowsPerPage;
      document.getElementById('firstBtn').disabled = page === 1 || visibleRows.length <= rowsPerPage;
      document.getElementById('lastBtn').disabled = page >= totalPagesForVisible || visibleRows.length <= rowsPerPage;
      
      // Show/hide first and last buttons if there are more than 4 pages
      const firstBtn = document.getElementById('firstBtn');
      const lastBtn = document.getElementById('lastBtn');
      if (totalPagesForVisible > 4) {
        firstBtn.style.display = 'inline-block';
        lastBtn.style.display = 'inline-block';
      } else {
        firstBtn.style.display = 'none';
        lastBtn.style.display = 'none';
      }
      
      // Update page number buttons visibility based on total pages
      document.querySelectorAll('.page-btn[data-page]').forEach((btn, index) => {
        const pageNum = index + 1;
        if (pageNum > totalPagesForVisible) {
          btn.style.display = 'none';
        } else {
          btn.style.display = 'inline-block';
        }
      });
    }
    
    function updatePageButtons(currentPage) {
      const pageButtons = document.querySelectorAll('.page-btn[data-page]');
      const maxVisiblePages = 3;
      
      // Show/hide page number buttons based on current page
      pageButtons.forEach((btn, index) => {
        const pageNum = index + 1;
        const shouldShow = 
          pageNum === 1 || 
          pageNum === totalPages || 
          (pageNum >= currentPage - 1 && pageNum <= currentPage + 1);
          
        if (shouldShow) {
          btn.style.display = 'inline-block';
          btn.dataset.page = pageNum;
          btn.textContent = pageNum;
          btn.classList.toggle('active', pageNum === currentPage);
        } else {
          btn.style.display = 'none';
        }
      });
    }
    
    // Initial page display
    window.addEventListener('load', function() {
      showPage(currentPage);
    });
    
    // Add event listeners for pagination buttons
    document.getElementById('firstBtn').addEventListener('click', () => {
      if (currentPage > 1) {
        currentPage = 1;
        showPage(currentPage);
      }
    });
    
    document.getElementById('prevBtn').addEventListener('click', () => {
      if (currentPage > 1) {
        currentPage--;
        showPage(currentPage);
      }
    });
    
    document.getElementById('nextBtn').addEventListener('click', () => {
      if (currentPage < totalPages) {
        currentPage++;
        showPage(currentPage);
      }
    });
    
    document.getElementById('lastBtn').addEventListener('click', () => {
      if (currentPage < totalPages) {
        currentPage = totalPages;
        showPage(currentPage);
      }
    });
    
    // Add event listeners for page number buttons
    document.querySelectorAll('.page-btn[data-page]').forEach(btn => {
      btn.addEventListener('click', () => {
        const page = parseInt(btn.dataset.page);
        if (page !== currentPage) {
          currentPage = page;
          showPage(currentPage);
        }
      });
    });
    
    // When filters are applied, reset to first page
    Object.values(filters).forEach(filter => {
      filter.addEventListener('input', () => {
        currentPage = 1;
        applyFilters();
        showPage(currentPage);
      });
    });
    
    // Action buttons
    document.querySelectorAll('.btn-action').forEach(button => {
      button.addEventListener('click', (e) => {
        const action = e.target.classList.contains('btn-view') ? 'view' :
                      e.target.classList.contains('btn-resolve') ? 'resolve' :
                      e.target.classList.contains('btn-reject') ? 'reject' : '';
        
        console.log(`${action} action clicked`);
      });
    });
  </script>
@endsection