// JavaScript untuk Halaman Keranjang Belanja

class CartManager {
  constructor() {
    this.cartItems = [];
    this.init();
  }

  init() {
    this.bindEvents();
    this.calculateTotal();
  }

  bindEvents() {
    // Event listener untuk tombol tambah jumlah
    document.querySelectorAll('.qty-btn.plus').forEach(button => {
      button.addEventListener('click', (e) => {
        this.incrementQuantity(e.target);
      });
    });

    // Event listener untuk tombol kurang jumlah
    document.querySelectorAll('.qty-btn.minus').forEach(button => {
      button.addEventListener('click', (e) => {
        this.decrementQuantity(e.target);
      });
    });

    // Event listener untuk input jumlah
    document.querySelectorAll('.qty-input').forEach(input => {
      input.addEventListener('change', (e) => {
        this.updateQuantity(e.target);
      });

      input.addEventListener('input', (e) => {
        this.validateQuantityInput(e.target);
      });
    });

    // Event listener untuk checkbox "Pilih Semua"
    const selectAllCheckbox = document.getElementById('selectAllTop');
    if (selectAllCheckbox) {
      selectAllCheckbox.addEventListener('change', (e) => {
        this.toggleSelectAll(e.target.checked);
      });
    }

    // Event listener untuk checkbox item individual
    document.querySelectorAll('.item-check:not(#selectAllTop)').forEach(checkbox => {
      checkbox.addEventListener('change', () => {
        this.updateSelectAllCheckbox();
      });
    });

    // Event listener untuk tombol hapus
    document.querySelectorAll('.delete-btn').forEach(button => {
      button.addEventListener('click', (e) => {
        const row = e.target.closest('tr');
        if (row) {
          this.removeItem(row);
        }
      });
    });
  }

  incrementQuantity(button) {
    const input = button.previousElementSibling;
    let value = parseInt(input.value) || 0;
    input.value = value + 1;
    this.updateItemSubtotal(input);
    this.calculateTotal();
  }

  decrementQuantity(button) {
    const input = button.nextElementSibling;
    let value = parseInt(input.value) || 1;
    if (value > 1) {
      input.value = value - 1;
      this.updateItemSubtotal(input);
      this.calculateTotal();
    }
  }

  updateQuantity(input) {
    let value = parseInt(input.value);
    if (isNaN(value) || value < 1) {
      value = 1;
    } else if (value > 99) {
      value = 99;
    }
    input.value = value;
    this.updateItemSubtotal(input);
    this.calculateTotal();
  }

  validateQuantityInput(input) {
    let value = parseInt(input.value);
    if (isNaN(value) || value < 1) {
      input.value = 1;
    } else if (value > 99) {
      input.value = 99;
    }
  }

  toggleSelectAll(checked) {
    const checkboxes = document.querySelectorAll('.item-check:not(#selectAllTop)');
    checkboxes.forEach(checkbox => {
      checkbox.checked = checked;
    });
    this.calculateTotal();
  }

  updateSelectAllCheckbox() {
    const checkboxes = document.querySelectorAll('.item-check:not(#selectAllTop)');
    const selectAllCheckbox = document.getElementById('selectAllTop');
    if (selectAllCheckbox) {
      const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
      selectAllCheckbox.checked = allChecked;
    }
    this.calculateTotal();
  }

  updateItemSubtotal(input) {
    const row = input.closest('tr');
    if (!row) return;

    const priceText = row.querySelector('.price-col').textContent;
    // Ambil angka dari string harga (misal: "Rp 45.000" -> 45000)
    const price = parseInt(priceText.replace(/[^0-9]/g, ''));
    const quantity = parseInt(input.value);
    const subtotal = price * quantity;

    // Format subtotal ke format Rupiah
    const subtotalElement = row.querySelector('.subtotal-col');
    subtotalElement.textContent = this.formatRupiah(subtotal);
  }

  removeItem(row) {
    if (confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')) {
      row.remove();
      this.calculateTotal();
      this.updateSelectAllCheckbox();
    }
  }

  calculateTotal() {
    let subtotal = 0;
    let itemCount = 0;

    document.querySelectorAll('.cart-table tbody tr').forEach(row => {
      const checkbox = row.querySelector('.item-check');
      if (checkbox && checkbox.checked) {
        const subtotalText = row.querySelector('.subtotal-col').textContent;
        const itemSubtotal = parseInt(subtotalText.replace(/[^0-9]/g, ''));
        subtotal += itemSubtotal;

        itemCount++;
      }
    });

    // Update subtotal display
    document.querySelector('#subtotal-count').textContent = itemCount;
    document.querySelector('#cart-subtotal').textContent = this.formatRupiah(subtotal);
    
    // Untuk sementara, total = subtotal (akan ditambahkan logika diskon dan ongkir nanti)
    document.querySelector('#cartTotal').textContent = this.formatRupiah(subtotal);
  }

  formatRupiah(angka) {
    const reverse = angka.toString().split('').reverse().join('');
    let ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return 'Rp ' + ribuan;
  }
}

// Inisialisasi CartManager ketika DOM siap
document.addEventListener('DOMContentLoaded', function() {
  new CartManager();
});

// Fungsi untuk menangani perubahan kuantitas (jika diperlukan di luar class)
function updateQuantity(input) {
  const cartManager = window.cartManager || new CartManager();
  cartManager.updateQuantity(input);
}