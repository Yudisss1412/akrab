document.addEventListener('DOMContentLoaded', () => {
    const welcomeText = document.querySelector('.welcome-text');
    const registerFormBox = document.querySelector('.register-form-box');
    setTimeout(() => {
        if(welcomeText) welcomeText.classList.add('is-visible');
    }, 100);
    setTimeout(() => {
        if(registerFormBox) registerFormBox.classList.add('is-visible');
    }, 300);
    const registerForm = document.getElementById('registerForm');
    if (!registerForm) return;

    const inputs = {
        name: document.getElementById('name'),
        email: document.getElementById('email'),
        password: document.getElementById('password'),
        password_confirmation: document.getElementById('password_confirmation')
    };

    const errors = {
        name: document.getElementById('name-error'),
        email: document.getElementById('email-error'),
        password: document.getElementById('password-error'),
        password_confirmation: document.getElementById('password-confirmation-error')
    };

    function showError(field, message) {
        errors[field].textContent = message;
        errors[field].classList.add('visible');
        inputs[field].classList.add('invalid');
    }

    function clearError(field) {
        errors[field].textContent = '';
        errors[field].classList.remove('visible');
        inputs[field].classList.remove('invalid');
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    registerForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        let isFormValid = true;
        Object.keys(inputs).forEach(field => clearError(field));

        if (inputs.name.value.trim() === '') {
            showError('name', 'Harap isi bidang ini.');
            isFormValid = false;
        }

        if (inputs.email.value.trim() === '') {
            showError('email', 'Harap isi bidang ini.');
            isFormValid = false;
        } else if (!isValidEmail(inputs.email.value.trim())) {
            showError('email', 'Format email tidak valid.');
            isFormValid = false;
        }

        if (inputs.password.value.trim() === '') {
            showError('password', 'Harap isi bidang ini.');
            isFormValid = false;
        } else if (inputs.password.value.trim().length < 8) {
            showError('password', 'Sandi minimal 8 karakter.');
            isFormValid = false;
        }

        if (inputs.password_confirmation.value.trim() !== inputs.password.value.trim()) {
            showError('password_confirmation', 'Konfirmasi password tidak cocok.');
            isFormValid = false;
        }

        if (!isFormValid) {
            return;
        }

        // Show loading state
        const submitButton = registerForm.querySelector('.register-button');
        const originalButtonText = submitButton.textContent;
        submitButton.textContent = 'Memproses...';
        submitButton.disabled = true;

        try {
            // Prepare form data
            const formData = new FormData();
            formData.append('name', inputs.name.value.trim());
            formData.append('email', inputs.email.value.trim());
            formData.append('password', inputs.password.value.trim());
            formData.append('password_confirmation', inputs.password_confirmation.value.trim());
            formData.append('_token', document.querySelector('input[name="_token"]').value);

            // Make AJAX request to Laravel backend
            const response = await fetch(registerForm.getAttribute('action'), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                // Registration successful
                console.log('Registration successful, redirecting...');
                
                registerFormBox.style.transition = 'all 0.5s ease';
                registerFormBox.style.opacity = '0';
                registerFormBox.style.transform = 'scale(0.9)';

                // Redirect to login page after successful registration
                setTimeout(() => {
                    window.location.href = '/login'; 
                }, 500);
            } else {
                // Handle validation errors
                const responseData = await response.json();
                
                if (responseData.errors) {
                    Object.keys(responseData.errors).forEach(field => {
                        if (errors[field]) {
                            showError(field, responseData.errors[field][0]);
                        }
                    });
                } else {
                    // General error message
                    showError('email', 'Terjadi kesalahan saat pendaftaran. Silakan coba lagi.');
                }
            }
        } catch (error) {
            console.error('Registration error:', error);
            showError('email', 'Terjadi kesalahan jaringan. Silakan coba lagi.');
        } finally {
            // Reset button state
            submitButton.textContent = originalButtonText;
            submitButton.disabled = false;
        }
    });
});
