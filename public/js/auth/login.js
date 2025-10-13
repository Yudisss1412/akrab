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

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    loginForm.addEventListener('submit', async (event) => {
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

        if (!isFormValid) {
            return;
        }

        // Show loading state
        const submitButton = loginForm.querySelector('.login-button');
        const originalButtonText = submitButton.textContent;
        submitButton.textContent = 'Memproses...';
        submitButton.disabled = true;

        try {
            // Prepare form data
            const formData = new FormData();
            formData.append('email', emailValue);
            formData.append('password', passwordValue);
            formData.append('_token', document.querySelector('input[name="_token"]').value);

            // Make AJAX request to Laravel backend
            const response = await fetch('/login', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const responseData = await response.json();

            if (response.ok) {
                // Login successful - redirect to intended page or dashboard
                console.log('Login successful, redirecting...');
                
                if (loginFormBox) {
                    loginFormBox.style.transition = 'all 0.5s ease';
                    loginFormBox.style.opacity = '0';
                    loginFormBox.style.transform = 'scale(0.9)';
                }

                // Redirect to dashboard or intended page
                setTimeout(() => {
                    window.location.href = responseData.redirect || '/dashboard';
                }, 500);
            } else {
                // Login failed - show error message
                if (responseData.message) {
                    emailError.textContent = responseData.message;
                } else if (responseData.errors && responseData.errors.email) {
                    emailError.textContent = responseData.errors.email[0];
                } else if (responseData.errors && responseData.errors.password) {
                    passwordError.textContent = responseData.errors.password[0];
                } else {
                    emailError.textContent = 'Email atau sandi salah.';
                }
                
                emailError.classList.add('visible');
                emailInput.classList.add('invalid');
                passwordInput.classList.add('invalid');
            }
        } catch (error) {
            console.error('Login error:', error);
            emailError.textContent = 'Terjadi kesalahan saat login. Silakan coba lagi.';
            emailError.classList.add('visible');
            emailInput.classList.add('invalid');
            passwordInput.classList.add('invalid');
        } finally {
            // Reset button state
            submitButton.textContent = originalButtonText;
            submitButton.disabled = false;
        }
    });
});
