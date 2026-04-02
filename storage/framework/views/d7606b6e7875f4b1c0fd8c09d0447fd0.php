



<?php $__env->startSection('title', 'Keranjang Belanja — AKRAB'); ?>


<?php $__env->startSection('header'); ?>
  <?php echo $__env->make('components.customer.header.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('styles'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('css/customer/keranjang.css')); ?>" />
<?php $__env->stopPush(); ?>


<?php $__env->startSection('content'); ?>
  <main class="cart-page shell">
    <div class="container">
      
      <div class="page-header">
        <h1 class="page-title">Keranjang Belanja</h1>
        <a href="<?php echo e(url()->previous() ?: route('cust.welcome')); ?>" class="continue-shopping-link">
          ← Lanjut Belanja
        </a>
      </div>

      
      <div class="cart-layout">
        
        <div class="cart-products">
          
          <div class="products-card">
            
            <table class="cart-table">
              <thead>
                <tr>
                  <th class="check-col">
                    <input type="checkbox" id="selectAllTop" class="item-check">
                  </th>
                  <th>Produk</th>
                  <th class="price-col">Harga Satuan</th>
                  <th class="qty-col">Jumlah</th>
                  <th class="subtotal-col">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr data-item-id="<?php echo e($item['id'] ?? $item->id); ?>">
                  <td class="check-col" data-label="Pilih">
                    <div class="check">
                      <input type="checkbox" class="item-check" checked data-item-id="<?php echo e($item['id'] ?? $item->id); ?>">
                    </div>
                  </td>
                  <td class="product-col" data-label="Produk">
                    <div class="product-thumb-container">
                      <div class="product-thumb">
                        <img src="<?php echo e(($item['product'] ?? $item->product)->main_image ? asset('storage/' . ($item['product'] ?? $item->product)->main_image) : asset('src/placeholder.png')); ?>" alt="<?php echo e(($item['product'] ?? $item->product)->name); ?>">
                      </div>
                      <button class="delete-btn-float" data-item-id="<?php echo e($item['id'] ?? $item->id); ?>">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                    <div class="product-info">
                      <h3 class="product-name"><?php echo e(($item['product'] ?? $item->product)->name); ?></h3>
                      <div class="product-sku">SKU: <?php echo e(($item['product'] ?? $item->product)->sku ?? 'N/A'); ?></div>
                      <?php if($item['product_variant'] ?? $item->productVariant ?? null): ?>
                      <div class="product-variant">Varian: <?php echo e(($item['product_variant'] ?? $item->productVariant)->name); ?></div>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td class="price-col" data-label="Harga">
                    <?php
                      $hasDiscount = $item['has_discount'] ?? false;
                      $originalPrice = $item['original_price'] ?? (($item['product'] ?? $item->product)->price + (($item['product_variant'] ?? $item->productVariant ?? null) ? ($item['product_variant'] ?? $item->productVariant)->additional_price : 0));
                      $discountedPrice = $item['discounted_price'] ?? $originalPrice;
                    ?>
                    
                    <?php if($hasDiscount): ?>
                      <div style="display: flex; flex-direction: column; gap: 4px;">
                        <span style="text-decoration: line-through; color: #999; font-size: 0.85em;">
                          Rp <?php echo e(number_format($originalPrice, 0, ',', '.')); ?>

                        </span>
                        <span style="color: #dc3545; font-weight: 600;">
                          Rp <?php echo e(number_format($discountedPrice, 0, ',', '.')); ?>

                        </span>
                      </div>
                    <?php else: ?>
                      Rp <?php echo e(number_format($originalPrice, 0, ',', '.')); ?>

                    <?php endif; ?>
                  </td>
                  <td class="qty-col" data-label="Jumlah">
                    <div class="qty-controls">
                      <button class="qty-btn minus" data-item-id="<?php echo e($item['id'] ?? $item->id); ?>">-</button>
                      <input type="number" class="qty-input" value="<?php echo e($item['quantity'] ?? $item->quantity); ?>" min="0" max="99" data-item-id="<?php echo e($item['id'] ?? $item->id); ?>">
                      <button class="qty-btn plus" data-item-id="<?php echo e($item['id'] ?? $item->id); ?>">+</button>
                    </div>
                  </td>
                  <td class="subtotal-col" data-label="Subtotal">
                    <?php
                      $originalPrice = $item['original_price'] ?? (($item['product'] ?? $item->product)->price + (($item['product_variant'] ?? $item->productVariant ?? null) ? ($item['product_variant'] ?? $item->productVariant)->additional_price : 0));
                      $subtotalPrice = $originalPrice * ($item['quantity'] ?? $item->quantity);
                    ?>
                    Rp <?php echo e(number_format($subtotalPrice, 0, ',', '.')); ?>

                  </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="5" class="text-center">
                    <p>Keranjang Anda kosong. <a href="<?php echo e(url()->previous() ?: route('cust.welcome')); ?>">Lanjutkan belanja</a></p>
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>

            <!-- Mobile Product Cards - Mobile Version -->
            <div class="mobile-products-list">
              <?php $__empty_1 = true; $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="product-item-card" data-item-id="<?php echo e($item['id'] ?? $item->id); ?>">
                <div class="product-item-thumb-container">
                  <div class="check">
                    <input type="checkbox" class="item-check" checked data-item-id="<?php echo e($item['id'] ?? $item->id); ?>">
                  </div>
                  <div class="product-item-thumb">
                    <img src="<?php echo e(($item['product'] ?? $item->product)->main_image ? asset('storage/' . ($item['product'] ?? $item->product)->main_image) : asset('src/placeholder.png')); ?>" alt="<?php echo e(($item['product'] ?? $item->product)->name); ?>">
                  </div>
                  <button class="delete-btn-mobile" data-item-id="<?php echo e($item['id'] ?? $item->id); ?>">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
                <div class="product-item-details">
                  <h3 class="product-item-name"><?php echo e(($item['product'] ?? $item->product)->name); ?></h3>
                  <div class="product-sku">SKU: <?php echo e(($item['product'] ?? $item->product)->sku ?? 'N/A'); ?></div>
                  <?php if($item['product_variant'] ?? $item->productVariant ?? null): ?>
                  <div class="product-variant">Varian: <?php echo e(($item['product_variant'] ?? $item->productVariant)->name); ?></div>
                  <?php endif; ?>
                  <div class="product-item-price">
                    <?php
                      $hasDiscount = $item['has_discount'] ?? false;
                      $originalPrice = $item['original_price'] ?? (($item['product'] ?? $item->product)->price + (($item['product_variant'] ?? $item->productVariant ?? null) ? ($item['product_variant'] ?? $item->productVariant)->additional_price : 0));
                      $discountedPrice = $item['discounted_price'] ?? $originalPrice;
                    ?>
                    
                    <?php if($hasDiscount): ?>
                      <div style="display: flex; flex-direction: column; gap: 4px;">
                        <span style="text-decoration: line-through; color: #999; font-size: 0.85em;">
                          Rp <?php echo e(number_format($originalPrice, 0, ',', '.')); ?>

                        </span>
                        <span style="color: #dc3545; font-weight: 600;">
                          Rp <?php echo e(number_format($discountedPrice, 0, ',', '.')); ?>

                        </span>
                      </div>
                    <?php else: ?>
                      Rp <?php echo e(number_format($originalPrice, 0, ',', '.')); ?>

                    <?php endif; ?>
                  </div>
                  <div class="product-item-qty">
                    <div class="qty-controls">
                      <button class="qty-btn minus" data-item-id="<?php echo e($item['id'] ?? $item->id); ?>">-</button>
                      <input type="number" class="qty-input" value="<?php echo e($item['quantity'] ?? $item->quantity); ?>" min="0" max="99" data-item-id="<?php echo e($item['id'] ?? $item->id); ?>">
                      <button class="qty-btn plus" data-item-id="<?php echo e($item['id'] ?? $item->id); ?>">+</button>
                    </div>
                  </div>
                  <div class="product-item-subtotal">
                    <?php
                      $originalPrice = $item['original_price'] ?? (($item['product'] ?? $item->product)->price + (($item['product_variant'] ?? $item->productVariant ?? null) ? ($item['product_variant'] ?? $item->productVariant)->additional_price : 0));
                      $subtotalPrice = $originalPrice * ($item['quantity'] ?? $item->quantity);
                    ?>
                    Subtotal: Rp <?php echo e(number_format($subtotalPrice, 0, ',', '.')); ?>

                  </div>
                </div>
              </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <div class="text-center">
                <p>Keranjang Anda kosong. <a href="<?php echo e(url()->previous() ?: route('cust.welcome')); ?>">Lanjutkan belanja</a></p>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Kolom Kanan: Ringkasan Belanja -->
        <div class="cart-summary">
          <div class="summary-card">
            <h2>Ringkasan Belanja</h2>

            <div class="summary-details-container">
              <div class="summary-details">
                <div class="summary-row">
                  <span>Subtotal (<span id="subtotal-count"><?php echo e($totalItems ?? $cartItems->count()); ?></span> produk)</span>
                  <span id="cart-subtotal">Rp <?php echo e(number_format($cartSubtotal ?? 0, 0, ',', '.')); ?></span>
                </div>
                <div class="summary-row discount-row">
                  <span><i class="bi bi-tag-fill" style="color: #28a745;"></i> Diskon</span>
                  <span style="color: #28a745; font-weight: 600;">- Rp <?php echo e(number_format($totalDiscount ?? 0, 0, ',', '.')); ?></span>
                </div>
                <div class="summary-row">
                  <span>Total Berat</span>
                  <span><?php echo e(number_format($totalWeight, 2, ',', '.')); ?> kg</span>
                </div>
              </div>

              <div class="summary-total">
                <span>Total</span>
                <span id="cartTotal">Rp <?php echo e(number_format($cartTotal ?? 0, 0, ',', '.')); ?></span>
              </div>
            </div>

            <a href="<?php echo e(route('checkout')); ?>" class="btn-checkout">
              Lanjut ke Pembayaran
            </a>
          </div>
        </div>
      </div>

      <!-- Sticky Checkout Bar for Mobile -->
      <div class="sticky-checkout-bar">
        <div class="checkout-total">Total: Rp <span id="mobile-cartTotal"><?php echo e(number_format($cartTotal ?? 0, 0, ',', '.')); ?></span></div>
        <a href="<?php echo e(route('checkout')); ?>" class="checkout-btn-mobile">
          Checkout
        </a>
      </div>
    </div>
  </main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
  <script src="<?php echo e(asset('js/customer/keranjang.js')); ?>?v=22"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('footer'); ?>
  <?php echo $__env->make('components.customer.footer.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ecommerce-akrab\resources\views/customer/keranjang.blade.php ENDPATH**/ ?>