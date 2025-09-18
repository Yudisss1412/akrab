/* ======= Dummy data (bisa di-API-kan nanti) ======= */
// Utilities
// Menghindari konflik dengan script.js yang sudah mendefinisikan $
const $q = (sel, el = document) => el.querySelector(sel);
const $qa = (sel, el = document) => Array.from(el.querySelectorAll(sel));

const data = {
  user: { name: "yudistiradwianggara" },
  belumDinilai: [
    {
      id: "b1",
      timeISO: "2025-08-29T19:50:00+07:00",
      product: {
        title: "Kaki Kursi Kantor 280 Tanpa Roda (Baru)",
        variant: "",
        url: "#"
      }
    }
  ],
  dinilai: [
    {
      id: "r1",
      timeISO: "2025-07-19T15:37:00+07:00",
      rating: 5,
      kv: [
        ["Fungsi", "beroprasi dengan lancar"],
        ["Fitur", "bagus dan berguna"],
        ["Desain", "menarik dan modern"],
      ],
      product: {
        title: "【MG】 Stick Stik DS4 LightBar + DUS ASLI",
        variant: "Variasi: Hitam, SAMA BOX + KABEL",
        url: "#"
      }
    },
    {
      id: "r2",
      timeISO: "2024-12-04T19:39:00+07:00",
      rating: 5,
      kv: [],
      product: {
        title: "Gedi Luxury 13011 Jam Tangan Wanita Rantai",
        variant: "Variasi: Full Black",
        url: "#"
      }
    }
  ]
};

console.log('=== DUMMY DATA LOADED ===');
console.log('Data object:', data);
console.log('Data dinilai:', data.dinilai);
console.log('Data belum dinilai:', data.belumDinilai);

function formatDate(iso){
  try {
    // Contoh: 29-08-2025 19:50
    const d = new Date(iso);
    const pad = n => n.toString().padStart(2,'0');
    const dd = pad(d.getDate());
    const mm = pad(d.getMonth()+1);
    const yyyy = d.getFullYear();
    const hh = pad(d.getHours());
    const mi = pad(d.getMinutes());
    return `${dd}-${mm}-${yyyy} ${hh}:${mi}`;
  } catch (error) {
    console.error('Error formatting date:', error);
    return '';
  }
}

// SVG bintang yang sama dengan halaman produk detail
const STAR_FULL  = `<svg viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z"/></svg>`;
const STAR_EMPTY = `<svg viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M17.3316 32.9493L23.5003 29.2285L29.6691 32.9982L28.0535 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535L20.6607 20.5628L13.5128 21.1993L18.9472 25.9482L17.3316 32.9493ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z"/></svg>`;

function makeStars(n=0){
  try {
    console.log('makeStars called with rating:', n);
    const wrap = document.createElement('div');
    wrap.className = 'stars';
    
    // Pastikan n adalah angka antara 0-5
    const rating = Math.max(0, Math.min(5, Math.floor(n)));
    console.log('Rating after validation:', rating);
    
    // Buat bintang dari kiri ke kanan (1 sampai 5)
    for(let i=1; i<=5; i++){
      const span = document.createElement('span');
      const isFilled = (i <= rating);
      span.className = 'star-svg ' + (isFilled ? 'filled' : 'empty');
      // Tambahkan atribut data untuk debugging
      span.setAttribute('data-star-index', i);
      span.setAttribute('data-is-filled', isFilled);
      
      // Gunakan innerHTML yang sesuai berdasarkan apakah bintang terisi atau tidak
      span.innerHTML = isFilled ? STAR_FULL : STAR_EMPTY;
      wrap.appendChild(span);
      console.log('Star', i, 'class:', span.className, 'filled:', isFilled, 'innerHTML length:', span.innerHTML.length);
    }
    
    // Tambahkan event listener untuk debugging
    wrap.addEventListener('click', (e) => {
      console.log('Stars container clicked');
    });
    
    console.log('makeStars returning:', wrap);
    return wrap;
  } catch (error) {
    console.error('Error in makeStars:', error);
    return document.createElement('div');
  }
}

function addKV(ul, kvPairs){
  kvPairs.forEach(([k,v])=>{
    const li = document.createElement('li');
    li.innerHTML = `<span class="k">${k}:</span><span class="v">${v}</span>`;
    ul.appendChild(li);
  });
}

/* ======= Renderers ======= */
function renderStats(){
  // Stats section has been removed from HTML
  // So we skip this section
}

function createReviewCard(item, withRating = false) {
  try {
    console.log('=== CREATING REVIEW CARD ===');
    console.log('Item data:', item);
    console.log('With rating:', withRating);
    
    if (!item) {
      console.error('CRITICAL: Item parameter is NULL or UNDEFINED');
      return null;
    }
    
    const card = document.createElement('article');
    card.className = 'review-card';
    card.dataset.id = item.id || 'unknown';
    
    console.log('Card element created:', card);

    // Header
    const header = document.createElement('div');
    header.className = 'rev-head';
    console.log('Header element created:', header);
    
    const userInfo = document.createElement('div');
    userInfo.className = 'user';
    console.log('User info element created:', userInfo);
    
    const userName = document.createElement('div');
    userName.className = 'user__name';
    userName.textContent = data.user.name || 'Unknown User';
    userInfo.appendChild(userName);
    console.log('User name element created:', userName);
    
    header.appendChild(userInfo);
    console.log('User info appended to header');
    
    // Selalu tampilkan bintang rating jika item memiliki rating
    if (item.rating !== undefined) {
      console.log('Creating stars for rating:', item.rating);
      const starsWrap = makeStars(item.rating);
      console.log('Stars wrap created:', starsWrap);
      if (starsWrap) {
        // Tambahkan event listener untuk mengubah rating
        starsWrap.style.cursor = 'pointer';
        starsWrap.addEventListener('click', (e) => {
          // Pastikan hanya merespon klik pada bintang itu sendiri
          console.log('Card stars clicked');
          openReviewModal(item, card);
        });
        
        header.appendChild(starsWrap);
        console.log('Stars wrap appended to header');
      } else {
        console.warn('makeStars returned null');
      }
    }
    
    const timeEl = document.createElement('time');
    timeEl.className = 'rev-time';
    timeEl.dateTime = item.timeISO || new Date().toISOString();
    timeEl.textContent = item.timeISO ? formatDate(item.timeISO) : 'Unknown date';
    console.log('Time element created:', timeEl);
    header.appendChild(timeEl);
    console.log('Time element appended to header');
    
    card.appendChild(header);
    console.log('Header appended to card');

    // Body
    const body = document.createElement('div');
    body.className = 'rev-body';
    console.log('Body element created:', body);
    
    // Tampilkan KV list jika ada
    if (item.kv && item.kv.length) {
      console.log('Creating KV list with', item.kv.length, 'items');
      const kvUL = document.createElement('ul');
      kvUL.className = 'kv';
      addKV(kvUL, item.kv);
      body.appendChild(kvUL);
      console.log('KV list appended to body');
    }
    
    const productLink = document.createElement('a');
    productLink.className = 'product';
    productLink.href = item.product?.url || '#';
    console.log('Product link created:', productLink);
    
    const productTitle = document.createElement('div');
    productTitle.className = 'product__title';
    productTitle.textContent = item.product?.title || 'Produk';
    productLink.appendChild(productTitle);
    console.log('Product title created:', productTitle);
    
    if (item.product?.variant) {
      const productVariant = document.createElement('div');
      productVariant.className = 'product__variant';
      productVariant.textContent = item.product.variant;
      productLink.appendChild(productVariant);
      console.log('Product variant created:', productVariant);
    }
    
    body.appendChild(productLink);
    console.log('Product link appended to body');
    card.appendChild(body);
    console.log('Body appended to card');

    // Footer
    const footer = document.createElement('div');
    footer.className = 'rev-foot';
    console.log('Footer element created:', footer);
    
    const helpBtn = document.createElement('button');
    helpBtn.className = 'btn';
    helpBtn.textContent = 'Bantu';
    helpBtn.addEventListener('click', (e)=>{
      e.currentTarget.classList.toggle('is-on');
      if(e.currentTarget.classList.contains('is-on')){
        e.currentTarget.style.borderColor = 'var(--ok)';
        e.currentTarget.style.color = 'var(--ok)';
      }else{
        e.currentTarget.style.borderColor = 'var(--border)';
        e.currentTarget.style.color = 'var(--text)';
      }
    });
    footer.appendChild(helpBtn);
    console.log('Help button created and appended');
    
    const updateBtn = document.createElement('button');
    updateBtn.className = 'btn primary';
    updateBtn.textContent = item.rating !== undefined ? 'Perbarui' : 'Tulis Ulasan';
    updateBtn.addEventListener('click', ()=> {
      if (item.rating !== undefined) {
        openReviewModal(item, card);
      } else {
        alert('Aksi: Tulis Ulasan untuk item ' + (item.product?.title || ''));
      }
    });
    footer.appendChild(updateBtn);
    console.log('Update button created and appended');
    
    card.appendChild(footer);
    console.log('Footer appended to card');

    console.log('=== REVIEW CARD CREATED SUCCESSFULLY ===');
    console.log('Final card element:', card);
    return card;
  } catch (error) {
    console.error('FATAL ERROR in createReviewCard:', error);
    console.error('Stack trace:', error.stack);
    return null;
  }
}

function renderList(container, items, {withRating}={}){
  try {
    console.log('=== RENDERLIST CALLED ===');
    console.log('Parameters:', {container, items, withRating});
    
    if (!container) {
      console.error('CRITICAL: Container parameter is NULL or UNDEFINED');
      return;
    }
    
    console.log('Container element:', container);
    console.log('Container innerHTML before clearing:', container.innerHTML);
    
    // Clear container
    container.innerHTML = '';
    console.log('Container cleared');
    
    // If no items, show empty state
    if(!items || items.length===0){
      console.log('No items to render, showing empty state');
      const parentSection = container.closest('.tab-content');
      if (parentSection) {
        const emptyState = parentSection.querySelector('.empty-state');
        if (emptyState) {
          console.log('Showing empty state');
          emptyState.hidden = false;
        } else {
          console.warn('Empty state element not found in parent section');
        }
      } else {
        console.warn('Parent section (.tab-content) not found for empty state');
      }
      return;
    }

    // Hide empty state
    console.log('Items found, hiding empty state');
    const parentSection = container.closest('.tab-content');
    if (parentSection) {
      const emptyState = parentSection.querySelector('.empty-state');
      if (emptyState) {
        console.log('Hiding empty state');
        emptyState.hidden = true;
      } else {
        console.warn('Empty state element not found in parent section');
      }
    } else {
      console.warn('Parent section (.tab-content) not found for hiding empty state');
    }

    // Render items
    console.log('Rendering', items.length, 'items');
    items.forEach((item, index)=>{
      console.log('Rendering item', index, ':', item);
      // Gunakan withRating dari parameter atau tentukan berdasarkan keberadaan rating di item
      const shouldShowRating = withRating !== undefined ? withRating : (item.rating !== undefined);
      const card = createReviewCard(item, shouldShowRating);
      if (card) {
        console.log('Appending card to container');
        container.appendChild(card);
      } else {
        console.warn('createReviewCard returned null for item', index);
      }
    });
    
    console.log('=== RENDERLIST COMPLETE ===');
    console.log('Final container innerHTML:', container.innerHTML);
  } catch (error) {
    console.error('FATAL ERROR in renderList:', error);
    console.error('Stack trace:', error.stack);
  }
}

/* ======= Tabs & Toolbar ======= */
function setupTabs(){
  try {
    console.log('=== SETTING UP TABS ===');
    
    const tabs = $qa('.tab');
    console.log('Found tabs:', tabs);
    console.log('Number of tabs found:', tabs.length);
    
    if (tabs.length === 0) {
      console.warn('No tabs found with selector .tab');
      return;
    }

    function activate(tabElement){
      try {
        console.log('Activating tab:', tabElement);
        if (!tabElement) {
          console.error('Tab element is null or undefined');
          return;
        }
        
        // Remove active class from all tabs
        const allTabs = $qa('.tab');
        console.log('All tabs to deactivate:', allTabs);
        allTabs.forEach(t => {
          console.log('Removing active class from tab:', t);
          t.classList.remove('active');
        });
        
        // Add active class to clicked tab
        console.log('Adding active class to tab:', tabElement);
        tabElement.classList.add('active');
        
        // Hide all tab contents
        const tabContents = $qa('.tab-content');
        console.log('Tab contents to hide:', tabContents);
        tabContents.forEach(content => {
          console.log('Hiding tab content:', content);
          content.classList.remove('active');
          content.hidden = true;
        });
        
        // Show appropriate content based on tab
        const tabName = tabElement.dataset.tab;
        console.log('Tab name from data attribute:', tabName);
        if (!tabName) {
          console.error('Tab element missing data-tab attribute');
          return;
        }
        
        const targetContent = $q(`#${tabName}-content`);
        console.log('Target content element:', targetContent);
        if(targetContent) {
          console.log('Showing target content');
          targetContent.classList.add('active');
          targetContent.hidden = false;
        } else {
          console.error(`Target content #${tabName}-content not found`);
        }
      } catch (error) {
        console.error('Error in activate function:', error);
      }
    }

    tabs.forEach((t, index) => {
      console.log('Adding click listener to tab', index, ':', t);
      t.addEventListener('click', () => {
        console.log('Tab', index, 'clicked');
        activate(t);
      });
    });

    // init - activate first tab
    if(tabs.length > 0) {
      console.log('Activating first tab');
      activate(tabs[0]);
    }
    
    console.log('=== TABS SETUP COMPLETE ===');
  } catch (error) {
    console.error('FATAL ERROR in setupTabs:', error);
    console.error('Stack trace:', error.stack);
  }
}

function setupSearchAndSort(){
  // Untuk saat ini kita tidak mengimplementasikan search dan sort
  // karena tidak ada elemen input yang sesuai dalam HTML
}

let currentEditCard = null;

function openReviewModal(item, cardElement) {
  console.log('Opening review modal for item:', item);
  console.log('Item rating:', item.rating);
  
  // Simpan referensi card yang sedang diedit
  currentEditCard = cardElement;
  
  // Isi data ke dalam modal
  const modal = document.getElementById('editReviewModal');
  if (!modal) {
    console.log('Modal element not found');
    return;
  }
  
  // Isi data produk
  const productNameInput = document.getElementById('productName');
  if (productNameInput) {
    productNameInput.value = item.product?.title || '';
  }
  
  // Isi rating - pastikan menggunakan rating terbaru dari item
  const ratingContainer = document.getElementById('rating');
  if (ratingContainer) {
    console.log('Setting rating in modal to:', item.rating);
    // Reset rating - hapus semua kelas active terlebih dahulu
    const allStars = ratingContainer.querySelectorAll('.star');
    allStars.forEach(star => {
      star.classList.remove('active');
    });
    
    // Aktifkan bintang sesuai rating terbaru
    allStars.forEach((star) => {
      const starValue = parseInt(star.dataset.value);
      const isActive = starValue <= (item.rating || 0);
      if (isActive) {
        star.classList.add('active');
      }
      console.log('Star', starValue, 'set to active:', isActive);
    });
    
    // Log status akhir untuk debugging
    allStars.forEach((star, index) => {
      const isActive = star.classList.contains('active');
      console.log('Final Modal Star', index + 1, 'active:', isActive);
    });
  }
  
  // Isi ulasan teks
  const reviewTextInput = document.getElementById('reviewText');
  if (reviewTextInput) {
    // Format kv pairs menjadi teks yang mudah dibaca
    let reviewText = '';
    if (item.kv && item.kv.length > 0) {
      reviewText = item.kv.map(([key, value]) => `${key}: ${value}`).join('\n');
    }
    reviewTextInput.value = reviewText;
  }
  
  // Simpan ID review untuk penggunaan saat submit
  const reviewIdInput = document.getElementById('reviewId');
  if (reviewIdInput) {
    reviewIdInput.value = item.id || '';
  }
  
  // Tampilkan modal
  modal.hidden = false;
  
  console.log('Modal opened with rating:', item.rating);
}

function closeReviewModal() {
  const modal = document.getElementById('editReviewModal');
  if (modal) {
    modal.hidden = true;
  }
  // Reset referensi card
  currentEditCard = null;
  
  // Update tampilan card setelah modal ditutup
  console.log('Review modal closed');
}

function setupModalEventListeners() {
  // Setup close button
  const closeModalBtn = $q('#closeModal');
  if (closeModalBtn) {
    closeModalBtn.addEventListener('click', closeReviewModal);
  }
  
  // Setup overlay click to close
  const modalOverlay = $q('.modal-overlay');
  if (modalOverlay) {
    modalOverlay.addEventListener('click', closeReviewModal);
  }
  
  // Setup rating stars
  const ratingContainer = $q('#rating');
  if (ratingContainer) {
    console.log('Setting up rating container event listener');
    ratingContainer.addEventListener('click', (e) => {
      console.log('Rating container clicked, target:', e.target);
      console.log('Target class list:', e.target.classList);
      
      if (e.target.classList.contains('star')) {
        const value = parseInt(e.target.dataset.value);
        console.log('Star clicked, value:', value);
        
        // Aktifkan bintang secara berurutan dari kiri ke kanan
        const stars = ratingContainer.querySelectorAll('.star');
        console.log('Total stars found:', stars.length);
        
        // Reset semua bintang terlebih dahulu
        stars.forEach(star => {
          star.classList.remove('active');
        });
        
        // Aktifkan bintang dari 1 hingga nilai yang diklik
        for (let i = 0; i < stars.length; i++) {
          const starValue = parseInt(stars[i].dataset.value);
          if (starValue <= value) {
            stars[i].classList.add('active');
          }
        }
        
        // Log status akhir untuk debugging
        stars.forEach((star, index) => {
          const isActive = star.classList.contains('active');
          console.log('Final Clicked Star', index + 1, 'active:', isActive);
        });
        
        // Update nilai rating di UI real-time (opsional)
        console.log('Rating updated to:', value);
      } else {
        console.log('Clicked element is not a star');
      }
    });
  }
  
  // Setup form submit
  const editReviewForm = $q('#editReviewForm');
  if (editReviewForm) {
    editReviewForm.addEventListener('submit', (e) => {
      e.preventDefault();
      console.log('Form submitted');
      saveReviewChanges();
    });
  }
  
  // Setup delete button
  const deleteReviewBtn = $q('#deleteReview');
  if (deleteReviewBtn) {
    deleteReviewBtn.addEventListener('click', () => {
      // Hapus ulasan (implementasi sesuai kebutuhan)
      console.log('Deleting review');
      closeReviewModal();
    });
  }
}

function saveReviewChanges() {
  console.log('saveReviewChanges called');
  // Dapatkan data dari form
  const reviewId = document.getElementById('reviewId').value;
  
  // Hitung rating dengan cara yang lebih akurat
  let rating = 0;
  const ratingContainer = document.getElementById('rating');
  if (ratingContainer) {
    const stars = ratingContainer.querySelectorAll('.star');
    // Hitung bintang aktif dari kiri ke kanan
    for (let i = 0; i < stars.length; i++) {
      if (stars[i].classList.contains('active')) {
        rating = i + 1; // Rating adalah posisi bintang terakhir yang aktif
      } else {
        break; // Berhenti ketika menemukan bintang tidak aktif
      }
    }
  }
  
  console.log('Calculated rating from modal:', rating);
  
  // Log semua bintang dan status aktifnya untuk debugging
  if (ratingContainer) {
    const allStars = ratingContainer.querySelectorAll('.star');
    allStars.forEach((star, index) => {
      const isActive = star.classList.contains('active');
      console.log('Modal Star', index + 1, 'active:', isActive);
    });
  }
  
  const reviewText = document.getElementById('reviewText').value;
  
  // Parsing teks ulasan menjadi pasangan key-value
  const kv = [];
  if (reviewText) {
    const lines = reviewText.split('\n');
    lines.forEach(line => {
      if (line.trim()) {
        const colonIndex = line.indexOf(':');
        if (colonIndex > 0) {
          const key = line.substring(0, colonIndex).trim();
          const value = line.substring(colonIndex + 1).trim();
          kv.push([key, value]);
        } else {
          // Jika tidak ada format key:value, tambahkan sebagai teks umum
          kv.push(['Ulasan', line.trim()]);
        }
      }
    });
  }
  
  // Gunakan referensi card yang disimpan atau temukan card yang sesuai
  let reviewCard = currentEditCard;
  if (!reviewCard) {
    reviewCard = document.querySelector(".review-card[data-id='" + reviewId + "']");
  }
  
  if (reviewCard) {
    console.log('Updating card rating to:', rating);
    // Perbarui rating di header card
    updateCardRating(reviewCard, rating);
    
    // Perbarui konten KV jika ada
    const kvList = reviewCard.querySelector('.kv');
    if (kvList) {
      // Simpan referensi ke parent dan next sibling sebelum menghapus
      const parent = kvList.parentNode;
      const nextSibling = kvList.nextSibling;
      
      // Hapus KV list lama
      kvList.remove();
      
      // Buat KV list baru
      const newKvList = document.createElement('ul');
      newKvList.className = 'kv';
      addKV(newKvList, kv);
      
      // Sisipkan KV list baru di posisi yang sama
      if (nextSibling) {
        parent.insertBefore(newKvList, nextSibling);
      } else {
        parent.appendChild(newKvList);
      }
    }
    
    // Update data item dalam objek data untuk memastikan perubahan persisten
    const reviewItem = data.dinilai.find(item => item.id === reviewId);
    if (reviewItem) {
      const oldRating = reviewItem.rating;
      reviewItem.rating = rating;
      reviewItem.kv = kv;
      console.log('Data updated from rating', oldRating, 'to', rating, ':', reviewItem);
    }
  }
  
  // Tutup modal
  closeReviewModal();
  
  // Reset referensi card
  currentEditCard = null;
  
  // Tampilkan pesan sukses (opsional)
  console.log('Review changes saved successfully');
  console.log('Updated rating:', rating);
}

function updateCardRating(cardElement, newRating) {
  console.log('updateCardRating called with:', {cardElement, newRating});
  // Perbarui rating di header card
  const header = cardElement.querySelector('.rev-head');
  if (header) {
    // Hapus bintang lama jika ada
    const oldStars = header.querySelector('.stars');
    if (oldStars) {
      console.log('Removing old stars');
      oldStars.remove();
    }
    
    // Buat bintang baru
    console.log('Creating new stars with rating:', newRating);
    const newStars = makeStars(newRating);
    
    // Tambahkan event listener untuk mengubah rating
    newStars.style.cursor = 'pointer';
    newStars.addEventListener('click', (e) => {
      // Temukan item data berdasarkan ID card
      const cardId = cardElement.dataset.id;
      const item = data.dinilai.find(i => i.id === cardId);
      if (item) {
        console.log('Opening modal from card stars click');
        console.log('Item rating when opening modal:', item.rating);
        // Update rating item sebelum membuka modal
        item.rating = newRating;
        openReviewModal(item, cardElement);
      }
    });
    
    // Cari posisi yang benar untuk menyisipkan bintang
    // Urutan yang benar: userInfo → stars → timeEl
    const timeEl = header.querySelector('.rev-time');
    if (timeEl) {
      // Sisipkan bintang sebelum elemen waktu
      console.log('Inserting stars before time element');
      header.insertBefore(newStars, timeEl);
    } else {
      // Jika tidak ada elemen waktu, tambahkan di akhir header
      console.log('Appending stars to header');
      header.appendChild(newStars);
    }
    
    // Log status bintang yang baru dibuat untuk debugging
    const starElements = newStars.querySelectorAll('.star-svg');
    starElements.forEach((star, index) => {
      const isFilled = star.classList.contains('filled');
      const innerHTML = star.innerHTML;
      console.log('New Card Star', index + 1, 'filled:', isFilled, 'innerHTML contains FULL:', innerHTML.includes('M11.4076 41.1253'));
    });
    
    console.log('Card rating updated to:', newRating);
  } else {
    console.error('Header not found in card element');
  }
}

/* ======= Init ======= */
document.addEventListener('DOMContentLoaded', ()=>{
  try {
    console.log('=== INITIALIZING HALAMAN ULASAN ===');
    console.log('Document loaded, starting initialization');
    
    // Setup modal event listeners
    setupModalEventListeners();
    
    // Check if required elements exist
    const reviewList = $q('#reviewList');
    const ulasanContent = $q('#ulasan-content');
    const emptyState = $q('#emptyState');
    
    console.log('Elements found:', {
      reviewList: reviewList ? 'YES' : 'NO',
      ulasanContent: ulasanContent ? 'YES' : 'NO',
      emptyState: emptyState ? 'YES' : 'NO'
    });
    
    if (!reviewList) {
      console.error('CRITICAL: reviewList element (#reviewList) NOT FOUND!');
      return;
    }
    
    console.log('Dummy data:', data);
    console.log('Data dinilai:', data.dinilai);
    
    // renderStats(); // Stats section has been removed from HTML, so we skip this
    
    console.log('Rendering reviews...');
    // Tambahkan pengecekan tambahan untuk data
    if (data && data.dinilai) {
      console.log('Data dinilai ditemukan, jumlah:', data.dinilai.length);
      renderList(reviewList, data.dinilai, {withRating:true});
    } else {
      console.error('Data dinilai tidak ditemukan atau tidak valid');
      // Tampilkan empty state jika data tidak valid
      if (emptyState) {
        emptyState.hidden = false;
      }
    }
    
    console.log('Setting up tabs...');
    setupTabs();
    
    console.log('Setting up search and sort...');
    setupSearchAndSort();

    // back button (optional: change URL according to your app)
    const backBtn = $q('#btnBack');
    if (backBtn) {
      console.log('Setting up back button...');
      backBtn.addEventListener('click', ()=> history.length>1 ? history.back() : location.href = './profil_pembeli.html');
    } else {
      console.warn('Back button (#btnBack) not found');
    }
    
    console.log('=== INITIALIZATION COMPLETE ===');
  } catch (error) {
    console.error('FATAL ERROR in document initialization:', error);
    console.error('Stack trace:', error.stack);
  }
});