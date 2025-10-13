// Address selection
document.addEventListener('DOMContentLoaded', function() {
  // Tombol checkout sudah ditangani di blade template (checkout.blade.php)
  // Hanya tangani interaksi alamat dan metode pembayaran di sini

  // Address selection - clicking on card selects it
  const addressCards = document.querySelectorAll('.address-card');
  if (addressCards.length > 0) {
    addressCards.forEach(card => {
      card.addEventListener('click', function(e) {
        // Don't select if clicking on a button
        if (!e.target.closest('.btn')) {
          addressCards.forEach(c => c.classList.remove('selected'));
          this.classList.add('selected');
        }
      });
    });
  }

  // Address selection - clicking "Gunakan" button also selects the address
  const useAddressButtons = document.querySelectorAll('.address-card .btn-secondary:not(.ubah-btn)');
  if (useAddressButtons.length > 0) {
    useAddressButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        e.stopPropagation();
        const card = this.closest('.address-card');
        addressCards.forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
      });
    });
  }

  // Edit address buttons
  const editAddressButtons = document.querySelectorAll('.ubah-btn');
  if (editAddressButtons.length > 0) {
    editAddressButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        e.stopPropagation();
        const card = this.closest('.address-card');
        const addressName = card.querySelector('h3').textContent;
        const addressDetail = card.querySelector('.address-detail').textContent;
        
        // Select this address when editing
        addressCards.forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        
        // Show edit dialog (in a real app, this would open a modal)
        alert(`Mengedit alamat: ${addressName}
Detail: ${addressDetail}

Fitur edit akan dibuka dalam dialog/modal di implementasi nyata.`);
      });
    });
  }

  // Add new address functionality
  const addAddressButton = document.querySelector('.btn-add-address');
  const addAddressModal = document.getElementById('addAddressModal');
  const closeModalButton = document.getElementById('closeModal');
  const cancelAddAddressButton = document.getElementById('cancelAddAddress');
  const addAddressForm = document.getElementById('addAddressForm');
  
  if (addAddressButton && addAddressModal) {
    // Open modal when "Tambah Alamat Baru" is clicked
    addAddressButton.addEventListener('click', function(e) {
      e.preventDefault();
      addAddressModal.hidden = false;
      document.body.style.overflow = 'hidden'; // Prevent background scrolling
    });
    
    // Close modal functions
    function closeAddAddressModal() {
      addAddressModal.hidden = true;
      document.body.style.overflow = ''; // Restore scrolling
      addAddressForm.reset(); // Reset form
    }
    
    // Close modal when clicking close button
    if (closeModalButton) {
      closeModalButton.addEventListener('click', closeAddAddressModal);
    }
    
    // Close modal when clicking cancel button
    if (cancelAddAddressButton) {
      cancelAddAddressButton.addEventListener('click', closeAddAddressModal);
    }
    
    // Close modal when clicking overlay
    const modalOverlay = addAddressModal.querySelector('.modal-overlay');
    if (modalOverlay) {
      modalOverlay.addEventListener('click', closeAddAddressModal);
    }
    
    // Close modal when pressing Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && !addAddressModal.hidden) {
        closeAddAddressModal();
      }
    });
    
    // Handle form submission
    if (addAddressForm) {
      addAddressForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const addressName = document.getElementById('addressName').value;
        const recipientName = document.getElementById('recipientName').value;
        const addressDetail = document.getElementById('addressDetail').value;
        const phoneNumber = document.getElementById('phoneNumber').value;
        const isPrimary = document.getElementById('isPrimary').checked;
        
        // Validate form
        if (!addressName || !recipientName || !addressDetail || !phoneNumber) {
          alert('Mohon lengkapi semua field yang diperlukan.');
          return;
        }
        
        // Create new address card
        addNewAddressCard(addressName, recipientName, addressDetail, phoneNumber, isPrimary);
        
        // Close modal and reset form
        closeAddAddressModal();
        
        // Show success message
        alert('Alamat baru berhasil ditambahkan!');
      });
    }
  }
  
  // Function to add new address card to the list
  function addNewAddressCard(addressName, recipientName, addressDetail, phoneNumber, isPrimary) {
    const addressList = document.querySelector('.address-list');
    if (!addressList) return;
    
    // Create new address card element
    const newAddressCard = document.createElement('div');
    newAddressCard.className = 'address-card';
    
    // If this is primary address, remove primary badge from existing addresses
    if (isPrimary) {
      document.querySelectorAll('.address-card .badge.primary').forEach(badge => {
        badge.remove();
      });
    }
    
    newAddressCard.innerHTML = `
      <div class="address-header">
        <h3>${addressName}</h3>
        ${isPrimary ? '<span class="badge primary">Utama</span>' : ''}
      </div>
      <p class="address-detail">${recipientName}<br>${addressDetail}<br>${phoneNumber}</p>
      <div class="address-actions">
        <button class="btn btn-secondary">Gunakan</button>
        <button class="btn btn-ghost ubah-btn">Ubah</button>
      </div>
    `;
    
    // Add to the beginning of the address list (before the "Tambah Alamat Baru" button)
    const addButton = addressList.querySelector('.btn-add-address');
    if (addButton) {
      addressList.insertBefore(newAddressCard, addButton);
    } else {
      addressList.appendChild(newAddressCard);
    }
    
    // Add event listeners to the new card
    newAddressCard.addEventListener('click', function(e) {
      if (!e.target.closest('.btn')) {
        addressCards.forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
      }
    });
    
    // Add event listener to the "Gunakan" button in the new card
    const useButton = newAddressCard.querySelector('.btn-secondary:not(.ubah-btn)');
    if (useButton) {
      useButton.addEventListener('click', function(e) {
        e.stopPropagation();
        const card = this.closest('.address-card');
        addressCards.forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
      });
    }
    
    // Add event listener to the "Ubah" button in the new card
    const editButton = newAddressCard.querySelector('.ubah-btn');
    if (editButton) {
      editButton.addEventListener('click', function(e) {
        e.stopPropagation();
        const card = this.closest('.address-card');
        const addressName = card.querySelector('h3').textContent;
        const addressDetail = card.querySelector('.address-detail').textContent;
        
        // Select this address when editing
        addressCards.forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        
        // Show edit dialog (in a real app, this would open a modal)
        alert(`Mengedit alamat: ${addressName}
Detail: ${addressDetail}

Fitur edit akan dibuka dalam dialog/modal di implementasi nyata.`);
      });
    }
    
    // Select the new address
    addressCards.forEach(c => c.classList.remove('selected'));
    newAddressCard.classList.add('selected');
  }

  // Payment method dropdown
  const paymentMethodSelect = document.getElementById('paymentMethod');
  const paymentDetails = document.getElementById('paymentDetails');
  
  if (paymentMethodSelect && paymentDetails) {
    // Show initial payment details
    updatePaymentDetails(paymentMethodSelect.value);
    
    // Update details when selection changes
    paymentMethodSelect.addEventListener('change', function() {
      updatePaymentDetails(this.value);
    });
  }
  
  function updatePaymentDetails(method) {
    let html = '';
    
    switch(method) {
      case 'bank_transfer':
        html = `
          <h3>Transfer Bank</h3>
          <p>Bayar ke rekening bank berikut:</p>
          <p><strong>Bank BCA</strong><br>
          No. Rekening: 123-456-7890<br>
          a/n PT. AKRAB Indonesia</p>
          <p>Pastikan Anda mentransfer jumlah yang tepat dan menyimpan bukti transfer.</p>
        `;
        break;
        
      case 'e_wallet':
        html = `
          <h3>Dompet Digital</h3>
          <p>Pilih dompet digital Anda:</p>
          <p>• OVO<br>
          • GoPay<br>
          • DANA<br>
          • ShopeePay</p>
          <p>Anda akan diarahkan ke aplikasi dompet digital setelah checkout.</p>
        `;
        break;
        
      case 'cod':
        html = `
          <h3>Cash on Delivery (COD)</h3>
          <p>Bayar langsung saat barang diterima.</p>
          <div class="cod-info">
            <p><strong>Informasi Penting:</strong></p>
            <p>• Pembayaran dilakukan dalam bentuk tunai kepada kurir saat pengiriman</p>
            <p>• Siapkan uang pas untuk mempermudah transaksi</p>
            <p>• Pastikan Anda ada di lokasi pengiriman saat kurir tiba</p>
          </div>
        `;
        break;
        
      default:
        html = '<p>Silakan pilih metode pembayaran</p>';
    }
    
    paymentDetails.innerHTML = html;
  }
});