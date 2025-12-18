@extends('layouts.admin')

@section('title', 'Edit Pengguna - ' . $user->name . ' - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <style>
    .form-container {
      background: var(--white);
      border-radius: var(--ak-radius);
      border: 1px solid var(--ak-border);
      box-shadow: var(--box-shadow);
      overflow: hidden;
      transition: var(--transition);
      margin-top: 1.5rem;
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
@endpush

@section('content')
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content">
          <div class="page-header">
            <h2 class="page-title">Edit Pengguna - {{ $user->name }}</h2>
            <a href="{{ route('sellers.index', ['tab' => 'buyers']) }}" class="btn btn-secondary">
              Kembali ke Daftar
            </a>
          </div>

          <div class="form-container">
            <div class="form-header">
              <h3 class="form-title">Form Edit Pengguna</h3>
            </div>
            <div class="form-content">
              <form action="{{ route('sellers.update_user', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                  <label for="name" class="form-label required">Nama Lengkap</label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="email" class="form-label required">Alamat Email</label>
                  <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="status" class="form-label required">Status Akun</label>
                  <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="">Pilih Status</option>
                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                  </select>
                  @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-actions">
                  <button type="submit" class="btn btn-primary">Update Pengguna</button>
                  <a href="{{ route('sellers.index', ['tab' => 'buyers']) }}" class="btn btn-secondary">Batal</a>
                </div>
              </form>
            </div>
          </div>
        </main>
      </div>
    </div>
  </div>

  @include('components.admin_penjual.footer')
@endsection