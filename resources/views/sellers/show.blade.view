<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Detail Penjual - {{ $seller->store_name }} — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <style>
    /* Enhanced styles for detail page */
    :root {
      --primary: #006E5C;
      --primary-light: #a8d5c9;
      --secondary: #6c757d;
      --success: #28a745;
      --danger: #dc3545;
      --warning: #ffc107;
      --info: #17a2b8;
      --light: #f8f9fa;
      --dark: #343a40;
      --white: #ffffff;
      --gray: #6c757d;
      --border: #dee2e6;
      --box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
      --border-radius: 0.375rem;
      --transition: all 0.3s ease;
    }
    
    body {
      background-color: #f5f7fa;
      font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
    }
    
    .main-content {
      padding-top: 2rem;
      padding-bottom: 2rem;
    }
    
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.5rem;
      padding: 0.25rem 0;
    }
    
    .page-title {
      margin: 0;
      font-size: 1.75rem;
      font-weight: 700;
      color: var(--primary);
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .page-title i {
      color: var(--primary-light);
    }
    
    .detail-container {
      background: var(--white);
      border-radius: var(--border-radius);
      border: none;
      box-shadow: var(--box-shadow);
      overflow: hidden;
      transition: var(--transition);
    }
    
    .detail-container:hover {
      box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
    }
    
    .detail-header {
      padding: 1.25rem 1.5rem;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      color: white;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .detail-title {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .detail-content {
      padding: 1.75rem;
    }
    
    .detail-section {
      margin-bottom: 1.5rem;
      background: #f8f9fa;
      padding: 1.25rem;
      border-radius: var(--border-radius);
    }
    
    .detail-table {
      width: 100%;
      margin-bottom: 0;
    }
    
    .detail-table th, .detail-table td {
      padding: 0.7rem 1rem;
      vertical-align: top;
      border-top: 1px solid var(--border);
    }
    
    .detail-table th {
      width: 30%;
      font-weight: 600;
      color: #495057;
      background-color: rgba(0, 110, 92, 0.05);
      border-top: none;
    }
    
    .detail-table tr:first-child th,
    .detail-table tr:first-child td {
      border-top: none;
    }
    
    .btn {
      border-radius: var(--border-radius);
      padding: 0.5rem 1rem;
      font-weight: 600;
      transition: var(--transition);
      border: none;
      margin-right: 0.5rem;
    }
    
    .btn-primary {
      background: var(--primary);
      border: 1px solid var(--primary);
    }
    
    .btn-primary:hover {
      background: #005a4a;
      border-color: #005a4a;
      transform: translateY(-2px);
    }
    
    .btn-secondary {
      background: var(--secondary);
      border: 1px solid var(--secondary);
    }
    
    .btn-secondary:hover {
      background: #5a6268;
      border-color: #545b62;
      transform: translateY(-2px);
    }
    
    .btn-warning {
      background: var(--warning);
      border: 1px solid var(--warning);
      color: #212529;
    }
    
    .btn-warning:hover {
      background: #e0a800;
      border-color: #d39e00;
      color: #212529;
    }
    
    .btn-danger {
      background: var(--danger);
      border: 1px solid var(--danger);
    }
    
    .btn-danger:hover {
      background: #c82333;
      border-color: #bd2130;
    }
    
    .badge {
      padding: 0.4em 0.7em;
      font-size: 0.8em;
      font-weight: 500;
      border-radius: 50rem;
    }
    
    .card {
      box-shadow: var(--box-shadow);
      border: none;
      border-radius: var(--border-radius);
      transition: var(--transition);
    }

    .action-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-top: 1.5rem;
    }

    .action-buttons .btn {
      margin-bottom: 0.5rem;
    }

    @media (max-width: 768px) {
      .action-buttons {
        flex-direction: column;
      }

      .action-buttons .btn {
        width: 100%;
        margin-right: 0;
      }
    }
  </style>
</head>
<body>
  @include('components.admin_penjual.header')
  
  <div class="main-layout">
    <div class="content-wrapper">
      <main class="admin-page-content main-content">
        <div class="page-header">
          <h2 class="page-title">Detail Penjual - {{ $seller->store_name }}</h2>
          <a href="{{ route('sellers.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar
          </a>
        </div>

        <div class="detail-container">
          <div class="detail-header">
            <h3 class="detail-title">Informasi Penjual</h3>
          </div>
          <div class="detail-content">
            <div class="row">
              <div class="col-md-6">
                <div class="detail-section">
                  <table class="table table-borderless detail-table">
                    <tr>
                      <th>ID Toko</th>
                      <td>{{ $seller->id }}</td>
                    </tr>
                    <tr>
                      <th>Nama Toko</th>
                      <td>{{ $seller->store_name }}</td>
                    </tr>
                    <tr>
                      <th>Nama Pemilik</th>
                      <td>{{ $seller->owner_name }}</td>
                    </tr>
                    <tr>
                      <th>Email</th>
                      <td>{{ $seller->email }}</td>
                    </tr>
                    <tr>
                      <th>Tanggal Bergabung</th>
                      <td>{{ $seller->join_date ? $seller->join_date->format('d M Y') : '-' }}</td>
                    </tr>
                  </table>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="detail-section">
                  <table class="table table-borderless detail-table">
                    <tr>
                      <th>Status</th>
                      <td>
                        @php
                          $statusClass = '';
                          $statusText = '';
                          switch($seller->status) {
                            case 'aktif':
                              $statusClass = 'bg-success';
                              $statusText = 'Aktif';
                              break;
                            case 'ditangguhkan':
                              $statusClass = 'bg-danger';
                              $statusText = 'Ditangguhkan';
                              break;
                            case 'menunggu_verifikasi':
                              $statusClass = 'bg-warning text-dark';
                              $statusText = 'Menunggu Verifikasi';
                              break;
                            case 'baru':
                              $statusClass = 'bg-info';
                              $statusText = 'Baru';
                              break;
                            default:
                              $statusClass = 'bg-secondary';
                              $statusText = 'Tidak Dikenal';
                          }
                        @endphp
                        <span class="badge rounded-pill {{ $statusClass }}">{{ $statusText }}</span>
                      </td>
                    </tr>
                    <tr>
                      <th>Jumlah Produk Aktif</th>
                      <td>{{ $seller->active_products_count }}</td>
                    </tr>
                    <tr>
                      <th>Total Penjualan (GMV)</th>
                      <td>Rp {{ number_format($seller->total_sales, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                      <th>Rating Toko</th>
                      <td>
                        <span class="badge bg-warning text-dark">
                          <i class="fas fa-star"></i> {{ number_format($seller->rating, 1) }}
                        </span>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
            
            <div class="row mt-4">
              <div class="col-12">
                <div class="action-buttons d-flex flex-wrap gap-2">
                  <a href="{{ route('sellers.edit', $seller) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Edit
                  </a>

                  <form action="{{ route('sellers.destroy', $seller) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus penjual ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                      <i class="fas fa-trash-alt me-1"></i>Hapus
                    </button>
                  </form>

                  @if($seller->status !== 'ditangguhkan')
                    <form action="{{ route('sellers.suspend', $seller) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-warning text-dark" onclick="return confirm('Apakah Anda yakin ingin menangguhkan penjual ini?')">
                        <i class="fas fa-pause-circle me-1"></i>Tangguhkan
                      </button>
                    </form>
                  @else
                    <form action="{{ route('sellers.activate', $seller) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin mengaktifkan kembali penjual ini?')">
                        <i class="fas fa-play-circle me-1"></i>Aktifkan
                      </button>
                    </form>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')
</body>
</html>