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
    card.className = 'pop-modern-card';
    card.innerHTML = `
      <img class="pop-modern-img" src="${produk.image}" alt="${produk.name}">
      <div class="pop-modern-body">
        <div class="pop-modern-title">${produk.name}</div>
        <div class="pop-modern-price">${produk.price}</div>
        <button class="pop-modern-btn lihat-detail-btn" data-product-id="${produk.id}" data-idx="${idx}">Pratinjau</button>
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
