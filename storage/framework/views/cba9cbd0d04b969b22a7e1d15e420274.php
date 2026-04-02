

<?php $__env->startSection('title', 'Pengiriman'); ?>

<?php $__env->startSection('header'); ?>
  <?php echo $__env->make('components.customer.header.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('css/customer/transaksi/pengiriman.css')); ?>"/>
  <link rel="stylesheet" href="<?php echo e(asset('css/customer/transaksi/pengiriman_additional.css')); ?>"/>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
  <main class="pengiriman-page">
    <div class="container">
      <div class="page-header">
        <h1>Pengiriman</h1>
        <div class="progress-steps">
          <div class="step active">
            <span class="step-number">1</span>
            <span class="step-label">Alamat</span>
          </div>
          <div class="step active">
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
          <span>Pengiriman • Langkah 2 dari 3</span>
        </div>
      </div>

      <div class="pengiriman-content">
        <div class="pengiriman-left">
          <!-- Alamat Pengiriman -->
          <section class="alamat-section">
            <div class="section-header">
              <h2>Alamat Pengiriman</h2>
            </div>

            <div class="alamat-card">
              <div class="alamat-content">
                <div class="primary-badge">Utama</div>
                <?php if(isset($order) && $order && $order->shipping_address): ?>
                  <h3><?php echo e($order->shipping_address->recipient_name); ?></h3>
                  <div>
                    <span class="alamat-line"><?php echo e($order->shipping_address->full_address); ?></span><br />
                    <span class="alamat-line"><?php echo e($order->shipping_address->district); ?>, <?php echo e($order->shipping_address->city); ?></span><br />
                    <span class="alamat-line"><?php echo e($order->shipping_address->province); ?></span>
                  </div>
                  <p class="alamat-phone"><?php echo e($order->shipping_address->phone); ?></p>
                <?php else: ?>
                  <h3>Alamat tidak ditemukan</h3>
                  <div>
                    <span class="alamat-line">Alamat pengiriman tidak ditemukan</span>
                  </div>
                  <p class="alamat-phone">-</p>
                <?php endif; ?>
              </div>
            </div>
          </section>

          <!-- Metode Pengiriman -->
          <section class="pengiriman-section">
            <div class="section-header">
              <h2>Metode Pengiriman</h2>
            </div>

            <div class="pengiriman-content">
              <div class="shipping-options">
                <div class="shipping-option <?php if(isset($order) && $order->shipping_courier == 'reguler'): ?> selected <?php endif; ?>" data-shipping-method="reguler" data-shipping-cost="15000">
                  <div class="option-header">
                    <div class="option-info">
                      <h3>Reguler</h3>
                      <p>3-5 hari kerja</p>
                    </div>
                    <div class="option-price">Rp15.000</div>
                  </div>
                  <p class="option-description">Layanan pengiriman reguler dengan asuransi barang.</p>
                </div>

                <div class="shipping-option <?php if(isset($order) && $order->shipping_courier == 'kilat'): ?> selected <?php endif; ?>" data-shipping-method="kilat" data-shipping-cost="25000">
                  <div class="option-header">
                    <div class="option-info">
                      <h3>Kilat</h3>
                      <p>1-2 hari kerja</p>
                    </div>
                    <div class="option-price">Rp25.000</div>
                  </div>
                  <p class="option-description">Layanan pengiriman cepat dengan prioritas pengemasan.</p>
                </div>

                <div class="shipping-option <?php if(isset($order) && $order->shipping_courier == 'same_day'): ?> selected <?php endif; ?>" data-shipping-method="same_day" data-shipping-cost="50000">
                  <div class="option-header">
                    <div class="option-info">
                      <h3>Same Day</h3>
                      <p>Hari ini juga</p>
                    </div>
                    <div class="option-price">Rp50.000</div>
                  </div>
                  <p class="option-description">Pengiriman dalam hari yang sama dengan kurir khusus.</p>
                </div>
              </div>
            </div>
          </section>
        </div>

        <div class="pengiriman-right">
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
              <?php if(isset($order) && $order && $order->items->count() > 0): ?>
                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="produk-preview">
                    <img src="<?php echo e(asset($item->product->main_image ?? 'src/default-product.png')); ?>" alt="<?php echo e($item->product->name ?? 'Produk'); ?>" />
                    <div class="produk-info">
                      <h4><?php echo e($item->product->name ?? 'Produk tidak ditemukan'); ?></h4>
                      <p class="produk-harga">Rp <?php echo e(number_format($item->unit_price, 0, ',', '.')); ?></p>
                    </div>
                    <span class="produk-qty">x<?php echo e($item->quantity); ?></span>
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
                <?php if(isset($order) && $order): ?>
                  <span class="subtotal-amount">Rp <?php echo e(number_format($order->sub_total, 0, ',', '.')); ?></span>
                <?php else: ?>
                  <span class="subtotal-amount">Rp 0</span>
                <?php endif; ?>
              </div>
              <?php if(isset($order) && $order && ($order->discount ?? 0) > 0): ?>
              <div class="total-row discount-row">
                <span><i class="bi bi-tag-fill" style="color: #28a745;"></i> Diskon</span>
                <span class="discount-amount" style="color: #28a745; font-weight: 600;">- Rp <?php echo e(number_format($order->discount, 0, ',', '.')); ?></span>
              </div>
              <?php endif; ?>
              <div class="total-row">
                <span>Ongkos Kirim</span>
                <?php if(isset($order) && $order): ?>
                  <span class="shipping-cost-amount">Rp <?php echo e(number_format($order->shipping_cost, 0, ',', '.')); ?></span>
                <?php else: ?>
                  <span class="shipping-cost-amount">Rp 0</span>
                <?php endif; ?>
              </div>
              <div class="total-row">
                <span>Asuransi</span>
                <span>Rp 1.500</span>
              </div>
              <div class="total-row total-amount" style="border-top: 1px solid #eee; padding-top: 10px; margin-top: 10px;">
                <span><strong>Total</strong></span>
                <?php if(isset($order) && $order): ?>
                  <span><strong>Rp <?php echo e(number_format($order->total_amount, 0, ',', '.')); ?></strong></span>
                <?php else: ?>
                  <span><strong>Rp 0</strong></span>
                <?php endif; ?>
              </div>

              <a href="<?php echo e(route('cust.pembayaran')); ?>" class="btn btn-primary btn-lanjut-pembayaran">
                Lanjut ke Pembayaran
              </a>
            </div>
          </section>
        </div>
      </div>

      <!-- Review Section - only show if order is delivered -->
      <?php if(isset($order) && $order && $order->status === 'delivered'): ?>
      <div class="review-section">
        <h2 class="section-title">Beri Ulasan untuk Pesanan Ini</h2>
        <div class="products-to-review">
          <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              // Check if user has already reviewed this product in this order
              $existingReview = \App\Models\Review::where('user_id', auth()->id())
                                                   ->where('product_id', $item->product_id)
                                                   ->where('order_id', $order->id)
                                                   ->first();
            ?>

            <?php if(!$existingReview): ?>
            <div class="product-review-card">
              <div class="product-info">
                <img src="<?php echo e($item->product->image ? asset('storage/' . $item->product->image) : asset('src/placeholder_produk.png')); ?>" alt="<?php echo e($item->product->name); ?>">
                <div class="product-details">
                  <h3><?php echo e($item->product->name); ?></h3>
                  <p class="shop-name">Toko: <?php echo e($item->product->seller->name ?? 'Toko Tidak Diketahui'); ?></p>
                </div>
              </div>
              <a href="<?php echo e(route('ulasan.create', $item->id)); ?>" class="btn btn-primary">Beri Ulasan</a>
            </div>
            <?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

  </main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Shipping option selection
      const shippingOptions = document.querySelectorAll('.shipping-option');
      const orderNumber = <?php echo json_encode($order->order_number ?? null, 15, 512) ?>;

      shippingOptions.forEach(option => {
        option.addEventListener('click', function() {
          shippingOptions.forEach(opt => opt.classList.remove('selected'));
          this.classList.add('selected');

          // Get the selected shipping method and cost
          const shippingMethod = this.getAttribute('data-shipping-method');
          const shippingCost = parseInt(this.getAttribute('data-shipping-cost'));

          // Update the summary with new shipping cost and total
          updateShippingSummary(shippingMethod, shippingCost);
        });
      });

      // Function to update shipping summary via AJAX
      function updateShippingSummary(shippingMethod, shippingCost) {
        // Define order number from PHP
        <?php
          $actualOrder = $order ?? $latestOrder ?? null;
        ?>
        const orderNumber = <?php echo json_encode($actualOrder ? $actualOrder->order_number : null, 15, 512) ?>;

        if (!orderNumber) {
          console.error('Order number not found');
          return;
        }

        // Show loading indicator
        const totalAmountElement = document.querySelector('.total-amount');
        if (totalAmountElement) {
          totalAmountElement.innerHTML = 'Memperbarui...';
        }

        // Send AJAX request to update shipping method
        fetch('<?php echo e(route("cust.pengiriman.update")); ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '<?php echo e(csrf_token()); ?>'
          },
          body: JSON.stringify({
            order_number: orderNumber,
            shipping_method: shippingMethod
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Update the summary with new values
            updateSummaryDisplay(data.order);
          } else {
            console.error('Error updating shipping method:', data.message);
            // Revert to previous state if there was an error
            alert('Terjadi kesalahan: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat memperbarui metode pengiriman');
        });
      }

      // Function to update the payment summary display
      function updateSummaryDisplay(order) {
        const subtotalElement = document.querySelector('.subtotal-amount');
        const shippingCostElement = document.querySelector('.shipping-cost-amount');
        const totalElement = document.querySelector('.total-amount');
        const discountElement = document.querySelector('.discount-amount');

        if (subtotalElement) {
          subtotalElement.textContent = 'Rp ' + formatCurrency(order.sub_total);
        }

        if (shippingCostElement) {
          shippingCostElement.textContent = 'Rp ' + formatCurrency(order.shipping_cost);
        }

        // Update discount if exists
        if (discountElement && order.discount > 0) {
          discountElement.textContent = '- Rp ' + formatCurrency(order.discount);
        }

        if (totalElement) {
          // Total = subtotal - discount + shipping + insurance
          totalElement.textContent = 'Rp ' + formatCurrency(order.total_amount);
        }
      }

      // Currency formatting function for Indonesian Rupiah format (without currency symbol to avoid duplication)
      // Format: 15.000 (with dots for thousands separator, commas are for decimals)
      function formatCurrency(amount) {
        // Format with thousands separator using dots (.)
        return new Intl.NumberFormat('id-ID', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }).format(amount).replace('Rp', '').trim();
      }


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

      // Lanjut ke pembayaran button
      const paymentBtn = document.querySelector('.btn-lanjut-pembayaran');
      if (paymentBtn) {
        paymentBtn.addEventListener('click', function() {
          // Redirect to payment page
          window.location.href = '<?php echo e(route("cust.pembayaran")); ?>';
        });
      }
    });
  </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ecommerce-akrab\resources\views/customer/transaksi/pengiriman.blade.php ENDPATH**/ ?>