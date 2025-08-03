<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - UMKM AKRAB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&family=Inter:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>

    <div class="register-container">
        <div class="register-content">
            <div class="welcome-text">
                <h1>Gabung & Dukung<br>UMKM Lokal</h1>
                <p>Akses akunmu untuk mulai menjelajahi produk UMKM terbaik dari seluruh Indonesia.</p>
                <p>Belanja langsung dari para pelaku usaha lokal dengan mudah dan aman.</p>
            </div>
            <div class="register-form-box">
                <h2>Silahkan Daftar</h2>
                <form id="registerForm" novalidate>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                        <p class="error-message" id="email-error"></p>
                    </div>
                    <div class="form-group">
                        <label for="phone">No HP</label>
                        <input type="tel" id="phone" name="phone" required>
                        <p class="error-message" id="phone-error"></p>
                    </div>
                    <div class="form-group">
                        <label for="address">Alamat</label>
                        <input type="text" id="address" name="address" required>
                        <p class="error-message" id="address-error"></p>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
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
    <script src="js/register.js"></script>
</body>
</html>
