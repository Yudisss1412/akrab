@extends('layouts.app')

@section('title', 'Edit Profil')

@section('header')
  @include('components.customer.header.header_compact')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/profil/edit_profil.css') }}">
  <link rel="stylesheet" href="{{ asset('css/customer/profil/edit_profil_additional.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
@endpush

@section('content')
    <div class="page">
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

      <form class="edit-form" id="editProfileForm" action="{{ route('profil.pembeli.update') }}" method="POST" novalidate>
        @csrf
        @method('PUT')
        <div class="form-group field">
          <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required placeholder=" ">
          <label for="name">Nama Lengkap</label>
          <p class="error-message" id="name-error"></p>
        </div>

        <div class="form-group field">
          <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required placeholder=" ">
          <label for="email">Email</label>
          <p class="error-message" id="email-error"></p>
        </div>

        <div class="form-group field">
          <input type="tel" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required placeholder=" ">
          <label for="phone">Nomor Telepon</label>
          <p class="error-message" id="phone-error"></p>
        </div>

        <div class="form-group field">
          <input type="text" id="province" name="province" value="{{ old('province', auth()->user()->province ?? '') }}" required placeholder=" " />
          <label for="province">Provinsi</label>
          <p class="error-message" id="province-error"></p>
        </div>

        <div class="form-group field">
          <input type="text" id="city" name="city" value="{{ old('city', auth()->user()->city ?? '') }}" required placeholder=" " />
          <label for="city">Kota/Kabupaten</label>
          <p class="error-message" id="city-error"></p>
        </div>

        <div class="form-group field">
          <input type="text" id="district" name="district" value="{{ old('district', auth()->user()->district ?? '') }}" required placeholder=" " />
          <label for="district">Kecamatan</label>
          <p class="error-message" id="district-error"></p>
        </div>

        <div class="form-group field">
          <input type="text" id="ward" name="ward" value="{{ old('ward', auth()->user()->ward ?? '') }}" required placeholder=" " />
          <label for="ward">Kelurahan</label>
          <p class="error-message" id="ward-error"></p>
        </div>

        <div class="form-group field">
          <textarea id="full_address" name="full_address" rows="3" required placeholder=" ">{{ old('full_address', auth()->user()->full_address ?? auth()->user()->address ?? '') }}</textarea>
          <label for="full_address">Alamat Lengkap</label>
          <p class="error-message" id="full_address-error"></p>
        </div>

        <div class="form-group">
          <textarea id="bio" name="bio" rows="3" placeholder="Ceritakan sedikit tentang dirimu...">{{ old('bio', auth()->user()->bio ?? '') }}</textarea>
          <label for="bio">Bio</label>
        </div>

        <div class="form-actions">
          <button type="button" class="btn btn-secondary" onclick="history.back()">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
      <div id="formAlertContainer" class="form-alert-container"></div>
    </section>
    </div>
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
          previewContainer.innerHTML = '<img src="' + e.target.result + '" alt="Avatar" class="avatar-preview-image">';
        }
        reader.readAsDataURL(file);
      }
    });
  </script>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection