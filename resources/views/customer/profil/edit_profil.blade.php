<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Profil</title>
  <link rel="stylesheet" href="{{ asset('css/customer/profil/edit_profil.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
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

      <form id="editProfileForm" novalidate>
        <!-- Nama depan & belakang -->
        <div class="grid two">
          <div class="field">
            <label for="firstName">Nama depan</label>
            <input id="firstName" name="firstName" type="text" placeholder="Andi" autocomplete="given-name" required aria-describedby="firstName-error">
            <small id="firstName-error" class="error-message" role="alert" aria-live="polite"></small>
          </div>

          <div class="field">
            <label for="lastName">Nama belakang</label>
            <input id="lastName" name="lastName" type="text" placeholder="Saputra" autocomplete="family-name" required aria-describedby="lastName-error">
            <small id="lastName-error" class="error-message" role="alert" aria-live="polite"></small>
          </div>
        </div>

        <!-- Email -->
        <div class="field with-icon">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" placeholder="nama@email.com" autocomplete="email" required aria-describedby="email-error">
          <span class="suffix-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </span>
          <small id="email-error" class="error-message" role="alert" aria-live="polite"></small>
        </div>

        <!-- Alamat -->
        <div class="field">
          <label for="address">Alamat</label>
          <input id="address" name="address" type="text" placeholder="Jl. Melati No. 123, Bandung" autocomplete="street-address" required aria-describedby="address-error">
          <small id="address-error" class="error-message" role="alert" aria-live="polite"></small>
        </div>

        <!-- Nomor kontak -->
        <div class="field">
          <label for="phone">Nomor kontak</label>
          <input id="phone" name="phone" type="tel" placeholder="+62…" autocomplete="tel" required aria-describedby="phone-error">
          <small id="phone-error" class="error-message" role="alert" aria-live="polite"></small>
        </div>

        <!-- Kata sandi -->
        <div class="field with-icon">
          <label for="password">Kata sandi</label>
          <input id="password" name="password" type="password" placeholder="••••••••" autocomplete="new-password" required aria-describedby="password-error password-hint">
          <button type="button" class="showhide" aria-label="Tampilkan/sembunyikan kata sandi">👁</button>
          <span class="suffix-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </span>
          <small id="password-hint" class="hint">Minimal 8 karakter, ada huruf besar, huruf kecil, angka, & simbol.</small>
          <small id="password-error" class="error-message" role="alert" aria-live="polite"></small>
        </div>

        <!-- Aksi -->
        <div class="actions">
          <a href="{{ url()->previous() }}" class="btn btn-ghost" role="button">Batal</a>
          <button type="submit" class="btn btn-primary" id="saveBtn" disabled>Simpan</button>
        </div>
      </form>
    </section>

    <div id="toast" class="toast" role="status" aria-live="polite" aria-atomic="true"></div>
  </main>

  <script src="{{ asset('js/edit_profil.js') }}"></script>
</body>
</html>
