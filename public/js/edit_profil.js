(function(){
  const form = document.getElementById('editProfileForm');
  const email = document.getElementById('email');
  const password = document.getElementById('password');
  const first = document.getElementById('firstName');
  const last  = document.getElementById('lastName');
  const addr  = document.getElementById('address');
  const phone = document.getElementById('phone');
  const saveBtn = document.getElementById('saveBtn');
  const toast = document.getElementById('toast');

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
    if (em) em.textContent = ok ? '' : msg || '';
  }

  function validateNameFirst(){
    const val = first.value.trim();
    if (val.length < 2) return setState(first, false, 'Minimal 2 karakter.');
    if (val.length > 50) return setState(first, false, 'Maksimal 50 karakter.');
    if (!onlyLetters(val)) return setState(first, false, 'Hanya huruf, spasi, titik, apostrof, atau minus.');
    return setState(first, true);
  }
  function validateNameLast(){
    const val = last.value.trim();
    if (val.length < 1) return setState(last, false, 'Tidak boleh kosong.');
    if (val.length > 50) return setState(last, false, 'Maksimal 50 karakter.');
    if (!onlyLetters(val)) return setState(last, false, 'Hanya huruf, spasi, titik, apostrof, atau minus.');
    return setState(last, true);
  }
  function validateEmail(){
    const val = email.value.trim();
    if (!val) return setState(email, false, 'Email wajib diisi.');
    if (!isEmail(val)) return setState(email, false, 'Format email tidak valid.');
    return setState(email, true);
  }
  function validateAddress(){
    const val = addr.value.trim();
    if (val.length < 10) return setState(addr, false, 'Alamat terlalu pendek (≥ 10 karakter).');
    if (val.length > 120) return setState(addr, false, 'Alamat terlalu panjang (≤ 120 karakter).');
    if (val.split(/\s+/).length < 3) return setState(addr, false, 'Cantumkan detail yang cukup (jalan, nomor, kota).');
    return setState(addr, true);
  }
  function validatePhone(){
    const raw = phone.value.replace(/\s+/g, '');
    if (!raw) return setState(phone, false, 'Nomor kontak wajib diisi.');
    if (!isPhone(raw)) return setState(phone, false, 'Gunakan format internasional (mis. +62812xxxx).');
    return setState(phone, true);
  }
  function validatePassword(){
    const v = password.value; // jangan trim password
    if (v.length < 8) return setState(password, false, 'Minimal 8 karakter.');
    if (!hasUpper(v)) return setState(password, false, 'Wajib ada huruf besar.');
    if (!hasLower(v)) return setState(password, false, 'Wajib ada huruf kecil.');
    if (!hasNumber(v)) return setState(password, false, 'Wajib ada angka.');
    if (!hasSymbol(v)) return setState(password, false, 'Wajib ada simbol.');
    return setState(password, true);
  }

  function validateAll(){
    const r1 = validateNameFirst();
    const r2 = validateNameLast();
    const r3 = validateEmail();
    const r4 = validateAddress();
    const r5 = validatePhone();
    const r6 = validatePassword();
    const ok = [r1,r2,r3,r4,r5,r6].every(()=>true); // setState sudah mengatur kelas; kita cek via DOM
    // enable/disable tombol via kondisi aktual:
    const allOk =
      !first.closest('.field').classList.contains('invalid') &&
      !last .closest('.field').classList.contains('invalid') &&
      !email.closest('.field').classList.contains('invalid') &&
      !addr .closest('.field').classList.contains('invalid') &&
      !phone.closest('.field').classList.contains('invalid') &&
      !password.closest('.field').classList.contains('invalid');

    saveBtn.disabled = !allOk;
    return allOk;
  }

  // hook input
  [
    [first, validateNameFirst],
    [last, validateNameLast],
    [email, validateEmail],
    [addr, validateAddress],
    [phone, validatePhone],
    [password, validatePassword],
  ].forEach(([el, fn])=>{
    el.addEventListener('input', fn);
    el.addEventListener('change', fn);
  });

  // show/hide password
  const sh = document.querySelector('.showhide');
  if (sh) {
    sh.addEventListener('click', ()=>{
      const isPwd = password.getAttribute('type') === 'password';
      password.setAttribute('type', isPwd ? 'text' : 'password');
    });
  }

  // submit
  form.addEventListener('submit', (e)=>{
    e.preventDefault();
    if (!validateAll()){
      // fokus ke field pertama yang invalid
      const firstInvalid = form.querySelector('.field.invalid input, .field.invalid select, .field.invalid textarea');
      if (firstInvalid){ firstInvalid.focus({preventScroll:false}); firstInvalid.scrollIntoView({behavior:'smooth', block:'center'}); }
      return;
    }
    // simulasi sukses
    toast.textContent = 'Profil berhasil disimpan ✔';
    toast.classList.add('show');
    setTimeout(()=>toast.classList.remove('show'), 3300);
  });

  // initial validate (untuk nilai yang sudah terisi dari server)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ()=>{ validateAll(); });
  } else {
    validateAll();
  }
})();
