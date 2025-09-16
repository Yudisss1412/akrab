// Demo: klik metode pembayaran → toggle aktif
document.querySelectorAll('.methods button').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    document.querySelectorAll('.methods button').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    alert('Metode pembayaran dipilih: ' + btn.innerText);
  });
});

// Demo: klik buat pesanan
document.querySelector('.btn-order').addEventListener('click', ()=>{
  alert('Pesanan berhasil dibuat!');
});
