

<?php $__env->startSection('title', 'Checkout'); ?>

<?php $__env->startSection('header'); ?>
  <?php echo $__env->make('components.customer.header.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('css/customer/transaksi/checkout.css')); ?>"/>
  <link rel="stylesheet" href="<?php echo e(asset('css/customer/transaksi/checkout_additional.css')); ?>"/>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
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
        <!-- Mobile Step Indicator -->
        <div class="mobile-step-indicator">
          <span>Alamat • Langkah 1 dari 3</span>
        </div>
      </div>

      <form id="checkoutForm" action="<?php echo e(route('checkout.process')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <!-- Field tersembunyi untuk metode pengiriman default -->
        <input type="hidden" name="shipping_method" value="reguler" id="hiddenShippingMethod" />

        <!-- Debug: Menampilkan data yang dikirim -->
        <div class="checkout-debug-hidden">
          <input type="hidden" name="debug_trace" value="checkout_form" />
        </div>

        <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <script>
          // Jika ada error validasi, tampilkan form alamat dalam mode edit
          <?php if($errors->any()): ?>
            document.addEventListener('DOMContentLoaded', function() {
              document.getElementById('alamatCard').style.display = 'none';
              document.getElementById('alamatFormSection').style.display = 'block';
              document.getElementById('editAlamatBtn').innerHTML = '<i class="bi bi-check"></i>';
            });
          <?php else: ?>
            // Jika tidak ada error, pastikan form alamat disembunyikan saat halaman dimuat
            document.addEventListener('DOMContentLoaded', function() {
              document.getElementById('alamatFormSection').style.display = 'none';
            });
          <?php endif; ?>
        </script>

        <div class="checkout-content">
          <div class="checkout-left">
            <!-- Alamat Pengiriman -->
            <section class="alamat-section">
              <div class="section-header">
                <h2>Alamat Pengiriman</h2>
                <button type="button" class="btn-edit" id="editAlamatBtn">
                  <i class="bi bi-pencil"></i>
                </button>
              </div>

              <div class="alamat-card" id="alamatCard">
                <div class="alamat-content">
                  <div class="primary-badge">Utama</div>
                  <h3 id="alamatNama"><?php echo e($user->name ?? ''); ?></h3>
                  <div id="alamatDetail">
                    <?php if($user): ?>
                      <?php
                        $alamatParts = [];

                        // Tambahkan full_address jika ada
                        if($user->full_address) {
                            $alamatParts[] = $user->full_address;
                        }

                        // Tambahkan kecamatan jika ada dan berbeda dari full_address
                        if($user->district && !str_contains(strtolower($user->full_address ?? ''), strtolower($user->district))) {
                            $alamatParts[] = $user->district;
                        }

                        // Tambahkan kota jika ada dan berbeda dari full_address
                        if($user->city && !str_contains(strtolower($user->full_address ?? ''), strtolower($user->city))) {
                            $alamatParts[] = $user->city;
                        }

                        // Tambahkan provinsi jika ada dan berbeda dari full_address
                        if($user->province && !str_contains(strtolower($user->full_address ?? ''), strtolower($user->province))) {
                            $alamatParts[] = $user->province;
                        }

                        // Hapus elemen duplikat dan kosong dari array
                        $uniqueAlamatParts = array_unique(array_filter($alamatParts, function($part) {
                            return !empty(trim($part));
                        }));
                      ?>

                      <?php if(count($uniqueAlamatParts) > 0): ?>
                        <?php $__currentLoopData = $uniqueAlamatParts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alamatPart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <span class="alamat-line"><?php echo e($alamatPart); ?></span>
                          <?php if(!$loop->last): ?><br /><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php else: ?>
                        <span class="alamat-line">Alamat tidak lengkap</span>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="alamat-line">User tidak ditemukan</span>
                    <?php endif; ?>
                  </div>
                  <p class="alamat-phone" id="alamatPhone"><?php echo e($user->phone ?? ''); ?></p>
                </div>
              </div>

              <!-- Form untuk alamat pengiriman -->
              <div class="alamat-form" id="alamatFormSection" style="display: none;">
                <div class="form-group field">
                  <input type="text" id="recipient_name" name="recipient_name"
                         value="<?php echo e(old('recipient_name', $user->name ?? '')); ?>" required placeholder=" " />
                  <label for="recipient_name">Nama Penerima</label>
                </div>

                <div class="form-group field">
                  <input type="tel" id="phone" name="phone"
                         value="<?php echo e(old('phone', $user->phone ?? '')); ?>" required placeholder=" " />
                  <label for="phone">Nomor Telepon</label>
                </div>

                <div class="form-group field">
                  <input type="text" id="province" name="province"
                         value="<?php echo e(old('province', $user->province ?? '')); ?>" required placeholder=" " />
                  <label for="province">Provinsi</label>
                </div>

                <div class="form-group field">
                  <input type="text" id="city" name="city"
                         value="<?php echo e(old('city', $user->city ?? '')); ?>" required placeholder=" " />
                  <label for="city">Kota/Kabupaten</label>
                </div>

                <div class="form-group field">
                  <input type="text" id="district" name="district"
                         value="<?php echo e(old('district', $user->district ?? '')); ?>" required placeholder=" " />
                  <label for="district">Kecamatan</label>
                </div>

                <div class="form-group field">
                  <input type="text" id="ward" name="ward"
                         value="<?php echo e(old('ward', $user->ward ?? '')); ?>" required placeholder=" " />
                  <label for="ward">Kelurahan</label>
                </div>

                <div class="form-group field">
                  <textarea id="full_address" name="full_address" rows="3" required placeholder=" "><?php echo e(old('full_address', $user->full_address ?? $user->address ?? '')); ?></textarea>
                  <label for="full_address">Alamat Lengkap</label>
                </div>
              </div>
            </section>

            <!-- Ringkasan Pesanan -->
            <section class="ringkasan-section">
              <div class="section-header">
                <h2>Ringkasan Pesanan</h2>
              </div>

              <div class="ringkasan-content">
                <?php if(isset($cartItems) && $cartItems->count() > 0): ?>
                  <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="produk-item">
                      <img src="<?php echo e(asset(($item['product'] ?? $item->product)->main_image ?? 'src/default-product.png')); ?>" alt="<?php echo e(($item['product'] ?? $item->product)->name ?? ''); ?>" />
                      <div class="item-info">
                        <h4><?php echo e(($item['product'] ?? $item->product)->name ?? ''); ?></h4>
                        <p><?php echo e($item['quantity'] ?? $item->quantity); ?> x Rp <?php echo e(number_format(($item['product'] ?? $item->product)->price ?? 0, 0, ',', '.')); ?></p>
                      </div>
                      <div class="item-harga">Rp <?php echo e(number_format((($item['product'] ?? $item->product)->price ?? 0) * ($item['quantity'] ?? $item->quantity), 0, ',', '.')); ?></div>
                    </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                  <!-- Tidak tampilkan apapun jika keranjang kosong di ringkasan pesanan -->
                <?php endif; ?>

                <div class="biaya-detail">
                  <div class="detail-row">
                    <span>Subtotal</span>
                    <span>Rp <?php echo e(number_format($subTotal, 0, ',', '.')); ?></span>
                  </div>
                  <?php if($totalDiscount > 0): ?>
                  <div class="detail-row discount-row">
                    <span><i class="bi bi-tag-fill" style="color: #28a745;"></i> Diskon</span>
                    <span style="color: #28a745; font-weight: 600;">- Rp <?php echo e(number_format($totalDiscount, 0, ',', '.')); ?></span>
                  </div>
                  <?php endif; ?>
                  <!-- Sembunyikan biaya pengiriman sampai pengguna memilih metode pengiriman -->
                  <div class="detail-row checkout-shipping-cost-hidden">
                    <span>Biaya Pengiriman</span>
                    <span id="shippingCost">Rp <?php echo e(number_format($shippingCost, 0, ',', '.')); ?></span>
                  </div>
                  <div class="detail-row total">
                    <span>Total</span>
                    <span id="totalHarga">Rp <?php echo e(number_format($total, 0, ',', '.')); ?></span>
                  </div>
                </div>
              </div>
            </section>
          </div>

          <div class="checkout-right">
            <!-- Ringkasan Belanja -->
            <section class="ringkasan-belanja">
              <div class="section-header">
                <h3>Ringkasan Belanja</h3>
                <button type="button" class="btn-ghost" id="toggleRingkasan">
                  <span class="toggle-text">Lihat Rincian</span>
                  <i class="bi bi-chevron-down chevron-icon"></i>
                </button>
              </div>

              <div class="ringkasan-content" id="ringkasanDetails">
                
                
                <?php if(isset($cartItems) && $cartItems->count() > 0): ?>
                  <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="produk-preview">
                      <img src="<?php echo e(asset(($item['product'] ?? $item->product)->main_image ?? 'src/default-product.png')); ?>" alt="<?php echo e(($item['product'] ?? $item->product)->name ?? ''); ?>" />
                      <div class="produk-info">
                        <h4><?php echo e(($item['product'] ?? $item->product)->name ?? ''); ?></h4>
                        <p class="produk-harga">Rp <?php echo e(number_format(($item['product'] ?? $item->product)->price ?? 0, 0, ',', '.')); ?></p>
                      </div>
                      <span class="produk-qty">x<?php echo e($item['quantity'] ?? $item->quantity); ?></span>
                    </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                  <div class="empty-cart-message">
                    <p>Tidak ada produk dalam keranjang Anda saat ini.</p>
                  </div>
                <?php endif; ?>
              </div>

              <div class="total-section">
                <div class="total-row">
                  <span>Total Belanja</span>
                  <span>Rp <?php echo e(number_format($subTotal, 0, ',', '.')); ?></span>
                </div>

                <button type="submit" class="btn btn-primary btn-checkout" id="prosesPesananBtn">
                  Proses Pesanan
                </button>
              </div>
            </section>
          </div>
        </div>
      </form>

      <!-- Sticky Bottom Action Bar -->
      <div class="sticky-action-bar">
        <div class="total-tagihan">Rp <?php echo e(number_format($subTotal, 0, ',', '.')); ?></div>
        <button type="submit" form="checkoutForm" class="btn btn-primary btn-proses-pesanan">
          Proses Pesanan
        </button>
      </div>
    </div>
  </main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('checkoutForm');
      const shippingCostElement = document.getElementById('shippingCost');
      const totalHargaElement = document.getElementById('totalHarga');
      const prosesPesananBtn = document.getElementById('prosesPesananBtn');

      // Ambil nilai awal dari data Blade
      let subtotal = <?php echo e($subTotal); ?>;
      // Biaya pengiriman disembunyikan sampai pengguna memilih metode pengiriman
      let shippingCost = 0; // Set ke 0 karena biaya pengiriman disembunyikan

      // Update tampilan harga awal - hanya tampilkan subtotal karena pengiriman belum dipilih
      if (shippingCostElement) {
        shippingCostElement.textContent = new Intl.NumberFormat('id-ID', {
          style: 'currency',
          currency: 'IDR',
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }).format(shippingCost).replace('Rp', '').trim();
      }

      totalHargaElement.textContent = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(subtotal).replace('Rp', '').trim();

      // Edit alamat functionality
      const editBtn = document.getElementById('editAlamatBtn');
      const alamatCard = document.getElementById('alamatCard');
      const alamatFormSection = document.getElementById('alamatFormSection');

      if (editBtn) {
        // Gunakan state untuk melacak mode tombol
        let isEditMode = false;

        editBtn.addEventListener('click', function() {
          if (!isEditMode) {
            // Mode edit: tampilkan form, sembunyikan card
            alamatCard.style.display = 'none';
            alamatFormSection.style.display = 'block';
            editBtn.innerHTML = '<i class="bi bi-check"></i>';
            isEditMode = true;
          } else {
            // Mode simpan: validasi dan kirim ke server untuk update temporary data
            const recipientName = document.getElementById('recipient_name').value;
            const phone = document.getElementById('phone').value;
            const province = document.getElementById('province').value;
            const city = document.getElementById('city').value;
            const district = document.getElementById('district').value;
            const ward = document.getElementById('ward').value;
            const fullAddress = document.getElementById('full_address').value;

            if (!recipientName || !phone || !province || !city || !district || !ward || !fullAddress) {
              alert('Mohon lengkapi semua field alamat');
              return;
            }

            // Simpan alamat hanya di sisi klien (tidak dikirim ke server)
            // Karena sistem kita hanya untuk sementara di sesi checkout ini
            document.getElementById('alamatNama').textContent = recipientName || 'Nama tidak tersedia';
            document.getElementById('alamatDetail').innerHTML = fullAddress ? fullAddress.replace(/,/g, '<br>') : 'Alamat tidak lengkap';
            document.getElementById('alamatPhone').textContent = phone || 'Nomor telepon tidak tersedia';

            // Kembali ke tampilan card
            alamatCard.style.display = 'block';
            alamatFormSection.style.display = 'none';
            editBtn.innerHTML = '<i class="bi bi-pencil"></i>';
            isEditMode = false;

            // Tampilkan notifikasi sukses
            alert('Alamat berhasil diperbarui untuk checkout saat ini');
          }
        });
      }

      // Tambahkan validasi sebelum submit
      form.addEventListener('submit', function(e) {
        // Ambil semua nilai field alamat terlepas dari apakah form dalam mode tampil atau edit
        const recipientName = document.getElementById('recipient_name').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const province = document.getElementById('province').value.trim();
        const city = document.getElementById('city').value.trim();
        const district = document.getElementById('district').value.trim();
        const ward = document.getElementById('ward').value.trim();
        const fullAddress = document.getElementById('full_address').value.trim();

        // Validasi apakah semua field alamat telah diisi
        if (!recipientName || !phone || !province || !city || !district || !ward || !fullAddress) {
          e.preventDefault(); // Mencegah pengiriman form

          // Tampilkan pesan notifikasi yang lebih informatif
          alert('Mohon lengkapi semua field alamat pengiriman sebelum melanjutkan proses pesanan. Form alamat akan ditampilkan untuk Anda isi.');

          // Tampilkan form alamat (karena mungkin sedang dalam mode kartu)
          document.getElementById('alamatCard').style.display = 'none';
          document.getElementById('alamatFormSection').style.display = 'block';

          // Kembalikan status tombol
          prosesPesananBtn.innerHTML = 'Proses Pesanan';
          prosesPesananBtn.disabled = false;

          // Fokus ke field pertama yang kosong untuk membantu user
          if (!recipientName) {
            document.getElementById('recipient_name').focus();
          } else if (!phone) {
            document.getElementById('phone').focus();
          } else if (!province) {
            document.getElementById('province').focus();
          } else if (!city) {
            document.getElementById('city').focus();
          } else if (!district) {
            document.getElementById('district').focus();
          } else if (!ward) {
            document.getElementById('ward').focus();
          } else if (!fullAddress) {
            document.getElementById('full_address').focus();
          }

          return false;
        }

        // Tampilkan loading pada tombol
        prosesPesananBtn.innerHTML = 'Memproses...';
        prosesPesananBtn.disabled = true;
      });

      // Reset status tombol jika ada error
      <?php if($errors->any()): ?>
        if (prosesPesananBtn) {
          prosesPesananBtn.innerHTML = 'Proses Pesanan';
          prosesPesananBtn.disabled = false;
        }
      <?php endif; ?>

      // Toggle ringkasan belanja section
      const toggleBtn = document.getElementById('toggleRingkasan');
      const ringkasanDetails = document.getElementById('ringkasanDetails');
      const toggleText = toggleBtn.querySelector('.toggle-text');
      const chevronIcon = toggleBtn.querySelector('.chevron-icon');

      if (toggleBtn && ringkasanDetails) {
        toggleBtn.addEventListener('click', function() {
          ringkasanDetails.classList.toggle('show');

          if (ringkasanDetails.classList.contains('show')) {
            ringkasanDetails.style.display = 'block';
            toggleText.textContent = 'Sembunyikan Rincian';
            chevronIcon.classList.add('rotated');
          } else {
            ringkasanDetails.style.display = 'none';
            toggleText.textContent = 'Lihat Rincian';
            chevronIcon.classList.remove('rotated');
          }
        });
      }
    });
  </script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('footer'); ?>
  <?php echo $__env->make('components.customer.footer.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?> 

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ecommerce-akrab\resources\views/customer/transaksi/checkout.blade.php ENDPATH**/ ?>