/**
 * Helper functions untuk menangani CSRF token
 */

// Fungsi untuk mendapatkan token CSRF terbaru
function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

// Fungsi untuk membuat request dengan CSRF token otomatis
async function fetchWithCsrf(url, options = {}) {
  // Ambil token CSRF terbaru sebelum setiap request
  const csrfToken = getCsrfToken();

  // Siapkan headers
  const headers = {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': csrfToken,
    ...options.headers
  };

  // Hapus X-CSRF-TOKEN untuk request GET karena tidak diperlukan
  if (options.method && options.method.toUpperCase() === 'GET') {
    delete headers['X-CSRF-TOKEN'];
  }

  // Gabungkan opsi
  const fetchOptions = {
    ...options,
    headers
  };

  try {
    const response = await fetch(url, fetchOptions);

    // Cek apakah response merupakan CSRF token mismatch
    if (response.status === 419) {
      showNotification('Sesi telah kedaluwarsa, silakan muat ulang halaman', 'error');
      throw new Error('CSRF token mismatch');
    }

    return response;
  } catch (error) {
    console.error('Error in fetchWithCsrf:', error);
    throw error;
  }
}

// Fungsi untuk menampilkan notifikasi
function showNotification(message, type = 'info') {
  // Buat elemen notifikasi di tengah layar
  const notification = document.createElement('div');

  // Gaya untuk notifikasi di tengah layar
  Object.assign(notification.style, {
    position: 'fixed',
    top: '50%',
    left: '50%',
    transform: 'translate(-50%, -50%)',
    padding: '16px 24px',
    borderRadius: '8px',
    color: 'white',
    backgroundColor: type === 'error' ? '#ef4444' : type === 'success' ? '#10b981' : '#3b82f6',
    zIndex: '9999',
    fontWeight: '600',
    boxShadow: '0 4px 6px rgba(0,0,0,0.1)',
    fontSize: '14px',
    maxWidth: '400px',
    wordWrap: 'break-word',
    opacity: '0',
    transition: 'opacity 0.3s ease-in-out'
  });

  notification.textContent = message;

  // Tambahkan ke body
  document.body.appendChild(notification);

  // Tampilkan dengan efek fade-in
  setTimeout(() => {
    notification.style.opacity = '1';
  }, 10);

  // Hapus notifikasi setelah 3 detik
  setTimeout(() => {
    notification.style.opacity = '0';
    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 300);
  }, 3000);
}

// Export functions agar bisa digunakan di file lain
if (typeof module !== 'undefined' && module.exports) {
  // Node.js
  module.exports = { getCsrfToken, fetchWithCsrf, showNotification };
} else {
  // Browser
  window.CsrfHelper = {
    getCsrfToken,
    fetchWithCsrf,
    showNotification
  };
}