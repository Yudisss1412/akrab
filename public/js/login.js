document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('email-error');
    const passwordError = document.getElementById('password-error');
    const correctEmail = 'user@gmail.com';
    const correctPassword = 'password123';
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    loginForm.addEventListener('submit', (event) => {
        event.preventDefault();
        emailError.textContent = '';
        emailError.classList.remove('visible');
        emailInput.classList.remove('invalid');
        
        passwordError.textContent = '';
        passwordError.classList.remove('visible');
        passwordInput.classList.remove('invalid');

        const emailValue = emailInput.value.trim();
        const passwordValue = passwordInput.value.trim();
        if (emailValue === '') {
            emailError.textContent = 'Harap isi bidang ini.';
            emailError.classList.add('visible');
            emailInput.classList.add('invalid');
            return;
        }
        if (!isValidEmail(emailValue)) {
            emailError.textContent = 'Format email tidak valid.';
            emailError.classList.add('visible');
            emailInput.classList.add('invalid');
            return;
        }
        if (passwordValue === '') {
            passwordError.textContent = 'Harap isi bidang ini.';
            passwordError.classList.add('visible');
            passwordInput.classList.add('invalid');
            return;
        }
        if (emailValue !== correctEmail || passwordValue !== correctPassword) {
            emailError.textContent = 'Email atau sandi salah.';
            emailError.classList.add('visible');
            emailInput.classList.add('invalid');
            passwordInput.classList.add('invalid');
            return;
        }
        console.log('Formulir valid, mengalihkan ke halaman profil...');
        window.location.href = '/profile';
    });
});
