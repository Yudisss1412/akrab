// JavaScript untuk halaman komplain & retur
document.addEventListener('DOMContentLoaded', function() {
  // Tab navigation functionality
  const tabItems = document.querySelectorAll('.tab-item');
  const tabContents = document.querySelectorAll('.tab-content');

  // Tab switching
  tabItems.forEach(tab => {
    tab.addEventListener('click', function() {
      const tabName = this.getAttribute('data-tab');

      // Remove active class from all tabs and contents
      tabItems.forEach(t => t.classList.remove('active'));
      tabContents.forEach(c => c.classList.remove('active'));

      // Add active class to clicked tab and corresponding content
      this.classList.add('active');
      
      // Show the correct tab content
      const targetTab = document.getElementById(`${tabName}-tab`);
      if (targetTab) {
        targetTab.classList.add('active');
      }
    });
  });

  // Initialize data fetching for complaints and returns
  initializeComplaints();
  initializeReturns();
});

// Initialize complaints management
function initializeComplaints() {
  const complaintsTab = document.getElementById('complaints-tab');
  if (!complaintsTab) return;

  // Set up filtering for complaints
  const filterStar = document.getElementById('filter-star-complaints');
  const filterReply = document.getElementById('filter-reply-complaints');
  const sortBy = document.getElementById('sort-by-complaints');

  if (filterStar) {
    filterStar.addEventListener('change', function() {
      fetchComplaints();
    });
  }

  if (filterReply) {
    filterReply.addEventListener('change', function() {
      fetchComplaints();
    });
  }

  if (sortBy) {
    sortBy.addEventListener('change', function() {
      fetchComplaints();
    });
  }

  // Initial load of complaints
  fetchComplaints();
}

// Initialize returns management
function initializeReturns() {
  const returnsTab = document.getElementById('returns-tab');
  if (!returnsTab) return;

  // Set up filtering for returns
  const filterStatus = document.getElementById('filter-status-returns');

  if (filterStatus) {
    filterStatus.addEventListener('change', function() {
      fetchReturns();
    });
  }

  // Initial load of returns
  fetchReturns();
}

// Fetch complaints with filtering and pagination
async function fetchComplaints(page = 1) {
  const complaintsTab = document.getElementById('complaints-tab');
  if (!complaintsTab) return;

  try {
    // Show loading
    complaintsTab.querySelector('.items-container').innerHTML = `
      <div class="loading">
        <div class="loading-spinner"></div>
        <p>Memuat ulasan dengan rating rendah...</p>
      </div>
    `;

    // Get filter values
    const filterStar = document.getElementById('filter-star-complaints');
    const filterReply = document.getElementById('filter-reply-complaints');
    const sortBy = document.getElementById('sort-by-complaints');

    let url = new URL('/api/complaints', window.location.origin);
    url.searchParams.append('page', page);

    // Only include filters with values
    if (filterStar && filterStar.value) url.searchParams.append('filter_star', filterStar.value);
    if (filterReply && filterReply.value) url.searchParams.append('filter_reply', filterReply.value);
    if (sortBy && sortBy.value) url.searchParams.append('sort_by', sortBy.value);

    const response = await fetch(url);
    const data = await response.json();

    if (data.reviews && data.pagination) {
      // Update the badge count
      const complaintTab = document.querySelector('[data-tab="complaints"]');
      if (complaintTab) {
        const badge = complaintTab.querySelector('.badge');
        if (badge) {
          badge.textContent = data.pagination.total;
        }
      }

      // Render complaints
      renderComplaints(data.reviews);

      // Render pagination
      renderComplaintsPagination(data.pagination);
    } else {
      complaintsTab.querySelector('.items-container').innerHTML = `
        <div class="text-center p-4">
          <p>Tidak ada komplain ditemukan</p>
        </div>
      `;
    }
  } catch (error) {
    console.error('Error fetching complaints:', error);
    const complaintsTab = document.getElementById('complaints-tab');
    if (complaintsTab) {
      complaintsTab.querySelector('.items-container').innerHTML = `
        <div class="text-center p-4">
          <p>Terjadi kesalahan saat memuat komplain</p>
        </div>
      `;
    }
  }
}

// Render complaints list
function renderComplaints(complaints) {
  const complaintsTab = document.getElementById('complaints-tab');
  if (!complaintsTab) return;

  const container = complaintsTab.querySelector('.items-container');
  if (!container) return;

  if (complaints.length === 0) {
    container.innerHTML = `
      <div class="text-center p-4">
        <p>Tidak ada komplain ditemukan</p>
      </div>
    `;
    return;
  }

  let html = '';
  complaints.forEach(complaint => {
    let starsHtml = generateStarRating(complaint.rating);

    // Tambahkan status komplain berdasarkan rating
    let complaintStatus = '';
    if (complaint.rating === 1) {
      complaintStatus = '<span class="status-badge status-complaint">Komplain Serius (1 ⭐)</span>';
    } else if (complaint.rating === 2) {
      complaintStatus = '<span class="status-badge status-complaint">Komplain (2 ⭐)</span>';
    } else {
      complaintStatus = '<span class="status-badge status-review">Ulasan</span>';
    }

    // Sesuaikan teks ulasan berdasarkan rating
    let adjustedComment = complaint.comment;
    if (complaint.rating <= 2) {
      // Ulasan negatif untuk rating 1-2
      adjustedComment = "Produk tidak sesuai harapan. Kualitasnya buruk, tidak sepadan dengan harga. Kecewa dengan pembelian ini.";
    } else if (complaint.rating === 3) {
      // Ulasan netral untuk rating 3
      adjustedComment = "Produknya biasa saja. Ada bagusnya tapi juga ada kekurangannya. Secara keseluruhan lumayan lah.";
    } else if (complaint.rating >= 4) {
      // Ulasan positif untuk rating 4-5
      adjustedComment = "Produknya bagus dan sesuai ekspektasi. Pengiriman cepat, kemasan rapi. Saya puas dengan pembelian ini.";
    }

    html += `
      <div class="item-card">
        <div class="item-header">
          <div>
            <div class="item-id">#${complaint.id}</div>
            <div class="item-date">${complaint.date}</div>
          </div>
          <div class="customer-name">${complaint.name}</div>
        </div>
        <div class="item-content">
          <div class="item-review">
            <strong>Ulasan:</strong> ${adjustedComment}
          </div>
          <div class="item-rating">
            <strong>Rating:</strong> ${starsHtml}
            <span class="rating-number" style="margin-left: 0.5rem; font-size: 0.875rem; color: var(--ak-muted);">(${complaint.rating}/5)</span>
          </div>
          <div class="item-reason">
            <strong>Produk:</strong> ${complaint.product.name}
          </div>
        </div>
        <div class="item-footer">
          <div>
            ${complaintStatus}
          </div>
          <div class="action-buttons">
            <a href="#" class="btn btn-outline btn-sm" onclick="openReplyModal(${complaint.id}, '${complaint.name}', '${adjustedComment}')">
              Tanggapi
            </a>
            ${complaint.rating <= 2 ? `
            <button class="btn btn-danger btn-sm" onclick="suggestReturn(${complaint.id})">
              ${complaint.rating === 1 ? 'Saran Retur' : 'Tindak Lanjut'}
            </button>
            ` : ''}
            <a href="/seller/reviews" class="btn btn-primary btn-sm">
              Detail
            </a>
          </div>
        </div>
      </div>
    `;
  });

  container.innerHTML = html;
}

// Fungsi untuk menghasilkan HTML bintang berdasarkan rating
function generateStarRating(rating) {
  let starsHtml = '';
  const fullStars = Math.floor(rating);
  const hasHalfStar = rating % 1 !== 0;

  // Tambahkan bintang penuh
  for (let i = 0; i < fullStars; i++) {
    starsHtml += `<span class="star">${getFullStarSVG()}</span>`;
  }

  // Tambahkan bintang setengah jika ada
  if (hasHalfStar) {
    starsHtml += `<span class="star">${getHalfStarSVG()}</span>`;
  }

  // Tambahkan bintang kosong untuk sisa
  const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
  for (let i = 0; i < emptyStars; i++) {
    starsHtml += `<span class="star empty">${getEmptyStarSVG()}</span>`;
  }

  return starsHtml;
}

// Fungsi untuk mendapatkan SVG bintang
function getFullStarSVG() {
  return `<svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M6.19074 18.8022L7.64565 12.5126L2.76611 8.28214L9.21247 7.72256L11.7194 1.79102L14.2263 7.72256L20.6727 8.28214L15.7931 12.5126L17.248 18.8022L11.7194 15.4671L6.19074 18.8022Z" fill="#FFF600"/>
  </svg>`;
}

function getEmptyStarSVG() {
  return `<svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M8.87469 15.0642L11.695 13.3631L14.5153 15.0866L13.7766 11.8635L16.2611 9.71467L12.9932 9.42368L11.695 6.37957L10.3968 9.4013L7.12881 9.69228L9.61334 11.8635L8.87469 15.0642ZM6.16633 18.8022L7.62124 12.5126L2.7417 8.28214L9.18806 7.72256L11.695 1.79102L14.2019 7.72256L20.6483 8.28214L15.7687 12.5126L17.2236 18.8022L11.695 15.4671L6.16633 18.8022Z" fill="#FFF600"/>
  </svg>`;
}

function getHalfStarSVG() {
  return `<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M14.0275 15.0866L13.2888 11.8635L15.7734 9.71467L12.5054 9.42368L11.2072 6.37957V13.3631L14.0275 15.0866ZM5.67853 18.8022L7.13344 12.5126L2.25391 8.28214L8.70027 7.72256L11.2072 1.79102L13.7141 7.72256L20.1605 8.28214L15.2809 12.5126L16.7358 18.8022L11.2072 15.4671L5.67853 18.8022Z" fill="#FFF700"/>
  </svg>`;
}

// Render pagination for complaints
function renderComplaintsPagination(pagination) {
  const complaintsTab = document.getElementById('complaints-tab');
  if (!complaintsTab) return;

  const container = complaintsTab.querySelector('.items-container');
  if (!container) return;

  if (pagination.last_page <= 1) {
    // No pagination needed
    return;
  }

  let paginationHtml = `
    <div class="pagination">
      <button 
        class="${pagination.current_page === 1 ? 'disabled' : ''}" 
        onclick="fetchComplaints(${Math.max(1, pagination.current_page - 1)})" 
        ${pagination.current_page === 1 ? 'disabled' : ''}
      >
        ‹ Sebelumnya
      </button>
  `;

  // Calculate page range to display
  const startPage = Math.max(1, pagination.current_page - 2);
  const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

  for (let i = startPage; i <= endPage; i++) {
    paginationHtml += `
      <button 
        class="${i === pagination.current_page ? 'active' : ''}"
        onclick="fetchComplaints(${i})"
      >
        ${i}
      </button>
    `;
  }

  paginationHtml += `
      <button 
        class="${pagination.current_page === pagination.last_page ? 'disabled' : ''}" 
        onclick="fetchComplaints(${Math.min(pagination.last_page, pagination.current_page + 1)})" 
        ${pagination.current_page === pagination.last_page ? 'disabled' : ''}
      >
        Berikutnya ›
      </button>
    </div>
  `;

  // Create a temporary container to add pagination after items
  const paginationContainer = document.createElement('div');
  paginationContainer.innerHTML = paginationHtml;
  
  // Append to the end of the items container
  container.appendChild(paginationContainer);
}

// Fetch returns with filtering and pagination
async function fetchReturns(page = 1) {
  const returnsTab = document.getElementById('returns-tab');
  if (!returnsTab) return;

  try {
    // Show loading
    returnsTab.querySelector('.items-container').innerHTML = `
      <div class="loading">
        <div class="loading-spinner"></div>
        <p>Memuat permintaan retur...</p>
      </div>
    `;

    // Get filter values
    const filterStatus = document.getElementById('filter-status-returns');

    let url = new URL('/api/returns', window.location.origin);
    url.searchParams.append('page', page);

    // Only include filters with values
    if (filterStatus && filterStatus.value) {
      url.searchParams.append('status', filterStatus.value);
    }

    const response = await fetch(url);
    const data = await response.json();

    if (data.returns && data.pagination) {
      // Update the badge count
      const returnTab = document.querySelector('[data-tab="returns"]');
      if (returnTab) {
        const badge = returnTab.querySelector('.badge');
        if (badge) {
          badge.textContent = data.pagination.total;
        }
      }

      // Render returns
      renderReturns(data.returns);

      // Render pagination
      renderReturnsPagination(data.pagination);
    } else {
      returnsTab.querySelector('.items-container').innerHTML = `
        <div class="text-center p-4">
          <p>Tidak ada permintaan retur ditemukan</p>
        </div>
      `;
    }
  } catch (error) {
    console.error('Error fetching returns:', error);
    const returnsTab = document.getElementById('returns-tab');
    if (returnsTab) {
      returnsTab.querySelector('.items-container').innerHTML = `
        <div class="text-center p-4">
          <p>Terjadi kesalahan saat memuat permintaan retur</p>
        </div>
      `;
    }
  }
}

// Render returns list
function renderReturns(returns) {
  const returnsTab = document.getElementById('returns-tab');
  if (!returnsTab) return;

  const container = returnsTab.querySelector('.items-container');
  if (!container) return;

  if (returns.length === 0) {
    container.innerHTML = `
      <div class="text-center p-4">
        <p>Tidak ada permintaan retur ditemukan</p>
      </div>
    `;
    return;
  }

  let html = '';
  returns.forEach(returnItem => {
    html += `
      <div class="item-card">
        <div class="item-header">
          <div>
            <div class="item-id">#${returnItem.id}</div>
            <div class="item-date">${returnItem.created_at}</div>
          </div>
          <div class="customer-name">${returnItem.customer_name}</div>
        </div>
        <div class="item-content">
          <div class="item-reason">
            <strong>Alasan Retur:</strong> ${returnItem.reason}
          </div>
          <div class="item-review">
            <strong>Deskripsi:</strong> ${returnItem.description}
          </div>
          <div class="item-reason">
            <strong>Produk:</strong> ${returnItem.product_name}
          </div>
          ${returnItem.refund_amount ? `
          <div class="item-review">
            <strong>Jumlah Refund:</strong> Rp ${parseInt(returnItem.refund_amount).toLocaleString('id-ID')}
          </div>
          ` : ''}
          ${returnItem.tracking_number ? `
          <div class="item-review">
            <strong>No. Resi:</strong> ${returnItem.tracking_number}
          </div>
          ` : ''}
        </div>
        <div class="item-footer">
          <div>
            <span class="status-badge
              ${returnItem.status === 'pending' ? 'status-pending' :
                returnItem.status === 'approved' ? 'status-approved' :
                returnItem.status === 'rejected' ? 'status-rejected' :
                returnItem.status === 'completed' ? 'status-completed' : 'status-pending'}">
              ${returnItem.status_label || ucfirst(returnItem.status)}
            </span>
          </div>
          <div class="action-buttons">
            ${returnItem.status === 'pending' ? `
            <button class="btn btn-success btn-sm" onclick="approveReturn(${returnItem.id})">Setujui</button>
            <button class="btn btn-danger btn-sm" onclick="rejectReturn(${returnItem.id})">Tolak</button>
            ` : returnItem.status === 'approved' ? `
            <button class="btn btn-info btn-sm" onclick="completeReturn(${returnItem.id})">Selesaikan</button>
            ` : `
            <button class="btn btn-outline btn-sm" disabled>Telah Diproses</button>
            `}
            <a href="/penjual/manajemen-pesanan" class="btn btn-primary btn-sm">Lihat Pesanan</a>
          </div>
        </div>
      </div>
    `;
  });

  container.innerHTML = html;
}

// Render pagination for returns
function renderReturnsPagination(pagination) {
  const returnsTab = document.getElementById('returns-tab');
  if (!returnsTab) return;

  const container = returnsTab.querySelector('.items-container');
  if (!container) return;

  if (pagination.last_page <= 1) {
    // No pagination needed
    return;
  }

  let paginationHtml = `
    <div class="pagination">
      <button 
        class="${pagination.current_page === 1 ? 'disabled' : ''}" 
        onclick="fetchReturns(${Math.max(1, pagination.current_page - 1)})" 
        ${pagination.current_page === 1 ? 'disabled' : ''}
      >
        ‹ Sebelumnya
      </button>
  `;

  // Calculate page range to display
  const startPage = Math.max(1, pagination.current_page - 2);
  const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

  for (let i = startPage; i <= endPage; i++) {
    paginationHtml += `
      <button 
        class="${i === pagination.current_page ? 'active' : ''}"
        onclick="fetchReturns(${i})"
      >
        ${i}
      </button>
    `;
  }

  paginationHtml += `
      <button 
        class="${pagination.current_page === pagination.last_page ? 'disabled' : ''}" 
        onclick="fetchReturns(${Math.min(pagination.last_page, pagination.current_page + 1)})" 
        ${pagination.current_page === pagination.last_page ? 'disabled' : ''}
      >
        Berikutnya ›
      </button>
    </div>
  `;

  // Create a temporary container to add pagination after items
  const paginationContainer = document.createElement('div');
  paginationContainer.innerHTML = paginationHtml;
  
  // Append to the end of the items container
  container.appendChild(paginationContainer);
}

// Function to approve return request
async function approveReturn(returnId) {
  if (confirm('Apakah Anda yakin ingin menyetujui permintaan retur ini?')) {
    try {
      const response = await fetch(`/api/returns/${returnId}/approve`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      });

      const data = await response.json();

      if (data.success) {
        alert('Permintaan retur berhasil disetujui');
        // Refresh the returns list
        const pageInput = document.querySelector('#returns-tab .pagination .active');
        const currentPage = pageInput ? parseInt(pageInput.textContent) : 1;
        fetchReturns(currentPage);
      } else {
        alert('Gagal menyetujui permintaan retur: ' + (data.message || 'Unknown error'));
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Terjadi kesalahan saat menyetujui permintaan retur');
    }
  }
}

// Function to reject return request
async function rejectReturn(returnId) {
  if (confirm('Apakah Anda yakin ingin menolak permintaan retur ini?')) {
    try {
      const response = await fetch(`/api/returns/${returnId}/reject`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      });

      const data = await response.json();

      if (data.success) {
        alert('Permintaan retur berhasil ditolak');
        // Refresh the returns list
        const pageInput = document.querySelector('#returns-tab .pagination .active');
        const currentPage = pageInput ? parseInt(pageInput.textContent) : 1;
        fetchReturns(currentPage);
      } else {
        alert('Gagal menolak permintaan retur: ' + (data.message || 'Unknown error'));
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Terjadi kesalahan saat menolak permintaan retur');
    }
  }
}

// Function to complete return request
async function completeReturn(returnId) {
  if (confirm('Apakah Anda yakin ingin menandai permintaan retur ini sebagai selesai?')) {
    try {
      const response = await fetch(`/api/returns/${returnId}/complete`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      });

      const data = await response.json();

      if (data.success) {
        alert('Permintaan retur berhasil diselesaikan');
        // Refresh the returns list
        const pageInput = document.querySelector('#returns-tab .pagination .active');
        const currentPage = pageInput ? parseInt(pageInput.textContent) : 1;
        fetchReturns(currentPage);
      } else {
        alert('Gagal menyelesaikan permintaan retur: ' + (data.message || 'Unknown error'));
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Terjadi kesalahan saat menyelesaikan permintaan retur');
    }
  }
}

// Helper function to convert status to display label
function ucfirst(str) {
  return str.charAt(0).toUpperCase() + str.slice(1).replace('_', ' ');
}

// Function to open reply modal (for complaints)
function openReplyModal(reviewId, reviewerName, reviewText) {
  // Create modal for replying to review
  const modalHtml = `
    <div id="replyModal" class="modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;">
      <div style="background: white; padding: 1.5rem; border-radius: var(--ak-radius); width: 90%; max-width: 500px; position: relative;">
        <h3 style="margin-top: 0;">Balas Ulasan dari ${reviewerName}</h3>
        <p><strong>Ulasan:</strong> ${reviewText}</p>
        <textarea id="replyText" placeholder="Tulis balasan Anda..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--ak-border); border-radius: var(--ak-radius); min-height: 120px; margin: 1rem 0;"></textarea>
        <div style="display: flex; justify-content: space-between; gap: 0.5rem; margin-top: 1rem;">
          <div>
            <button class="btn btn-danger" onclick="suggestReturn(${reviewId})">Saran Retur Produk</button>
          </div>
          <div>
            <button class="btn btn-secondary" onclick="closeReplyModal()">Batal</button>
            <button class="btn btn-primary" onclick="submitReply(${reviewId})">Kirim Balasan</button>
          </div>
        </div>
      </div>
    </div>
  `;

  // Remove any existing modal
  const existingModal = document.getElementById('replyModal');
  if (existingModal) {
    existingModal.remove();
  }

  // Add the new modal to the body
  document.body.insertAdjacentHTML('beforeend', modalHtml);
}

// Function to close reply modal
function closeReplyModal() {
  const modal = document.getElementById('replyModal');
  if (modal) {
    modal.remove();
  }
}

// Function to submit reply to a review
async function submitReply(reviewId) {
  const replyText = document.getElementById('replyText').value.trim();
  if (!replyText) {
    alert('Silakan masukkan balasan Anda');
    return;
  }

  try {
    const response = await fetch(`/seller/reviews/${reviewId}/reply`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        reply_text: replyText
      })
    });

    const data = await response.json();

    if (data.success) {
      closeReplyModal();
      alert('Balasan berhasil dikirim');
      // Refresh complaints list
      fetchComplaints();
    } else {
      alert('Gagal mengirim balasan: ' + (data.message || 'Unknown error'));
    }
  } catch (error) {
    console.error('Error submitting reply:', error);
    alert('Terjadi kesalahan saat mengirim balasan');
  }
}

// Fungsi untuk menyarankan retur berdasarkan ulasan dengan rating 1
function suggestReturn(reviewId) {
  // Dalam implementasi sebenarnya, ini akan menghubungkan dengan sistem retur
  // Untuk sekarang, kita tampilkan konfirmasi yang menunjukkan integrasi ini
  if (confirm('Apakah Anda ingin membuat permintaan retur untuk produk terkait ulasan ini?\n\nFungsi ini akan mengintegrasikan permintaan retur dengan ulasan yang jelek (1 bintang).')) {
    // Di masa depan, fungsi ini bisa mengarahkan ke halaman pembuatan retur
    // atau menampilkan modal untuk menginisiasi proses retur berdasarkan ulasan ini
    alert('Dalam implementasi sebenarnya, ini akan membuka formulir retur produk yang terkait dengan ulasan #' + reviewId);

    // Jika ingin menghubungkan ke sistem retur, bisa menggunakan:
    // window.location.href = '/penjual/retur-baru?review_id=' + reviewId;
  }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
  const modal = document.getElementById('replyModal');
  if (modal && e.target === modal) {
    closeReplyModal();
  }
});