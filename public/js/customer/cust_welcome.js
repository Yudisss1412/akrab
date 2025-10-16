// Function to load real product data
async function loadProdukPopuler() {
  try {
    const response = await fetch('/api/products/popular'); // API endpoint to get popular products
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error loading popular products:', error);
    // Fallback to dummy data if API fails
    return [
      {
        id: 1,
        name: "Kopi Liberika 250gr",
        price: "Rp 45.000",
        image: "src/product_1.png",
        description: "Kopi Liberika asli dengan aroma khas dan rasa unik. Cocok untuk seduhan manual dan penikmat kopi sejati.",
        specifications: ["Ukuran: 250gr", "Asal: Indonesia", "Bahan: Kopi Liberika Murni"]
      },
      {
        id: 2,
        name: "Sabun Herbal Zaitun",
        price: "Rp 25.000",
        image: "src/product_1.png",
        description: "Sabun herbal alami dari minyak zaitun, lembut untuk kulit sensitif, cocok untuk seluruh anggota keluarga.",
        specifications: ["Berat: 100gr", "Asal: Bandung", "Bahan: Zaitun, Minyak Nabati"]
      },
      {
        id: 3,
        name: "Keranjang Rotan Handmade",
        price: "Rp 120.000",
        image: "src/product_1.png",
        description: "Keranjang rotan asli buatan tangan, cocok untuk dekorasi rumah, hamper, atau tempat penyimpanan serbaguna.",
        specifications: ["Ukuran: 32x22cm", "Asal: Yogyakarta", "Bahan: Rotan Alami"]
      },
      {
        id: 4,
        name: "Brownies Panggang",
        price: "Rp 38.000",
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
      modalPrice.textContent = produk.price;
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
        price: "Rp 0",
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

  // Check if user is logged in by checking if there's a CSRF token
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!csrfToken) {
    showNotification('Silakan login terlebih dahulu', 'error');
    return;
  }

  try {
    const response = await fetch('/cart/add', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
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
