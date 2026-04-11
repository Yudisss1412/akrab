<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Manajemen Ulasan — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/penjual/manajemen_ulasan.css') }}">
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body>
  @include('components.admin_penjual.header')

  <div class="main-layout">
    <div class="content-wrapper">
      <main class="content admin-page-content" role="main">
        <!-- Page Header -->
        <section class="page-header">
          <h1>Manajemen Ulasan untuk {{ auth()->user()->name }}</h1>
        </section>

        <!-- Stats Card -->
        <section class="stats-grid">
          <div class="stat-card">
            <p class="stat-title">Rating Rata-rata</p>
            <div class="stat-rating-display">
              <span class="stat-value">{{ number_format($averageRating, 1) }}</span>
              <div class="star-rating">
                @php
                  $fullStars = floor($averageRating);
                  $emptyStars = 5 - $fullStars;
                  for ($i = 0; $i < $fullStars; $i++) {
                    echo '<svg width="20" height="20" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.19074 18.8022L7.64565 12.5126L2.76611 8.28214L9.21247 7.72256L11.7194 1.79102L14.2263 7.72256L20.6727 8.28214L15.7931 12.5126L17.248 18.8022L11.7194 15.4671L6.19074 18.8022Z" fill="#FFF600"/></svg>';
                  }
                  for ($i = 0; $i < $emptyStars; $i++) {
                    echo '<svg width="20" height="20" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.87469 15.0642L11.695 13.3631L14.5153 15.0866L13.7766 11.8635L16.2611 9.71467L12.9932 9.42368L11.695 6.37957L10.3968 9.4013L7.12881 9.69228L9.61334 11.8635L8.87469 15.0642ZM6.16633 18.8022L7.62124 12.5126L2.7417 8.28214L9.18806 7.72256L11.695 1.79102L14.2019 7.72256L20.6483 8.28214L15.7687 12.5126L17.2236 18.8022L11.695 15.4671L6.16633 18.8022Z" fill="#FFF600"/></svg>';
                  }
                @endphp
              </div>
            </div>
          </div>

          <div class="stat-card">
            <p class="stat-title">Total Ulasan</p>
            <p class="stat-value">{{ $totalReviews }}</p>
          </div>

          <div class="stat-card">
            <p class="stat-title">Rincian Ulasan</p>
            <div class="star-breakdown">
              @foreach($ratingStats as $rating => $count)
              <div class="star-row">
                <span class="star-label">
                  <span class="star">@php
                    $fullStars = floor($rating);
                    $emptyStars = 5 - $fullStars;
                    for ($i = 0; $i < $fullStars; $i++) {
                      echo '<svg width="20" height="20" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.19074 18.8022L7.64565 12.5126L2.76611 8.28214L9.21247 7.72256L11.7194 1.79102L14.2263 7.72256L20.6727 8.28214L15.7931 12.5126L17.248 18.8022L11.7194 15.4671L6.19074 18.8022Z" fill="#FFF600"/></svg>';
                    }
                    for ($i = 0; $i < $emptyStars; $i++) {
                      echo '<svg width="20" height="20" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.87469 15.0642L11.695 13.3631L14.5153 15.0866L13.7766 11.8635L16.2611 9.71467L12.9932 9.42368L11.695 6.37957L10.3968 9.4013L7.12881 9.69228L9.61334 11.8635L8.87469 15.0642ZM6.16633 18.8022L7.62124 12.5126L2.7417 8.28214L9.18806 7.72256L11.695 1.79102L14.2019 7.72256L20.6483 8.28214L15.7687 12.5126L17.2236 18.8022L11.695 15.4671L6.16633 18.8022Z" fill="#FFF600"/></svg>';
                    }
                  @endphp</span> {{ $rating }}
                </span>
                <div class="star-progress">
                  <div class="star-progress-bar" style="width: {{ $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0 }}%"></div>
                </div>
                <span class="star-count">{{ $count }}</span>
              </div>
              @endforeach
            </div>
          </div>
        </section>

        <!-- Filter Row -->
        <div class="filter-row">
          <div class="filter-group">
            <label for="filter-star" class="filter-label">
              <svg class="filter-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
              </svg>
              Filter Bintang
            </label>
            <select id="filter-star" class="filter-control" x-ref="filterStar"
                    @change="updateFilter('filterStar', $event.target.value);
                              console.log('Filter star changed and state updated to:', $event.target.value);
                              fetchReviews();">
              <option value="">Semua Bintang</option>
              <option value="5">5 Bintang</option>
              <option value="4">4 Bintang</option>
              <option value="3">3 Bintang</option>
              <option value="2">2 Bintang</option>
              <option value="1">1 Bintang</option>
            </select>
          </div>

          <div class="filter-group">
            <label for="filter-reply" class="filter-label">
              <svg class="filter-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
              </svg>
              Status Balasan
            </label>
            <select id="filter-reply" class="filter-control" x-ref="filterReply"
                    @change="updateFilter('filterReply', $event.target.value);
                              console.log('Filter reply changed and state updated to:', $event.target.value);
                              fetchReviews();">
              <option value="">Semua Status</option>
              <option value="replied">Telah Dibalas</option>
              <option value="pending">Belum Dibalas</option>
            </select>
          </div>

          <div class="filter-group">
            <label for="sort-by" class="filter-label">
              <svg class="filter-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 6h18M3 12h12M3 18h6"/>
              </svg>
              Urutkan
            </label>
            <select id="sort-by" class="filter-control" x-ref="sortBy"
                    @change="updateFilter('sortBy', $event.target.value);
                              console.log('Sort by changed and state updated to:', $event.target.value);
                              fetchReviews();">
              <option value="newest">Terbaru</option>
              <option value="oldest">Terlama</option>
              <option value="highest">Rating Tertinggi</option>
              <option value="lowest">Rating Terendah</option>
            </select>
          </div>
        </div>

        <!-- Reviews List with Alpine.js -->
        <div x-data="{
          reviews: [],
          currentPage: 1,
          lastPage: 1,
          total: 0,
          perPage: 10,
          loading: true,

          // State untuk menyimpan filter saat ini
          currentFilters: {
            filterStar: null,
            filterReply: null,
            sortBy: 'newest'
          },

          init() {
            // Set initial filter values from URL parameters only (since .view might not process blade syntax)
            const urlParams = new URLSearchParams(window.location.search);
            const filterStar = urlParams.get('filter_star') || null;
            const filterReply = urlParams.get('filter_reply') || null;
            const sortBy = urlParams.get('sort_by') || 'newest';

            // Set the select values
            if (filterStar) {
              document.getElementById('filter-star').value = filterStar;
            }
            if (filterReply) {
              document.getElementById('filter-reply').value = filterReply;
            }
            document.getElementById('sort-by').value = sortBy;

            // Update state filter
            this.currentFilters = {
              filterStar: filterStar,
              filterReply: filterReply,
              sortBy: sortBy
            };

            this.fetchReviews();
          },

          // Fungsi untuk mendapatkan nilai filter saat ini
          getFilterValues() {
            return this.currentFilters;
          },

          // Fungsi untuk memperbarui state filter
          updateFilter(filterName, value) {
            this.currentFilters[filterName] = value;
            console.log('Updated filter state:', this.currentFilters);

            // Tambahkan logging tambahan untuk debugging
            console.log('Filter update - Name:', filterName, 'Value:', value);
          },
          
          fetchReviews() {
            this.loading = true;

            // Ambil nilai filter menggunakan state Alpine.js
            const { filterStar, filterReply, sortBy } = this.getFilterValues();

            console.log('=== FRONTEND DEBUG START ===');
            console.log('Filter values being sent:', { filterStar, filterReply, sortBy });

            // Bangun URL
            const baseUrl = `{{ route('seller.reviews.api') }}`;
            let url = new URL(baseUrl, window.location.origin);

            // Reset ke halaman 1 dan tambahkan parameter
            url.searchParams.set('page', '1');
            if (filterStar) url.searchParams.set('filter_star', filterStar);
            if (filterReply) url.searchParams.set('filter_reply', filterReply);
            if (sortBy) url.searchParams.set('sort_by', sortBy);

            // Tambahkan timestamp untuk mencegah cache
            url.searchParams.set('t', new Date().getTime());

            console.log('FETCH URL:', url.toString());
            console.log('=== FRONTEND DEBUG END ===');

            fetch(url)
              .then(response => {
                console.log('Response Status:', response.status);
                return response.json();
              })
              .then(data => {
                console.log('=== RESPONSE DATA ===');
                console.log('Total items:', data.pagination?.total);
                console.log('Items on page:', data.reviews.length);

                // Tampilkan informasi validasi dari API
                if (data.validation) {
                  console.log('VALIDATION FROM API:', data.validation);
                }

                // Tampilkan informasi rating dan reply status dari hasil
                const ratingsReceived = data.reviews.map(r => r.rating);
                const repliesReceived = data.reviews.map(r => r.replied ? 'replied' : 'pending');

                console.log('Ratings received (first 10):', ratingsReceived.slice(0, 10));
                console.log('Reply status received (first 10):', repliesReceived.slice(0, 10));

                // Verifikasi apakah hasil sesuai filter
                if (filterStar) {
                  const allMatchRating = data.reviews.every(r => r.rating == filterStar);
                  console.log('Rating Filter Check:', {
                    expected: filterStar,
                    allMatch: allMatchRating,
                    uniqueRatings: [...new Set(ratingsReceived)]
                  });
                }

                if (filterReply) {
                  const expectedReplyStatus = filterReply === 'replied';
                  const allMatchReply = data.reviews.every(r => r.replied === expectedReplyStatus);
                  console.log('Reply Filter Check:', {
                    expected: filterReply,
                    expectedReplyStatus: expectedReplyStatus,
                    allMatch: allMatchReply,
                    uniqueReplyStatus: [...new Set(repliesReceived)]
                  });
                }

                console.log('=== RESPONSE DATA END ===');

                // Validasi data dari API sebelum menggunakannya
                if (!data || !data.reviews || !Array.isArray(data.reviews)) {
                  console.error('Invalid API response:', data);
                  this.reviews = [];
                  this.loading = false;
                  return;
                }

                // Force update dengan mengosongkan dulu
                this.reviews = [];

                // Gunakan nextTick untuk memastikan DOM diperbarui
                this.$nextTick(() => {
                  // Update state
                  this.reviews = [...data.reviews] || []; // Gunakan spread untuk memastikan reaktivitas
                  this.currentPage = 1;
                  this.lastPage = data.pagination?.last_page || 1;
                  this.total = data.pagination?.total || 0;
                  this.perPage = data.pagination?.per_page || 10;
                  this.loading = false;

                  console.log('State updated. New reviews count:', this.reviews.length);
                  console.log('Sample of new data:', this.reviews.slice(0, 3).map(r => ({id: r.id, rating: r.rating, comment: r.comment?.substring(0, 30) || 'no comment'})));
                });

                // Debug tambahan: tampilkan informasi filter yang aktif
                console.log('Active filters:', { filterStar, filterReply, sortBy });
              })
              .catch(error => {
                console.error('Fetch Error:', error);
                this.loading = false;
              });
          },
          
          changePage(page) {
            if (page >= 1 && page <= this.lastPage) {
              this.currentPage = page;
              this.fetchReviews();
            }
          },
          
          submitReply(reviewId, replyText, updateReviewState) {
            fetch(`/seller/reviews/${reviewId}/reply`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Accept': 'application/json'
              },
              body: JSON.stringify({
                reply_text: replyText
              })
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                // Show success message
                alert('Balasan berhasil dikirim');

                // Refresh the reviews data to show the updated status
                this.fetchReviews();
              } else {
                alert('Gagal mengirim balasan: ' + data.message);
              }
            })
            .catch(error => {
              console.error('Error submitting reply:', error);
              alert('Terjadi kesalahan saat mengirim balasan');
            });
          },

          generateStarRating(rating, total) {
            // Kita akan menggunakan fungsi global untuk menghindari masalah parsing
            // Fungsi ini hanya sebagai placeholder dan akan ditimpa oleh fungsi global
            // setelah script diload
            if (typeof window !== 'undefined' && typeof generateStarRatingGlobal !== 'undefined') {
              return generateStarRatingGlobal(rating, total);
            }
            return '';
          },

          // Fungsi ini seharusnya hanya digunakan dalam kondisi khusus, bukan untuk tampilan biasa
          // Untuk sekarang kita hanya kembalikan komentar asli
          adjustReviewTextByRating(rating, comment) {
            // Kembalikan komentar asli dari API
            return comment;
          }
        }">
          <div x-show="loading" class="text-center" style="padding: 2rem; text-align: center;">
            <p>Memuat ulasan...</p>
          </div>
          
          <template x-for="review in reviews" :key="'review-' + review.id + '-v' + (Math.random() * 100000000).toFixed(0)">
            <div class="review-card" x-data="{ isReplying: false, replyText: '' }">
              <div class="review-header">
                <div class="reviewer-info">
                  <h3 class="reviewer-name" x-text="review.name"></h3>
                  <p class="review-date" x-text="review.date"></p>
                </div>
                <div class="review-rating" x-html="generateStarRating(review.rating, 5)"></div>
              </div>

              <div class="review-content">
                <p class="review-text" x-text="review.comment"></p>
              </div>

              <div class="review-product">
                <img :src="review.product.image" alt="Gambar Produk" class="product-image">
                <div class="product-info">
                  <h4 class="product-name">
                    <a href="#" @click.prevent="window.location.href = `/penjual/produk/${review.product.id}`" x-text="review.product.name"></a>
                  </h4>
                  <div class="review-meta">
                    <span class="meta-badge" x-show="review.replied">✓ Dibalas</span>
                    <span class="meta-badge meta-badge--pending" x-show="!review.replied">⏳ Belum Dibalas</span>
                  </div>
                </div>
              </div>

              <div class="review-actions">
                <button class="reply-btn" @click="isReplying = !isReplying">
                  <span x-show="!isReplying && !review.replied">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    Balas Ulasan
                  </span>
                  <span x-show="!isReplying && review.replied">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    Lihat Balasan
                  </span>
                  <span x-show="isReplying">Batal</span>
                </button>
              </div>

              <div class="reply-form" x-show="isReplying" x-cloak>
                <div class="reply-form-header">
                  <h4>Balas Ulasan</h4>
                  <button class="reply-form-close" @click="isReplying = false; replyText = ''">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                  </button>
                </div>
                <textarea
                  class="reply-textarea"
                  placeholder="Tulis balasan untuk ulasan ini..."
                  x-model="replyText"
                  x-ref="replyTextarea"
                  x-effect="if (isReplying) $refs.replyTextarea.focus()"
                  maxlength="500"
                  rows="4"
                ></textarea>

                <div class="reply-form-footer">
                  <div class="char-count" x-text="`${replyText.length}/500 karakter`"></div>
                  <div class="reply-form-actions">
                    <button class="btn-secondary" @click="isReplying = false; replyText = ''">Batal</button>
                    <button class="reply-btn reply-btn-submit" @click="submitReply(review.id, replyText, (reply) => { review.reply = reply; review.replied = true; isReplying = false; replyText = ''; })">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                      </svg>
                      Kirim Balasan
                    </button>
                  </div>
                </div>
              </div>

              <div x-show="review.replied && !isReplying" class="reply-display">
                <div class="reply-display-header">
                  <h4>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                    </svg>
                    Balasan Penjual
                  </h4>
                </div>
                <p x-text="review.reply"></p>
              </div>
            </div>
          </template>
          
          <!-- Pagination with Alpine.js -->
          <div class="pagination-container" x-show="!loading && lastPage > 1">
            <div class="pagination-info">
              <span x-text="`Halaman ${currentPage} dari ${lastPage}`"></span>
              <span class="pagination-divider">•</span>
              <span x-text="`${total} ulasan`"></span>
            </div>
            <div class="pagination">
              <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1" class="pagination-btn pagination-btn--prev">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M15 18l-6-6 6-6"/>
                </svg>
                <span class="pagination-btn-text">Sebelumnya</span>
              </button>

              <template x-for="page in Array.from({length: Math.min(5, lastPage)}, (_, i) => {
                let start = Math.max(1, currentPage - 2);
                let end = Math.min(lastPage, start + 4);
                if (end - start < 4) start = Math.max(1, end - 4);
                return start + i;
              }).filter(page => page >= 1 && page <= lastPage)">
                <button
                  :key="page"
                  @click="changePage(page)"
                  :class="{'pagination-btn--active': page === currentPage}"
                  x-show="page >= Math.max(1, currentPage - 2) && page <= Math.min(lastPage, currentPage + 2)"
                  x-text="page"
                  class="pagination-btn pagination-btn--number"
                ></button>
              </template>

              <button @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage" class="pagination-btn pagination-btn--next">
                <span class="pagination-btn-text">Berikutnya</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M9 18l6-6-6-6"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <script>
    // Fungsi global untuk membuat bintang SVG
    function generateStarRatingGlobal(rating, total = 5) {
      let stars = '';
      const fullStars = Math.floor(rating);
      const hasHalfStar = rating % 1 !== 0;

      // Tambahkan bintang penuh
      for (let i = 0; i < fullStars; i++) {
        stars += '<span class="star"><svg width="20" height="20" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.19074 18.8022L7.64565 12.5126L2.76611 8.28214L9.21247 7.72256L11.7194 1.79102L14.2263 7.72256L20.6727 8.28214L15.7931 12.5126L17.248 18.8022L11.7194 15.4671L6.19074 18.8022Z" fill="#FFF600"/></svg></span>';
      }

      // Tambahkan bintang setengah jika ada
      if (hasHalfStar) {
        stars += '<span class="star"><svg width="20" height="20" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.0275 15.0866L13.2888 11.8635L15.7734 9.71467L12.5054 9.42368L11.2072 6.37957V13.3631L14.0275 15.0866ZM5.67853 18.8022L7.13344 12.5126L2.25391 8.28214L8.70027 7.72256L11.2072 1.79102L13.7141 7.72256L20.1605 8.28214L15.2809 12.5126L16.7358 18.8022L11.2072 15.4671L5.67853 18.8022Z" fill="#FFF700"/></svg></span>';
      }

      // Tambahkan bintang kosong untuk sisa
      const emptyStars = total - fullStars - (hasHalfStar ? 1 : 0);
      for (let i = 0; i < emptyStars; i++) {
        stars += '<span class="star empty"><svg width="20" height="20" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.87469 15.0642L11.695 13.3631L14.5153 15.0866L13.7766 11.8635L16.2611 9.71467L12.9932 9.42368L11.695 6.37957L10.3968 9.4013L7.12881 9.69228L9.61334 11.8635L8.87469 15.0642ZM6.16633 18.8022L7.62124 12.5126L2.7417 8.28214L9.18806 7.72256L11.695 1.79102L14.2019 7.72256L20.6483 8.28214L15.7687 12.5126L17.2236 18.8022L11.695 15.4671L6.16633 18.8022Z" fill="#FFF600"/></svg></span>';
      }

      return stars;
    }

    // Fungsi global untuk menyesuaikan teks ulasan berdasarkan rating
    // SEKARANG: Kita kembalikan komentar asli dari database agar sesuai dengan data yang difilter
    function adjustReviewTextByRating(rating, comment) {
      // Kembalikan komentar asli dari database, bukan teks default
      // Ini memungkinkan perbedaan komentar benar-benar terlihat setelah filter diterapkan
      return comment;
    }

    // Fungsi global untuk merefresh data ulasan
    function refreshReviews() {
      // Cari elemen dengan Alpine.js data
      const reviewContainer = document.querySelector('[x-data*="reviews"]');
      if (reviewContainer && reviewContainer.__x) {
        // Akses komponen Alpine.js dan panggil fetchReviews
        const component = reviewContainer.__x.$data;
        if (component && typeof component.fetchReviews === 'function') {
          component.fetchReviews();
        }
      }
    }

    // Fungsi global untuk memperbarui data ulasan secara manual
    function updateReviewsData() {
      refreshReviews();
    }
  </script>
</body>
</html>
