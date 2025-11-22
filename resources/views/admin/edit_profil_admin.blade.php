<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Edit Profil Admin — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/penjual/edit_profil_penjual.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
</head>
<body>
  @include('components.admin_penjual.header')

  <div class="main-layout">
    <div class="content-wrapper">
      <main class="content admin-page-content" role="main">
        <div class="page">
          <section class="card form-card" aria-labelledby="title-edit-profile">
          <header class="form-head">
            <h1 id="title-edit-profile">Edit Profil Admin</h1>
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

          <form class="edit-form" id="editAdminProfileForm" action="{{ route('profil.admin.update') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')
            <div class="form-group field">
              <input type="text" id="name" name="name" value="{{ old('name', $admin->name) }}" required placeholder=" ">
              <label for="name">Nama Lengkap</label>
              <p class="error-message" id="name-error"></p>
            </div>

            <div class="form-group field">
              <input type="email" id="email" name="email" value="{{ old('email', $admin->email) }}" required placeholder=" ">
              <label for="email">Email</label>
              <p class="error-message" id="email-error"></p>
            </div>

            <div class="form-group field">
              <input type="tel" id="phone" name="phone" value="{{ old('phone', $admin->phone ?? '+62 812-3456-7890') }}" placeholder=" ">
              <label for="phone">Nomor Telepon</label>
              <p class="error-message" id="phone-error"></p>
            </div>

            <div class="form-group field">
              <input type="text" id="position" name="position" value="{{ old('position', $admin->role->name ?? 'Administrator') }}" required placeholder=" ">
              <label for="position">Jabatan</label>
              <p class="error-message" id="position-error"></p>
            </div>

            <div class="form-group field">
              <input type="text" id="accessLevel" name="accessLevel" value="{{ old('accessLevel', 'Super Admin') }}" required placeholder=" ">
              <label for="accessLevel">Level Akses</label>
              <p class="error-message" id="accessLevel-error"></p>
            </div>

            <div class="form-group field">
              <textarea id="bio" name="bio" rows="3" required placeholder=" ">{{ old('bio', 'Administrator utama platform AKRAB dengan akses penuh terhadap semua fitur dan pengelolaan sistem.') }}</textarea>
              <label for="bio">Deskripsi</label>
              <p class="error-message" id="bio-error"></p>
            </div>

            <div class="form-group field">
              <input type="password" id="currentPassword" name="currentPassword" placeholder=" " autocomplete="current-password">
              <label for="currentPassword">Password Saat Ini</label>
              <p class="error-message" id="currentPassword-error"></p>
            </div>

            <div class="form-group field">
              <input type="password" id="newPassword" name="newPassword" placeholder=" " autocomplete="new-password">
              <label for="newPassword">Password Baru</label>
              <p class="error-message" id="newPassword-error"></p>
            </div>

            <div class="form-group field">
              <input type="password" id="confirmPassword" name="confirmPassword" placeholder=" " autocomplete="new-password">
              <label for="confirmPassword">Konfirmasi Password Baru</label>
              <p class="error-message" id="confirmPassword-error"></p>
            </div>

            @csrf
            @method('PUT')
            <div class="form-actions">
              <a href="{{ route('profil.admin') }}" class="btn btn-secondary">Batal</a>
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </form>
          <div id="formAlertContainer" class="form-alert-container"></div>
        </section>
        </div> <!-- end of page -->
      </main>
    </div> <!-- end of content-wrapper -->
  </div> <!-- end of main-layout -->

  @include('components.admin_penjual.footer')

  <script src="{{ asset('js/admin/edit_profil_admin.js') }}"></script>
</body>
</html>