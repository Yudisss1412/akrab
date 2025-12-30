<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Komplain & Retur — AKRAB</title>
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

    /* Tab navigation */
    .tab-nav {
      display: flex;
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 0.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
      overflow-x: auto;
      flex-wrap: wrap;
    }

    .tab-item {
      flex: 1;
      min-width: 150px;
      text-align: center;
      padding: 0.75rem 1rem;
      cursor: pointer;
      border-radius: 8px;
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--ak-text);
      white-space: nowrap;
      transition: all 0.2s ease;
    }

    .tab-item.active {
      background: var(--ak-primary);
      color: white;
    }

    .tab-item:hover:not(.active) {
      background: #f3f4f6;
    }

    .tab-badge {
      background: var(--ak-primary-light);
      color: var(--ak-primary);
      border-radius: 50%;
      padding: 0.25rem 0.5rem;
      font-size: 0.75rem;
      margin-left: 0.25rem;
    }

    .tab-item.active .tab-badge {
      background: rgba(255, 255, 255, 0.3);
      color: white;
    }

    /* Tab content */
    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
    }

    /* Item card */
    .item-card {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
      margin-bottom: 1rem;
      overflow: hidden;
    }

    .item-header {
      padding: 1rem;
      border-bottom: 1px solid var(--ak-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .item-id {
      font-weight: 600;
      color: var(--ak-primary);
    }

    .item-date {
      color: var(--ak-muted);
      font-size: 0.875rem;
    }

    .customer-name {
      font-weight: 500;
    }

    .item-content {
      padding: 1rem;
      border-bottom: 1px solid var(--ak-border);
    }

    .item-comment {
      margin-bottom: 0.5rem;
    }

    .item-rating {
      display: flex;
      align-items: center;
      gap: 0.25rem;
      color: #fbbf24;
      font-weight: bold;
    }

    .item-footer {
      padding: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .action-buttons {
      display: flex;
      gap: 0.5rem;
    }

    /* Status badge */
    .status-badge {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 1rem;
      font-size: 0.75rem;
      font-weight: 500;
      text-transform: capitalize;
    }

    .status-pending {
      background: rgba(245, 158, 11, 0.1);
      color: #d97706;
    }

    .status-approved {
      background: rgba(34, 197, 94, 0.1);
      color: #16a34a;
    }

    .status-rejected {
      background: rgba(239, 68, 68, 0.1);
      color: #dc2626;
    }

    .status-completed {
      background: rgba(34, 197, 94, 0.1);
      color: #16a34a;
    }

    .status-complaint {
      background: rgba(239, 68, 68, 0.1);
      color: #dc2626;
      font-weight: 600;
    }

    /* Button styles */
    .btn {
      border: 1px solid transparent;
      border-radius: var(--ak-radius);
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }

    .btn-sm {
      padding: 0.375rem 0.75rem;
      font-size: 0.875rem;
    }

    .btn-outline {
      border: 1px solid var(--ak-primary);
      color: var(--ak-primary);
      background: transparent;
    }

    .btn-outline:hover {
      background: var(--ak-primary);
      color: white;
    }

    .btn-primary {
      background: var(--ak-primary);
      color: white;
    }

    .btn-primary:hover {
      background: #005a4a;
    }

    .btn-success {
      background: var(--ak-success);
      color: white;
    }

    .btn-success:hover {
      background: #059669;
    }

    .btn-danger {
      background: var(--ak-danger);
      color: white;
    }

    .btn-danger:hover {
      background: #dc2626;
    }

    /* Loading and Empty States */
    .loading, .empty-state {
      background: var(--ak-white);
      border-radius: var(--ak-radius);
      padding: 2rem;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--ak-border);
      margin-bottom: 1.5rem;
    }

    .empty-state {
      color: var(--ak-muted);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .filter-row {
        flex-direction: column;
        align-items: stretch;
      }

      .filter-group {
        min-width: 100%;
      }

      .action-buttons {
        flex-direction: column;
      }

      .tab-nav {
        justify-content: flex-start;
      }

      .tab-item {
        min-width: 120px;
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
          <h1>
            Komplain & Retur untuk {{ auth()->user()->name }}
          </h1>
        </section>

        <!-- Tab Navigation and Contents with Unified Alpine.js -->
        <div x-data="{
          activeTab: 'complaints',
          complaintsTotal: 0,
          returnsTotal: 0,

          // Complaints data
          complaints: [],
          complaintsCurrentPage: 1,
          complaintsLastPage: 1,
          complaintsPerPage: 10,
          complaintsLoading: true,

          // Returns data
          returns: [],
          returnsCurrentPage: 1,
          returnsLastPage: 1,
          returnsPerPage: 10,
          returnsLoading: true,

          setActiveTab(tab) {
            this.activeTab = tab;
          },

          init() {
            this.fetchComplaints();
            this.fetchReturns();
          },

          fetchComplaints() {
            this.complaintsLoading = true;

            const filterStar = document.getElementById('filter-star-complaints')?.value || null;
            const filterReply = document.getElementById('filter-reply-complaints')?.value || null;
            const sortBy = document.getElementById('sort-by-complaints')?.value || 'newest';

            let url = new URL('/penjual/komplain-retur/api', window.location.origin);
            url.searchParams.append('page', this.complaintsCurrentPage);

            if (filterStar) url.searchParams.append('filter_star', filterStar);
            if (filterReply) url.searchParams.append('filter_reply', filterReply);
            if (sortBy) url.searchParams.append('sort_by', sortBy);

            fetch(url)
              .then(response => response.json())
              .then(data => {
                this.complaints = data.reviews || [];
                this.complaintsCurrentPage = 1; // Reset to page 1 when filters change
                this.complaintsLastPage = data.pagination?.last_page || 1;
                this.complaintsTotal = data.pagination?.total || 0; // Update the tab count here
                this.complaintsPerPage = data.pagination?.per_page || 10;
                this.complaintsLoading = false;
              })
              .catch(error => {
                console.error('Error fetching complaints:', error);
                this.complaintsLoading = false;
              });
          },

          fetchReturns() {
            this.returnsLoading = true;

            const filterStatus = document.getElementById('filter-status-returns')?.value || null;

            let url = new URL('/api/returns', window.location.origin);
            url.searchParams.append('page', this.returnsCurrentPage);

            if (filterStatus) url.searchParams.append('filter_status', filterStatus);

            fetch(url)
              .then(response => response.json())
              .then(data => {
                this.returns = data.returns || [];
                this.returnsCurrentPage = 1; // Reset to page 1 when filters change
                this.returnsLastPage = data.pagination?.last_page || 1;
                this.returnsTotal = data.pagination?.total || 0; // Update the tab count here
                this.returnsPerPage = data.pagination?.per_page || 10;
                this.returnsLoading = false;
              })
              .catch(error => {
                console.error('Error fetching returns:', error);
                this.returnsLoading = false;
              });
          }
        }">
          <!-- Tab Navigation -->
          <div class="tab-nav">
            <div class="tab-item" :class="{ 'active': activeTab === 'complaints' }" @click="setActiveTab('complaints'); fetchComplaints();">
              Komplain
              <span class="tab-badge" x-text="complaintsTotal"></span>
            </div>
            <div class="tab-item" :class="{ 'active': activeTab === 'returns' }" @click="setActiveTab('returns'); fetchReturns();">
              Retur
              <span class="tab-badge" x-text="returnsTotal"></span>
            </div>
          </div>

          <!-- Tab Contents -->
          <div x-show="activeTab === 'complaints'" x-cloak>
            <!-- Info banner for complaints -->
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: var(--ak-radius); padding: 1rem; margin-bottom: 1.5rem; color: #dc2626;">
              <strong>Perhatian:</strong> Ulasan dengan rating 1 bintang (peringkat terendah) biasanya terkait dengan masalah produk dan kemungkinan besar akan mengarah pada permintaan retur. Penjual disarankan untuk merespon dengan cepat dan mempertimbangkan opsi retur.
            </div>

            <!-- Filter Row for Complaints -->
            <div class="filter-row">
              <div class="filter-group">
                <label for="filter-star-complaints">Filter berdasarkan Bintang</label>
                <select id="filter-star-complaints" class="filter-control" @change="fetchComplaints()">
                  <option value="">Semua Bintang</option>
                  <option value="1">1 Bintang</option>
                  <option value="2">2 Bintang</option>
                </select>
              </div>

              <div class="filter-group">
                <label for="filter-reply-complaints">Filter berdasarkan Status Balasan</label>
                <select id="filter-reply-complaints" class="filter-control" @change="fetchComplaints()">
                  <option value="">Semua Status</option>
                  <option value="replied">Telah Dibalas</option>
                  <option value="pending">Belum Dibalas</option>
                </select>
              </div>

              <div class="filter-group">
                <label for="sort-by-complaints">Urutkan berdasarkan</label>
                <select id="sort-by-complaints" class="filter-control" @change="fetchComplaints()">
                  <option value="newest">Terbaru</option>
                  <option value="oldest">Terlama</option>
                  <option value="highest">Rating Tertinggi</option>
                  <option value="lowest">Rating Terendah</option>
                </select>
              </div>
            </div>

            <!-- Complaints List -->
            <div>
              <div x-show="complaintsLoading" class="loading">
                <p>Memuat komplain...</p>
              </div>

              <div x-show="!complaintsLoading && complaints.length === 0" class="empty-state">
                <p>Belum ada komplain</p>
              </div>

              <template x-for="complaint in complaints" :key="'complaint-' + complaint.id">
                <div class="item-card" x-data="{ isReplying: false, replyText: '' }">
                  <div class="item-header">
                    <div>
                      <h3 class="customer-name" x-text="complaint.name"></h3>
                      <span class="item-date" x-text="complaint.date"></span>
                    </div>
                    <div class="item-rating" x-html="generateStarRating(complaint.rating, 5)"></div>
                  </div>

                  <div class="item-content">
                    <div class="item-comment" x-text="complaint.comment"></div>
                    <div class="item-product">
                      <img :src="complaint.product.image" alt="Gambar Produk" style="width: 50px; height: 50px; border-radius: var(--ak-radius); object-fit: cover; margin-right: 0.5rem; border: 1px solid var(--ak-border);">
                      <span x-text="complaint.product.name"></span>
                    </div>
                  </div>

                  <div class="item-footer">
                    <span class="status-badge" :class="complaint.replied ? 'status-approved' : 'status-pending'" x-text="complaint.replied ? 'Telah Dibalas' : 'Belum Dibalas'"></span>
                    <div class="action-buttons">
                      <button class="btn btn-sm btn-outline" @click="isReplying = !isReplying">
                        <span x-text="isReplying ? 'Batal' : 'Balas'"></span>
                      </button>
                    </div>
                  </div>

                  <div x-show="isReplying" x-cloak style="padding: 1rem; background: #f9fafb; border-top: 1px solid var(--ak-border);">
                    <textarea
                      class="filter-control"
                      placeholder="Tulis balasan untuk ulasan ini..."
                      x-model="replyText"
                      x-ref="replyTextarea"
                      x-effect="if (isReplying) $refs.replyTextarea.focus()"
                      style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; resize: vertical; min-height: 80px;"
                    ></textarea>
                    <div class="action-buttons">
                      <button class="btn btn-sm btn-outline" @click="isReplying = false; replyText = ''">Batal</button>
                      <button class="btn btn-sm btn-primary" @click="replyToComplaint(complaint.id, replyText, () => { complaint.reply = replyText; complaint.replied = true; isReplying = false; replyText = ''; })">
                        Kirim Balasan
                      </button>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>

          <div x-show="activeTab === 'returns'" x-cloak>
            <!-- Filter Row for Returns -->
            <div class="filter-row">
              <div class="filter-group">
                <label for="filter-status-returns">Filter berdasarkan Status</label>
                <select id="filter-status-returns" class="filter-control" @change="fetchReturns()">
                  <option value="">Semua Status</option>
                  <option value="pending">Menunggu</option>
                  <option value="approved">Disetujui</option>
                  <option value="rejected">Ditolak</option>
                  <option value="completed">Selesai</option>
                </select>
              </div>

              <div class="filter-group">
                <label>&nbsp;</label>
                <div style="display: flex; gap: 0.5rem;">
                  <a href="{{ route('seller.reviews.index') }}" class="btn btn-primary">Lihat Semua Ulasan</a>
                </div>
              </div>
            </div>

            <!-- Returns List -->
            <div>
              <div x-show="returnsLoading" class="loading">
                <p>Memuat retur...</p>
              </div>

              <div x-show="!returnsLoading && returns.length === 0" class="empty-state">
                <p>Belum ada permintaan retur</p>
              </div>

              <template x-for="returnItem in returns" :key="'return-' + returnItem.id">
                <div class="item-card">
                  <div class="item-header">
                    <div>
                      <h3 class="customer-name" x-text="returnItem.customer_name"></h3>
                      <span class="item-date" x-text="returnItem.created_at"></span>
                    </div>
                    <span class="status-badge" :class="'status-' + returnItem.status" x-text="returnItem.status_label"></span>
                  </div>

                  <div class="item-content">
                    <p><strong>Produk:</strong> <span x-text="returnItem.product_name"></span></p>
                    <p><strong>Alasan:</strong> <span x-text="returnItem.reason"></span></p>
                    <p><strong>Deskripsi:</strong> <span x-text="returnItem.description"></span></p>
                    <p><strong>Jumlah Pengembalian:</strong> Rp <span x-text="returnItem.refund_amount.toLocaleString()"></span></p>
                  </div>

                  <div class="item-footer">
                    <div class="action-buttons">
                      <button class="btn btn-sm btn-outline" :disabled="returnItem.status !== 'pending'" @click="approveReturn(returnItem.id)">Setujui</button>
                      <button class="btn btn-sm btn-outline btn-danger" :disabled="returnItem.status !== 'pending'" @click="rejectReturn(returnItem.id)">Tolak</button>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  @include('components.admin_penjual.footer')

  <script>
    // Fungsi untuk membuat bintang berdasarkan rating
    function generateStarRating(rating, total = 5) {
      let stars = '';
      const fullStars = Math.floor(rating);
      const hasHalfStar = rating % 1 !== 0;

      // Tambahkan bintang penuh
      for (let i = 0; i < fullStars; i++) {
        stars += '<span class="star" style="color: #fbbf24; margin-right: 2px;"><svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg></span>';
      }

      // Tambahkan bintang setengah jika ada
      if (hasHalfStar) {
        stars += '<span class="star-half" style="position: relative; color: #fbbf24; margin-right: 2px;"><svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg><svg style="width: 20px; height: 20px; position: absolute; top: 0; left: 0; clip-path: polygon(0 0, 50% 0, 50% 100%, 0 100%);" fill="white" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg></span>';
      }

      // Tambahkan bintang kosong untuk sisa
      const emptyStars = total - fullStars - (hasHalfStar ? 1 : 0);
      for (let i = 0; i < emptyStars; i++) {
        stars += '<span class="star-empty" style="color: #e5e7eb; margin-right: 2px;"><svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg></span>';
      }

      return stars;
    }

    // Fungsi untuk balas komplain
    function replyToComplaint(reviewId, replyText, onSuccess) {
      fetch(`/seller/reviews/${reviewId}/reply`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          reply_text: replyText
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          onSuccess();
        } else {
          alert('Gagal mengirim balasan: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error sending reply:', error);
        alert('Terjadi kesalahan saat mengirim balasan');
      });
    }

    // Fungsi untuk menyetujui retur
    function approveReturn(returnId) {
      fetch(`/seller/returns/${returnId}/approve`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload(); // Refresh untuk melihat perubahan status
        } else {
          alert('Gagal menyetujui retur: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error approving return:', error);
        alert('Terjadi kesalahan saat menyetujui retur');
      });
    }

    // Fungsi untuk menolak retur
    function rejectReturn(returnId) {
      fetch(`/seller/returns/${returnId}/reject`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload(); // Refresh untuk melihat perubahan status
        } else {
          alert('Gagal menolak retur: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error rejecting return:', error);
        alert('Terjadi kesalahan saat menolak retur');
      });
    }
  </script>
</body>
</html>
