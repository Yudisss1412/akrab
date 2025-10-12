// Data Produk Dummy
const produkPopuler = [
  {
    nama: "Kopi Liberika 250gr",
    harga: "Rp 45.000",
    gambar: ["src/product_1.png"],
    deskripsi: "Kopi Liberika asli dengan aroma khas dan rasa unik. Cocok untuk seduhan manual dan penikmat kopi sejati.",
    spesifikasi: ["Ukuran: 250gr", "Asal: Indonesia", "Bahan: Kopi Liberika Murni"]
  },
  {
    nama: "Sabun Herbal Zaitun",
    harga: "Rp 25.000",
    gambar: ["src/product_1.png"],
    deskripsi: "Sabun herbal alami dari minyak zaitun, lembut untuk kulit sensitif, cocok untuk seluruh anggota keluarga.",
    spesifikasi: ["Berat: 100gr", "Asal: Bandung", "Bahan: Zaitun, Minyak Nabati"]
  },
  {
    nama: "Keranjang Rotan Handmade",
    harga: "Rp 120.000",
    gambar: ["src/product_1.png"],
    deskripsi: "Keranjang rotan asli buatan tangan, cocok untuk dekorasi rumah, hamper, atau tempat penyimpanan serbaguna.",
    spesifikasi: ["Ukuran: 32x22cm", "Asal: Yogyakarta", "Bahan: Rotan Alami"]
  },
  {
    nama: "Brownies Panggang",
    harga: "Rp 38.000",
    gambar: ["src/product_1.png"],
    deskripsi: "Brownies panggang homemade, tekstur fudgy dengan topping choco chips melimpah. Fresh baked setiap hari!",
    spesifikasi: ["Berat: 200gr", "Asal: Surabaya", "Bahan: Cokelat, Telur, Terigu"]
  }
];

// ----- Render Produk Populer -----
function renderProdukPopuler() {
  const grid = document.getElementById('produk-populer-grid');
  if (!grid) return;
  grid.innerHTML = '';
  produkPopuler.forEach((produk, idx) => {
    const card = document.createElement('div');
    card.className = 'pop-modern-card';
    card.innerHTML = `
      <img class="pop-modern-img" src="${produk.gambar[0]}" alt="${produk.nama}">
      <div class="pop-modern-body">
        <div class="pop-modern-title">${produk.nama}</div>
        <div class="pop-modern-price">${produk.harga}</div>
        <button class="pop-modern-btn lihat-detail-btn" data-idx="${idx}">Pratinjau</button>
      </div>
    `;
    grid.appendChild(card);
  });
}
renderProdukPopuler();

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
    openProdukModal(idx);
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

function openProdukModal(idx) {
  if (!modal) return;

  const produk = produkPopuler[idx];
  currentProduk = produk;

  modalProduct.textContent = produk.nama;
  modalImg.src = produk.gambar[0];
  modalImg.alt = produk.nama;
  modalPrice.textContent = produk.harga;
  modalDesc.textContent = produk.deskripsi;

  // Spesifikasi
  modalSpecs.innerHTML = '';
  produk.spesifikasi.forEach(spec => {
    const li = document.createElement('li');
    li.textContent = spec;
    modalSpecs.appendChild(li);
  });

  // Thumbnail
  modalThumbs.innerHTML = '';
  produk.gambar.forEach((src, i) => {
    const thumb = document.createElement('img');
    thumb.src = src;
    thumb.alt = `Thumbnail ${i + 1}`;
    if (i === 0) thumb.classList.add('active');
    thumb.onclick = () => {
      modalImg.src = src;
      [...modalThumbs.children].forEach(img => img.classList.remove('active'));
      thumb.classList.add('active');
    };
    modalThumbs.appendChild(thumb);
  });

  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeProdukModal() {
  if (!modal) return;
  modal.style.display = 'none';
  document.body.style.overflow = '';
}

// Demo button actions
if (modalAddCart) {
  modalAddCart.onclick = () => alert('Produk ditambahkan ke keranjang!');
}
if (modalLihatDetail) {
  modalLihatDetail.onclick = () => alert('Menuju halaman detail produk (demo)');
}
