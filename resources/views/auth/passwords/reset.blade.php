<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - UMKM AKRAB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&family=Inter:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-content">
            <div class="welcome-text">
                <h1>Atur Ulang<br>Password!</h1>
                <p>Masukkan email dan password baru Anda untuk mengatur ulang password.</p>
            </div>
            <div class="login-form-box reset-password-form reset-password-page">
                <h2>Reset Password</h2>

                <form method="POST" action="{{ route('password.update') }}" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <input type="email" id="email" name="email" required placeholder=" " value="{{ old('email', $email ?? '') }}">
                        <label for="email">Email</label>
                        @error('email')
                            <p class="error-message" id="email-error">{{ $message }}</p>
                        @enderror
                        <p class="error-message" id="email-error"></p>
                    </div>

                    <div class="form-group">
                        <input type="password" id="password" name="password" required placeholder=" " minlength="8">
                        <label for="password">Password Baru</label>
                        @error('password')
                            <p class="error-message" id="password-error">{{ $message }}</p>
                        @enderror
                        <p class="error-message" id="password-error"></p>
                    </div>

                    <div class="form-group">
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder=" " minlength="8">
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        @error('password_confirmation')
                            <p class="error-message" id="password-confirmation-error">{{ $message }}</p>
                        @enderror
                        <p class="error-message" id="password-confirmation-error"></p>
                    </div>

                    <button type="submit" class="login-button">Reset Password</button>
                    <div class="form-links">
                        <a href="{{ route('login') }}">Kembali ke Login</a>
                        <a href="{{ route('register') }}">Belum punya akun? Daftar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/auth/password.js') }}" defer></script>
</body>
</html>