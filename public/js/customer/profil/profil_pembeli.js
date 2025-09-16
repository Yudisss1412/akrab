// Bikin wishlist hanya tampil 1 item (sisanya scroll),
// dan selaraskan batas bawah Riwayat Pesanan dengan bawah card Wishlist.
(function alignOrdersToWishlistBottom(){
  const wishCard   = document.getElementById('wishlistCard');
  const wishVP     = document.getElementById('wishlistViewport');
  const ordersSec  = document.getElementById('ordersSection');
  const ordersHead = ordersSec ? ordersSec.querySelector('.orders-head') : null;
  const ordersVP   = document.getElementById('ordersViewport');

  function clamp(n, min){ return n < min ? min : n; }

  function recompute(){
    // 1) Wishlist: tampilkan hanya 1 item
    if (wishVP) {
      const firstItem = wishVP.querySelector('.wishlist-card');
      if (firstItem) {
        // tinggi nyata item pertama
        const h = firstItem.getBoundingClientRect().height;
        wishVP.style.maxHeight = `${h}px`;
      }
    }

    // 2) Riwayat Pesanan: sejajarkan batas bawah dengan bawah card Wishlist
    if (wishCard && ordersSec && ordersHead && ordersVP) {
      const wishBottom = wishCard.getBoundingClientRect().bottom;
      const ordersTop  = ordersSec.getBoundingClientRect().top;
      const headH      = ordersHead.getBoundingClientRect().height;

      const secStyle   = getComputedStyle(ordersSec);
      const padTop     = parseFloat(secStyle.paddingTop) || 0;
      const padBottom  = parseFloat(secStyle.paddingBottom) || 0;

      // total ruang vertikal yang tersedia untuk viewport di dalam ordersSection
      let available = wishBottom - ordersTop - headH - padTop - padBottom;

      // cegah nilai minus/terlalu kecil
      available = clamp(available, 160);

      ordersVP.style.maxHeight = `${available}px`;
    }
  }

  window.addEventListener('load', recompute);
  window.addEventListener('resize', recompute);
  // antisipasi font/layout async
  setTimeout(recompute, 60);
})();

// Demo handler "Beli Lagi"
document.addEventListener('click', (e)=>{
  const btn = e.target.closest('.buy-again');
  if(!btn) return;
  const card = btn.closest('.order-card');
  const id = card?.dataset?.orderId || '(tanpa nomor)';
  alert(`Pesanan ${id} dimasukkan lagi ke keranjang ✅`);
});

// Toggle tombol wishlist (love)
document.addEventListener('click', (e)=>{
  const btn = e.target.closest('.btn-wish');
  if(!btn) return;

  const offSvg = btn.querySelector('.svg-off');
  const onSvg  = btn.querySelector('.svg-on');
  const isOn   = btn.getAttribute('aria-pressed') === 'true';

  btn.setAttribute('aria-pressed', (!isOn).toString());
  if(isOn){
    if(offSvg) offSvg.style.display = '';
    if(onSvg)  onSvg.style.display  = 'none';
    btn.dataset.state = 'off';
  }else{
    if(offSvg) offSvg.style.display = 'none';
    if(onSvg)  onSvg.style.display  = '';
    btn.dataset.state = 'on';
  }
});

// === Clamp "Riwayat Ulasan" to show exactly one card height, scroll the rest ===
(function clampReviewsToOneCard(){
  function recompute(){
    var vp = document.querySelector('.reviews-viewport');
    if (!vp) return;
    var first = vp.querySelector('.review-card');
    if (!first) return;

    // Hitung tinggi card pertama (setelah gambar termuat)
    var h = first.getBoundingClientRect().height;
    if (h && h > 0) {
      vp.style.maxHeight = h + 'px';
      vp.style.overflowY = 'auto';
    }
  }

  // Recompute saat layout siap, gambar selesai, dan saat resize
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', recompute);
  } else {
    recompute();
  }
  window.addEventListener('load', recompute);
  window.addEventListener('resize', recompute);
})();

document.addEventListener('click', function(e){
  const link = e.target.closest('a.js-logout');
  if (!link) return;

  // (opsional) bersihkan jejak login di storage
  try {
    const keep = new Set(['akrab_wishlist']);
    Object.keys(localStorage).forEach(k=>{
      if (!keep.has(k) && /^(akrab_|token|access_token|refresh_token|user|remember)/i.test(k)) {
        localStorage.removeItem(k);
      }
    });
    sessionStorage.clear();
  } catch {}

  // Penting: JANGAN panggil e.preventDefault(),
  // biarkan browser lanjut ke link.href ({{ route('welcome') }})
});
