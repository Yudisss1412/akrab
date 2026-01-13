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
            emailError.textContent = 'Harap di isi dengan benar!';
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
            passwordError.textContent = 'Harap di isi dengan benar!';
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
                // Login successful - show notification and redirect
                console.log('Login successful, showing notification and redirecting...');
                console.log('Response data:', responseData);
                
                if (loginFormBox) {
                    loginFormBox.style.transition = 'all 0.5s ease';
                    loginFormBox.style.opacity = '0';
                    loginFormBox.style.transform = 'scale(0.9)';
                }

                // Check if redirect URL exists in response
                const redirectUrl = responseData.redirect || '/dashboard';
                console.log('Redirect URL:', redirectUrl);

                // Show success toast notification in the center of screen
                const toast = document.createElement('div');
                toast.style.position = 'fixed';
                toast.style.top = '50%';
                toast.style.left = '50%';
                toast.style.transform = 'translate(-50%, -50%)';
                toast.style.backgroundColor = '#10b981';
                toast.style.color = 'white';
                toast.style.padding = '16px 24px';
                toast.style.borderRadius = '8px';
                toast.style.zIndex = '9999';
                toast.style.fontWeight = '600';
                toast.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
                toast.textContent = 'Login berhasil! Selamat datang kembali.';
                
                // Initially hidden
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s ease-in-out';
                
                try {
                    document.body.appendChild(toast);
                } catch (e) {
                    console.error('Error appending toast to document body:', e);
                }
                
                // Show with fade-in effect
                setTimeout(() => {
                    toast.style.opacity = '1';
                }, 10);

                // Redirect to dashboard or intended page after showing notification
                setTimeout(() => {
                    // Fade out before redirect
                    toast.style.opacity = '0';
                    
                    setTimeout(() => {
                        if (document.body.contains(toast)) {
                            try {
                                document.body.removeChild(toast);
                            } catch (e) {
                                console.error('Error removing toast from document body:', e);
                            }
                        }
                    }, 300);
                    
                    window.location.href = redirectUrl;
                }, 2500); // Wait for 2.5 seconds to allow toast to show and fade out
            } else {
                // Login failed - show error message
                if (responseData.message) {
                    emailError.textContent = responseData.message;
                } else if (responseData.errors && responseData.errors.email) {
                    emailError.textContent = responseData.errors.email[0];
                } else if (responseData.errors && responseData.errors.password) {
                    passwordError.textContent = responseData.errors.password[0];
                } else {
                    emailError.textContent = 'Email/Password Anda Salah';
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
