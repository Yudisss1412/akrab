

<?php $__env->startSection('title', 'Daftar Produk — AKRAB'); ?>

<?php $__env->startSection('header'); ?>
  <?php echo $__env->make('components.customer.header.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
  <link href="<?php echo e(asset('css/customer/produk/halaman_produk.css')); ?>?v=26" rel="stylesheet"/>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
  
  <main class="produk-page" role="main">
    <div class="produk-header-row">
      <h1 class="produk-title" id="produk-heading">Daftar Produk</h1>
      <!-- Button Filter yang hanya tampil di mobile -->
      <button class="mobile-filter-toggle" id="mobile-filter-btn" aria-expanded="false">
        Filter & Urutkan
      </button>
    </div>

    <!-- Container untuk filter - disembunyikan di mobile -->
    <div class="produk-filter-container" id="mobile-filter-container">
      <div aria-label="Filter produk" class="produk-filter-right" role="region">
        <div class="filter-group">
          <label for="filter-kategori">Kategori</label>
          <select aria-controls="produk-grid" id="filter-kategori" name="kategori">
            <option value="all">Semua</option>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($category->name); ?>"><?php echo e($category->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        <div class="filter-group">
          <label for="filter-subkategori">Sub Kategori</label>
          <select aria-controls="produk-grid" id="filter-subkategori" name="subkategori">
            <option value="">Semua</option>
          </select>
        </div>

        <div class="filter-group">
          <label for="filter-harga-min">Harga Min</label>
          <input type="number" id="filter-harga-min" name="min_price" placeholder="Rp0" min="0">
        </div>

        <div class="filter-group">
          <label for="filter-harga-max">Harga Max</label>
          <input type="number" id="filter-harga-max" name="max_price" placeholder="Rp999.999.999" min="0">
        </div>

        <div class="filter-group">
          <label for="filter-rating">Rating</label>
          <select aria-controls="produk-grid" id="filter-rating" name="rating">
            <option value="">Semua Rating</option>
            <option value="4">4+ Bintang</option>
            <option value="3">3+ Bintang</option>
            <option value="2">2+ Bintang</option>
            <option value="1">1+ Bintang</option>
          </select>
        </div>

        <div class="filter-group">
          <label for="filter-urutkan">Urutkan</label>
          <select aria-controls="produk-grid" id="filter-urutkan" name="sort">
            <option value="popular">Terpopuler</option>
            <option value="newest">Terbaru</option>
            <option value="price-low">Harga Terendah</option>
            <option value="price-high">Harga Tertinggi</option>
          </select>
        </div>
      </div>
    </div>

    
    <div class="produk-grid" id="produk-grid" role="feed">
      
    </div>

    
    <?php if(request()->has('q')): ?>
    <div class="search-result-info" style="margin: 15px 0; padding: 12px 16px; background-color: #ffffff; border-radius: 8px; border-left: 4px solid #006E5C; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
      <h3 style="margin: 0 0 4px 0; font-size: 1.1rem; color: #006E5C;">Hasil pencarian untuk: "<?php echo e(request()->input('q')); ?>"</h3>
      <p style="margin: 0; font-size: 0.95rem; color: #666;">Ditemukan <?php echo e($products->count()); ?> produk</p>
    </div>

    
    <div class="produk-grid" id="produk-grid" role="feed">
      <?php if($products->count() > 0): ?>
        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="produk-card" data-product-id="<?php echo e($product->id); ?>">
          <?php
            $gambar_produk = $product->gambar ?? (is_array($product->formatted_images) ? ($product->formatted_images[0] ?? null) : ($product->formatted_images->first() ?? null));
          ?>
          <img src="<?php echo e($gambar_produk ?? asset('src/placeholder_produk.png')); ?>"
               alt="<?php echo e($product->name); ?>"
               onerror="this.onerror=null; this.src='<?php echo e(asset('src/placeholder_produk.png')); ?>';"
               loading="lazy">
          <div class="produk-card-info">
            <div class="produk-card-content">
              <h3 class="produk-card-name"><?php echo e($product->name); ?></h3>
              <div class="produk-card-sub"><?php echo e($product->subcategory_name ?? $product->category->name ?? 'Umum'); ?></div>
              <div class="produk-card-price"><?php echo e($product->formatted_harga ?? 'Rp ' . number_format($product->price, 0, ',', '.')); ?></div>
              <div class="produk-card-toko">
                <a href="/toko/<?php echo e($product->seller->store_name ?? $product->seller->name ?? $product->seller_id ?? 'toko-tidak-ditemukan'); ?>"
                   class="toko-link"
                   data-seller-name="<?php echo e($product->seller->store_name ?? $product->seller->name ?? 'Toko '.($product->seller->id ?? $product->seller_id ?? 'Toko-Tidak-Dikenal')); ?>">
                  <?php echo e($product->seller->store_name ?? $product->seller->name ?? 'Toko '.($product->seller->id ?? $product->seller_id ?? 'Toko-Tidak-Dikenal')); ?>

                </a>
              </div>
              <div class="produk-card-stars" aria-label="Rating <?php echo e($product->rating); ?> dari 5">
                <?php
                  $rating = $product->rating ?? 0;
                  $fullStars = floor($rating);
                  $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
                  $emptyStars = 5 - $fullStars - $halfStar;

                  echo str_repeat('<svg width="20" height="20" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF600"/></svg>', $fullStars);

                  if ($halfStar) {
                    echo '<svg width="20" height="20" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M29.6691 32.9982L28.0534 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535V29.2285L29.6691 32.9982ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#FFF700"/></svg>';
                  }

                  echo str_repeat('<svg width="20" height="20" viewBox="0 0 47 47" xmlns="http://www.w3.org/2000/svg"><path d="M17.3316 32.9493L23.5003 29.2285L29.6691 32.9982L28.0535 25.9482L33.4878 21.1993L26.3399 20.6118L23.5003 13.9535L20.6607 20.5628L13.5128 21.1993L18.9472 25.9482L17.3316 32.9493ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z" fill="#D1D5DB"/></svg>', $emptyStars);
                ?>
              </div>
            </div>
          </div>
          <div class="produk-card-actions">
            <a class="btn-lihat" data-product-id="<?php echo e($product->id); ?>" href="/produk_detail/<?php echo e($product->id); ?>">Lihat Detail</a>
            <button class="btn-add" data-product-id="<?php echo e($product->id); ?>" data-name="<?php echo e($product->name); ?>" type="button">+ Keranjang</button>
          </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php else: ?>
        <div class="no-results-message">
          <p>Maaf, tidak ditemukan produk yang sesuai dengan pencarian Anda.</p>
        </div>
      <?php endif; ?>
    </div>

    
    <style>
      .produk-filter-container {
        display: none !important;
      }
    </style>

    <?php else: ?>

    
    <div class="produk-grid" id="produk-grid" role="feed">
      
    </div>

    
    <div class="produk-rekomendasi produk-section" role="region" aria-labelledby="rekomendasi-heading">
      <div class="rekomendasi-header">
        <h3 id="rekomendasi-heading" class="produk-subtitle">Rekomendasi Produk</h3>
        <a class="produk-see-all" href="#">Lihat Semua</a>
      </div>
      <div class="produk-grid" id="rekom-grid" role="feed">
        
      </div>
      <div class="produk-pagination" id="rekom-pagination">
        
      </div>
    </div>

    
    <div class="produk-populer produk-section" role="region" aria-labelledby="populer-heading">
      <div class="produk-populer-header">
        <h3 id="populer-heading" class="produk-subtitle">Produk Paling Populer</h3>
        <a class="produk-see-all" href="#">Lihat Semua</a>
      </div>
      <div class="produk-grid" id="populer-grid" role="feed">
        
      </div>
      <div class="produk-pagination" id="populer-pagination">
        
      </div>
    </div>

    <?php endif; ?>
  </main>

  
  <script>
    // Pastikan script produk pakai input navbar (ID sama seperti di cust_welcome)
    window.__AKRAB_SEARCH_INPUT_ID__ = 'navbar-search';

    // Fungsi untuk toggle filter di mobile
    document.addEventListener('DOMContentLoaded', function() {
      const filterToggleBtn = document.getElementById('mobile-filter-btn');
      const filterContainer = document.getElementById('mobile-filter-container');

      if (filterToggleBtn && filterContainer) {
        // Set initial arrow direction
        filterToggleBtn.innerHTML = 'Filter & Urutkan 🔽';

        filterToggleBtn.addEventListener('click', function() {
          const isExpanded = filterToggleBtn.getAttribute('aria-expanded') === 'true';

          // Toggle show/hide filter container
          filterContainer.classList.toggle('show', !isExpanded);

          // Update aria-expanded attribute
          filterToggleBtn.setAttribute('aria-expanded', !isExpanded);

          // Update arrow icon based on state
          if (isExpanded) {
            filterToggleBtn.innerHTML = 'Filter & Urutkan 🔽';  // Arrow down when collapsed
          } else {
            filterToggleBtn.innerHTML = 'Filter & Urutkan 🔼';  // Arrow up when expanded
          }
        });
      }
    });
  </script>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
  <script defer src="<?php echo e(asset('js/customer/produk/halaman_produk.js')); ?>?v=26"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('footer'); ?>
  <?php echo $__env->make('components.customer.footer.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ecommerce-akrab\resources\views/customer/produk/halaman_produk.blade.php ENDPATH**/ ?>