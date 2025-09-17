@extends('layouts.app')

@section('title', 'Edit Profil')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/profil/edit_profil.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')
  <main class="page">
    <section class="card form-card" aria-labelledby="title-edit-profile">
      <header class="form-head">
        <h1 id="title-edit-profile">Edit profil</h1>
        <div class="avatar-box">
          <!-- Foto bisa diklik -->
          <label for="avatarInput" class="avatar-label">
            <img id="avatarPreview" src="{{ $user->avatar_url ?? 'default.png' }}" alt="Avatar">
          </label>
          <input type="file" id="avatarInput" name="avatar" accept="image/*" hidden>
        </div>
      </header>

      <form class="edit-form" id="editProfileForm">
        <div class="form-group">
          <label for="name">Nama Lengkap</label>
          <input type="text" id="name" name="name" value="{{ $user->name ?? '' }}" required>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="{{ $user->email ?? '' }}" required>
        </div>

        <div class="form-group">
          <label for="phone">Nomor Telepon</label>
          <input type="tel" id="phone" name="phone" value="{{ $user->phone ?? '' }}" required>
        </div>

        <div class="form-group">
          <label for="address">Alamat</label>
          <textarea id="address" name="address" rows="3" required>{{ $user->address ?? '' }}</textarea>
        </div>

        <div class="form-group">
          <label for="bio">Bio</label>
          <textarea id="bio" name="bio" rows="3" placeholder="Ceritakan sedikit tentang dirimu...">{{ $user->bio ?? '' }}</textarea>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="history.back()">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </section>
  </main>
@endsection

@push('scripts')
  <script>
    // Preview avatar saat dipilih
    document.getElementById('avatarInput').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
      }
    });

    // Form submission
    document.getElementById('editProfileForm').addEventListener('submit', function(e) {
      e.preventDefault();
      // Simulasi simpan data
      alert('Profil berhasil diperbarui!');
      // Redirect ke halaman profil
      window.location.href = '{{ route("profil.pembeli") }}';
    });
  </script>
@endpush