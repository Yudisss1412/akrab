<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - UMKM AKRAB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&family=Inter:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
</head>
<body class="js-enabled">
    <div class="register-container">
        <div class="register-content">
            <div class="welcome-text is-visible">
                <h1>Gabung & Dukung<br>UMKM Lokal</h1>
                <p>Buat akunmu untuk mulai menjelajahi produk UMKM terbaik dari seluruh Indonesia.</p>
                <p>Belanja langsung dari para pelaku usaha lokal dengan mudah dan aman.</p>
            </div>
            <div class="login-form-box register-page is-visible">
                <h2>Silahkan Daftar</h2>
                <form id="registerForm" method="POST" action="{{ route('register') }}" novalidate>
                    @csrf
                    <!-- [DIUBAH] Struktur di dalam setiap form-group diperbaiki. -->
                    <!-- Elemen <input> sekarang berada SEBELUM <label>. -->
                    <div class="form-group">
                        <input type="text" id="name" name="name" required placeholder=" " value="{{ old('name') }}">
                        <label for="name">Nama Lengkap</label>
                        @error('name')
                            <p class="error-message" id="name-error">{{ $message }}</p>
                        @enderror
                        <p class="error-message" id="name-error"></p>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" required placeholder=" " value="{{ old('email') }}">
                        <label for="email">Email</label>
                        @error('email')
                            <p class="error-message" id="email-error">{{ $message }}</p>
                        @enderror
                        <p class="error-message" id="email-error"></p>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" required placeholder=" ">
                        <label for="password">Password</label>
                        @error('password')
                            <p class="error-message" id="password-error">{{ $message }}</p>
                        @enderror
                        <p class="error-message" id="password-error"></p>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder=" ">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <p class="error-message" id="password-confirmation-error"></p>
                    </div>
                    <button type="submit" class="register-button">Daftar</button>
                    <div class="form-links">
                        <a href="/login">Sudah punya akun? Masuk</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Pastikan path ke file JS sudah benar -->
    <script src="{{ asset('js/auth/register.js') }}" defer></script>
</body>
</html>
