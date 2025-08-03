document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');
    if (!registerForm) return;
    const inputs = {
        email: document.getElementById('email'),
        phone: document.getElementById('phone'),
        address: document.getElementById('address'),
        password: document.getElementById('password')
    };

    const errors = {
        email: document.getElementById('email-error'),
        phone: document.getElementById('phone-error'),
        address: document.getElementById('address-error'),
        password: document.getElementById('password-error')
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

    registerForm.addEventListener('submit', (event) => {
        event.preventDefault();
        let isFormValid = true;
        Object.keys(inputs).forEach(field => clearError(field));
        if (inputs.email.value.trim() === '') {
            showError('email', 'Harap isi bidang ini.');
            isFormValid = false;
        } else if (!isValidEmail(inputs.email.value.trim())) {
            showError('email', 'Format email tidak valid.');
            isFormValid = false;
        }
        if (inputs.phone.value.trim() === '') {
            showError('phone', 'Harap isi bidang ini.');
            isFormValid = false;
        }
        if (inputs.address.value.trim() === '') {
            showError('address', 'Harap isi bidang ini.');
            isFormValid = false;
        }
        if (inputs.password.value.trim() === '') {
            showError('password', 'Harap isi bidang ini.');
            isFormValid = false;
        } else if (inputs.password.value.trim().length < 8) {
            showError('password', 'Sandi minimal 8 karakter.');
            isFormValid = false;
        }
        if (isFormValid) {
            console.log('Formulir pendaftaran valid, mengalihkan ke halaman profil...');
            window.location.href = '/profile';
        }
    });
});
