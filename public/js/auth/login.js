document.addEventListener('DOMContentLoaded', () => {
    const welcomeText = document.querySelector('.welcome-text');
    const loginFormBox = document.querySelector('.login-form-box');

    setTimeout(() => {
        if (welcomeText) welcomeText.classList.add('is-visible');
    }, 100);
    setTimeout(() => {
        if (loginFormBox) loginFormBox.classList.add('is-visible');
    }, 300);

    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;

    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('email-error');
    const passwordError = document.getElementById('password-error');

    // ===== NEW: daftar akun dummy (seller & buyer) + redirect masing-masing
    const dummyAccounts = [
        { email: 'seller@demo.test', password: 'password123', role: 'seller', redirect: '/dashboard_penjual' },
        { email: 'buyer@demo.test',  password: 'password123', role: 'buyer',  redirect: '/cust_welcome'  },
        { email: 'admin@demo.test',  password: 'password123', role: 'admin',  redirect: '/dashboard_admin' } // ← tambah ini
        // boleh tambah lagi:
        // { email: 'admin@demo.test', password: 'password123', role: 'admin', redirect: '/admin.html' },
    ];

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    loginForm.addEventListener('submit', (event) => {
        event.preventDefault();
        let isFormValid = true;

        // reset error state
        emailError.textContent = '';
        emailError.classList.remove('visible');
        emailInput.classList.remove('invalid');

        passwordError.textContent = '';
        passwordError.classList.remove('visible');
        passwordInput.classList.remove('invalid');

        const emailValue = emailInput.value.trim();
        const passwordValue = passwordInput.value.trim();

        // validasi dasar
        if (emailValue === '') {
            emailError.textContent = 'Harap isi bidang ini.';
            emailError.classList.add('visible');
            emailInput.classList.add('invalid');
            isFormValid = false;
        } else if (!isValidEmail(emailValue)) {
            emailError.textContent = 'Format email tidak valid.';
            emailError.classList.add('visible');
            emailInput.classList.add('invalid');
            isFormValid = false;
        }

        if (passwordValue === '') {
            passwordError.textContent = 'Harap isi bidang ini.';
            passwordError.classList.add('visible');
            passwordInput.classList.add('invalid');
            isFormValid = false;
        }

        // ===== CHANGED: cek ke daftar akun dummy, bukan ke 1 email/password statis
        let matchedAccount = null;
        if (isFormValid) {
            matchedAccount = dummyAccounts.find(acc =>
                acc.email === emailValue && acc.password === passwordValue
            );

            if (!matchedAccount) {
                emailError.textContent = 'Email atau sandi salah.';
                emailError.classList.add('visible');
                emailInput.classList.add('invalid');
                passwordInput.classList.add('invalid');
                isFormValid = false;
            }
        }

        if (!isFormValid) {
            return;
        }

        // ===== NEW: simpan "session" ringan (biar halaman tujuan bisa guard per role)
        localStorage.setItem('auth', JSON.stringify({
            email: matchedAccount.email,
            role: matchedAccount.role
        }));

        console.log('Formulir login valid, mengalihkan...');
        if (loginFormBox) {
            loginFormBox.style.transition = 'all 0.5s ease';
            loginFormBox.style.opacity = '0';
            loginFormBox.style.transform = 'scale(0.9)';
        }

        // ===== CHANGED: redirect sesuai akun (seller → /seller.html, buyer → /buyer.html)
        setTimeout(() => {
            window.location.href = matchedAccount.redirect;
        }, 500);
    });
});
