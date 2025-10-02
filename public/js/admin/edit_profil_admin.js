// Script untuk mengelola floating label dan validasi form di edit profil admin
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('editAdminProfileForm');
  const fields = [
    'adminName', 'username', 'email', 'phone', 
    'position', 'accessLevel', 'bio', 
    'currentPassword', 'newPassword', 'confirmPassword'
  ];
  
  // Fungsi untuk mengelola floating label
  fields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
      // Periksa apakah field memiliki nilai saat dimuat
      if (field.value) {
        field.classList.add('has-value');
      }
      
      // Tambahkan event listener untuk floating label
      field.addEventListener('focus', function() {
        this.parentElement.classList.add('active');
      });
      
      field.addEventListener('blur', function() {
        if (this.value === '') {
          this.parentElement.classList.remove('active');
        }
      });
    }
  });
  
  // Validasi form sebelum submit
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Reset error messages
    document.querySelectorAll('.error-message').forEach(el => {
      el.classList.remove('visible');
      el.textContent = '';
    });
    
    // Reset field states
    document.querySelectorAll('.form-group').forEach(group => {
      group.classList.remove('invalid', 'valid');
    });
    
    let isValid = true;
    const formData = new FormData(form);
    
    // Validasi email
    const emailField = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (formData.get('email') && !emailRegex.test(formData.get('email'))) {
      document.getElementById('email-error').textContent = 'Format email tidak valid';
      document.getElementById('email-error').classList.add('visible');
      emailField.parentElement.classList.add('invalid');
      isValid = false;
    }
    
    // Validasi nomor telepon
    const phoneField = document.getElementById('phone');
    const phoneRegex = /^[+]?[\d\s\-\(\)]+$/;
    if (formData.get('phone') && !phoneRegex.test(formData.get('phone'))) {
      document.getElementById('phone-error').textContent = 'Format nomor telepon tidak valid';
      document.getElementById('phone-error').classList.add('visible');
      phoneField.parentElement.classList.add('invalid');
      isValid = false;
    }
    
    // Validasi password jika diisi
    if (formData.get('newPassword')) {
      // Panjang password minimal 8 karakter
      if (formData.get('newPassword').length < 8) {
        document.getElementById('newPassword-error').textContent = 'Password minimal 8 karakter';
        document.getElementById('newPassword-error').classList.add('visible');
        document.querySelector('#newPassword').parentElement.classList.add('invalid');
        isValid = false;
      }
      
      // Cek apakah password baru dan konfirmasi cocok
      if (formData.get('newPassword') !== formData.get('confirmPassword')) {
        document.getElementById('confirmPassword-error').textContent = 'Password baru dan konfirmasi password tidak cocok';
        document.getElementById('confirmPassword-error').classList.add('visible');
        document.querySelector('#confirmPassword').parentElement.classList.add('invalid');
        isValid = false;
      }
    }
    
    if (isValid) {
      // Tampilkan pesan sukses sementara atau submit form
      showSuccessAlert('Profil admin berhasil diperbarui!');
      
      // Simulasi submit form setelah delay
      setTimeout(() => {
        form.submit();
      }, 1500);
    }
  });
  
  // Preview avatar
  const avatarInput = document.getElementById('avatarInput');
  const avatarPreview = document.getElementById('avatarPreview');
  
  avatarInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(event) {
        const img = document.createElement('img');
        img.src = event.target.result;
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.borderRadius = '50%';
        img.style.objectFit = 'cover';
        avatarPreview.innerHTML = '';
        avatarPreview.appendChild(img);
      };
      reader.readAsDataURL(file);
    }
  });
  
  // Toggle show/hide password
  const passwordFields = ['currentPassword', 'newPassword', 'confirmPassword'];
  passwordFields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
      // Tambahkan tombol show/hide password
      field.type = 'password';
      
      // Cek apakah tombol show/hide sudah ada sebelum menambahkan
      const existingToggleBtn = field.parentElement.querySelector('.showhide');
      if (!existingToggleBtn) {
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'showhide';
        toggleBtn.textContent = 'Lihat';
        toggleBtn.addEventListener('click', function() {
          const isPassword = field.type === 'password';
          field.type = isPassword ? 'text' : 'password';
          toggleBtn.textContent = isPassword ? 'Sembunyikan' : 'Lihat';
        });
        
        field.parentElement.appendChild(toggleBtn);
      }
    }
  });
  
  // Fungsi untuk menampilkan alert
  function showSuccessAlert(message) {
    const alertContainer = document.getElementById('formAlertContainer');
    const alertDiv = document.createElement('div');
    alertDiv.className = 'custom-alert success';
    alertDiv.innerHTML = `
      <span>${message}</span>
      <button type="button" class="close-btn">&times;</button>
    `;
    
    alertContainer.appendChild(alertDiv);
    
    // Tambahkan event listener untuk tombol close
    alertDiv.querySelector('.close-btn').addEventListener('click', function() {
      alertDiv.remove();
    });
    
    // Otomatis hapus alert setelah beberapa detik
    setTimeout(() => {
      if (alertDiv.parentNode) {
        alertDiv.remove();
      }
    }, 5000);
  }
});