@extends('layouts.app')

@section('title', 'Checkout')

@section('header')
  @include('components.header_compact')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/transaksi/checkout.css') }}"/>
@endpush

@section('content')
  <main class="checkout-page">
    <div class="container">
      <div class="page-header">
        <h1>Checkout</h1>
        <div class="progress-steps">
          <div class="step active">
            <span class="step-number">1</span>
            <span class="step-label">Alamat</span>
          </div>
          <div class="step">
            <span class="step-number">2</span>
            <span class="step-label">Pengiriman</span>
          </div>
          <div class="step">
            <span class="step-number">3</span>
            <span class="step-label">Pembayaran</span>
          </div>
        </div>
      </div>

      <div class="checkout-content">
        <div class="main-content">
          <!-- Alamat Pengiriman -->
          <section class="shipping-address">
            <h2>Alamat Pengiriman</h2>
            <div class="address-list">
              <div class="address-card selected">
                <div class="address-header">
                  <h3>Rumah</h3>
                  <span class="badge primary">Utama</span>
                </div>
                <p class="address-detail">Andi Saputra<br>Jl. Anggrek No. 12, Bandung<br>0812-3456-7890</p>
                <div class="address-actions">
                  <button class="btn btn-secondary ubah-btn">Ubah</button>
                </div>
              </div>

              <div class="address-card">
                <div class="address-header">
                  <h3>Kantor</h3>
                </div>
                <p class="address-detail">PT. Maju Bersama<br>Jl. Melati No. 45, Jakarta<br>021-1234567</p>
                <div class="address-actions">
                  <button class="btn btn-ghost ubah-btn">Ubah</button>
                </div>
              </div>

              <button class="btn btn-add-address">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Alamat Baru
              </button>
            </div>
          </section>

          <!-- Ringkasan Pesanan -->
          <section class="order-summary">
            <h2>Ringkasan Pesanan</h2>
            <div class="order-items">
              <div class="order-item">
                <img src="https://picsum.photos/seed/cup/64/64" alt="Produk" class="item-image">
                <div class="item-details">
                  <h3 class="item-name">Hard Case Premium Hybrid Clear</h3>
                  <p class="item-variant">Jet Black · iPhone 13</p>
                  <p class="item-price">Rp12.000 × 1</p>
                </div>
                <div class="item-total">Rp12.000</div>
              </div>

              <div class="order-item">
                <img src="https://picsum.photos/seed/charger/64/64" alt="Produk" class="item-image">
                <div class="item-details">
                  <h3 class="item-name">Fast Charging Adapter 20W</h3>
                  <p class="item-variant">Putih</p>
                  <p class="item-price">Rp45.000 × 1</p>
                </div>
                <div class="item-total">Rp45.000</div>
              </div>
            </div>

            <div class="order-notes">
              <label for="orderNotes">Catatan untuk Penjual (Opsional)</label>
              <textarea id="orderNotes" placeholder="Contoh: Warna biru dong, terima kasih"></textarea>
            </div>
          </section>

          <!-- Metode Pembayaran -->
          <section class="payment-methods">
            <h2>Metode Pembayaran</h2>
            <div class="form-group">
              <label for="paymentMethod">Pilih Metode Pembayaran</label>
              <select id="paymentMethod" class="form-control">
                <option value="bank_transfer">Transfer Bank</option>
                <option value="e_wallet">Dompet Digital</option>
                <option value="cod">Cash on Delivery (COD)</option>
              </select>
            </div>
            
            <!-- Payment method details container -->
            <div class="payment-details" id="paymentDetails">
              <!-- Details will be shown based on selection -->
            </div>
          </section>
        </div>

        <div class="sidebar">
          <!-- Ringkasan Pembayaran -->
          <section class="payment-summary">
            <h2>Ringkasan Pembayaran</h2>
            <div class="summary-details">
              <div class="summary-row">
                <span>Subtotal (2 produk)</span>
                <span>Rp57.000</span>
              </div>
              <div class="summary-row">
                <span>Ongkos Kirim</span>
                <span>Rp9.000</span>
              </div>
              <div class="summary-row">
                <span>Asuransi Pengiriman</span>
                <span>Rp1.500</span>
              </div>
              <div class="summary-row discount">
                <span>Diskon</span>
                <span>-Rp5.000</span>
              </div>
              <div class="summary-divider"></div>
              <div class="summary-row total">
                <span>Total</span>
                <span class="total-amount">Rp62.500</span>
              </div>
            </div>

            <button class="btn btn-primary btn-checkout">
              Bayar Sekarang
            </button>
          </section>
        </div>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Checkout button
      const checkoutBtn = document.querySelector('.btn-checkout');
      if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
          // Simulasi proses checkout
          alert('Pesanan berhasil dibuat! Mengarahkan ke halaman pembayaran...');
          // Redirect ke halaman pembayaran
          window.location.href = '#';
        });
      }

      // Address selection (updated to work with dynamically added addresses)
      document.querySelector('.address-list').addEventListener('click', function(e) {
        const addressCard = e.target.closest('.address-card');
        if (addressCard && !e.target.closest('.btn')) {
          // Remove selected class from all addresses
          document.querySelectorAll('.address-card').forEach(card => {
            card.classList.remove('selected');
          });
          // Add selected class to clicked address
          addressCard.classList.add('selected');
        }
        
        // Handle "Ubah" button clicks for existing addresses
        const ubahBtn = e.target.closest('.ubah-btn');
        if (ubahBtn) {
          e.stopPropagation();
          const addressCard = ubahBtn.closest('.address-card');
          
          // Get address data from the card
          const addressName = addressCard.querySelector('h3').textContent;
          const addressDetailElement = addressCard.querySelector('.address-detail');
          const addressDetailText = addressDetailElement.innerHTML;
          const isPrimary = addressCard.querySelector('.badge.primary') !== null;
          
          // Parse the address details
          const details = addressDetailText.split('<br>');
          const recipientName = details[0];
          const fullAddress = details[1];
          const phoneNumber = details[2];
          
          // Open edit modal with current data
          openEditModal(addressName, recipientName, fullAddress, phoneNumber, isPrimary);
        }
      });

      // Modal functionality
      const addAddressBtn = document.querySelector('.btn-add-address');
      const modal = document.getElementById('addAddressModal');
      const closeModalBtn = document.getElementById('closeModal');
      const cancelAddAddressBtn = document.getElementById('cancelAddAddress');
      const modalOverlay = document.querySelector('.modal-overlay');
      
      // Open modal
      if (addAddressBtn) {
        addAddressBtn.addEventListener('click', function() {
          if (modal) {
            modal.removeAttribute('hidden');
          }
        });
      }
      
      // Close modal functions
      function closeModal() {
        if (modal) {
          modal.setAttribute('hidden', '');
        }
      }
      
      // Close modal when clicking close button
      if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
      }
      
      // Close modal when clicking cancel button
      if (cancelAddAddressBtn) {
        cancelAddAddressBtn.addEventListener('click', closeModal);
      }
      
      // Close modal when clicking outside the content
      if (modalOverlay) {
        modalOverlay.addEventListener('click', closeModal);
      }
      
      // Close modal when pressing Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeModal();
        }
      });
      
      // Form submission
      const addAddressForm = document.getElementById('addAddressForm');
      if (addAddressForm) {
        addAddressForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          // Get form elements
          const addressName = document.getElementById('addressName');
          const recipientName = document.getElementById('recipientName');
          const addressDetail = document.getElementById('addressDetail');
          const phoneNumber = document.getElementById('phoneNumber');
          const isPrimary = document.getElementById('isPrimary');
          
          // Reset previous error states
          const formGroups = addAddressForm.querySelectorAll('.form-group');
          formGroups.forEach(group => {
            group.classList.remove('has-error');
          });
          
          // Validation flags
          let isValid = true;
          
          // Validate address name
          if (!addressName.value.trim()) {
            showError(addressName, 'Nama alamat tidak boleh kosong');
            isValid = false;
          }
          
          // Validate recipient name
          if (!recipientName.value.trim()) {
            showError(recipientName, 'Nama penerima tidak boleh kosong');
            isValid = false;
          }
          
          // Validate address detail
          if (!addressDetail.value.trim()) {
            showError(addressDetail, 'Alamat lengkap tidak boleh kosong');
            isValid = false;
          }
          
          // Validate phone number (Indonesian format)
          const phoneRegex = /^(\+62|62|0)8[1-9][0-9]{6,10}$/;
          if (!phoneNumber.value.trim()) {
            showError(phoneNumber, 'Nomor telepon tidak boleh kosong');
            isValid = false;
          } else if (!phoneRegex.test(phoneNumber.value.trim())) {
            showError(phoneNumber, 'Format nomor telepon tidak valid (contoh: 081234567890)');
            isValid = false;
          }
          
          // If form is valid, submit it
          if (isValid) {
            // Get form data
            const formData = {
              addressName: addressName.value,
              recipientName: recipientName.value,
              addressDetail: addressDetail.value,
              phoneNumber: phoneNumber.value,
              isPrimary: isPrimary.checked
            };
            
            // Here you would normally send the form data to the server
            console.log('Form submitted with data:', formData);
            
            // Add new address to the address list
            addAddressToPage(formData);
            
            // Show success message
            showSuccess('Alamat berhasil ditambahkan!' + (isPrimary.checked ? ' dan dijadikan sebagai alamat utama.' : ''));
            
            // Reset form
            addAddressForm.reset();
            
            // Close modal after a delay
            setTimeout(() => {
              closeModal();
            }, 2000);
          } else {
            // Show form error message
            showFormError('Mohon periksa kembali data yang Anda masukkan.');
          }
        });
      }
      
      // Show error message for a field
      function showError(field, message) {
        // Add error class to parent form-group
        const formGroup = field.closest('.form-group');
        formGroup.classList.add('has-error');
        
        // Remove existing error message if any
        const existingError = formGroup.querySelector('.error-message');
        if (existingError) {
          existingError.remove();
        }
        
        // Create and add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        errorDiv.style.color = 'var(--danger)';
        errorDiv.style.fontSize = '13px';
        errorDiv.style.marginTop = '5px';
        
        field.parentNode.appendChild(errorDiv);
      }
      
      // Show success message
      function showSuccess(message) {
        // Remove any existing success message
        const existingSuccess = document.querySelector('.success-message');
        if (existingSuccess) {
          existingSuccess.remove();
        }
        
        // Create and add success message
        const successDiv = document.createElement('div');
        successDiv.className = 'custom-alert success-message';
        successDiv.textContent = message;
        
        document.body.appendChild(successDiv);
        
        // Remove success message after 3 seconds
        setTimeout(() => {
          if (successDiv.parentNode) {
            successDiv.parentNode.removeChild(successDiv);
          }
        }, 3000);
      }
      
      // Show error message (for form validation errors)
      function showFormError(message) {
        // Remove any existing form error message
        const existingError = document.querySelector('.form-error-message');
        if (existingError) {
          existingError.remove();
        }
        
        // Create and add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'custom-alert form-error-message';
        errorDiv.textContent = message;
        
        // Add close button
        const closeBtn = document.createElement('span');
        closeBtn.className = 'alert-close';
        closeBtn.innerHTML = '&times;';
        closeBtn.onclick = function() {
          errorDiv.remove();
        };
        
        errorDiv.appendChild(closeBtn);
        document.body.appendChild(errorDiv);
        
        // Remove error message after 5 seconds
        setTimeout(() => {
          if (errorDiv.parentNode) {
            errorDiv.parentNode.removeChild(errorDiv);
          }
        }, 5000);
      }
      
      // Add new address to the address list
      function addAddressToPage(addressData) {
        // Get the address list container
        const addressList = document.querySelector('.address-list');
        
        // Create new address card element
        const addressCard = document.createElement('div');
        addressCard.className = 'address-card';
        
        // If this is a primary address, add the primary badge and select it
        let primaryBadge = '';
        if (addressData.isPrimary) {
          primaryBadge = '<span class="badge primary">Utama</span>';
          // Remove selected class from other addresses
          const existingAddresses = document.querySelectorAll('.address-card');
          existingAddresses.forEach(addr => {
            addr.classList.remove('selected');
          });
          // Add selected class to new address
          addressCard.classList.add('selected');
        }
        
        // Set the HTML content for the address card
        addressCard.innerHTML = `
          <div class="address-header">
            <h3>${addressData.addressName}</h3>
            ${primaryBadge}
          </div>
          <p class="address-detail">${addressData.recipientName}<br>${addressData.addressDetail}<br>${addressData.phoneNumber}</p>
          <div class="address-actions">
            <button class="btn btn-secondary ubah-btn">Ubah</button>
          </div>
        `;
        
        // Insert the new address card before the "Tambah Alamat Baru" button
        const addButton = document.querySelector('.btn-add-address');
        addressList.insertBefore(addressCard, addButton);
        
        // Add event listener to make this address selectable
        addressCard.addEventListener('click', function(e) {
          if (!e.target.closest('.btn')) {
            // Remove selected class from all addresses
            document.querySelectorAll('.address-card').forEach(card => {
              card.classList.remove('selected');
            });
            // Add selected class to this address
            this.classList.add('selected');
          }
        });
      }
    });
  </script>
@endpush

<!-- Modal Tambah Alamat -->
<div class="modal" id="addAddressModal" hidden>
  <div class="modal-overlay"></div>
  <div class="modal-content">
    <div class="modal-header">
      <h2>Tambah Alamat Baru</h2>
      <button class="modal-close" id="closeModal">&times;</button>
    </div>
    <form id="addAddressForm">
      <div class="form-group">
        <label for="addressName">Nama Alamat</label>
        <input type="text" id="addressName" class="form-control" placeholder="Contoh: Rumah, Kantor" required>
      </div>
      <div class="form-group">
        <label for="recipientName">Nama Penerima</label>
        <input type="text" id="recipientName" class="form-control" placeholder="Nama lengkap penerima" required>
      </div>
      <div class="form-group">
        <label for="addressDetail">Alamat Lengkap</label>
        <textarea id="addressDetail" class="form-control" rows="3" placeholder="Contoh: Jl. Merdeka No. 123, RT 01/RW 02" required></textarea>
      </div>
      <div class="form-group">
        <label for="phoneNumber">Nomor Telepon</label>
        <input type="tel" id="phoneNumber" class="form-control" placeholder="0812-3456-7890" required>
      </div>
      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox" id="isPrimary">
          <span class="checkmark"></span>
          Jadikan sebagai alamat utama
        </label>
      </div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" id="cancelAddAddress">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Alamat</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Ubah Alamat -->
<div class="modal" id="editAddressModal" hidden>
  <div class="modal-overlay"></div>
  <div class="modal-content">
    <div class="modal-header">
      <h2>Ubah Alamat</h2>
      <button class="modal-close" id="closeEditModal">&times;</button>
    </div>
    <form id="editAddressForm">
      <input type="hidden" id="editAddressId">
      <div class="form-group">
        <label for="editAddressName">Nama Alamat</label>
        <input type="text" id="editAddressName" class="form-control" placeholder="Contoh: Rumah, Kantor" required>
      </div>
      <div class="form-group">
        <label for="editRecipientName">Nama Penerima</label>
        <input type="text" id="editRecipientName" class="form-control" placeholder="Nama lengkap penerima" required>
      </div>
      <div class="form-group">
        <label for="editAddressDetail">Alamat Lengkap</label>
        <textarea id="editAddressDetail" class="form-control" rows="3" placeholder="Contoh: Jl. Merdeka No. 123, RT 01/RW 02" required></textarea>
      </div>
      <div class="form-group">
        <label for="editPhoneNumber">Nomor Telepon</label>
        <input type="tel" id="editPhoneNumber" class="form-control" placeholder="0812-3456-7890" required>
      </div>
      <div class="form-group">
        <label class="checkbox-label">
          <input type="checkbox" id="editIsPrimary">
          <span class="checkmark"></span>
          Jadikan sebagai alamat utama
        </label>
      </div>
      <div class="form-actions">
        <button type="button" class="btn btn-secondary" id="cancelEditAddress">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Open edit modal with address data
  function openEditModal(addressName, recipientName, addressDetail, phoneNumber, isPrimary) {
    // Get modal elements
    const modal = document.getElementById('editAddressModal');
    const addressNameInput = document.getElementById('editAddressName');
    const recipientNameInput = document.getElementById('editRecipientName');
    const addressDetailInput = document.getElementById('editAddressDetail');
    const phoneNumberInput = document.getElementById('editPhoneNumber');
    const isPrimaryCheckbox = document.getElementById('editIsPrimary');
    const editAddressForm = document.getElementById('editAddressForm');
    
    // Fill form with current data
    addressNameInput.value = addressName;
    recipientNameInput.value = recipientName;
    addressDetailInput.value = addressDetail;
    phoneNumberInput.value = phoneNumber;
    isPrimaryCheckbox.checked = isPrimary;
    
    // Store the original address name in the form's dataset
    editAddressForm.dataset.originalAddressName = addressName;
    
    // Show modal
    modal.removeAttribute('hidden');
  }
  
  // Update address card with new data
  function updateAddressCard(oldAddressName, newAddressData) {
    // Find the address card that matches the old address name
    const addressCards = document.querySelectorAll('.address-card');
    let targetCard = null;
    
    addressCards.forEach(card => {
      const cardAddressName = card.querySelector('h3').textContent;
      if (cardAddressName === oldAddressName) {
        targetCard = card;
      }
    });
    
    if (targetCard) {
      // Update the address card content
      const addressHeader = targetCard.querySelector('.address-header');
      const addressDetailElement = targetCard.querySelector('.address-detail');
      
      // Update address name
      targetCard.querySelector('h3').textContent = newAddressData.addressName;
      
      // Update or add primary badge
      const existingBadge = addressHeader.querySelector('.badge.primary');
      if (newAddressData.isPrimary) {
        if (!existingBadge) {
          const primaryBadge = document.createElement('span');
          primaryBadge.className = 'badge primary';
          primaryBadge.textContent = 'Utama';
          addressHeader.appendChild(primaryBadge);
        }
      } else {
        if (existingBadge) {
          existingBadge.remove();
        }
      }
      
      // Update address details
      addressDetailElement.innerHTML = `${newAddressData.recipientName}<br>${newAddressData.addressDetail}<br>${newAddressData.phoneNumber}`;
      
      // If this is now the primary address, select it
      if (newAddressData.isPrimary) {
        // Remove selected class from all addresses
        document.querySelectorAll('.address-card').forEach(card => {
          card.classList.remove('selected');
        });
        // Add selected class to this address
        targetCard.classList.add('selected');
      }
    }
  }
  
  // Close edit modal
  function closeEditModal() {
    const modal = document.getElementById('editAddressModal');
    modal.setAttribute('hidden', '');
  }
  
  // Add event listeners for edit modal
  document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editAddressModal');
    const closeEditModalBtn = document.getElementById('closeEditModal');
    const cancelEditAddressBtn = document.getElementById('cancelEditAddress');
    const editModalOverlay = editModal ? editModal.querySelector('.modal-overlay') : null;
    const editAddressForm = document.getElementById('editAddressForm');
    
    // Close edit modal when clicking close button
    if (closeEditModalBtn) {
      closeEditModalBtn.addEventListener('click', closeEditModal);
    }
    
    // Close edit modal when clicking cancel button
    if (cancelEditAddressBtn) {
      cancelEditAddressBtn.addEventListener('click', closeEditModal);
    }
    
    // Close edit modal when clicking outside the content
    if (editModalOverlay) {
      editModalOverlay.addEventListener('click', closeEditModal);
    }
    
    // Close edit modal when pressing Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const editModal = document.getElementById('editAddressModal');
        if (editModal && !editModal.hasAttribute('hidden')) {
          closeEditModal();
        }
      }
    });
    
    // Handle edit form submission
    if (editAddressForm) {
      editAddressForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const addressName = document.getElementById('editAddressName').value;
        const recipientName = document.getElementById('editRecipientName').value;
        const addressDetail = document.getElementById('editAddressDetail').value;
        const phoneNumber = document.getElementById('editPhoneNumber').value;
        const isPrimary = document.getElementById('editIsPrimary').checked;
        
        // Get the original address name (before editing)
        // We'll need to store this when opening the modal
        const originalAddressName = editAddressForm.dataset.originalAddressName || addressName;
        
        // Here you would normally send the form data to the server
        console.log('Edit form submitted with data:', {
          addressName,
          recipientName,
          addressDetail,
          phoneNumber,
          isPrimary
        });
        
        // Update the address card with new data
        const newAddressData = {
          addressName: addressName,
          recipientName: recipientName,
          addressDetail: addressDetail,
          phoneNumber: phoneNumber,
          isPrimary: isPrimary
        };
        updateAddressCard(originalAddressName, newAddressData);
        
        // Show success message
        showSuccess('Alamat berhasil diperbarui!');
        
        // Close modal
        closeEditModal();
      });
    }
  });
</script>