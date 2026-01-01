@extends('layouts.admin')

@section('title', 'Edit Pengguna - ' . $user->name . ' - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin/dashboard_admin.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin/edit_user.css') }}">
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
