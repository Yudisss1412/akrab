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
      --border-radius: 8px;
      --transition: all 0.3s ease;
      --ak-radius: 8px;
      --ak-border: #dee2e6;
      --ak-bg: #f8f9fa;
      --ak-text: #333;
      --ak-muted: #6c757d;
    }

    body {
      background-color: var(--ak-bg);
      font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: var(--ak-text);
    }

    .main-content {
      padding-top: 2rem;
      padding-bottom: 2rem;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      padding: 0.25rem 0;
    }

    .page-title {
      margin: 0;
      font-size: 1.5rem;
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
      border-radius: var(--ak-radius);
      border: 1px solid var(--ak-border);
      box-shadow: var(--box-shadow);
      overflow: hidden;
      transition: var(--transition);
    }

    .form-container:hover {
      box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.2);
    }

    .form-header {
      padding: 1.5rem;
      background: linear-gradient(135deg, var(--primary) 0%, #00806d 100%);
      color: white;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .form-title {
      margin: 0;
      font-size: 1.2rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .form-content {
      padding: 1.75rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-label {
      font-weight: 600;
      color: var(--ak-muted);
      margin-bottom: 0.5rem;
      display: block;
    }

    .form-control, .form-select {
      border: 1px solid var(--ak-border);
      border-radius: var(--ak-radius);
      padding: 0.75rem;
      font-size: 1rem;
      background: var(--white);
      transition: var(--transition);
      width: 100%;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 110, 92, 0.1);
      outline: none;
    }

    .form-control {
      height: auto;
    }

    .btn {
      border-radius: var(--ak-radius);
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: var(--transition);
      border: none;
      font-size: 1rem;
      cursor: pointer;
    }

    .btn-primary {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
    }

    .btn-primary:hover {
      background: #005a4a;
      border-color: #005a4a;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 110, 92, 0.2);
    }

    .btn-secondary {
      background: var(--ak-bg);
      color: var(--ak-text);
      border-color: var(--ak-border);
    }

    .btn-secondary:hover {
      background: #e2e6ea;
      color: var(--ak-text);
      border-color: #adb5bd;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(108, 117, 125, 0.2);
    }

    .btn-warning {
      background: var(--warning);
      color: #212529;
      border-color: var(--warning);
    }

    .btn-warning:hover {
      background: #e0a800;
      color: #212529;
      border-color: #d39e00;
    }

    .btn-danger {
      background: var(--danger);
      color: white;
      border-color: var(--danger);
    }

    .btn-danger:hover {
      background: #c82333;
      color: white;
      border-color: #bd2130;
    }

    .invalid-feedback {
      display: block;
      width: 100%;
      margin-top: 0.5rem;
      font-size: 0.875rem;
      color: var(--danger);
    }

    .form-control.is-invalid {
      border-color: var(--danger);
      box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }

    .form-actions {
      display: flex;
      gap: 1rem;
      margin-top: 2rem;
      padding-top: 1.5rem;
      border-top: 1px solid var(--ak-border);
    }

    .form-actions .btn {
      padding: 0.75rem 2rem;
    }

    .card {
      box-shadow: var(--box-shadow);
      border: none;
      border-radius: var(--ak-radius);
      transition: var(--transition);
    }

    .required::after {
      content: " *";
      color: var(--danger);
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

              <div class="form-group">
                <label for="store_name" class="form-label required">Nama Toko</label>
                <input type="text" class="form-control @error('store_name') is-invalid @enderror" id="store_name" name="store_name" value="{{ old('store_name', $seller->store_name) }}" required>
                @error('store_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="owner_name" class="form-label required">Nama Pemilik</label>
                <input type="text" class="form-control @error('owner_name') is-invalid @enderror" id="owner_name" name="owner_name" value="{{ old('owner_name', $seller->owner_name) }}" required>
                @error('owner_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="email" class="form-label required">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $seller->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-group">
                <label for="status" class="form-label required">Status</label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                  <option value="">Pilih Status</option>
                  <option value="aktif" {{ old('status', $seller->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                  <option value="ditangguhkan" {{ old('status', $seller->status) == 'ditangguhkan' ? 'selected' : '' }}>Ditangguhkan</option>
                  <option value="menunggu_verifikasi" {{ old('status', $seller->status) == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                  <option value="baru" {{ old('status', $seller->status) == 'baru' ? 'selected' : '' }}>Baru</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="form-actions">
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
