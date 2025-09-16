<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - UMKM AKRAB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&family=Inter:wght@400&display=swap" rel="stylesheet">
    <!-- Pastikan path ke file CSS sudah benar -->
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
</head>
<body>
    <div class="register-container">
        <div class="register-content">
            <div class="welcome-text">
                <h1>Gabung & Dukung<br>UMKM Lokal</h1>
                <p>Buat akunmu untuk mulai menjelajahi produk UMKM terbaik dari seluruh Indonesia.</p>
                <p>Belanja langsung dari para pelaku usaha lokal dengan mudah dan aman.</p>
            </div>
            <div class="register-form-box">
                <h2>Silahkan Daftar</h2>
                <form id="registerForm" novalidate>
                    <!-- [DIUBAH] Struktur di dalam setiap form-group diperbaiki. -->
                    <!-- Elemen <input> sekarang berada SEBELUM <label>. -->
                    <div class="form-group">
                        <input type="email" id="email" name="email" required placeholder=" ">
                        <label for="email">Email</label>
                        <p class="error-message" id="email-error"></p>
                    </div>
                    <div class="form-group">
                        <input type="tel" id="phone" name="phone" required placeholder=" ">
                        <label for="phone">No HP</label>
                        <p class="error-message" id="phone-error"></p>
                    </div>
                    <div class="form-group">
                        <input type="text" id="address" name="address" required placeholder=" ">
                        <label for="address">Alamat</label>
                        <p class="error-message" id="address-error"></p>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" required placeholder=" ">
                        <label for="password">Password</label>
                        <p class="error-message" id="password-error"></p>
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
    <script src="{{ asset('js/register.js') }}" defer></script>
</body>
</html>
