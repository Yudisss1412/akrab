@extends('layouts.admin')

@section('title', 'Edit Pengguna - ' . $user->name . ' - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <style>
    .user-form-container {
      max-width: 600px;
      margin: 0 auto;
    }

    .form-card {
      background: white;
      border-radius: var(--ak-radius);
      border: 1px solid var(--ak-border);
      box-shadow: 0 8px 20px rgba(0,0,0,.05);
      padding: 2rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #495057;
    }

    .form-control {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid var(--border);
      border-radius: var(--ak-radius);
      font-size: 1rem;
      transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
      border-color: #006E5C;
      outline: 0;
      box-shadow: 0 0 0 0.2rem rgba(0, 110, 92, 0.25);
    }

    .btn-submit {
      background-color: #006E5C;
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: var(--ak-radius);
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    .btn-submit:hover {
      background-color: #005a4a;
    }

    .btn-back {
      background-color: #6c757d;
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: var(--ak-radius);
      font-size: 1rem;
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      margin-right: 0.5rem;
      transition: background-color 0.2s ease;
    }

    .btn-back:hover {
      background-color: #5a6268;
      color: white;
    }
  </style>
@endpush

@section('content')
  <div class="container-fluid">
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="admin-page-content">
          <div class="user-form-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h1 class="page-title">Edit Pengguna</h1>
            </div>

            <div class="form-card">
              <form action="{{ route('sellers.update_user', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                  <label for="name" class="form-label">Nama</label>
                  <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                  @error('name')
                    <div class="text-danger mt-1">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                  @error('email')
                    <div class="text-danger mt-1">{{ $message }}</div>
                  @enderror
                </div>

                <div class="form-group">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-control" id="status" name="status">
                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="suspended" {{ $user->status == 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                  </select>
                </div>

                <div class="mt-4">
                  <a href="{{ route('sellers.index', ['tab' => 'buyers']) }}" class="btn-back">Batal</a>
                  <button type="submit" class="btn-submit">Simpan Perubahan</button>
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