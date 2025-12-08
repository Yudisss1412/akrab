// JavaScript untuk Halaman Keranjang Belanja

class CartManager {
  constructor() {
    this.cartItems = [];
    this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    this.init();
  }

  async init() {
    this.bindEvents();
    await this.calculateTotal();
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

  async incrementQuantity(button) {
    const input = button.previousElementSibling;
    let value = parseInt(input.value) || 0;
    input.value = value + 1;
    await this.updateItemQuantity(input);
    await this.calculateTotal();
  }

  async decrementQuantity(button) {
    const input = button.nextElementSibling;
    let value = parseInt(input.value) || 1;
    input.value = value - 1;
    
    // If quantity reaches 0, remove the item from cart
    if (input.value <= 0) {
      const row = input.closest('tr');
      if (row) {
        await this.removeItemFromCart(row);
      }
    } else {
      await this.updateItemQuantity(input);
      await this.calculateTotal();
    }
  }

  async updateQuantity(input) {
    let value = parseInt(input.value);
    if (isNaN(value)) {
      value = 1;
    } else if (value > 99) {
      value = 99;
    } else if (value < 0) {
      value = 0;
    }
    
    input.value = value;
    
    // If quantity is 0, remove the item
    if (value <= 0) {
      const row = input.closest('tr');
      if (row) {
        await this.removeItemFromCart(row);
      }
    } else {
      await this.updateItemQuantity(input);
      await this.calculateTotal();
    }
  }

  validateQuantityInput(input) {
    let value = parseInt(input.value);
    if (isNaN(value)) {
      input.value = 1;
    } else if (value > 99) {
      input.value = 99;
    } else if (value < 0) {
      input.value = 0;
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

  async updateItemQuantity(input) {
    const row = input.closest('tr');
    if (!row) return;

    const itemId = row.dataset.itemId;
    const quantity = parseInt(input.value);

    try {
      const response = await fetch(`/cart/update/${itemId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': this.csrfToken
        },
        body: JSON.stringify({ quantity: quantity })
      });

      const result = await response.json();

      if (result.success) {
        // If quantity is 0, remove the row from the UI
        if (quantity <= 0) {
          row.remove();
          await this.calculateTotal();
          this.updateSelectAllCheckbox();
          this.showNotification('Produk berhasil dihapus dari keranjang', 'success');

          // Update cart count in header via API
          if (window.updateCartCount) {
            window.updateCartCount();
          }
        } else {
          this.updateItemSubtotal(input);
          this.showNotification(result.message, 'success');

          // Update cart count in header via API
          if (window.updateCartCount) {
            window.updateCartCount();
          }
        }
      } else {
        this.showNotification(result.message || 'Gagal memperbarui kuantitas', 'error');
        // Kembalikan jumlah ke nilai sebelumnya jika gagal
        input.value = input.dataset.previousValue || 1;
        this.updateItemSubtotal(input);
      }
    } catch (error) {
      console.error('Error updating item quantity:', error);
      this.showNotification('Terjadi kesalahan saat memperbarui kuantitas', 'error');
      // Kembalikan jumlah ke nilai sebelumnya jika terjadi error
      input.value = input.dataset.previousValue || 1;
      this.updateItemSubtotal(input);

      // Update cart count in header via API to ensure synchronization
      if (window.updateCartCount) {
        window.updateCartCount();
      }
    }
  }

  // New function to remove item from cart when quantity reaches 0
  async removeItemFromCart(row) {
    const itemId = row.dataset.itemId;
    
    try {
      const response = await fetch(`/cart/remove/${itemId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': this.csrfToken
        }
      });

      const result = await response.json();

      if (result.success) {
        row.remove();
        await this.calculateTotal();
        this.updateSelectAllCheckbox();
        this.showNotification(result.message, 'success');

        // Update cart count in header via API
        if (window.updateCartCount) {
          window.updateCartCount();
        }
      } else {
        this.showNotification(result.message || 'Gagal menghapus item', 'error');
        // If removal failed, reset back to 1
        const input = row.querySelector('.qty-input');
        if (input) {
          input.value = 1;
          await this.updateItemQuantity(input);
          await this.calculateTotal();
        }
      }
    } catch (error) {
      console.error('Error removing item:', error);
      this.showNotification('Terjadi kesalahan saat menghapus item', 'error');
      // If removal failed, reset back to 1
      const input = row.querySelector('.qty-input');
      if (input) {
        input.value = 1;
        await this.updateItemQuantity(input);
        await this.calculateTotal();
      }
    }
  }

  updateItemSubtotal(input) {
    const row = input.closest('tr');
    if (!row) return;

    // Simpan nilai kuantitas sebelumnya
    input.dataset.previousValue = input.value;

    const priceText = row.querySelector('.price-col').textContent;
    // Ambil angka dari string harga (misal: "Rp 45.000" -> 45000)
    const price = parseInt(priceText.replace(/[^0-9]/g, ''));
    const quantity = parseInt(input.value);
    const subtotal = price * quantity;

    // Format subtotal ke format Rupiah
    const subtotalElement = row.querySelector('.subtotal-col');
    subtotalElement.textContent = this.formatRupiah(subtotal);
  }

  async removeItem(row) {
    const itemId = row.dataset.itemId;
    
    if (confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')) {
      try {
        const response = await fetch(`/cart/remove/${itemId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': this.csrfToken
          }
        });

        const result = await response.json();

        if (result.success) {
          row.remove();
          await this.calculateTotal();
          this.updateSelectAllCheckbox();
          this.showNotification(result.message, 'success');

          // Update cart count in header via API
          if (window.updateCartCount) {
            window.updateCartCount();
          }
        } else {
          this.showNotification(result.message || 'Gagal menghapus item', 'error');
        }
      } catch (error) {
        console.error('Error removing item:', error);
        this.showNotification('Terjadi kesalahan saat menghapus item', 'error');
      }
    }
  }

  async calculateTotal() {
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
    // Format as Rupiah using Indonesian locale for currency
    // This will format as Rp X.XXX,XX with 2 decimal places
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 2,  // Tampilkan selalu 2 angka desimal
      maximumFractionDigits: 2   // Batasi hanya 2 angka desimal
    }).format(angka);
  }

  showNotification(message, type = 'info') {
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