@extends('layouts.app')

@section('title', 'Checkout')

@section('header')
  @include('components.customer.header.header')
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
            <span class="step-label">Checkout</span>
          </div>
          <div class="step">
            <span class="step-number">2</span>
            <span class="step-label">Pembayaran</span>
          </div>
          <div class="step">
            <span class="step-number">3</span>
            <span class="step-label">Selesai</span>
          </div>
        </div>
      </div>

      <div class="checkout-content">
        <div class="checkout-left">
          <!-- Alamat Pengiriman -->
          <section class="alamat-section">
            <div class="section-header">
              <h2>Alamat Pengiriman</h2>
              <button class="btn-edit" id="editAlamatBtn">
                <i class="bi bi-pencil"></i>
              </button>
            </div>
            
            <div class="alamat-card" id="alamatCard">
              <div class="alamat-content">
                <div class="primary-badge">Utama</div>
                <h3 id="alamatNama">Budi Santoso</h3>
                <p id="alamatDetail">
                  Jl. Merdeka No. 123, Kel. Sukajadi, Kec. Lembursitu, <br>
                  Kota Bandung, Jawa Barat 40235
                </p>
                <p class="alamat-phone" id="alamatPhone">0812-3456-7890</p>
              </div>
            </div>
          </section>

          <!-- Metode Pengiriman -->
          <section class="pengiriman-section">
            <div class="section-header">
              <h2>Metode Pengiriman</h2>
            </div>
            
            <div class="pengiriman-options">
              <label class="option-card active" data-metode="reguler">
                <input type="radio" name="metode" value="reguler" checked />
                <div class="option-content">
                  <div class="option-header">
                    <h4>REGULER</h4>
                    <span class="harga">Rp 15.000</span>
                  </div>
                  <p>3-5 hari kerja</p>
                </div>
              </label>
              
              <label class="option-card" data-metode="express">
                <input type="radio" name="metode" value="express" />
                <div class="option-content">
                  <div class="option-header">
                    <h4>EXPRESS</h4>
                    <span class="harga">Rp 25.000</span>
                  </div>
                  <p>1-2 hari kerja</p>
                </div>
              </label>
            </div>
          </section>

          <!-- Ringkasan Pesanan -->
          <section class="ringkasan-section">
            <div class="section-header">
              <h2>Ringkasan Pesanan</h2>
            </div>
            
            <div class="ringkasan-content">
              <div class="produk-item">
                <img src="{{ asset('src/CangkirKeramik1.png') }}" alt="Cangkir Keramik" />
                <div class="item-info">
                  <h4>Cangkir Keramik</h4>
                  <p>1 x Rp 45.000</p>
                </div>
                <div class="item-harga">Rp 45.000</div>
              </div>
              
              <div class="produk-item">
                <img src="{{ asset('src/PiringKayu.png') }}" alt="Piring Kayu" />
                <div class="item-info">
                  <h4>Piring Kayu</h4>
                  <p>2 x Rp 75.000</p>
                </div>
                <div class="item-harga">Rp 150.000</div>
              </div>
              
              <div class="biaya-detail">
                <div class="detail-row">
                  <span>Subtotal</span>
                  <span>Rp 195.000</span>
                </div>
                <div class="detail-row">
                  <span>Biaya Pengiriman</span>
                  <span>Rp 15.000</span>
                </div>
                <div class="detail-row total">
                  <span>Total</span>
                  <span>Rp 210.000</span>
                </div>
              </div>
            </div>
          </section>
        </div>

        <div class="checkout-right">
          <!-- Ringkasan Belanja -->
          <section class="ringkasan-belanja">
            <h3>Ringkasan Belanja</h3>
            
            <div class="produk-preview">
              <img src="{{ asset('src/CangkirKeramik1.png') }}" alt="Cangkir Keramik" />
              <div class="produk-info">
                <h4>Cangkir Keramik</h4>
                <p class="produk-harga">Rp 45.000</p>
              </div>
              <span class="produk-qty">1</span>
            </div>
            
            <div class="produk-preview">
              <img src="{{ asset('src/PiringKayu.png') }}" alt="Piring Kayu" />
              <div class="produk-info">
                <h4>Piring Kayu</h4>
                <p class="produk-harga">Rp 75.000</p>
              </div>
              <span class="produk-qty">2</span>
            </div>
            
            <div class="total-section">
              <div class="total-row">
                <span>Total Belanja</span>
                <span>Rp 210.000</span>
              </div>
              
              <button class="btn btn-primary btn-checkout">
                Bayar Sekarang
              </button>
            </div>
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
          // Redirect to shipping page
          window.location.href = '{{ route("cust.pengiriman") }}';
        });
      }
      
      // Metode pengiriman selection
      const metodeOptions = document.querySelectorAll('.option-card');
      metodeOptions.forEach(option => {
        option.addEventListener('click', function() {
          metodeOptions.forEach(opt => opt.classList.remove('active'));
          this.classList.add('active');
          
          // Update harga on summary
          const harga = this.querySelector('.harga').textContent;
          const selectedMetode = this.getAttribute('data-metode');
          console.log('Metode pengiriman dipilih:', selectedMetode, harga);
        });
      });
      
      // Edit alamat functionality
      const editBtn = document.getElementById('editAlamatBtn');
      if (editBtn) {
        editBtn.addEventListener('click', function() {
          // Show edit address modal
          const modal = document.createElement('div');
          modal.className = 'modal-overlay';
          modal.innerHTML = `
            <div class="modal-content">
              <div class="modal-header">
                <h3>Edit Alamat</h3>
                <button class="modal-close">&times;</button>
              </div>
              <div class="modal-body">
                <form id="alamatForm">
                  <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" value="Budi Santoso" required />
                  </div>
                  <div class="form-group">
                    <label for="alamat">Alamat Lengkap</label>
                    <textarea id="alamat" rows="3" required>Jl. Merdeka No. 123, Kel. Sukajadi, Kec. Lembursitu, Kota Bandung, Jawa Barat 40235</textarea>
                  </div>
                  <div class="form-group">
                    <label for="telepon">Nomor Telepon</label>
                    <input type="tel" id="telepon" value="0812-3456-7890" required />
                  </div>
                  <div class="form-actions">
                    <button type="button" class="btn btn-outline" id="batalEdit">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                  </div>
                </form>
              </div>
            </div>
          `;
          
          document.body.appendChild(modal);
          
          // Close modal functions
          const closeModal = () => {
            document.body.removeChild(modal);
          };
          
          modal.querySelector('.modal-close').addEventListener('click', closeModal);
          modal.querySelector('#batalEdit').addEventListener('click', closeModal);
          modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
          });
          
          // Save address
          modal.querySelector('#alamatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const nama = document.getElementById('nama').value;
            const alamat = document.getElementById('alamat').value;
            const telepon = document.getElementById('telepon').value;
            
            // Update address card
            document.getElementById('alamatNama').textContent = nama;
            document.getElementById('alamatDetail').innerHTML = alamat.split(', ').join('<br>');
            document.getElementById('alamatPhone').textContent = telepon;
            
            // Show success message
            showSuccess('Alamat berhasil diperbarui!');
            
            // Close modal
            closeModal();
          });
        });
      }
    });
    
    // Success message function
    function showSuccess(message) {
      // Remove any existing alerts
      const existingAlert = document.querySelector('.alert-success');
      if (existingAlert) existingAlert.remove();
      
      // Create alert element
      const alert = document.createElement('div');
      alert.className = 'alert alert-success show';
      alert.innerHTML = `
        <div class="alert-content">
          <i class="bi bi-check-circle"></i>
          <span>${message}</span>
        </div>
      `;
      
      document.body.appendChild(alert);
      
      // Auto remove after 3 seconds
      setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => {
          document.body.removeChild(alert);
        }, 300);
      }, 3000);
    }
  });
</script>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection