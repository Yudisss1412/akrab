<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Edit Penjual - {{ $seller->store_name }} — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <style>
    /* Enhanced styles for form page */
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
    
    .form-container {
      background: var(--white);
      border-radius: var(--border-radius);
      border: none;
      box-shadow: var(--box-shadow);
      overflow: hidden;
      transition: var(--transition);
    }
    
    .form-container:hover {
      box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
    }
    
    .form-header {
      padding: 1.25rem 1.5rem;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      color: white;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .form-title {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .form-content {
      padding: 1.75rem;
    }
    
    .form-label {
      font-weight: 600;
      color: #495057;
      margin-bottom: 0.5rem;
    }
    
    .form-control, .form-select {
      border: 1px solid var(--border);
      border-radius: var(--border-radius);
      padding: 0.65rem 0.75rem;
      transition: var(--transition);
    }
    
    .form-control:focus, .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.2rem rgba(0, 110, 92, 0.25);
      outline: 0;
    }
    
    .btn {
      border-radius: var(--border-radius);
      padding: 0.5rem 1rem;
      font-weight: 600;
      transition: var(--transition);
      border: none;
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
    
    .invalid-feedback {
      display: block;
      width: 100%;
      margin-top: 0.25rem;
      font-size: 0.875rem;
      color: var(--danger);
    }
    
    .form-control.is-invalid {
      border-color: var(--danger);
      box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .card {
      box-shadow: var(--box-shadow);
      border: none;
      border-radius: var(--border-radius);
      transition: var(--transition);
    }
  </style>
</head>
<body>
  @include('components.admin_penjual.header')
  
  <div class="main-layout">
    <div class="content-wrapper">
      <main class="admin-page-content main-content">
        <div class="page-header">
          <h2 class="page-title">Edit Penjual - {{ $seller->store_name }}</h2>
          <a href="{{ route('sellers.index') }}" class="btn btn-secondary">
            Kembali ke Daftar
          </a>
        </div>

        <div class="form-container">
          <div class="form-header">
            <h3 class="form-title">Form Edit Penjual</h3>
          </div>
          <div class="form-content">
            <form action="{{ route('sellers.update', $seller) }}" method="POST">
              @csrf
              @method('PUT')
              
              <div class="mb-3">
                <label for="store_name" class="form-label">Nama Toko <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('store_name') is-invalid @enderror" id="store_name" name="store_name" value="{{ old('store_name', $seller->store_name) }}" required>
                @error('store_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="mb-3">
                <label for="owner_name" class="form-label">Nama Pemilik <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('owner_name') is-invalid @enderror" id="owner_name" name="owner_name" value="{{ old('owner_name', $seller->owner_name) }}" required>
                @error('owner_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $seller->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="mb-3">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                  <option value="aktif" {{ old('status', $seller->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                  <option value="ditangguhkan" {{ old('status', $seller->status) == 'ditangguhkan' ? 'selected' : '' }}>Ditangguhkan</option>
                  <option value="menunggu_verifikasi" {{ old('status', $seller->status) == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                  <option value="baru" {{ old('status', $seller->status) == 'baru' ? 'selected' : '' }}>Baru</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="mb-3">
                <button type="submit" class="btn btn-primary">Update Penjual</button>
                <a href="{{ route('sellers.index') }}" class="btn btn-secondary">Batal</a>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')
</body>
</html>