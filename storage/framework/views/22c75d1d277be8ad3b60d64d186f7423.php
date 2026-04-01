<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UMKM AKRAB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&family=Inter:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/auth/login.css')); ?>">
</head>
<body>
    <div class="login-container">
        <div class="login-content">
            <div class="welcome-text">
                <h1>Selamat Datang<br>Kembali!</h1>
                <p>Akses akunmu untuk mulai menjelajahi produk UMKM terbaik dari seluruh Indonesia.</p>
                <p>Belanja langsung dari para pelaku usaha lokal dengan mudah dan aman.</p>
            </div>
            <div class="login-form-box login-page">
                <h2>Silahkan Masuk</h2>
                <form id="loginForm" method="POST" action="<?php echo e(route('login')); ?>" novalidate>
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <input type="email" id="email" name="email" required placeholder=" " value="<?php echo e(old('email')); ?>">
                        <label for="email">Email</label>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="error-message" id="email-error"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="error-message" id="email-error"></p>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" required placeholder=" ">
                        <label for="password">Password</label>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="error-message" id="password-error"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="error-message" id="password-error"></p>
                    </div>
                    <button type="submit" class="login-button">Masuk</button>
                    <div class="form-links">
                        <a href="<?php echo e(route('password.request')); ?>">Lupa Password?</a>
                        <a href="/register">Belum punya akun? Daftar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="<?php echo e(asset('js/auth/login.js')); ?>" defer></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\ecommerce-akrab\resources\views/auth/login.blade.php ENDPATH**/ ?>