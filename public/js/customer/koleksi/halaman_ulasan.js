/* ======= Data will be loaded from API ======= */
// Utilities
// Menghindari konflik dengan script.js yang sudah mendefinisikan $
const $q = (sel, el = document) => el.querySelector(sel);
const $qa = (sel, el = document) => Array.from(el.querySelectorAll(sel));

// Define data object that will be populated from API
let data = {
  user: { name: "" },
  belumDinilai: [],
  dinilai: []
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
        starsWrap.addEventListener('click', handleCardStarsClick);
        
        // Fungsi terpisah untuk menangani klik bintang di card
        function handleCardStarsClick(e) {
          // Pastikan hanya merespon klik pada bintang itu sendiri
          console.log('Card stars clicked');
          openReviewModal(item, card);
        }
        
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

    // Images section (if any images exist)
    if (item.images && item.images.length > 0) {
      const imagesContainer = document.createElement('div');
      imagesContainer.className = 'review-images';
      
      const imagesTitle = document.createElement('div');
      imagesTitle.className = 'images-title';
      imagesTitle.textContent = 'Foto Ulasan:';
      imagesContainer.appendChild(imagesTitle);
      
      const imagesGrid = document.createElement('div');
      imagesGrid.className = 'images-grid';
      
      item.images.forEach((image, index) => {
        const imageWrapper = document.createElement('div');
        imageWrapper.className = 'image-wrapper';
        imageWrapper.addEventListener('click', () => {
          openLightbox(image, index, item.images);
        });
        
        // Untuk demo, kita akan buat placeholder gambar
        // Dalam implementasi nyata, ini akan berisi URL gambar yang sebenarnya
        const placeholder = document.createElement('div');
        placeholder.className = 'image-placeholder';
        placeholder.textContent = '📷';
        
        const imageInfo = document.createElement('div');
        imageInfo.className = 'image-info';
        imageInfo.textContent = image.name || `Foto ${index + 1}`;
        
        imageWrapper.appendChild(placeholder);
        imageWrapper.appendChild(imageInfo);
        imagesGrid.appendChild(imageWrapper);
      });
      
      imagesContainer.appendChild(imagesGrid);
      card.appendChild(imagesContainer);
      console.log('Images section appended to card');
    }

    // Footer
    const footer = document.createElement('div');
    footer.className = 'rev-foot';
    console.log('Footer element created:', footer);
    
    const updateBtn = document.createElement('button');
    updateBtn.className = 'btn btn-primary';
    updateBtn.textContent = item.rating !== undefined ? 'Perbarui' : 'Tulis Ulasan';
    
    // Store item and card references in the button's dataset for debugging
    updateBtn.dataset.itemId = item.id;
    updateBtn.dataset.itemTitle = item.product?.title || '';
    
    updateBtn.addEventListener('click', ()=> {
      console.log('Perbarui button clicked');
      console.log('Button dataset:', updateBtn.dataset);
      console.log('Item data:', item);
      console.log('Card element:', card);
      if (item.rating !== undefined) {
        console.log('Opening review modal');
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

function renderList(container, items, {withRating}={}) {
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
      t.addEventListener('click', handleTabClick);
      
      // Fungsi terpisah untuk menangani klik tab
      function handleTabClick() {
        console.log('Tab', index, 'clicked');
        activate(t);
      }
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
  console.log('=== OPENING REVIEW MODAL ===');
  console.log('Opening review modal for item:', item);
  console.log('Item rating:', item.rating);
  console.log('Card element:', cardElement);
  
  // Simpan referensi card yang sedang diedit
  currentEditCard = cardElement;
  console.log('Set currentEditCard:', currentEditCard);
  
  // Tambahkan class ke body untuk mencegah scrolling
  document.body.classList.add('modal-open');
  console.log('Added modal-open class to body');
  
  // Reset image files for new review
  window.currentReviewImages = [];
  console.log('Reset window.currentReviewImages to empty array');
  
  // Isi data ke dalam modal
  const modal = document.getElementById('editReviewModal');
  console.log('Modal element:', modal);
  console.log('Modal element type:', typeof modal);
  console.log('Modal element found:', !!modal);
  
  if (!modal) {
    console.log('Modal element not found');
    // Try to find it another way
    const modals = document.querySelectorAll('#editReviewModal');
    console.log('Number of modal elements found with querySelectorAll:', modals.length);
    if (modals.length > 0) {
      console.log('Found modal with querySelectorAll:', modals[0]);
    }
    return;
  }
  console.log('Modal element found, continuing...');
  
  // Check if modal is in the DOM
  console.log('Modal parent node:', modal.parentNode);
  console.log('Modal is connected to DOM:', modal.isConnected);
  
  // Check modal styles
  console.log('Modal computed display:', window.getComputedStyle(modal).display);
  console.log('Modal hasAttribute hidden:', modal.hasAttribute('hidden'));
  
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
  
  // Jika item memiliki gambar, siapkan untuk ditampilkan di modal
  if (item.images && item.images.length > 0) {
    // Konversi gambar yang ada ke format yang bisa ditampilkan di modal
    window.currentReviewImages = item.images.map(img => ({
      name: img.name,
      size: img.size,
      type: img.type,
      dataURL: img.dataURL || img.url || null
    }));
  }
  
  // Update image previews
  const previewContainer = document.getElementById('previewContainer');
  const dropArea = document.getElementById('dropArea');
  if (previewContainer && dropArea) {
    if (window.currentReviewImages.length > 0) {
      previewContainer.style.display = 'block';
      dropArea.style.display = 'none';
      updateImagePreviews();
    } else {
      previewContainer.style.display = 'none';
      dropArea.style.display = 'block';
    }
  }
  
  // Simpan ID review untuk penggunaan saat submit
  const reviewIdInput = document.getElementById('reviewId');
  if (reviewIdInput) {
    reviewIdInput.value = item.id || '';
    console.log('Set review ID input to:', item.id);
  }
  
  // Setup modal event listeners setiap kali modal dibuka
  setupModalEventListeners();
  
  // Tampilkan modal
  console.log('About to show modal, current hidden state:', modal.hidden);
  console.log('Modal hasAttribute hidden:', modal.hasAttribute('hidden'));
  // Use removeAttribute instead of setting hidden property to false
  modal.removeAttribute('hidden');
  console.log('Modal hidden attribute removed');
  console.log('Modal hasAttribute hidden after removal:', modal.hasAttribute('hidden'));
  
  // Force reflow to ensure changes take effect
  modal.offsetHeight;
  
  // Check if modal is now visible
  console.log('Modal computed display after removal:', window.getComputedStyle(modal).display);
  
  console.log('Modal opened with rating:', item.rating);
  console.log('=== FINISHED OPENING REVIEW MODAL ===');
}

function closeReviewModal() {
  console.log('=== CLOSING REVIEW MODAL ===');
  const modal = document.getElementById('editReviewModal');
  console.log('Modal element:', modal);
  if (modal) {
    // Use setAttribute instead of setting hidden property to true
    modal.setAttribute('hidden', '');
    console.log('Modal hidden attribute added');
  }
  // Reset referensi card
  currentEditCard = null;
  
  // Hapus class dari body untuk mengizinkan scrolling kembali
  document.body.classList.remove('modal-open');
  console.log('Removed modal-open class from body');
  
  // Reset image files
  window.currentReviewImages = [];
  
  // Reset form
  const editReviewForm = $q('#editReviewForm');
  if (editReviewForm) {
    editReviewForm.reset();
    console.log('Edit review form reset');
  }
  
  // Reset rating stars
  const ratingContainer = document.getElementById('rating');
  if (ratingContainer) {
    const stars = ratingContainer.querySelectorAll('.star');
    stars.forEach(star => {
      star.classList.remove('active');
    });
    console.log('Rating stars reset');
  }
  
  // Reset image previews
  const previewContainer = document.getElementById('previewContainer');
  const dropArea = document.getElementById('dropArea');
  if (previewContainer && dropArea) {
    previewContainer.style.display = 'none';
    dropArea.style.display = 'block';
    const imagePreviews = document.getElementById('imagePreviews');
    if (imagePreviews) {
      imagePreviews.innerHTML = '';
    }
    console.log('Image previews reset');
  }
  
  // Reset review ID
  const reviewIdInput = document.getElementById('reviewId');
  if (reviewIdInput) {
    reviewIdInput.value = '';
    console.log('Review ID input reset');
  }
  
  // Tutup lightbox jika terbuka
  closeLightbox();
  
  console.log('Review modal closed');
  console.log('=== FINISHED CLOSING REVIEW MODAL ===');
}

function setupModalEventListeners() {
  console.log('=== SETTING UP MODAL EVENT LISTENERS ===');
  
  // Setup close button
  const closeModalBtn = $q('#closeModal');
  console.log('Close button:', closeModalBtn);
  if (closeModalBtn) {
    closeModalBtn.addEventListener('click', closeReviewModal);
    console.log('Close button event listener added');
  } else {
    console.log('Close button not found');
  }
  
  // Setup overlay click to close
  const modalOverlay = $q('.modal-overlay');
  console.log('Modal overlay:', modalOverlay);
  if (modalOverlay) {
    modalOverlay.addEventListener('click', closeReviewModal);
    console.log('Modal overlay event listener added');
  } else {
    console.log('Modal overlay not found');
  }
  
  // Setup rating stars
  const ratingContainer = $q('#rating');
  console.log('Rating container:', ratingContainer);
  if (ratingContainer) {
    console.log('Setting up rating container event listener');
    ratingContainer.addEventListener('click', handleRatingClick);
    console.log('Rating container event listener added');
  } else {
    console.log('Rating container not found');
  }
  
  // Setup form submit
  const editReviewForm = $q('#editReviewForm');
  console.log('Edit review form:', editReviewForm);
  if (editReviewForm) {
    editReviewForm.addEventListener('submit', handleFormSubmit);
    console.log('Edit review form event listener added');
  } else {
    console.log('Edit review form not found');
  }
  
  // Setup delete button
  const deleteReviewBtn = $q('#deleteReview');
  console.log('Delete review button:', deleteReviewBtn);
  if (deleteReviewBtn) {
    deleteReviewBtn.addEventListener('click', handleDeleteReview);
    console.log('Delete review button event listener added');
  } else {
    console.log('Delete review button not found');
  }
  
  // Setup drag and drop functionality
  setupDragAndDrop();
  console.log('=== FINISHED SETTING UP MODAL EVENT LISTENERS ===');
}

// Fungsi terpisah untuk menangani klik rating
function handleRatingClick(e) {
  console.log('Rating container clicked, target:', e.target);
  console.log('Target class list:', e.target.classList);
  
  if (e.target.classList.contains('star')) {
    const value = parseInt(e.target.dataset.value);
    console.log('Star clicked, value:', value);
    
    // Aktifkan bintang secara berurutan dari kiri ke kanan
    const ratingContainer = document.getElementById('rating');
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
}

// Fungsi terpisah untuk menangani submit form
function handleFormSubmit(e) {
  e.preventDefault();
  console.log('Form submitted');
  saveReviewChanges();
}

// Fungsi terpisah untuk menangani hapus ulasan
function handleDeleteReview() {
  if (confirm('Apakah Anda yakin ingin menghapus ulasan ini?')) {
    const reviewId = document.getElementById('reviewId').value.trim();
    if (reviewId) {
      deleteReviewFromServer(reviewId)
        .then(response => {
          if (response.success) {
            // Close modal and remove the review from the UI
            closeReviewModal();
            
            // Remove the review from local data
            removeFromLocalData(reviewId);
            
            // Show success message
            alert('Ulasan berhasil dihapus');
          } else {
            console.error('Error deleting review:', response.message);
            alert('Gagal menghapus ulasan: ' + response.message);
          }
        })
        .catch(error => {
          console.error('Error deleting review:', error);
          alert('Terjadi kesalahan saat menghapus ulasan');
        });
    }
  }
}

// Function to delete review from server
async function deleteReviewFromServer(reviewId) {
  const response = await fetch(`/api/reviews/${reviewId}`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : ''
    }
  });
  
  return await response.json();
}

// Function to remove the review from local data
function removeFromLocalData(reviewId) {
  // Find the review in the local data and remove it
  const reviewIndex = data.dinilai.findIndex(r => r.id == reviewId);
  if (reviewIndex !== -1) {
    data.dinilai.splice(reviewIndex, 1);
    
    // Remove the card from the UI
    if (currentEditCard && currentEditCard.parentNode) {
      currentEditCard.parentNode.removeChild(currentEditCard);
    }
    
    // Check if there are no more reviews and show empty state
    const reviewList = $q('#reviewList');
    if (reviewList && reviewList.children.length === 0) {
      const parentSection = reviewList.closest('.tab-content');
      if (parentSection) {
        const emptyState = parentSection.querySelector('.empty-state');
        if (emptyState) {
          emptyState.hidden = false;
        }
      }
    }
  }
}

function saveReviewChanges() {
  console.log('saveReviewChanges called');
  // Dapatkan data dari form
  const reviewId = document.getElementById('reviewId').value.trim();
  console.log('Review ID from form:', reviewId);
  
  if (!reviewId) {
    console.error('Review ID is empty or undefined');
    return;
  }
  
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
  
  // Dapatkan file gambar yang dipilih (jika ada)
  const selectedFiles = window.currentReviewImages || [];
  console.log('Selected images:', selectedFiles);
  console.log('Number of selected images:', selectedFiles.length);
  console.log('Window currentReviewImages:', window.currentReviewImages);
  console.log('Window currentReviewImages type:', typeof window.currentReviewImages);
  
  // For debugging - let's log the actual files
  if (selectedFiles && selectedFiles.length > 0) {
    selectedFiles.forEach((file, index) => {
      console.log('File', index, ':', file.name, file.size, file.type);
    });
  } else {
    console.log('No files selected');
  }
  
  // Gunakan referensi card yang disimpan atau temukan card yang sesuai
  let reviewCard = currentEditCard;
  console.log('Current edit card:', currentEditCard);
  console.log('Current edit card type:', typeof currentEditCard);
  if (!reviewCard) {
    console.log('Searching for card with ID:', reviewId);
    reviewCard = document.querySelector(".review-card[data-id='" + reviewId + "']");
    console.log('Found card by querySelector:', reviewCard);
    if (!reviewCard) {
      console.error('Could not find review card with ID:', reviewId);
      // Try to find any card as fallback
      reviewCard = document.querySelector('.review-card');
      console.log('Fallback - found any card:', reviewCard);
    }
  } else {
    console.log('Using current edit card');
  }
  
  console.log('Review card found:', reviewCard);
  console.log('Review ID:', reviewId);
  console.log('Review card dataset ID:', reviewCard ? reviewCard.dataset.id : 'N/A');
  
  // Update review di backend (for now, just show a message)
  updateReviewOnServer(reviewId, rating, reviewText, selectedFiles)
    .then(response => {
      if (response.success) {
        // Tutup modal
        closeReviewModal();
        
        // Reset referensi card
        currentEditCard = null;
        
        // Reset image files
        window.currentReviewImages = [];
        
        // Update local data and UI
        updateLocalReviewData(reviewId, rating, reviewText, response.review);
        
        console.log('Review changes saved successfully');
        console.log('Updated rating:', rating);
        
        // Show success message
        alert('Ulasan berhasil diperbarui');
      } else {
        console.error('Error saving review:', response.message);
        alert('Gagal menyimpan ulasan: ' + response.message);
      }
    })
    .catch(error => {
      console.error('Error saving review:', error);
      alert('Terjadi kesalahan saat menyimpan ulasan');
    });
  
  // Reset form setelah perubahan disimpan
  const editReviewForm = $q('#editReviewForm');
  if (editReviewForm) {
    editReviewForm.reset();
  }
  
  // Reset review ID
  const reviewIdInput = document.getElementById('reviewId');
  if (reviewIdInput) {
    reviewIdInput.value = '';
  }
}

// Function to update review on server
async function updateReviewOnServer(reviewId, rating, reviewText, files) {
  const formData = new FormData();
  formData.append('rating', rating);
  formData.append('review_text', reviewText);
  formData.append('_token', document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '');
  
  // Add files to form data if any
  if (files.length > 0) {
    for (let i = 0; i < files.length; i++) {
      // If the file is an actual File object, add it. Otherwise, it might be a placeholder
      if (files[i] instanceof File) {
        formData.append('media[]', files[i], files[i].name);
      } else if (files[i].file && files[i].file instanceof File) {
        // If it's a wrapper object containing the file
        formData.append('media[]', files[i].file, files[i].file.name);
      } else if (files[i].name && files[i].dataURL) {
        // If it's a placeholder object, we may need to handle differently
        console.log('File is a placeholder with dataURL - need to handle image update separately if implemented');
      }
    }
  }
  
  const response = await fetch(`/api/reviews/${reviewId}`, {
    method: 'POST', // Using POST with _method PUT for Laravel compatibility
    body: formData
  });
  
  return await response.json();
}

// Function to update local data after successful API call
function updateLocalReviewData(reviewId, rating, reviewText, reviewData) {
  // Find the review in the local data and update it
  const reviewIndex = data.dinilai.findIndex(r => r.id == reviewId);
  if (reviewIndex !== -1) {
    // Update the review data
    data.dinilai[reviewIndex].rating = rating;
    if (reviewData.review_text) {
      // Update kv array with the new review text
      data.dinilai[reviewIndex].kv = [['Ulasan', reviewData.review_text]];
    }
    
    // Re-render the affected review card
    if (currentEditCard) {
      // Update card rating display
      updateCardRating(currentEditCard, rating);
      
      // For now we won't update the review text in the card to avoid complex DOM manipulation
      // The card shows kv pairs, which we've already updated in the data
    }
  }
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
    newStars.addEventListener('click', handleStarsClick);
    
    // Fungsi terpisah untuk menangani klik bintang
    function handleStarsClick(e) {
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
    }
    
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

function updateCardImages(cardElement, images) {
  console.log('=== updateCardImages called ===');
  console.log('Card element:', cardElement);
  console.log('Card element tag name:', cardElement ? cardElement.tagName : 'N/A');
  console.log('Card element class list:', cardElement ? cardElement.classList : 'N/A');
  console.log('Images to display:', images);
  console.log('Number of images:', images ? images.length : 0);
  console.log('Images type:', typeof images);
  
  // Validate inputs
  if (!cardElement) {
    console.error('Card element is null or undefined');
    return;
  }
  
  if (!images || images.length === 0) {
    console.log('No images to display');
    // Hide any existing images container
    const existingContainer = cardElement.querySelector('.review-images');
    if (existingContainer) {
      console.log('Hiding existing images container');
      existingContainer.style.display = 'none';
    } else {
      console.log('No existing images container to hide');
    }
    return;
  }
  
  // Temukan atau buat container untuk gambar
  let imagesContainer = cardElement.querySelector('.review-images');
  if (!imagesContainer) {
    console.log('Creating new images container');
    imagesContainer = document.createElement('div');
    imagesContainer.className = 'review-images';
    // Sisipkan setelah rev-body tapi sebelum rev-foot
    const body = cardElement.querySelector('.rev-body');
    const footer = cardElement.querySelector('.rev-foot');
    console.log('Body element:', body);
    console.log('Footer element:', footer);
    if (body && footer) {
      console.log('Inserting images container before footer');
      try {
        cardElement.insertBefore(imagesContainer, footer);
      } catch (e) {
        console.error('Error inserting images container:', e);
      }
    } else if (body) {
      console.log('Appending images container to body');
      try {
        body.appendChild(imagesContainer);
      } catch (e) {
        console.error('Error appending images container to body:', e);
      }
    } else {
      console.log('Appending images container to card');
      try {
        cardElement.appendChild(imagesContainer);
      } catch (e) {
        console.error('Error appending images container to card:', e);
      }
    }
    console.log('Created new images container');
  } else {
    console.log('Found existing images container');
  }
  
  // Kosongkan container
  imagesContainer.innerHTML = '';
  console.log('Cleared images container');
  
  // Tampilkan gambar
  console.log('Displaying', images.length, 'images');
  imagesContainer.style.display = 'block';
  
  const imagesTitle = document.createElement('div');
  imagesTitle.className = 'images-title';
  imagesTitle.textContent = 'Foto Ulasan:';
  imagesContainer.appendChild(imagesTitle);
  
  const imagesGrid = document.createElement('div');
  imagesGrid.className = 'images-grid';
  
  images.forEach((image, index) => {
    console.log('Creating image element for:', image);
    const imageWrapper = document.createElement('div');
    imageWrapper.className = 'image-wrapper';
    // Tambahkan event listener baru
    imageWrapper.addEventListener('click', () => {
      openLightbox(image, index, images);
    });
    
    // Untuk demo, kita akan buat placeholder gambar
    // Dalam implementasi nyata, ini akan berisi URL gambar yang sebenarnya
    // Gunakan gambar sebenarnya jika dataURL tersedia, jika tidak gunakan placeholder
    let imageElement;
    if (image.dataURL || image.url) {
      const img = document.createElement('img');
      img.src = image.dataURL || image.url;
      img.alt = image.name || `Foto ${index + 1}`;
      img.className = 'review-image';
      imageElement = img;
    } else {
      const placeholder = document.createElement('div');
      placeholder.className = 'image-placeholder';
      placeholder.textContent = '📷';
      imageElement = placeholder;
    }
    
    imageWrapper.appendChild(imageElement);
    
    const imageInfo = document.createElement('div');
    imageInfo.className = 'image-info';
    imageInfo.textContent = image.name || `Foto ${index + 1}`;
    
    imageWrapper.appendChild(imageInfo);
    imagesGrid.appendChild(imageWrapper);
  });
  
  imagesContainer.appendChild(imagesGrid);
  console.log('Added images to container');
  
  console.log('Card images updated successfully');
}

function openLightbox(image, index, images) {
  console.log('Opening lightbox for image:', image);
  
  // Buat atau temukan lightbox
  let lightbox = document.getElementById('imageLightbox');
  if (!lightbox) {
    lightbox = document.createElement('div');
    lightbox.id = 'imageLightbox';
    lightbox.className = 'lightbox';
    
    const lightboxContent = document.createElement('div');
    lightboxContent.className = 'lightbox-content';
    lightboxContent.textContent = '📷 ' + (image.name || 'Foto Produk');
    
    const closeBtn = document.createElement('button');
    closeBtn.className = 'lightbox-close';
    closeBtn.innerHTML = '&times;';
    closeBtn.addEventListener('click', handleCloseButtonClick);
    
    // Fungsi terpisah untuk menangani klik tombol close
    function handleCloseButtonClick() {
      closeLightbox();
    }
    
    lightbox.appendChild(lightboxContent);
    lightbox.appendChild(closeBtn);
    document.body.appendChild(lightbox);
    
    // Tambahkan event listener untuk menutup lightbox saat klik di luar gambar
    lightbox.addEventListener('click', handleLightboxClick);
    
    // Fungsi terpisah untuk menangani klik lightbox
    function handleLightboxClick(e) {
      if (e.target === lightbox) {
        closeLightbox();
      }
    }
  }
  
  // Tampilkan lightbox
  lightbox.classList.add('open');
  
  // Mencegah scrolling background
  document.body.classList.add('modal-open');
}

function closeLightbox() {
  const lightbox = document.getElementById('imageLightbox');
  if (lightbox) {
    lightbox.classList.remove('open');
  }
  
  // Kembalikan scrolling background
  document.body.classList.remove('modal-open');
}

function setupDragAndDrop() {
  const dropArea = document.getElementById('dropArea');
  const fileInput = document.getElementById('fileInput');
  const browseLink = dropArea.querySelector('.browse-link');
  const previewContainer = document.getElementById('previewContainer');
  const imagePreviews = document.getElementById('imagePreviews');
  const clearAllBtn = document.getElementById('clearAllBtn');
  
  console.log('Setting up drag and drop functionality');
  console.log('Drop area:', dropArea);
  console.log('File input:', fileInput);
  
  // Initialize array to store selected files
  window.currentReviewImages = [];
  console.log('Initialized window.currentReviewImages as empty array');
  
  if (!dropArea || !fileInput) {
    console.log('Drag and drop elements not found');
    return;
  }
  
  // Click on browse link opens file input
  browseLink.addEventListener('click', handleBrowseLinkClick);
  
  // Click on drop area opens file input
  dropArea.addEventListener('click', handleDropAreaClick);
  
  // Handle file selection via input
  fileInput.addEventListener('change', handleFileInputChange);
  
  // Prevent default drag behaviors
  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
  });
  
  // Highlight drop area when item is dragged over it
  ['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, highlight, false);
  });
  
  ['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, unhighlight, false);
  });
  
  // Handle dropped files
  dropArea.addEventListener('drop', handleDrop, false);
  
  // Handle clear all button
  if (clearAllBtn) {
    clearAllBtn.addEventListener('click', handleClearAllClick);
  }
  
  function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
  }
  
  function highlight() {
    dropArea.classList.add('drag-over');
  }
  
  function unhighlight() {
    dropArea.classList.remove('drag-over');
  }
  
  function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFiles(files);
  }
  
  function handleFiles(files) {
  console.log('Handling files:', files);
  console.log('Number of files:', files.length);
  // Kosongkan array sebelum menambahkan file baru untuk menghindari duplikasi
  window.currentReviewImages = [];
  
  [...files].forEach(file => {
    console.log('Processing file:', file.name, file.size, file.type);
    if (file.type.startsWith('image/')) {
      // Baca file sebagai data URL agar bisa ditampilkan
      const reader = new FileReader();
      reader.onload = function(e) {
        // Tambahkan dataURL ke file object
        file.dataURL = e.target.result;
        window.currentReviewImages.push(file);
        console.log('Added file to currentReviewImages with dataURL:', file.name);
        // Update preview setelah file selesai dibaca
        updateImagePreviews();
      };
      reader.readAsDataURL(file);
    } else {
      console.log('File is not an image, skipping:', file.name);
    }
  });
  console.log('Current review images after handling:', window.currentReviewImages);
}
  
  function updateImagePreviews() {
    imagePreviews.innerHTML = '';
    
    if (window.currentReviewImages.length > 0) {
      previewContainer.style.display = 'block';
      dropArea.style.display = 'none';
      
      window.currentReviewImages.forEach((file, index) => {
        const previewDiv = document.createElement('div');
        previewDiv.className = 'image-preview';
        
        const img = document.createElement('img');
        // Gunakan dataURL yang sudah disimpan saat file diproses
        img.src = file.dataURL || '';
        img.alt = file.name;
        
        const removeBtn = document.createElement('button');
        removeBtn.className = 'remove-image';
        removeBtn.innerHTML = '&times;';
        removeBtn.addEventListener('click', handleRemoveImageClick);
        
        function handleRemoveImageClick(e) {
          e.stopPropagation();
          window.currentReviewImages.splice(index, 1);
          updateImagePreviews();
          
          // If no images left, show drop area again
          if (window.currentReviewImages.length === 0) {
            previewContainer.style.display = 'none';
            dropArea.style.display = 'block';
          }
        }
        
        previewDiv.appendChild(img);
        previewDiv.appendChild(removeBtn);
        imagePreviews.appendChild(previewDiv);
      });
    } else {
      previewContainer.style.display = 'none';
      dropArea.style.display = 'block';
    }
  }
  
  // Fungsi terpisah untuk menangani klik browse link
  function handleBrowseLinkClick(e) {
    e.preventDefault();
    fileInput.click();
  }
  
  // Fungsi terpisah untuk menangani klik drop area
  function handleDropAreaClick() {
    fileInput.click();
  }
  
  // Fungsi terpisah untuk menangani perubahan input file
  function handleFileInputChange(e) {
    handleFiles(e.target.files);
  }
  
  // Fungsi terpisah untuk menangani klik tombol clear all
  function handleClearAllClick() {
    window.currentReviewImages = [];
    updateImagePreviews();
  }
}

/* ======= Init ======= */
document.addEventListener('DOMContentLoaded', async ()=>{  // Changed to async
  try {
    console.log('=== INITIALIZING HALAMAN ULASAN ===');
    console.log('Document loaded, starting initialization');
    
    // Setup escape key listener
    document.addEventListener('keydown', handleEscapeKey);
    
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
    
    // Fetch real data from API
    try {
      console.log('Fetching user reviews data...');
      const response = await fetch('/api/user-reviews');
      const result = await response.json();
      
      if (result.success) {
        // Update global data object with fetched data
        data = {
          user: result.user,
          belumDinilai: [], // For now, we're only focusing on existing reviews
          dinilai: result.reviews || [] // All reviews are shown in 'dinilai' section
        };
        
        console.log('Data fetched successfully:', data);
      } else {
        console.error('API request failed:', result.message);
        // Use empty data as fallback
        data = {
          user: { name: "" },
          belumDinilai: [],
          dinilai: []
        };
      }
    } catch (error) {
      console.error('Error fetching user reviews:', error);
      // Use empty data as fallback
      data = {
        user: { name: "" },
        belumDinilai: [],
        dinilai: []
      };
    }
    
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
    
    // Check modal elements exist
    console.log('Checking modal elements...');
    const modal = document.getElementById('editReviewModal');
    const modalOverlay = $q('.modal-overlay');
    const ratingContainer = $q('#rating');
    const editReviewForm = $q('#editReviewForm');
    const closeModalBtn = $q('#closeModal');
    const deleteReviewBtn = $q('#deleteReview');
    
    console.log('Modal elements found:', {
      modal: modal ? 'YES' : 'NO',
      modalOverlay: modalOverlay ? 'YES' : 'NO',
      ratingContainer: ratingContainer ? 'YES' : 'NO',
      editReviewForm: editReviewForm ? 'YES' : 'NO',
      closeModalBtn: closeModalBtn ? 'YES' : 'NO',
      deleteReviewBtn: deleteReviewBtn ? 'YES' : 'NO'
    });
    
    // Setup modal event listeners AFTER reviews are rendered
    setupModalEventListeners();
    
    console.log('Setting up tabs...');
    setupTabs();
    
    console.log('Setting up search and sort...');
    setupSearchAndSort();

    console.log('=== INITIALIZATION COMPLETE ===');
  } catch (error) {
    console.error('FATAL ERROR in document initialization:', error);
    console.error('Stack trace:', error.stack);
  }
});

// Fungsi terpisah untuk menangani tombol escape
function handleEscapeKey(e) {
  if (e.key === 'Escape') {
    closeLightbox();
  }
}

// Test function to manually trigger image display (for debugging)
function testImageDisplay() {
  console.log('Testing image display...');
  const testImages = [
    { name: 'test1.jpg', size: 12345, type: 'image/jpeg' },
    { name: 'test2.png', size: 23456, type: 'image/png' }
  ];
  
  // Try to find a card
  const card = document.querySelector('.review-card');
  if (card) {
    console.log('Found card, updating images...');
    updateCardImages(card, testImages);
  } else {
    console.log('No card found');
  }
}