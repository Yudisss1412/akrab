<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Manajemen Ulasan — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <style>
    :root {
      --ak-primary: #006E5C;
      --ak-primary-light: #a8d5c9;
      --ak-white: #FFFFFF;
      --ak-background: #f0fdfa;
      --ak-text: #1D232A;
      --ak-muted: #6b7280;
      --ak-border: #E5E7EB;
      --ak-success: #10B981;
      --ak-danger: #EF4444;
      --ak-warning: #F59E0B;
      --ak-radius: 12px;
      --ak-space: 16px;
    }
    
    * {
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      color: var(--ak-text);
      background: var(--ak-background);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    
    .main-layout {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    
    .content-wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: 0 1.5rem;
    }
    
    /* Page header */
    .page-header {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
    }
    
    .page-header h1 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--ak-primary);
    }
    
    /* Stats Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    
    @media (min-width: 768px) {
      .stats-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }
    
    .stat-card {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
    }
    
    .stat-title {
      font-size: 0.875rem;
      color: var(--ak-muted);
      margin: 0 0 0.25rem 0;
    }
    
    .stat-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--ak-text);
      margin: 0;
    }
    
    .star-breakdown {
      width: 100%;
      margin-top: 0.5rem;
    }
    
    .star-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.25rem;
    }
    
    .star-label {
      display: flex;
      align-items: center;
      gap: 0.25rem;
      font-size: 0.875rem;
    }
    
    .star-progress {
      flex: 1;
      height: 8px;
      background: #e5e7eb;
      border-radius: 4px;
      margin: 0 0.5rem;
      overflow: hidden;
    }
    
    .star-progress-bar {
      height: 100%;
      background: var(--ak-warning);
    }
    
    /* Filter row */
    .filter-row {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 1rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      align-items: center;
    }
    
    .filter-group {
      flex: 1;
      min-width: 200px;
    }
    
    .filter-group label {
      display: block;
      font-size: 0.875rem;
      color: var(--ak-muted);
      margin-bottom: 0.5rem;
    }
    
    .filter-control {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid var(--ak-border);
      border-radius: var(--ak-radius);
      background: var(--ak-white);
    }
    
    /* Review card */
    .review-card {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
    }
    
    .review-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1rem;
    }
    
    .reviewer-info {
      flex: 1;
    }
    
    .reviewer-name {
      font-weight: 600;
      margin: 0;
    }
    
    .review-date {
      font-size: 0.875rem;
      color: var(--ak-muted);
      margin: 0.25rem 0 0;
    }
    
    .review-rating {
      display: flex;
      gap: 0.25rem;
    }
    
    .star {
      display: inline-block;
      vertical-align: middle;
      margin-right: 1px;
    }

    .star svg {
      width: 20px;
      height: 20px;
    }

    .star.empty svg {
      opacity: 0.4;
    }

    .inconsistency-warning {
      background: #fffbeb;
      border: 1px solid #fbbf24;
      border-radius: var(--ak-radius);
      padding: 0.5rem;
      margin-top: 0.5rem;
      font-size: 0.8rem;
      color: #92400e;
      font-weight: 500;
    }
    
    .review-content {
      margin: 1rem 0;
      line-height: 1.5;
    }
    
    .review-product {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin: 1rem 0;
      padding: 0.75rem;
      background: #f9fafb;
      border-radius: var(--ak-radius);
    }
    
    .product-image {
      width: 60px;
      height: 60px;
      border-radius: var(--ak-radius);
      object-fit: cover;
      border: 1px solid var(--ak-border);
    }
    
    .product-info {
      flex: 1;
    }
    
    .product-name {
      margin: 0;
      font-weight: 500;
      color: var(--ak-primary);
    }
    
    .product-name a {
      text-decoration: none;
      color: var(--ak-primary);
    }
    
    .product-name a:hover {
      text-decoration: underline;
    }
    
    .reply-btn {
      background: var(--ak-primary);
      color: white;
      border: none;
      border-radius: var(--ak-radius);
      padding: 0.5rem 1rem;
      font-weight: 500;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .reply-btn:hover {
      background: #005a4a;
    }
    
    .reply-form {
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid var(--ak-border);
    }
    
    .reply-textarea {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid var(--ak-border);
      border-radius: var(--ak-radius);
      min-height: 100px;
      resize: vertical;
      margin-bottom: 0.5rem;
    }
    
    .reply-textarea:focus {
      outline: none;
      border-color: var(--ak-primary);
    }
    
    .reply-actions {
      display: flex;
      justify-content: flex-end;
      gap: 0.5rem;
    }
    
    .btn-secondary {
      background: #e5e7eb;
      color: var(--ak-text);
      border: none;
      border-radius: var(--ak-radius);
      padding: 0.5rem 1rem;
      font-weight: 500;
      cursor: pointer;
    }
    
    .btn-secondary:hover {
      background: #d1d5db;
    }
    
    .char-count {
      text-align: right;
      font-size: 0.75rem;
      color: var(--ak-muted);
      margin-top: 0.25rem;
    }
    
    /* Pagination */
    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 0.5rem;
      margin-top: 1.5rem;
    }
    
    .pagination a, .pagination span {
      display: inline-block;
      padding: 0.5rem 0.75rem;
      border-radius: var(--ak-radius);
      text-decoration: none;
      font-size: 0.875rem;
    }
    
    .pagination .active {
      background: var(--ak-primary);
      color: white;
    }
    
    .card {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .page-header h1 {
        font-size: 1.25rem;
      }

      /* Stats Grid - horizontal scroll layout */
      .stats-grid {
        display: flex;
        overflow-x: auto;
        gap: 1rem;
        padding-bottom: 1rem;
        -webkit-overflow-scrolling: touch;
      }

      .stat-card {
        flex: 0 0 auto; /* Don't grow or shrink, maintain natural width */
        min-width: 200px; /* Ensure minimum width so content is readable */
        width: auto; /* Allow natural width */
      }

      .filter-row {
        flex-direction: column;
        gap: 1rem;
      }

      .filter-group {
        min-width: auto;
        width: 100%;
      }

      .filter-control {
        width: 100%;
        min-height: 44px; /* Ensure adequate touch target size */
      }

      .review-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
      }

      .review-rating {
        align-self: flex-start;
      }

      .review-product {
        flex-direction: row;
        align-items: center;
      }

      .product-image {
        width: 60px !important;
        height: 60px !important;
        min-width: 60px;
        min-height: 60px;
      }

      .reply-btn {
        width: 100%;
        justify-content: center;
        margin-top: 0.5rem;
        min-height: 44px; /* Ensure adequate touch target size */
      }

      .reply-form {
        width: 100%;
      }

      .reply-actions {
        flex-direction: column;
      }

      .reply-actions button {
        width: 100%;
        margin-bottom: 0.5rem;
      }

      .reply-actions button:last-child {
        margin-bottom: 0;
      }

      .star-breakdown {
        width: 100%;
      }

      .star-row {
        flex-direction: row;
        align-items: center;
        gap: 0.5rem;
      }

      .star-progress {
        flex: 1;
        min-width: 80px;
      }

      .pagination {
        flex-wrap: wrap;
        gap: 0.25rem;
      }

      .pagination a, .pagination span {
        padding: 0.5rem;
        font-size: 0.75rem;
      }
    }
  </style>
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
            <div style="display: flex; align-items: center; gap: 2px;">
              <span class="stat-value" style="margin-right: 0.5rem;">{{ number_format($averageRating, 1) }}</span>
              <span class="star">@php
                $fullStars = floor($averageRating);
                $emptyStars = 5 - $fullStars;
                for ($i = 0; $i < $fullStars; $i++) {
                  echo '<svg width="20" height="20" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.19074 18.8022L7.64565 12.5126L2.76611 8.28214L9.21247 7.72256L11.7194 1.79102L14.2263 7.72256L20.6727 8.28214L15.7931 12.5126L17.248 18.8022L11.7194 15.4671L6.19074 18.8022Z" fill="#FFF600"/></svg>';
                }
                for ($i = 0; $i < $emptyStars; $i++) {
                  echo '<svg width="20" height="20" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.87469 15.0642L11.695 13.3631L14.5153 15.0866L13.7766 11.8635L16.2611 9.71467L12.9932 9.42368L11.695 6.37957L10.3968 9.4013L7.12881 9.69228L9.61334 11.8635L8.87469 15.0642ZM6.16633 18.8022L7.62124 12.5126L2.7417 8.28214L9.18806 7.72256L11.695 1.79102L14.2019 7.72256L20.6483 8.28214L15.7687 12.5126L17.2236 18.8022L11.695 15.4671L6.16633 18.8022Z" fill="#FFF600"/></svg>';
                }
              @endphp</span>
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
                <span>{{ $count }}</span>
              </div>
              @endforeach
            </div>
          </div>
        </section>

        <!-- Filter Row -->
        <div class="filter-row">
          <div class="filter-group">
            <label for="filter-star">Filter berdasarkan Bintang</label>
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
            <label for="filter-reply">Filter berdasarkan Status Balasan</label>
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
            <label for="sort-by">Urutkan berdasarkan</label>
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

              <div class="review-content" x-text="review.comment"></div>

              <div class="review-product">
                <img :src="review.product.image" alt="Gambar Produk" class="product-image">
                <div class="product-info">
                  <h4 class="product-name">
                    <a href="#" @click.prevent="window.location.href = `/penjual/produk/${review.product.id}`" x-text="review.product.name"></a>
                  </h4>
                </div>
              </div>

              <div class="review-info" style="margin-top: 0.5rem; padding: 0.5rem; background: #f9f9f9; border-radius: var(--ak-radius); font-size: 0.8rem;">
                <strong>Info:</strong> Rating: <span x-text="review.rating"></span>, ID: <span x-text="review.id"></span>
                <span x-show="review.replied" style="color: green;"> • Dibalas</span>
                <span x-show="!review.replied" style="color: orange;"> • Belum Dibalas</span>

                <!-- Tambahkan indikator filter untuk debugging -->
                <div x-show="currentFilters.filterStar" style="margin-top: 0.2rem; font-weight: bold; color: blue;">
                  Filter: <span x-text="'Bintang ' + currentFilters.filterStar"></span>
                </div>
                <div x-show="currentFilters.filterReply" style="margin-top: 0.2rem; font-weight: bold; color: purple;">
                  Filter: <span x-text="currentFilters.filterReply === 'replied' ? 'Telah Dibalas' : 'Belum Dibalas'"></span>
                </div>
              </div>

              <button class="reply-btn" @click="isReplying = !isReplying">
                <span x-text="isReplying ? 'Batal' : (review.replied ? 'Lihat Balasan' : 'Balas')"></span>
              </button>

              <div class="reply-form" x-show="isReplying" x-cloak>
                <textarea
                  class="reply-textarea"
                  placeholder="Tulis balasan untuk ulasan ini..."
                  x-model="replyText"
                  x-ref="replyTextarea"
                  x-effect="if (isReplying) $refs.replyTextarea.focus()"
                  maxlength="500"
                ></textarea>

                <div class="char-count" x-text="`${replyText.length}/500 karakter`"></div>

                <div class="reply-actions">
                  <button class="btn-secondary" @click="isReplying = false; replyText = ''">Batal</button>
                  <button class="reply-btn" @click="submitReply(review.id, replyText, (reply) => { review.reply = reply; review.replied = true; isReplying = false; replyText = ''; })">
                    Kirim Balasan
                  </button>
                </div>
              </div>

              <div x-show="review.replied && !isReplying" class="reply-display" style="margin-top: 1rem; padding: 1rem; background: #f0fdfa; border-left: 3px solid var(--ak-primary); border-radius: 0 var(--ak-radius) var(--ak-radius) 0;">
                <strong>Balasan:</strong>
                <p style="margin: 0.5rem 0 0;" x-text="review.reply"></p>
              </div>
            </div>
          </template>
          
          <!-- Pagination with Alpine.js -->
          <div class="pagination" x-show="!loading && lastPage > 1">
            <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1" class="prev">‹ Sebelumnya</button>
            
            <template x-for="page in Array.from({length: Math.min(5, lastPage)}, (_, i) => {
              let start = Math.max(1, currentPage - 2);
              let end = Math.min(lastPage, start + 4);
              if (end - start < 4) start = Math.max(1, end - 4);
              return start + i;
            }).filter(page => page >= 1 && page <= lastPage)">
              <button 
                :key="page" 
                @click="changePage(page)" 
                :class="{'active': page === currentPage}" 
                x-show="page >= Math.max(1, currentPage - 2) && page <= Math.min(lastPage, currentPage + 2)"
                x-text="page"
              ></button>
            </template>
            
            <button @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage" class="next">Berikutnya ›</button>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')
  
  <script>
    // CSRF token meta tag
    const csrfToken = document.head.querySelector('meta[name=csrf-token]');
    if (!csrfToken) {
      // Create CSRF token meta tag if not exists
      const meta = document.createElement('meta');
      meta.name = 'csrf-token';
      meta.content = '{{ csrf_token() }}';
      document.head.appendChild(meta);
    }
  </script>

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