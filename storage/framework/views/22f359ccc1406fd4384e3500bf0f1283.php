

<?php $__env->startSection('title', 'Pembayaran'); ?>

<?php $__env->startSection('header'); ?>
  <?php echo $__env->make('components.customer.header.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('css/customer/transaksi/pembayaran.css')); ?>"/>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
  <main class="pembayaran-page">
    <div class="container">
      <div class="page-header">
        <h1>Pembayaran</h1>
        <div class="progress-steps">
          <div class="step active">
            <span class="step-number">1</span>
            <span class="step-label">Alamat</span>
          </div>
          <div class="step active">
            <span class="step-number">2</span>
            <span class="step-label">Pengiriman</span>
          </div>
          <div class="step active">
            <span class="step-number">3</span>
            <span class="step-label">Pembayaran</span>
          </div>
        </div>
        <!-- Mobile Step Indicator -->
        <div class="mobile-step-indicator">
          <span>Pembayaran • Langkah 3 dari 3</span>
        </div>
      </div>

      <div class="pembayaran-content">
        <div class="main-content-left">
          <!-- Ringkasan Pesanan -->
          <section class="order-summary">
            <h2>Ringkasan Pesanan</h2>
            <div class="order-items">
              <?php if(isset($order) && $order && $order->items->count() > 0): ?>
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="order-item">
                    <img src="<?php echo e(asset($item->product->main_image ?? 'src/default-product.png')); ?>" alt="<?php echo e($item->product->name ?? 'Produk'); ?>" class="item-image">
                    <div class="item-details">
                      <h3 class="item-name"><?php echo e($item->product->name ?? 'Produk tidak ditemukan'); ?></h3>
                      <?php if($item->variant): ?>
                        <p class="item-variant"><?php echo e($item->variant->name); ?></p>
                      <?php endif; ?>
                      <p class="item-price">Rp<?php echo e(number_format($item->unit_price, 0, ',', '.')); ?> × <?php echo e($item->quantity); ?></p>
                    </div>
                    <div class="item-total">Rp<?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></div>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php else: ?>
                <p>Detail pesanan tidak ditemukan.</p>
              <?php endif; ?>
            </div>
          </section>

          <!-- Detail Pengiriman -->
          <section class="shipping-details">
            <h2>Detail Pengiriman</h2>
            <div class="shipping-info">
              <?php if(isset($order) && $order && $order->shipping_address): ?>
                <div class="info-row">
                  <span class="label">Alamat Pengiriman</span>
                  <span class="value"><?php echo e($order->shipping_address->recipient_name); ?>, <?php echo e($order->shipping_address->full_address); ?>, <?php echo e($order->shipping_address->phone); ?></span>
                </div>
                <div class="info-row">
                  <span class="label">Metode Pengiriman</span>
                  <span class="value">
                    <?php if($order->shipping_courier == 'reguler'): ?>
                      Reguler (3-5 hari kerja)
                    <?php elseif($order->shipping_courier == 'kilat'): ?>
                      Kilat (1-2 hari kerja)
                    <?php elseif($order->shipping_courier == 'same_day'): ?>
                      Same Day (hari ini juga)
                    <?php else: ?>
                      <?php echo e(ucfirst($order->shipping_courier)); ?>

                    <?php endif; ?>
                  </span>
                </div>
              <?php else: ?>
                <p>Informasi pengiriman tidak ditemukan.</p>
              <?php endif; ?>
            </div>
          </section>

          <!-- Metode Pembayaran -->
          <section class="payment-methods">
            <h2>Metode Pembayaran</h2>
            <div class="payment-options">
              <div class="payment-option">
                <input type="radio" id="bankTransfer" name="paymentMethod" value="bank_transfer" checked>
                <label for="bankTransfer" class="option-content">
                  <div class="option-header">
                    <div class="option-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M3 7V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7Z" stroke="#006E5C" stroke-width="2"/>
                        <path d="M3 10H21" stroke="#006E5C" stroke-width="2"/>
                        <path d="M8 14H10" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                    </div>
                    <div class="option-text">
                      <h3>Pembayaran Online</h3>
                      <p>Transfer Bank, Kartu Kredit, dan lainnya (via Midtrans)</p>
                    </div>
                  </div>
                </label>
              </div>


              <div class="payment-option">
                <input type="radio" id="cod" name="paymentMethod" value="cod">
                <label for="cod" class="option-content">
                  <div class="option-header">
                    <div class="option-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#006E5C" stroke-width="2"/>
                        <path d="M12 16V12" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                        <path d="M12 8H12.01" stroke="#006E5C" stroke-width="2" stroke-linecap="round"/>
                      </svg>
                    </div>
                    <div class="option-text">
                      <h3>Cash on Delivery</h3>
                      <p>Bayar saat barang diterima</p>
                    </div>
                  </div>
                </label>
              </div>

            </div>
          </section>
        </div>

        <div class="payment-summary-column">
          <!-- Ringkasan Pembayaran -->
          <section class="payment-summary-card">
            <h2>Ringkasan Pembayaran</h2>
            <div class="summary-details">
              <?php if(isset($order) && $order): ?>
                <div class="summary-row">
                  <span>Subtotal (<?php echo e($order->items->sum('quantity')); ?> produk)</span>
                  <span>Rp<?php echo e(number_format($order->sub_total, 0, ',', '.')); ?></span>
                </div>
                <div class="summary-row">
                  <span>Ongkos Kirim</span>
                  <span>Rp<?php echo e(number_format($order->shipping_cost, 0, ',', '.')); ?></span>
                </div>
                <div class="summary-row">
                  <span>Asuransi Pengiriman</span>
                  <span>Rp1.500</span>
                </div>
                <div class="summary-row discount">
                  <span>Diskon</span>
                  <span style="color: #28a745; font-weight: 600;">-Rp<?php echo e(number_format($order->discount ?? 0, 0, ',', '.')); ?></span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row total">
                  <span>Total</span>
                  <span class="total-amount">Rp<?php echo e(number_format($order->total_amount + ($order->discount ?? 0), 0, ',', '.')); ?></span>
                </div>
              <?php else: ?>
                <p>Data pembayaran tidak ditemukan.</p>
              <?php endif; ?>
            </div>

            <button class="btn btn-primary btn-proses-pembayaran">
              Proses Pembayaran
            </button>
          </section>
        </div>
      </div>
    </div>
  </main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
  <!-- Midtrans Snap Script -->
  <script src="https://app.sandbox.midtrans.com/snap/snap.js"
          data-client-key="<?php echo e(config('midtrans.client_key')); ?>"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Payment method selection
      const paymentOptions = document.querySelectorAll('.payment-option');
      paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
          const radio = this.querySelector('input[type="radio"]');
          radio.checked = true;
        });
      });

      // Proses pembayaran button
      const payBtn = document.querySelector('.btn-proses-pembayaran');
      if (payBtn) {
        payBtn.addEventListener('click', function() {
          // Disable button to prevent double submission
          payBtn.disabled = true;
          payBtn.innerHTML = 'Memproses...';

          // Get selected payment method
          const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked').value;

          // Prepare the data to send
          const formData = new FormData();
          formData.append('payment_method', selectedMethod);
          <?php if(isset($order) && $order): ?>
            formData.append('order_number', '<?php echo e($order->order_number); ?>');
          <?php endif; ?>
          formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

          // Make AJAX request to process payment
          fetch('<?php echo e(route("payment.process.api")); ?>', {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Check if selected method is bank_transfer or e_wallet to show Midtrans popup
              const selectedMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
              const orderNumber = data.order_number;

              if (selectedMethod === 'bank_transfer' || selectedMethod === 'e_wallet') {
                // For bank_transfer and e_wallet, show Midtrans popup directly
                if (data.snap_token) {
                  // Show Midtrans Snap popup
                  snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                      /* handle success */
                      console.log(result);
                      showNotification('success', 'Pembayaran berhasil! Pesanan akan segera diproses.');
                      setTimeout(() => {
                        window.location.href = '<?php echo e(route("cust.welcome")); ?>';
                      }, 2000);
                    },
                    onPending: function(result) {
                      /* handle pending */
                      console.log(result);
                      showNotification('info', 'Pembayaran sedang diproses. Kami akan mengirimkan notifikasi setelah pembayaran diverifikasi.');
                      setTimeout(() => {
                        window.location.href = '<?php echo e(route("cust.welcome")); ?>';
                      }, 2000);
                    },
                    onError: function(result) {
                      /* handle error */
                      console.log(result);
                      showNotification('error', 'Pembayaran gagal. Silakan coba lagi.');
                    },
                    onClose: function() {
                      /* handle close */
                      showNotification('warning', 'Pembayaran dibatalkan. Silakan lanjutkan pembayaran kapan saja.');
                    }
                  });
                } else {
                  // If no snap token, redirect to confirmation page
                  window.location.href = '<?php echo e(route("payment.confirmation")); ?>?order=' + orderNumber + '&method=' + selectedMethod;
                }
              } else {
                // For other methods, redirect to confirmation page
                window.location.href = '<?php echo e(route("payment.confirmation")); ?>?order=' + orderNumber + '&method=' + selectedMethod;
              }
            } else {
              // Show error message
              alert(data.message || 'Terjadi kesalahan saat memproses pembayaran.');
              // Re-enable the button
              payBtn.disabled = false;
              payBtn.innerHTML = 'Proses Pembayaran';
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
            // Re-enable the button
            payBtn.disabled = false;
            payBtn.innerHTML = 'Proses Pembayaran';
          });
        });
      }

      // Show sticky checkout bar on mobile
      function showStickyCheckoutBar() {
        const stickyBar = document.querySelector('.sticky-checkout-bar');
        if (stickyBar) {
          // Get window width
          if (window.innerWidth < 768) {
            // Show the sticky bar
            stickyBar.style.display = 'flex';
          } else {
            // Hide the sticky bar on desktop
            stickyBar.style.display = 'none';
          }
        }
      }

      // Call on load
      showStickyCheckoutBar();

      // Call on resize
      window.addEventListener('resize', showStickyCheckoutBar);

      // Add responsive utility for better mobile experience
      function adjustLayoutForMobile() {
        const container = document.querySelector('.pembayaran-content');
        if (window.innerWidth < 768) {
          // On mobile, adjust layout if needed
          container.style.alignItems = 'stretch';
        } else {
          // Reset for desktop
          container.style.alignItems = '';
        }
      }

      // Call initial layout adjustment
      adjustLayoutForMobile();

      // Listen for resize events to adjust layout
      window.addEventListener('resize', adjustLayoutForMobile);
    });
  </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ecommerce-akrab\resources\views/customer/transaksi/pembayaran.blade.php ENDPATH**/ ?>