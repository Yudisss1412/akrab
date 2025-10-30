document.addEventListener('DOMContentLoaded', () => {
  /* ---------- KONST & UTIL: SVG sama dengan halaman_produk + sync wishlist ---------- */
  const WISHLIST_KEY = 'akrab_wishlist';

  // — BINTANG (exact) —
  const STAR_FULL  = `<svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF600"/></svg>`;
  const STAR_HALF  = `<svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M29.6691 32.9982L28.0534 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535V29.2285L29.6691 32.9982ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF700"/></svg>`;
  const STAR_EMPTY = `<svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M17.3316 32.9493L23.5003 29.2285L29.6691 32.9982L28.0535 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535L20.6607 20.5628L13.5128 21.1993L18.9472 25.9482L17.3316 32.9493ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF600"/></svg>`;

  // — HATI (exact) —
  const HEART_EMPTY= `<svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M23.6962 36.3271L23.5003 36.5229L23.2849 36.3271C13.9828 27.8867 7.83366 22.3054 7.83366 16.6458C7.83366 12.7292 10.7712 9.79167 14.6878 9.79167C17.7037 9.79167 20.6412 11.75 21.6791 14.4133H25.3216C26.3595 11.75 29.297 9.79167 32.3128 9.79167C36.2295 9.79167 39.167 12.7292 39.167 16.6458C39.167 22.3054 33.0178 27.8867 23.6962 36.3271ZM32.3128 5.875C28.9053 5.875 25.6349 7.46125 23.5003 9.94833C21.3657 7.46125 18.0953 5.875 14.6878 5.875C8.65616 5.875 3.91699 10.5946 3.91699 16.6458C3.91699 24.0287 10.5753 30.08 20.6607 39.2254L23.5003 41.8104L26.3399 39.2254C36.4253 30.08 43.0837 24.0287 43.0837 16.6458C43.0837 10.5946 38.3445 5.875 32.3128 5.875Z" fill="#FF0000"/></svg>`;
  const HEART_FILL = `<svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M23.5003 41.8104L20.6607 39.2254C10.5753 30.08 3.91699 24.0287 3.91699 16.6458C3.91699 10.5946 8.65616 5.875 14.6878 5.875C18.0953 5.875 21.3657 7.46125 23.5003 9.94833C25.6349 7.46125 28.9053 5.875 32.3128 5.875C38.3445 5.875 43.0837 10.5946 43.0837 16.6458C43.0837 24.0287 36.4253 30.08 26.3399 39.2254L23.5003 41.8104Z" fill="#F24822"/></svg>`;
  const heart = (on)=> (on ? HEART_FILL : HEART_EMPTY);

  const loadWishlist = () => {
    try { return new Set(JSON.parse(localStorage.getItem(WISHLIST_KEY)||'[]')); }
    catch { return new Set(); }
  };
  const saveWishlist = (set) => localStorage.setItem(WISHLIST_KEY, JSON.stringify([...set]));

  function starsHTML(num){
    const r = parseFloat(num)||0;
    let full = Math.floor(r), half = 0;
    const frac = r - full;
    if (frac >= .75) full += 1; else if (frac >= .25) half = 1;
    const empty = Math.max(0, 5 - full - half);
    return `${STAR_FULL.repeat(full)}${half?STAR_HALF:''}${STAR_EMPTY.repeat(empty)}`;
  }

  /* ---------- Galeri ---------- */
  const mainImg = document.getElementById('mainImage');
  const thumbs = document.querySelectorAll('.thumbs .thumb');
  
  // Function to validate and fix main image if needed
  function validateMainImage() {
    if (!mainImg) return;
    
    // Check if main image is valid (not broken)
    if (!mainImg.complete || mainImg.naturalWidth === 0) {
      // Main image failed to load, try to set from first thumbnail
      if (thumbs.length > 0) {
        const firstThumb = thumbs[0];
        mainImg.src = firstThumb.src;
      } else {
        // If no thumbnails, set to placeholder
        mainImg.src = 'https://via.placeholder.com/600x600';
      }
    }
  }
  
  // Handle gallery initialization based on number of thumbnails
  if (thumbs.length > 0) {
    // If there are thumbnails, handle active state and sync with main image
    const activeThumb = document.querySelector('.thumb.is-active');
    
    if (thumbs.length === 1) {
      // If only one thumbnail, make it active and set as main image
      thumbs[0].classList.add('is-active');
      const thumbSrc = thumbs[0].src;
      if (mainImg && mainImg.src !== thumbSrc) {
        mainImg.src = thumbSrc;
      }
    } else {
      // If multiple thumbnails, ensure first one is active by default when no active exists
      if (!activeThumb) {
        // No active thumbnail, set first as active and sync with main image
        thumbs[0].classList.add('is-active');
        const firstThumbSrc = thumbs[0].src;
        if (mainImg && mainImg.src !== firstThumbSrc) {
          mainImg.src = firstThumbSrc;
        }
      } else {
        // Sync main image with active thumbnail
        const activeThumbSrc = activeThumb.src;
        if (mainImg && mainImg.src !== activeThumbSrc) {
          mainImg.src = activeThumbSrc;
        }
      }
    }
  } else {
    // If no thumbnails exist, main image should be from server
    // But if it's broken, we need to handle it
  }
  
  // Validate main image after all setup is complete
  // Use a slightly delayed approach to ensure all images are loaded
  setTimeout(() => {
    if (mainImg) {
      // Force reload of main image to ensure it's loaded
      const currentSrc = mainImg.src;
      mainImg.src = currentSrc; // This triggers reload if needed
      
      // Additional validation after a brief delay
      setTimeout(validateMainImage, 50);
    }
  }, 0);
  
  // Also validate on window load as additional safety
  window.addEventListener('load', validateMainImage);
  
  thumbs.forEach(img => {
    img.addEventListener('click', () => {
      thumbs.forEach(t => t.classList.remove('is-active'));
      img.classList.add('is-active');
      if (mainImg) mainImg.src = img.src;
    });
  });

  /* ---------- Wishlist (sync) ---------- */
  const wishBtn  = document.getElementById('wishBtn');
  const nameEl   = document.getElementById('pdTitle');
  const PROD_NAME = (nameEl?.dataset.name || nameEl?.textContent || '').trim();
  const wl = loadWishlist();

  if (wishBtn && !wishBtn.innerHTML.trim()) { wishBtn.innerHTML = HEART_EMPTY; }

  function paintHeart(){
    const on = wl.has(PROD_NAME);
    if (wishBtn){
      wishBtn.classList.toggle('active', on);
      wishBtn.setAttribute('aria-pressed', on ? 'true' : 'false');
      wishBtn.innerHTML = heart(on);
    }
  }
  paintHeart();

  // ====== Broadcast Channel for Wishlist Sync ======
  const wishlistChannel = new BroadcastChannel('wishlist_sync');
  
  wishBtn?.addEventListener('click', async () => {
    if (!PROD_NAME) return;
    const isAdding = !wl.has(PROD_NAME);
    wl.has(PROD_NAME) ? wl.delete(PROD_NAME) : wl.add(PROD_NAME);
    saveWishlist(wl);
    paintHeart();
    
    // Notify other tabs about the change
    wishlistChannel.postMessage({
      type: 'WISHLIST_UPDATE',
      timestamp: Date.now()
    });
    
    // Also update the server with the wishlist change
    try {
      const productId = document.getElementById('pdTitle')?.dataset.productId;
      if (productId) {
        const response = await fetch('/wishlist', {
          method: isAdding ? 'POST' : 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ 
            product_id: productId 
          })
        });
        
        if (!response.ok) {
          // If server request fails, revert the change
          if (isAdding) {
            wl.add(PROD_NAME);
          } else {
            wl.delete(PROD_NAME);
          }
          saveWishlist(wl);
          paintHeart();
          showNotification('Gagal memperbarui wishlist', 'error');
        }
      }
    } catch (error) {
      console.error('Error updating wishlist on server:', error);
      showNotification('Gagal memperbarui wishlist', 'error');
    }
  });

  // Listen for changes from other tabs/windows via BroadcastChannel
  wishlistChannel.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'WISHLIST_UPDATE') {
      // Refresh from server to get latest state
      refreshWishlistFromServer();
    }
  });
  
  // Fallback to storage event for compatibility
  window.addEventListener('storage', (ev)=>{
    if (ev.key !== WISHLIST_KEY) return;
    const latest = loadWishlist();
    wl.clear(); latest.forEach(v=>wl.add(v));
    paintHeart();
  });
  
  // Function to refresh wishlist from server
  async function refreshWishlistFromServer() {
    try {
      const response = await fetch('/api/customer/wishlist', {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      });
      
      if (response.ok) {
        const data = await response.json();
        const wishlistArray = Array.isArray(data) ? data : [];
        
        // Update our localStorage based on server data
        const updatedWishlist = new Set();
        wishlistArray.forEach(item => {
          updatedWishlist.add(item.title); // Using title as the identifier
        });
        
        saveWishlist(updatedWishlist);
        wl.clear();
        updatedWishlist.forEach(v => wl.add(v));
        paintHeart();
      }
    } catch (error) {
      console.error('Error refreshing wishlist from server:', error);
    }
  }

  /* ---------- Rating bintang ---------- */
  const starsRow = document.getElementById('pdStars');
  if (starsRow){
    const rateText = starsRow.querySelector('.rating-text');
    const val = parseFloat((rateText?.textContent||'').trim())||0;
    starsRow.innerHTML = `${starsHTML(val)}${rateText ? rateText.outerHTML : ''}`;
  }

  // isi bintang di tiap ulasan (SSR atau nanti dummy)
  const fillReviewStars = () => {
    document.querySelectorAll('.rev-stars').forEach(box=>{
      const score = parseFloat(box.dataset.score || '5') || 5;
      box.innerHTML = starsHTML(score);
    });
  };
  fillReviewStars();

  /* ---------- CTA (with API) ---------- */
  const btnAdd = document.querySelector('.btn-add-cart');
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
  btnAdd && btnAdd.addEventListener('click', async () => {
    // Ambil ID produk dari elemen data
    const productTitle = document.getElementById('pdTitle');
    const productId = productTitle?.dataset.productId;
    
    if (!productId) {
      showNotification('Produk tidak ditemukan', 'error');
      return;
    }
    
    // Ambil kuantitas dari input kuantitas
    const qtyInput = document.querySelector('.qty-input');
    const quantity = parseInt(qtyInput?.value) || 1;
    
    try {
      const response = await fetch('/cart/add', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ 
          product_id: productId,
          quantity: quantity
        })
      });

      const result = await response.json();

      if (result.success) {
        showNotification(result.message, 'success');
      } else {
        showNotification(result.message || 'Gagal menambahkan ke keranjang', 'error');
      }
    } catch (error) {
      console.error('Error adding to cart:', error);
      showNotification('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
    }
  });
  
  function showNotification(message, type = 'info') {
    // Buat elemen notifikasi
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Gaya dasar untuk notifikasi
    Object.assign(notification.style, {
      position: 'fixed',
      top: '20px',
      right: '20px',
      padding: '12px 20px',
      borderRadius: '6px',
      color: '#fff',
      backgroundColor: type === 'error' ? '#dc3545' : type === 'success' ? '#28a745' : '#007bff',
      zIndex: '9999',
      boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
      fontSize: '14px',
      maxWidth: '400px',
      wordWrap: 'break-word'
    });
    
    // Tambahkan ke body
    document.body.appendChild(notification);
    
    // Hapus notifikasi setelah 3 detik
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 3000);
  }

  /* ---------- Sample review bila kosong ---------- */
  const hasServerReviews = document.querySelectorAll('#reviewGrid .review-card').length > 0;
  if (!hasServerReviews) renderReviews(sampleReviews);

  // setelah mungkin render dummy, pastikan bintang & scroller siap
  fillReviewStars();
  setupReviewScroller();
});

/* ---------- DUMMY DATA & HELPERS ---------- */
const sampleReviews = [
  { name:'Alex Mathio',    date:'13 Okt 2024', rating:5, text:'NextGen’s dedication to sustainability and ethical practices resonates strongly. Positioning the brand as a responsible choice.' },
  { name:'Alya Prameswari',date:'12 Okt 2024', rating:5, text:'Kualitas produk bagus, pengiriman cepat, dan kemasannya rapi. Overall puas dan akan repeat order.' },
  { name:'Dimas',          date:'10 Okt 2024', rating:4, text:'Rasa mantap, tekstur kental. Kristalisasi cair lagi setelah direndam air hangat.' },
  { name:'Rina',           date:'08 Okt 2024', rating:5, text:'Packaging aman, seller responsif. Recommended!' },
  { name:'Bagas',          date:'07 Okt 2024', rating:4, text:'Sesuai deskripsi, pengiriman cepat.' },
  { name:'Dewi',           date:'05 Okt 2024', rating:5, text:'Rasa enak, repeat order pasti.' },
  { name:'Hendra',         date:'03 Okt 2024', rating:4, text:'Harga oke, kualitas bagus.' },
  { name:'Salsa',          date:'01 Okt 2024', rating:5, text:'Aromanya strong, suka banget!' },
  { name:'Yusuf',          date:'29 Sep 2024', rating:4, text:'Pas buat oleh-oleh, keluarga suka.' },
  { name:'Maya',           date:'27 Sep 2024', rating:5, text:'Fresh & original taste.' }
];

function renderReviews(reviews){
  const grid = document.getElementById('reviewGrid');
  const cnt  = document.getElementById('reviewsCount');
  if(!grid) return;
  grid.innerHTML = (reviews||[]).map(r => `
    <div class="review-card">
      <div class="rev-avatar">${(r.name||'?').charAt(0).toUpperCase()}</div>
      <div class="rev-content">
        <div class="rev-head">
          <span class="rev-name">${r.name||''}</span>
          <span class="rev-date">${r.date||''}</span>
        </div>
        <div class="rev-stars" data-score="${r.rating||5}"></div>
        <p class="rev-text">${r.text||''}</p>
      </div>
    </div>
  `).join('');
  if (cnt) cnt.textContent = `${(reviews||[]).length} ulasan`;
}

function setupReviewScroller(){
  const track = document.getElementById('reviewGrid');
  const prev  = document.querySelector('.rev-prev');
  const next  = document.querySelector('.rev-next');
  if(!track || !prev || !next) return;

  const step = () => Math.max(track.clientWidth * 0.9, 320);

  const update = () => {
    const max = track.scrollWidth - track.clientWidth - 1;
    prev.disabled = track.scrollLeft <= 0;
    next.disabled = track.scrollLeft >= max;
  };

  prev.addEventListener('click', () => track.scrollBy({ left: -step(), behavior: 'smooth' }));
  next.addEventListener('click', () => track.scrollBy({ left:  step(), behavior: 'smooth' }));

  track.addEventListener('scroll', update);
  window.addEventListener('resize', update);
  update();
}
