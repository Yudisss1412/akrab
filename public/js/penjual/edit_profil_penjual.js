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

  // Debug: Check if all elements are found
  console.log('Elements found:');
  console.log('- form:', !!form);
  console.log('- shopName:', !!shopName);
  console.log('- ownerName:', !!ownerName);
  console.log('- email:', !!email);
  console.log('- phone:', !!phone);
  console.log('- addr:', !!addr);
  console.log('- shopDescription:', !!shopDescription);
  console.log('- bankName:', !!bankName);
  console.log('- accountNumber:', !!accountNumber);
  console.log('- accountHolder:', !!accountHolder);
  console.log('- saveBtn:', !!saveBtn);
  console.log('- avatarInput:', !!avatarInput);
  console.log('- avatarPreview:', !!avatarPreview);

  if (!form || !shopName || !ownerName || !email || !phone || !addr ||
      !shopDescription || !bankName || !accountNumber || !accountHolder || !saveBtn) {
    console.error('One or more elements not found! This will cause issues.');
    return; // Stop execution to prevent errors
  }

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

    // Logging untuk debugging
    console.log(`${input.id}: ${ok ? 'VALID' : 'INVALID'} - Message: ${msg || 'None'}`);
  }

  function validateShopName(isForSubmission = false){
    const val = shopName.value.trim();
    if (!val) {
      if (isForSubmission) {
        setState(shopName, false, 'Nama toko wajib diisi.');
        return false;
      } else {
        setState(shopName, true);
        return true;
      }
    }
    if (val.length < 3) {
      setState(shopName, false, 'Nama toko minimal 3 karakter.');
      return false;
    }
    if (val.length > 50) {
      setState(shopName, false, 'Nama toko maksimal 50 karakter.');
      return false;
    }
    setState(shopName, true);
    return true;
  }

  function validateOwnerName(isForSubmission = false){
    const val = ownerName.value.trim();
    if (!val) {
      if (isForSubmission) {
        setState(ownerName, false, 'Nama pemilik wajib diisi.');
        return false;
      } else {
        setState(ownerName, true);
        return true;
      }
    }
    if (val.length < 2) {
      setState(ownerName, false, 'Nama pemilik minimal 2 karakter.');
      return false;
    }
    if (val.length > 50) {
      setState(ownerName, false, 'Nama pemilik maksimal 50 karakter.');
      return false;
    }
    if (!onlyLetters(val)) {
      setState(ownerName, false, 'Hanya huruf, spasi, titik, apostrof, atau minus.');
      return false;
    }
    setState(ownerName, true);
    return true;
  }

  function validateEmail(isForSubmission = false){
    const val = email.value.trim();
    console.log(`Validating email: "${val}", isForSubmission: ${isForSubmission}`);

    if (!val) {
      console.log('Email is empty');
      if (isForSubmission) {
        console.log('Setting email as invalid for submission');
        setState(email, false, 'Email wajib diisi.');
        return false;
      } else {
        console.log('Setting email as valid for non-submission');
        setState(email, true);
        return true;
      }
    }
    if (!isEmail(val)) {
      console.log('Email format is invalid');
      setState(email, false, 'Format email tidak valid.');
      return false;
    }
    console.log('Email is valid');
    setState(email, true);
    return true;
  }

  function validatePhone(isForSubmission = false){
    const raw = phone.value.replace(/\s+/g, '');
    console.log(`Validating phone: "${raw}", isForSubmission: ${isForSubmission}`);

    if (!raw) {
      console.log('Phone is empty');
      if (isForSubmission) {
        console.log('Setting phone as invalid for submission');
        setState(phone, false, 'Nomor telepon wajib diisi.');
        return false;
      } else {
        console.log('Setting phone as valid for non-submission');
        setState(phone, true);
        return true;
      }
    }
    if (!isPhone(raw)) {
      console.log('Phone format is invalid');
      setState(phone, false, 'Gunakan format internasional (mis. +62812xxxx).');
      return false;
    }
    console.log('Phone is valid');
    setState(phone, true);
    return true;
  }

  function validateAddress(isForSubmission = false){
    const val = addr.value.trim();
    if (!val) {
      if (isForSubmission) {
        setState(addr, false, 'Alamat wajib diisi.');
        return false;
      } else {
        setState(addr, true);
        return true;
      }
    }
    if (val.length < 10) {
      setState(addr, false, 'Alamat terlalu pendek (≥ 10 karakter).');
      return false;
    }
    if (val.length > 120) {
      setState(addr, false, 'Alamat terlalu panjang (≤ 120 karakter).');
      return false;
    }
    if (val.split(/\s+/).length < 3) {
      setState(addr, false, 'Cantumkan detail yang cukup (jalan, nomor, kota).');
      return false;
    }
    setState(addr, true);
    return true;
  }

  function validateShopDescription(isForSubmission = false){
    const val = shopDescription.value.trim();
    if (!val) {
      if (isForSubmission) {
        setState(shopDescription, false, 'Deskripsi toko wajib diisi.');
        return false;
      } else {
        setState(shopDescription, true);
        return true;
      }
    }
    if (val.length < 10) {
      setState(shopDescription, false, 'Deskripsi terlalu pendek (minimal 10 karakter).');
      return false;
    }
    if (val.length > 200) {
      setState(shopDescription, false, 'Deskripsi terlalu panjang (maksimal 200 karakter).');
      return false;
    }
    setState(shopDescription, true);
    return true;
  }

  function validateBankName(isForSubmission = false){
    const val = bankName.value.trim();
    if (!val) {
      if (isForSubmission) {
        setState(bankName, false, 'Nama bank wajib diisi.');
        return false;
      }
      // Untuk validasi saat mengetik, field kosong dibiarkan valid
      setState(bankName, true);
      return true;
    }
    setState(bankName, true);
    return true;
  }

  function validateAccountNumber(isForSubmission = false){
    const val = accountNumber.value.trim();
    if (!val) {
      if (isForSubmission) {
        setState(accountNumber, false, 'Nomor rekening wajib diisi.');
        return false;
      }
      // Untuk validasi saat mengetik, field kosong dibiarkan valid
      setState(accountNumber, true);
      return true;
    }
    if (!isNumeric(val)) {
      setState(accountNumber, false, 'Nomor rekening hanya boleh angka.');
      return false;
    }
    if (val.length < 8) {
      setState(accountNumber, false, 'Nomor rekening minimal 8 digit.');
      return false;
    }
    if (val.length > 20) {
      setState(accountNumber, false, 'Nomor rekening maksimal 20 digit.');
      return false;
    }
    setState(accountNumber, true);
    return true;
  }

  function validateAccountHolder(isForSubmission = false){
    const val = accountHolder.value.trim();
    if (!val) {
      if (isForSubmission) {
        setState(accountHolder, false, 'Atas nama wajib diisi.');
        return false;
      }
      // Untuk validasi saat mengetik, field kosong dibiarkan valid
      setState(accountHolder, true);
      return true;
    }
    if (val.length < 2) {
      setState(accountHolder, false, 'Atas nama minimal 2 karakter.');
      return false;
    }
    if (val.length > 50) {
      setState(accountHolder, false, 'Atas nama maksimal 50 karakter.');
      return false;
    }
    if (!onlyLetters(val.replace(/[0-9]/g, ''))) {
      setState(accountHolder, false, 'Atas nama hanya huruf, spasi, titik, apostrof, atau minus.');
      return false;
    }
    setState(accountHolder, true);
    return true;
  }

  function validateAll(isForSubmission = false){
    console.log('Starting validateAll with isForSubmission:', isForSubmission);

    const results = [
      { name: 'shopName', result: validateShopName(isForSubmission) },
      { name: 'ownerName', result: validateOwnerName(isForSubmission) },
      { name: 'email', result: validateEmail(isForSubmission) },
      { name: 'phone', result: validatePhone(isForSubmission) },
      { name: 'address', result: validateAddress(isForSubmission) },
      { name: 'shopDescription', result: validateShopDescription(isForSubmission) },
      { name: 'bankName', result: validateBankName(isForSubmission) },
      { name: 'accountNumber', result: validateAccountNumber(isForSubmission) },
      { name: 'accountHolder', result: validateAccountHolder(isForSubmission) }
    ];

    console.log('Individual validation results:', results);
    const finalResult = results.every(item => item.result === true);
    console.log('Overall validation result:', finalResult);

    if (!isForSubmission) {
      // Jangan atur tombol submit di sini, karena kita sudah mengaturnya di fungsi evaluateSubmitButton
      // Fungsi ini hanya untuk memastikan semua field divalidasi
    }
    return finalResult;
  }

  // hook input
  shopName.addEventListener('input', () => {
    validateShopName();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });
  shopName.addEventListener('change', () => {
    validateShopName();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });

  ownerName.addEventListener('input', () => {
    validateOwnerName();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });
  ownerName.addEventListener('change', () => {
    validateOwnerName();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });

  email.addEventListener('input', () => {
    validateEmail();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });
  email.addEventListener('change', () => {
    validateEmail();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });

  phone.addEventListener('input', () => {
    validatePhone();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });
  phone.addEventListener('change', () => {
    validatePhone();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });

  addr.addEventListener('input', () => {
    validateAddress();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });
  addr.addEventListener('change', () => {
    validateAddress();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });

  shopDescription.addEventListener('input', () => {
    validateShopDescription();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });
  shopDescription.addEventListener('change', () => {
    validateShopDescription();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });

  // Handle select element for proper validation
  bankName.addEventListener('change', () => {
    validateBankName();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });

  accountNumber.addEventListener('input', () => {
    validateAccountNumber();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });
  accountNumber.addEventListener('change', () => {
    validateAccountNumber();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });

  accountHolder.addEventListener('input', () => {
    validateAccountHolder();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });
  accountHolder.addEventListener('change', () => {
    validateAccountHolder();
    evaluateSubmitButton(); // Re-evaluate button after validation
  });

  // submit
  form.addEventListener('submit', async (e)=>{
    e.preventDefault(); // Prevent default form submission

    console.log('Form submitted!');

    // Lakukan validasi manual untuk field-field wajib saat submission
    const isShopNameValid = validateShopName(true);
    const isOwnerNameValid = validateOwnerName(true);
    const isEmailValid = validateEmail(true);
    const isPhoneValid = validatePhone(true);
    const isAddressValid = validateAddress(true);
    const isShopDescriptionValid = validateShopDescription(true);
    const isBankNameValid = validateBankName(true);
    const isAccountNumberValid = validateAccountNumber(true);
    const isAccountHolderValid = validateAccountHolder(true);

    console.log('Manual validation results:', {
      shopName: isShopNameValid,
      ownerName: isOwnerNameValid,
      email: isEmailValid,
      phone: isPhoneValid,
      address: isAddressValid,
      shopDescription: isShopDescriptionValid,
      bankName: isBankNameValid,
      accountNumber: isAccountNumberValid,
      accountHolder: isAccountHolderValid
    });

    // Check if all required fields have values (not just validation status)
    const hasShopNameValue = shopName.value.trim() !== '';
    const hasOwnerNameValue = ownerName.value.trim() !== '';
    const hasEmailValue = email.value.trim() !== '';
    const hasPhoneValue = phone.value.replace(/\s+/g, '') !== '';
    const hasAddressValue = addr.value.trim() !== '';
    const hasShopDescriptionValue = shopDescription.value.trim() !== '';
    const hasBankNameValue = bankName.value.trim() !== '';
    const hasAccountNumberValue = accountNumber.value.trim() !== '';
    const hasAccountHolderValue = accountHolder.value.trim() !== '';

    // Log values for debugging
    console.log('Field values check:', {
      shopName: hasShopNameValue,
      ownerName: hasOwnerNameValue,
      email: hasEmailValue,
      phone: hasPhoneValue,
      address: hasAddressValue,
      shopDescription: hasShopDescriptionValue,
      bankName: hasBankNameValue,
      accountNumber: hasAccountNumberValue,
      accountHolder: hasAccountHolderValue
    });

    if (!(hasShopNameValue && hasOwnerNameValue && hasEmailValue && hasPhoneValue && hasAddressValue &&
          hasShopDescriptionValue && hasBankNameValue && hasAccountNumberValue && hasAccountHolderValue)) {
      console.log('One or more required fields are empty!');
      // Show error for empty fields
      if (!hasShopNameValue) setState(shopName, false, 'Nama toko wajib diisi.');
      if (!hasOwnerNameValue) setState(ownerName, false, 'Nama pemilik wajib diisi.');
      if (!hasEmailValue) setState(email, false, 'Email wajib diisi.');
      if (!hasPhoneValue) setState(phone, false, 'Nomor telepon wajib diisi.');
      if (!hasAddressValue) setState(addr, false, 'Alamat wajib diisi.');
      if (!hasShopDescriptionValue) setState(shopDescription, false, 'Deskripsi toko wajib diisi.');
      if (!hasBankNameValue) setState(bankName, false, 'Nama bank wajib diisi.');
      if (!hasAccountNumberValue) setState(accountNumber, false, 'Nomor rekening wajib diisi.');
      if (!hasAccountHolderValue) setState(accountHolder, false, 'Atas nama wajib diisi.');

      // fokus ke field pertama yang kosong
      const firstEmpty = [shopName, ownerName, email, phone, addr, shopDescription, bankName, accountNumber, accountHolder]
        .find(field => field.value.trim() === '');

      if (firstEmpty) {
        firstEmpty.focus({preventScroll:false});
        firstEmpty.scrollIntoView({behavior:'smooth', block:'center'});
      }
      return;
    }

    // Then check validation status
    if (!(isShopNameValid && isOwnerNameValid && isEmailValid && isPhoneValid && isAddressValid && isShopDescriptionValid && isBankNameValid && isAccountNumberValid && isAccountHolderValid)) {
      console.log('Manual validation failed for submission!');
      // fokus ke field pertama yang invalid
      const firstInvalid = form.querySelector('.field.invalid input, .field.invalid select, .field.invalid textarea');
      if (firstInvalid){
        firstInvalid.focus({preventScroll:false});
        firstInvalid.scrollIntoView({behavior:'smooth', block:'center'});
      }
      return;
    }

    // Disable button and show loading state
    saveBtn.disabled = true;
    saveBtn.textContent = 'Menyimpan...';

    try {
      // Create a plain object with all form values
      const formDataObj = {
        shopName: shopName.value.trim(),
        ownerName: ownerName.value.trim(),
        email: email.value.trim(),
        phone: phone.value.trim(),
        address: addr.value.trim(),
        shopDescription: shopDescription.value.trim(),
        bankName: bankName.value.trim(),
        accountNumber: accountNumber.value.trim(),
        accountHolder: accountHolder.value.trim(),
      };

      // Debug: log form data
      console.log('Sending form data:', formDataObj);

      const response = await fetch(form.action, {
        method: 'PUT',
        body: JSON.stringify(formDataObj),
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });

      console.log('Response status:', response.status);
      const data = await response.json();
      console.log('Response data:', data);

      if (data.success) {
        // Show success message
        showAlert('success', data.message);

        // Redirect to profile page after delay
        setTimeout(() => {
          window.location.href = '/profil_penjual';
        }, 1500);
      } else {
        // Handle validation errors
        if (data.errors) {
          console.log('Server validation errors:', data.errors);
          Object.keys(data.errors).forEach(fieldName => {
            let fieldId;
            switch(fieldName) {
              case 'shopName':
                fieldId = 'shopName';
                break;
              case 'ownerName':
                fieldId = 'ownerName';
                break;
              case 'email':
                fieldId = 'email';
                break;
              case 'phone':
                fieldId = 'phone';
                break;
              case 'address':
                fieldId = 'address';
                break;
              case 'shopDescription':
                fieldId = 'shopDescription';
                break;
              case 'bankName':
                fieldId = 'bankName';
                break;
              case 'accountNumber':
                fieldId = 'accountNumber';
                break;
              case 'accountHolder':
                fieldId = 'accountHolder';
                break;
              default:
                fieldId = fieldName;
            }

            const field = document.getElementById(fieldId);
            if (field) {
              setState(field, false, data.errors[fieldName][0]);
            }
          });
        } else {
          showAlert('error', data.message || 'Terjadi kesalahan saat menyimpan profil');
        }
      }
    } catch (error) {
      console.error('Error:', error);
      showAlert('error', 'Terjadi kesalahan jaringan. Silakan coba lagi.');
    } finally {
      // Re-enable button
      saveBtn.disabled = false;
      saveBtn.textContent = 'Simpan Perubahan';
    }
  });
  
  // Function to show alerts
  function showAlert(type, message) {
    const alertContainer = document.getElementById('formAlertContainer');
    if (!alertContainer) return;
    
    // Remove any existing alerts
    const existingAlert = alertContainer.querySelector('.alert');
    if (existingAlert) {
      existingAlert.remove();
    }
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
      <div class="alert-content">
        <strong>${type === 'success' ? 'Berhasil!' : 'Error!'}</strong>
        <p>${message}</p>
      </div>
      <button type="button" class="alert-close" onclick="this.parentElement.remove()">×</button>
    `;
    
    alertContainer.appendChild(alert);
    
    // Auto remove success alert after 5 seconds
    if (type === 'success') {
      setTimeout(() => {
        if (alert.parentNode) {
          alert.remove();
        }
      }, 5000);
    }
  }

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

  // Fungsi untuk mengevaluasi dan menetapkan kondisi tombol submit
  function evaluateSubmitButton() {
    const requiredFields = [
      {field: shopName, name: 'shopName'},
      {field: ownerName, name: 'ownerName'},
      {field: email, name: 'email'},
      {field: phone, name: 'phone'},
      {field: addr, name: 'address'},
      {field: shopDescription, name: 'shopDescription'},
      {field: bankName, name: 'bankName'},
      {field: accountNumber, name: 'accountNumber'},
      {field: accountHolder, name: 'accountHolder'}
    ];

    console.log('Evaluating submit button...');

    let allRequiredValid = true;
    const invalidFields = [];

    requiredFields.forEach(item => {
      // Check if field has value
      let hasValue = false;
      if (item.field.tagName === 'SELECT') {
        hasValue = item.field.value.trim() !== '' && item.field.value !== '';
      } else {
        hasValue = item.field.value.trim() !== '';
      }

      // Check if field is marked as invalid
      const isInvalid = item.field.closest('.field')?.classList.contains('invalid') || false;

      const fieldValid = hasValue && !isInvalid;

      console.log(`${item.name}: Value=${hasValue ? 'YES' : 'NO'}, Invalid=${isInvalid ? 'YES' : 'NO'}, Overall=${fieldValid ? 'VALID' : 'INVALID'}`);

      if (!fieldValid) {
        allRequiredValid = false;
        invalidFields.push(item.name);
      }
    });

    console.log(`All required fields valid: ${allRequiredValid}`);
    if (!allRequiredValid) {
      console.log(`Invalid fields: ${invalidFields.join(', ')}`);
    }

    if (saveBtn) {
      saveBtn.disabled = !allRequiredValid;
      console.log(`Submit button ${allRequiredValid ? 'ENABLED' : 'DISABLED'}`);
    }
  }

  // initial validate (untuk nilai yang sudah terisi dari server)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ()=>{
      // Validasi semua field untuk nilai awal
      validateShopName();
      validateOwnerName();
      validateEmail();
      validatePhone();
      validateAddress();
      validateShopDescription();
      validateBankName();
      validateAccountNumber();
      validateAccountHolder();

      // Evaluasi kondisi tombol submit setelah semua validasi awal
      evaluateSubmitButton();
      handlePrefilledFields();
    });
  } else {
    // Validasi semua field untuk nilai awal
    validateShopName();
    validateOwnerName();
    validateEmail();
    validatePhone();
    validateAddress();
    validateShopDescription();
    validateBankName();
    validateAccountNumber();
    validateAccountHolder();

    // Evaluasi kondisi tombol submit setelah semua validasi awal
    evaluateSubmitButton();
    handlePrefilledFields();
  }
})();