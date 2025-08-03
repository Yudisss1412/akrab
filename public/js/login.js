document.addEventListener('DOMContentLoaded', () => {
    const welcomeText = document.querySelector('.welcome-text');
    const loginFormBox = document.querySelector('.login-form-box');
    setTimeout(() => {
        if(welcomeText) welcomeText.classList.add('is-visible');
    }, 100);
    setTimeout(() => {
        if(loginFormBox) loginFormBox.classList.add('is-visible');
    }, 300);
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
        let isFormValid = true;
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

        if (isFormValid && (emailValue !== correctEmail || passwordValue !== correctPassword)) {
            emailError.textContent = 'Email atau sandi salah.';
            emailError.classList.add('visible');
            emailInput.classList.add('invalid');
            passwordInput.classList.add('invalid');
            isFormValid = false;
        }
        
        if (!isFormValid) {
            loginFormBox.classList.add('shake');
            setTimeout(() => {
                loginFormBox.classList.remove('shake');
            }, 500);
            return;
        }
        console.log('Formulir valid, mengalihkan ke halaman profil...');
        loginFormBox.style.transition = 'all 0.5s ease';
        loginFormBox.style.opacity = '0';
        loginFormBox.style.transform = 'scale(0.9)';

        setTimeout(() => {
            window.location.href = '/profile'; 
        }, 500);
    });
});
