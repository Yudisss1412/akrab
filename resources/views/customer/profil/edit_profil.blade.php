@extends('layouts.app')

@section('title', 'Edit Profil')

@section('header')
  @include('components.header_compact')
@endsection

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
            <div class="avatar-preview" id="avatarPreview">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                <path d="M12 10C14.2091 10 16 8.20914 16 6C16 3.79086 14.2091 2 12 2C9.79086 2 8 3.79086 8 6C8 8.20914 9.79086 10 12 10Z" stroke="#006E5C" stroke-width="1.5"/>
                <path d="M20 17.5C20 19.985 20 22 12 22C4 22 4 19.985 4 17.5C4 15.015 7.582 13 12 13C16.418 13 20 15.015 20 17.5Z" stroke="#006E5C" stroke-width="1.5"/>
              </svg>
            </div>
          </label>
          <input type="file" id="avatarInput" name="avatar" accept="image/*" hidden>
        </div>
      </header>

      <form class="edit-form" id="editProfileForm" novalidate>
        <div class="form-group field">
          <input type="text" id="name" name="name" value="{{ $user->name ?? '' }}" required placeholder=" ">
          <label for="name">Nama Lengkap</label>
          <p class="error-message" id="name-error"></p>
        </div>

        <div class="form-group field">
          <input type="email" id="email" name="email" value="{{ $user->email ?? '' }}" required placeholder=" ">
          <label for="email">Email</label>
          <p class="error-message" id="email-error"></p>
        </div>

        <div class="form-group field">
          <input type="tel" id="phone" name="phone" value="{{ $user->phone ?? '' }}" required placeholder=" ">
          <label for="phone">Nomor Telepon</label>
          <p class="error-message" id="phone-error"></p>
        </div>

        <div class="form-group field">
          <textarea id="address" name="address" rows="3" required placeholder=" ">{{ $user->address ?? '' }}</textarea>
          <label for="address">Alamat</label>
          <p class="error-message" id="address-error"></p>
        </div>

        <div class="form-group">
          <textarea id="bio" name="bio" rows="3" placeholder="Ceritakan sedikit tentang dirimu...">{{ $user->bio ?? '' }}</textarea>
          <label for="bio">Bio</label>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="history.back()">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
      <div id="formAlertContainer" class="form-alert-container"></div>
    </section>
  </main>
@endsection

@push('scripts')
  <script>
    // Define profile route for use in edit_profil.js
    window.PROFIL_ROUTE = '{{ route("profil.pembeli") }}';
  </script>
  <script src="{{ asset('js/customer/profil/edit_profil.js') }}"></script>
  <script>
    // Preview avatar saat dipilih
    document.getElementById('avatarInput').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          const previewContainer = document.getElementById('avatarPreview');
          previewContainer.innerHTML = '<img src="' + e.target.result + '" alt="Avatar" style="width:72px; height:72px; border-radius:50%; object-fit:cover;">';
        }
        reader.readAsDataURL(file);
      }
    });
  </script>
@endpush