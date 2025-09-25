(function(){
  const form = document.getElementById('editProfileForm');
  const email = document.getElementById('email');
  const password = document.getElementById('password');
  const name = document.getElementById('name');
  const addr  = document.getElementById('address');
  const phone = document.getElementById('phone');
  const saveBtn = document.querySelector('button[type="submit"]');
  // Remove reference to toast as it doesn't exist in HTML
  // Remove reference to toast as it doesn't exist in HTML

  // Avatar preview (opsional)
  const avatarInput = document.getElementById('avatarInput');
  const avatarPreview = document.getElementById('avatarPreview');
  if (avatarInput) {
    avatarInput.addEventListener('change', (e)=>{
      const file = e.target.files?.[0];
      if (!file) return;
      const url = URL.createObjectURL(file);
      avatarPreview.src = url;
    });
  }

  // Helpers
  const $err = id => document.getElementById(id + '-error');
  const onlyLetters = v => /^[A-Za-zÀ-ÖØ-öø-ÿ'.\- ]+$/.test(v); // nama Indonesia cenderung aman
  const isEmail = v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim());
  const isPhone = v => /^\+?[1-9]\d{8,14}$/.test(v.replace(/\s+/g, '')); // E.164: 9-15 digit
  const hasUpper = v => /[A-Z]/.test(v);
  const hasLower = v => /[a-z]/.test(v);
  const hasNumber= v => /\d/.test(v);
  const hasSymbol= v => /[^A-Za-z0-9]/.test(v);

  function setState(input, ok, msg){
    const field = input.closest('.field');
    if (!field) return;
    field.classList.toggle('valid', !!ok);
    field.classList.toggle('invalid', !ok);
    input.setAttribute('aria-invalid', ok ? 'false' : 'true');
    const em = $err(input.id);
    if (em) {
      em.textContent = ok ? '' : msg || '';
      em.classList.toggle('visible', !ok); // Tambahkan kelas 'visible' jika tidak valid
    }
  }

  function validateName(isForSubmission = false){
    const val = name.value.trim();
    if (!val) {
      if (isForSubmission) {
        return setState(name, false, 'Nama wajib diisi.');
      } else {
        // Jangan validasi jika kosong saat input, tapi tetap perbarui status visual
        setState(name, true);
        return true;
      }
    }
    if (val.length < 2) return setState(name, false, 'Minimal 2 karakter.');
    if (val.length > 100) return setState(name, false, 'Maksimal 100 karakter.');
    if (!onlyLetters(val)) return setState(name, false, 'Hanya huruf, spasi, titik, apostrof, atau minus.');
    return setState(name, true);
  }
  function validateEmail(isForSubmission = false){
    const val = email.value.trim();
    if (!val) {
      if (isForSubmission) {
        return setState(email, false, 'Email wajib diisi.');
      } else {
        return setState(email, true); // Jangan validasi jika kosong saat input
      }
    }
    if (!isEmail(val)) return setState(email, false, 'Format email tidak valid.');
    return setState(email, true);
  }
  function validateAddress(isForSubmission = false){
    const val = addr.value.trim();
    if (!val) {
      if (isForSubmission) {
        return setState(addr, false, 'Alamat wajib diisi.');
      } else {
        return setState(addr, true); // Jangan validasi jika kosong saat input
      }
    }
    if (val.length < 10) return setState(addr, false, 'Alamat terlalu pendek (≥ 10 karakter).');
    if (val.length > 120) return setState(addr, false, 'Alamat terlalu panjang (≤ 120 karakter).');
    if (val.split(/\s+/).length < 3) return setState(addr, false, 'Cantumkan detail yang cukup (jalan, nomor, kota).');
    return setState(addr, true);
  }
  function validatePhone(isForSubmission = false){
    const raw = phone.value.replace(/\s+/g, '');
    if (!raw) {
      if (isForSubmission) {
        return setState(phone, false, 'Nomor kontak wajib diisi.');
      } else {
        return setState(phone, true); // Jangan validasi jika kosong saat input
      }
    }
    if (!isPhone(raw)) return setState(phone, false, 'Gunakan format internasional (mis. +62812xxxx).');
    return setState(phone, true);
  }
  function validatePassword(isForSubmission = false){
    const v = password.value; // jangan trim password
    if (!v) {
      // Hanya validasi jika password diisi - tidak wajib dalam form ini
      setState(password, true);
      return true;
    }
    if (v.length < 8) return setState(password, false, 'Minimal 8 karakter.');
    if (!hasUpper(v)) return setState(password, false, 'Wajib ada huruf besar.');
    if (!hasLower(v)) return setState(password, false, 'Wajib ada huruf kecil.');
    if (!hasNumber(v)) return setState(password, false, 'Wajib ada angka.');
    if (!hasSymbol(v)) return setState(password, false, 'Wajib ada simbol.');
    return setState(password, true);
  }

  function validateAll(isForSubmission = false){
    const r1 = validateName(isForSubmission);
    const r2 = validateEmail(isForSubmission);
    const r3 = validateAddress(isForSubmission);
    const r4 = validatePhone(isForSubmission);
    // Only validate password if it exists and has a value
    const r5 = password && password.value ? validatePassword(isForSubmission) : true;
    const results = [r1,r2,r3,r4,r5];
    const ok = results.every(result => result === true);
    
    if (!isForSubmission) {
      // enable/disable tombol via kondisi aktual (hanya untuk real-time, bukan saat submit)
      const allOk =
        !name.closest('.field').classList.contains('invalid') &&
        !email.closest('.field').classList.contains('invalid') &&
        !addr .closest('.field').classList.contains('invalid') &&
        !phone.closest('.field').classList.contains('invalid') &&
        !(password && password.value && password.closest('.field').classList.contains('invalid'));

      if (saveBtn) saveBtn.disabled = !allOk;
    }
    return ok;
  }

  // hook input - using wrapper functions to pass the right parameter
  name.addEventListener('input', () => validateName());
  name.addEventListener('change', () => validateName());
  email.addEventListener('input', () => validateEmail());
  email.addEventListener('change', () => validateEmail());
  addr.addEventListener('input', () => validateAddress());
  addr.addEventListener('change', () => validateAddress());
  phone.addEventListener('input', () => validatePhone());
  phone.addEventListener('change', () => validatePhone());
  
  // Only add password validation if password field exists
  if (password) {
    password.addEventListener('input', validatePassword);
    password.addEventListener('change', validatePassword);
  }

  // show/hide password
  const sh = document.querySelector('.showhide');
  if (sh) {
    sh.addEventListener('click', ()=>{
      const isPwd = password.getAttribute('type') === 'password';
      password.setAttribute('type', isPwd ? 'text' : 'password');
    });
  }

  // submit
  form.addEventListener('submit', async (e)=>{
    e.preventDefault();
    if (!validateAll(true)){ // Pass true to enforce required validation
      // fokus ke field pertama yang invalid
      const firstInvalid = form.querySelector('.field.invalid input, .field.invalid select, .field.invalid textarea');
      if (firstInvalid){ firstInvalid.focus({preventScroll:false}); firstInvalid.scrollIntoView({behavior:'smooth', block:'center'}); }
      return;
    }
    
    // Show success message using the alert container from the HTML
    const alertContainer = document.getElementById('formAlertContainer');
    
    // Remove any existing alerts
    alertContainer.innerHTML = '';
    
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = 'custom-alert success';
    alertDiv.textContent = 'Profil berhasil diperbarui!';
    
    // Add close button
    const closeBtn = document.createElement('button');
    closeBtn.className = 'close-btn';
    closeBtn.innerHTML = '&times;';
    closeBtn.onclick = function() {
        alertDiv.remove();
    };
    
    alertDiv.appendChild(closeBtn);
    alertContainer.appendChild(alertDiv);
    
    // Redirect to profile page after 1.5 seconds
    setTimeout(() => {
      window.location.href = window.PROFIL_ROUTE || window.location.origin + '/profil';
    }, 1500);
  });

  // initial validate (untuk nilai yang sudah terisi dari server)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ()=>{ validateAll(); });
  } else {
    validateAll();
  }
})();
