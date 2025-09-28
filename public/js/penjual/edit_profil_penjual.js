(function(){
  const form = document.getElementById('editSellerProfileForm');
  const shopName = document.getElementById('shopName');
  const ownerName = document.getElementById('ownerName');
  const email = document.getElementById('email');
  const phone = document.getElementById('phone');
  const addr = document.getElementById('address');
  const shopDescription = document.getElementById('shopDescription');
  const bankName = document.getElementById('bankName');
  const accountNumber = document.getElementById('accountNumber');
  const accountHolder = document.getElementById('accountHolder');
  const saveBtn = document.querySelector('button[type="submit"]');
  const avatarInput = document.getElementById('avatarInput');
  const avatarPreview = document.getElementById('avatarPreview');

  // Avatar preview
  if (avatarInput) {
    avatarInput.addEventListener('change', (e)=>{
      const file = e.target.files?.[0];
      if (!file) return;
      const url = URL.createObjectURL(file);
      avatarPreview.innerHTML = `<img src="${url}" alt="Avatar" style="width:72px; height:72px; border-radius:50%; object-fit:cover;">`;
    });
  }

  // Helpers
  const $err = id => document.getElementById(id + '-error');
  const onlyLetters = v => /^[A-Za-zÀ-ÖØ-öø-ÿ'.\- ]+$/.test(v); // nama Indonesia cenderung aman
  const isEmail = v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()); // email validation
  const isPhone = v => /^\+?[1-9]\d{8,14}$/.test(v.replace(/\s+/g, '')); // E.164: 9-15 digit
  const isNumeric = v => /^\d+$/.test(v);

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

  function validateShopName(isForSubmission = false){
    const val = shopName.value.trim();
    if (!val) {
      if (isForSubmission) {
        return setState(shopName, false, 'Nama toko wajib diisi.');
      } else {
        setState(shopName, true);
        return true;
      }
    }
    if (val.length < 3) return setState(shopName, false, 'Nama toko minimal 3 karakter.');
    if (val.length > 50) return setState(shopName, false, 'Nama toko maksimal 50 karakter.');
    return setState(shopName, true);
  }

  function validateOwnerName(isForSubmission = false){
    const val = ownerName.value.trim();
    if (!val) {
      if (isForSubmission) {
        return setState(ownerName, false, 'Nama pemilik wajib diisi.');
      } else {
        setState(ownerName, true);
        return true;
      }
    }
    if (val.length < 2) return setState(ownerName, false, 'Nama pemilik minimal 2 karakter.');
    if (val.length > 50) return setState(ownerName, false, 'Nama pemilik maksimal 50 karakter.');
    if (!onlyLetters(val)) return setState(ownerName, false, 'Hanya huruf, spasi, titik, apostrof, atau minus.');
    return setState(ownerName, true);
  }

  function validateEmail(isForSubmission = false){
    const val = email.value.trim();
    if (!val) {
      if (isForSubmission) {
        return setState(email, false, 'Email wajib diisi.');
      } else {
        setState(email, true);
        return true;
      }
    }
    if (!isEmail(val)) return setState(email, false, 'Format email tidak valid.');
    return setState(email, true);
  }

  function validatePhone(isForSubmission = false){
    const raw = phone.value.replace(/\s+/g, '');
    if (!raw) {
      if (isForSubmission) {
        return setState(phone, false, 'Nomor telepon wajib diisi.');
      } else {
        setState(phone, true);
        return true;
      }
    }
    if (!isPhone(raw)) return setState(phone, false, 'Gunakan format internasional (mis. +62812xxxx).');
    return setState(phone, true);
  }

  function validateAddress(isForSubmission = false){
    const val = addr.value.trim();
    if (!val) {
      if (isForSubmission) {
        return setState(addr, false, 'Alamat wajib diisi.');
      } else {
        setState(addr, true);
        return true;
      }
    }
    if (val.length < 10) return setState(addr, false, 'Alamat terlalu pendek (≥ 10 karakter).');
    if (val.length > 120) return setState(addr, false, 'Alamat terlalu panjang (≤ 120 karakter).');
    if (val.split(/\s+/).length < 3) return setState(addr, false, 'Cantumkan detail yang cukup (jalan, nomor, kota).');
    return setState(addr, true);
  }

  function validateShopDescription(isForSubmission = false){
    const val = shopDescription.value.trim();
    if (!val) {
      if (isForSubmission) {
        return setState(shopDescription, false, 'Deskripsi toko wajib diisi.');
      } else {
        setState(shopDescription, true);
        return true;
      }
    }
    if (val.length < 10) return setState(shopDescription, false, 'Deskripsi terlalu pendek (minimal 10 karakter).');
    if (val.length > 200) return setState(shopDescription, false, 'Deskripsi terlalu panjang (maksimal 200 karakter).');
    return setState(shopDescription, true);
  }

  function validateBankName(isForSubmission = false){
    const val = bankName.value.trim();
    // Allow empty values for bank selection
    if (!val) {
      setState(bankName, true);
      return true;
    }
    return setState(bankName, true);
  }

  function validateAccountNumber(isForSubmission = false){
    const val = accountNumber.value.trim();
    if (!val) {
      if (isForSubmission) {
        return setState(accountNumber, false, 'Nomor rekening wajib diisi.');
      } else {
        setState(accountNumber, true);
        return true;
      }
    }
    if (!isNumeric(val)) return setState(accountNumber, false, 'Nomor rekening hanya boleh angka.');
    if (val.length < 8) return setState(accountNumber, false, 'Nomor rekening minimal 8 digit.');
    if (val.length > 20) return setState(accountNumber, false, 'Nomor rekening maksimal 20 digit.');
    return setState(accountNumber, true);
  }

  function validateAccountHolder(isForSubmission = false){
    const val = accountHolder.value.trim();
    if (!val) {
      if (isForSubmission) {
        return setState(accountHolder, false, 'Atas nama wajib diisi.');
      } else {
        setState(accountHolder, true);
        return true;
      }
    }
    if (val.length < 2) return setState(accountHolder, false, 'Atas nama minimal 2 karakter.');
    if (val.length > 50) return setState(accountHolder, false, 'Atas nama maksimal 50 karakter.');
    if (!onlyLetters(val.replace(/[0-9]/g, ''))) return setState(accountHolder, false, 'Atas nama hanya huruf, spasi, titik, apostrof, atau minus.');
    return setState(accountHolder, true);
  }

  function validateAll(isForSubmission = false){
    const results = [
      validateShopName(isForSubmission),
      validateOwnerName(isForSubmission),
      validateEmail(isForSubmission),
      validatePhone(isForSubmission),
      validateAddress(isForSubmission),
      validateShopDescription(isForSubmission),
      validateBankName(isForSubmission),
      validateAccountNumber(isForSubmission),
      validateAccountHolder(isForSubmission)
    ];
    
    const ok = results.every(result => result === true);
    
    if (!isForSubmission) {
      // enable/disable tombol via kondisi aktual (hanya untuk real-time, bukan saat submit)
      const allOk =
        !shopName.closest('.field').classList.contains('invalid') &&
        !ownerName.closest('.field').classList.contains('invalid') &&
        !email.closest('.field').classList.contains('invalid') &&
        !phone.closest('.field').classList.contains('invalid') &&
        !addr.closest('.field').classList.contains('invalid') &&
        !shopDescription.closest('.field').classList.contains('invalid') &&
        !bankName.closest('.field').classList.contains('invalid') &&
        !accountNumber.closest('.field').classList.contains('invalid') &&
        !accountHolder.closest('.field').classList.contains('invalid');

      if (saveBtn) saveBtn.disabled = !allOk;
    }
    return ok;
  }

  // hook input
  shopName.addEventListener('input', () => validateShopName());
  shopName.addEventListener('change', () => validateShopName());
  
  ownerName.addEventListener('input', () => validateOwnerName());
  ownerName.addEventListener('change', () => validateOwnerName());
  
  email.addEventListener('input', () => validateEmail());
  email.addEventListener('change', () => validateEmail());
  
  phone.addEventListener('input', () => validatePhone());
  phone.addEventListener('change', () => validatePhone());
  
  addr.addEventListener('input', () => validateAddress());
  addr.addEventListener('change', () => validateAddress());
  
  shopDescription.addEventListener('input', () => validateShopDescription());
  shopDescription.addEventListener('change', () => validateShopDescription());
  
  // Handle select element for proper validation
  bankName.addEventListener('change', () => {
    validateBankName();
  });
  
  accountNumber.addEventListener('input', () => validateAccountNumber());
  accountNumber.addEventListener('change', () => validateAccountNumber());
  
  accountHolder.addEventListener('input', () => validateAccountHolder());
  accountHolder.addEventListener('change', () => validateAccountHolder());

  // submit
  form.addEventListener('submit', async (e)=>{
    if (!validateAll(true)){ // Pass true to enforce required validation
      // fokus ke field pertama yang invalid
      const firstInvalid = form.querySelector('.field.invalid input, .field.invalid select, .field.invalid textarea');
      if (firstInvalid){ 
        firstInvalid.focus({preventScroll:false}); 
        firstInvalid.scrollIntoView({behavior:'smooth', block:'center'}); 
      }
      e.preventDefault();
      return;
    }
  });

  // Handle field classes for pre-filled values
  function handlePrefilledFields() {
    // Handle input and textarea elements
    const inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(input => {
      const field = input.closest('.field');
      if (field && input.value.trim() !== '') {
        field.classList.add('has-value');
      }
    });
    
    // Handle select elements
    const selects = form.querySelectorAll('select');
    selects.forEach(select => {
      const field = select.closest('.field');
      if (field && select.value.trim() !== '' && select.value !== '') {
        field.classList.add('has-value');
      }
    });
  }
  
  // initial validate (untuk nilai yang sudah terisi dari server)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ()=>{ 
      validateAll(); 
      handlePrefilledFields();
    });
  } else {
    validateAll();
    handlePrefilledFields();
  }
})();