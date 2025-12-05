<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - UMKM AKRAB</title>
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
                <p>Masukkan alamat email Anda dan kami akan kirimkan link untuk mengatur ulang password Anda.</p>
            </div>
            <div class="login-form-box password-request-form password-request-page">
                <h2>Lupa Password</h2>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" novalidate>
                    @csrf
                    <div class="form-group">
                        <input type="email" id="email" name="email" required placeholder=" " value="{{ old('email') }}">
                        <label for="email">Email</label>
                        @error('email')
                            <p class="error-message" id="email-error">{{ $message }}</p>
                        @enderror
                        <p class="error-message" id="email-error"></p>
                    </div>
                    <button type="submit" class="login-button">Kirim Link Atur Ulang</button>
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