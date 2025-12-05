document.addEventListener('DOMContentLoaded', () => {
    const welcomeText = document.querySelector('.welcome-text');
    const passwordFormBox = document.querySelector('.login-form-box');
    setTimeout(() => {
        if(welcomeText) welcomeText.classList.add('is-visible');
    }, 100);
    setTimeout(() => {
        if(passwordFormBox) passwordFormBox.classList.add('is-visible');
    }, 300);
});