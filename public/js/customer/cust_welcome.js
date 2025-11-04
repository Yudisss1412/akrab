// Format price function similar to halaman_produk.js
function formatPrice(price) {
  // If price is already formatted with 'Rp', return as is
  if (typeof price === 'string' && price.startsWith('Rp')) {
    // Extract numeric value and return properly formatted
    const numericValue = parseFloat(price.replace(/[^\d]/g, ''));
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(numericValue);
  }
  
  // Convert to number if it's a string
  const numericPrice = typeof price === 'string' ? parseFloat(price) : price;
  
  // Format as Rupiah using Indonesian locale for currency
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 2,  // Tampilkan selalu 2 angka desimal
    maximumFractionDigits: 2   // Batasi hanya 2 angka desimal
  }).format(numericPrice);
}

// SVG rating stars
const STAR_FULL = `<svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF600"/></svg>`;
const STAR_HALF = `<svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M29.6691 32.9982L28.0534 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535V29.2285L29.6691 32.9982ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF700"/></svg>`;
const STAR_EMPTY = `<svg width="27" height="27" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M17.3316 32.9493L23.5003 29.2285L29.6691 32.9982L28.0535 25.9482L33.4878 21.1993L26.3399 20.6118L23.5003 13.9535L20.6607 20.5628L13.5128 21.1993L18.9472 25.9482L17.3316 32.9493ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF600"/></svg>`;

// Create stars HTML function
function createStarsHTML(num) {
  const r = Math.max(0, Math.min(5, parseFloat(num) || 0));
  let full = Math.floor(r), half = 0;
  const frac = r - full;
  if (frac >= 0.75) full += 1; else if (frac >= 0.25) half = 1;
  const empty = Math.max(0, 5 - full - half);
  return `${STAR_FULL.repeat(full)}${half ? STAR_HALF : ''}${STAR_EMPTY.repeat(empty)}`;
}

// Image error handler function
function handleImageError(img) {
  if (!img) return;
  img.onerror = null; // Prevent infinite loop if placeholder also fails
  
  // Create simple SVG as fallback
  const svgString = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400"><rect width="100%" height="100%" fill="#eef6f4"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="22" fill="#7aa29b">Gambar tidak tersedia</text></svg>';
  
  img.src = 'data:image/svg+xml;utf8,' + encodeURIComponent(svgString);
}

// Function to load real product data
async function loadProdukPopuler() {
  try {
    const response = await fetch('/api/products/popular'); // API endpoint to get popular products
    const data = await response.json();
    
    // Format the price for each product using global formatPrice function
    const formattedData = data.map(product => ({
      ...product,
      price: window.formatPrice ? window.formatPrice(product.price || product.harga) : product.price
    }));
    
    return formattedData;
  } catch (error) {
    console.error('Error loading popular products:', error);
    // Fallback to dummy data if API fails
    return [
      {
        id: 1,
        name: "Kopi Liberika 250gr",
        price: window.formatPrice ? window.formatPrice(45000) : "Rp 45.000,00",
        image: "src/product_1.png",
        description: "Kopi Liberika asli dengan aroma khas dan rasa unik. Cocok untuk seduhan manual dan penikmat kopi sejati.",
        specifications: ["Ukuran: 250gr", "Asal: Indonesia", "Bahan: Kopi Liberika Murni"]
      },
      {
        id: 2,
        name: "Sabun Herbal Zaitun",
        price: window.formatPrice ? window.formatPrice(25000) : "Rp 25.000,00",
        image: "src/product_1.png",
        description: "Sabun herbal alami dari minyak zaitun, lembut untuk kulit sensitif, cocok untuk seluruh anggota keluarga.",
        specifications: ["Berat: 100gr", "Asal: Bandung", "Bahan: Zaitun, Minyak Nabati"]
      },
      {
        id: 3,
        name: "Keranjang Rotan Handmade",
        price: window.formatPrice ? window.formatPrice(120000) : "Rp 120.000,00",
        image: "src/product_1.png",
        description: "Keranjang rotan asli buatan tangan, cocok untuk dekorasi rumah, hamper, atau tempat penyimpanan serbaguna.",
        specifications: ["Ukuran: 32x22cm", "Asal: Yogyakarta", "Bahan: Rotan Alami"]
      },
      {
        id: 4,
        name: "Brownies Panggang",
        price: window.formatPrice ? window.formatPrice(38000) : "Rp 38.000,00",
        image: "src/product_1.png",
        description: "Brownies panggang homemade, tekstur fudgy dengan topping choco chips melimpah. Fresh baked setiap hari!",
        specifications: ["Berat: 200gr", "Asal: Surabaya", "Bahan: Cokelat, Telur, Terigu"]
      }
    ];
  }
}

// ----- Render Produk Populer -----
async function renderProdukPopuler() {
  const grid = document.getElementById('produk-populer-grid');
  if (!grid) return;
  
  // Load real product data
  const produkPopuler = await loadProdukPopuler();
  grid.innerHTML = '';
  
  produkPopuler.forEach((produk, idx) => {
    const card = document.createElement('div');
    card.className = 'produk-card'; // Gunakan class yang sama dengan produk utama
    card.setAttribute('data-product-id', produk.id);
    const tokoHref = `/toko/${produk.toko || produk.seller?.name || produk.seller_id || 'toko-tidak-ditemukan'}`;
    card.innerHTML = `
      <img src="${produk.image}" alt="${produk.name.replace(/"/g, '&quot;').replace(/'/g, '&apos;')}"
           onerror="handleImageError(this)"
           loading="lazy">
      <div class="produk-card-info">
        <h3 class="produk-card-name">${produk.name}</h3>
        <div class="produk-card-sub">${produk.kategori || produk.category?.name || 'Umum'}</div>
        <div class="produk-card-price">${produk.price}</div>
        <div class="produk-card-toko">
          <a href="${tokoHref}" class="toko-link" data-seller-name="${produk.toko || produk.seller?.name || 'Toko Umum'}">${produk.toko || produk.seller?.name || 'Toko Umum'}</a>
        </div>
        <div class="produk-card-stars" aria-label="Rating ${produk.rating || 0} dari 5">${createStarsHTML(produk.rating || 0)}</div>
        <div class="produk-rating-angka">${produk.rating || 0}</div>
      </div>
      <div class="produk-card-actions">
        <button class="btn-lihat lihat-detail-btn" data-product-id="${produk.id}" data-idx="${idx}">Lihat Detail</button>
        <button class="btn-add" data-product-id="${produk.id}" data-name="${produk.name}" type="button">+ Keranjang</button>
      </div>
    `;
    grid.appendChild(card);
  });
}
// Load the products when the page is ready
document.addEventListener('DOMContentLoaded', renderProdukPopuler);

// ----- Modal Handling -----
const modal = document.getElementById('modal-detail-produk');
const modalImg = document.getElementById('modal-img');
const modalProduct = document.getElementById('modal-product');
const modalPrice = document.getElementById('modal-price');
const modalDesc = document.getElementById('modal-desc');
const modalSpecs = document.getElementById('modal-specs');
const modalThumbs = document.getElementById('modal-thumbs');
const modalAddCart = document.getElementById('modal-addcart-btn');
const modalLihatDetail = document.getElementById('modal-lihatdetail-btn');
const modalCloseBtn = document.getElementById('modal-close-btn');

let currentProduk = null;

// Buka modal via event delegation
document.addEventListener('click', function(e) {
  if (e.target.classList.contains('lihat-detail-btn')) {
    const idx = e.target.getAttribute('data-idx');
    const productId = e.target.getAttribute('data-product-id');
    openProdukModal(idx, productId);
  }
});

// Tutup dengan klik di area overlay
if (modal) {
  modal.addEventListener('mousedown', function(e) {
    if (e.target === modal) closeProdukModal();
  });
}

// Tutup dengan tombol close (jika ditampilkan di CSS)
if (modalCloseBtn) {
  modalCloseBtn.addEventListener('click', closeProdukModal);
}

// Tutup dengan tombol Esc
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeProdukModal();
});

function openProdukModal(idx, productId) {
  if (!modal) return;

  // Since we load real products, we need to get them again or store them
  // For now, we'll fetch the specific product data
  fetch(`/api/products/${productId}`)
    .then(response => response.json())
    .then(produk => {
      currentProduk = produk;

      modalProduct.textContent = produk.name;
      modalImg.src = produk.image;
      modalImg.alt = produk.name;
      // Format the price using our global function
      modalPrice.textContent = window.formatPrice ? window.formatPrice(typeof produk.price === 'number' ? produk.price : produk.price) : produk.price;
      modalDesc.textContent = produk.description;

      // Spesifikasi
      modalSpecs.innerHTML = '';
      (produk.specifications || produk.spesifikasi || []).forEach(spec => {
        const li = document.createElement('li');
        li.textContent = spec;
        modalSpecs.appendChild(li);
      });

      // Thumbnail - for now using single image, but could be extended
      modalThumbs.innerHTML = '';
      const thumb = document.createElement('img');
      thumb.src = produk.image;
      thumb.alt = `Thumbnail`;
      thumb.classList.add('active');
      thumb.onclick = () => {
        modalImg.src = produk.image;
        [...modalThumbs.children].forEach(img => img.classList.remove('active'));
        thumb.classList.add('active');
      };
      modalThumbs.appendChild(thumb);

      // Store product ID in the add to cart button for easy access
      if (modalAddCart) {
        modalAddCart.setAttribute('data-product-id', productId);
      }

      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    })
    .catch(error => {
      console.error('Error loading product details:', error);
      // Fallback to dummy data approach if API fails
      const produk = {
        name: "Produk Tidak Ditemukan",
        image: "src/product_1.png",
        price: window.formatPrice ? window.formatPrice(0) : "Rp 0,00",
        description: "Produk tidak ditemukan di sistem.",
        specifications: []
      };
      
      currentProduk = produk;
      modalProduct.textContent = produk.name;
      modalImg.src = produk.image;
      modalImg.alt = produk.name;
      modalPrice.textContent = produk.price;
      modalDesc.textContent = produk.description;
      modalSpecs.innerHTML = '';
      
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    });
}

function closeProdukModal() {
  if (!modal) return;
  modal.style.display = 'none';
  document.body.style.overflow = '';
}

// Add to cart functionality
if (modalAddCart) {
  modalAddCart.onclick = function() {
    const productId = this.getAttribute('data-product-id');
    addToCart(productId);
  };
}

if (modalLihatDetail) {
  modalLihatDetail.onclick = () => {
    if (currentProduk && currentProduk.id) {
      window.location.href = `/produk_detail/${currentProduk.id}`;
    } else {
      alert('Menuju halaman detail produk (demo)');
    }
  };
}

// Function to add item to cart
async function addToCart(productId) {
  if (!productId) {
    showNotification('Produk tidak ditemukan', 'error');
    return;
  }

  // Get CSRF token from the meta tag in the layout
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  
  if (!csrfToken) {
    showNotification('Terjadi masalah dengan keamanan, silakan refresh halaman', 'error');
    return;
  }

  try {
    const response = await fetch('/cart/add', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest' // Important for Laravel to recognize AJAX requests
      },
      body: JSON.stringify({ 
        product_id: productId,
        quantity: 1 // Default quantity
      })
    });

    const result = await response.json();

    if (response.ok && result.success) {
      showNotification(result.message || 'Produk berhasil ditambahkan ke keranjang', 'success');
    } else {
      showNotification(result.message || 'Gagal menambahkan ke keranjang', 'error');
    }
  } catch (error) {
    console.error('Error adding to cart:', error);
    showNotification('Terjadi kesalahan saat menambahkan ke keranjang', 'error');
  }
}

function showNotification(message, type = 'info') {
  // Buat elemen notifikasi di tengah layar
  const notification = document.createElement('div');
  
  // Gaya untuk notifikasi di tengah layar
  Object.assign(notification.style, {
    position: 'fixed',
    top: '50%',
    left: '50%',
    transform: 'translate(-50%, -50%)',
    padding: '16px 24px',
    borderRadius: '8px',
    color: 'white',
    backgroundColor: type === 'error' ? '#ef4444' : type === 'success' ? '#10b981' : '#3b82f6',
    zIndex: '9999',
    fontWeight: '600',
    boxShadow: '0 4px 6px rgba(0,0,0,0.1)',
    fontSize: '14px',
    maxWidth: '400px',
    wordWrap: 'break-word',
    opacity: '0',
    transition: 'opacity 0.3s ease-in-out'
  });
  
  notification.textContent = message;
  
  // Tambahkan ke body
  document.body.appendChild(notification);
  
  // Tampilkan dengan efek fade-in
  setTimeout(() => {
    notification.style.opacity = '1';
  }, 10);
  
  // Hapus notifikasi setelah 3 detik
  setTimeout(() => {
    notification.style.opacity = '0';
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 300);
  }, 3000);
}
