// Script utilitas untuk UI/UX yang lebih baik
// Mobile-first approach dengan aksesibilitas dan touch experience yang optimal

// Fungsi format harga yang konsisten
function formatPrice(price) {
  // If price is already formatted with 'Rp', return as is
  if (typeof price === 'string' && price.startsWith('Rp')) {
    // Extract numeric value and return properly formatted
    const numericValue = parseFloat(price.replace(/[^\d]/g, ''));
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(numericValue);
  }

  // Convert to number if it's a string
  const numericPrice = typeof price === 'string' ? parseFloat(price) : price;

  // Format as Rupiah using Indonesian locale for currency
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 2,  // Tampilkan selalu 2 angka desimal
    maximumFractionDigits: 2   // Batasi hanya 2 angka desimal
  }).format(numericPrice);
}

// Fungsi untuk menampilkan toast notification
function showToast(message, type = 'info') {
  // Hapus toast lama jika ada
  const existingToast = document.querySelector('.toast-notification');
  if (existingToast) {
    existingToast.remove();
  }

  const toast = document.createElement('div');
  toast.className = `toast-notification toast-${type}`;
  toast.setAttribute('role', 'alert');
  toast.setAttribute('aria-live', 'polite');
  
  const icons = {
    success: '✅',
    error: '❌', 
    warning: '⚠️',
    info: 'ℹ️'
  };
  
  toast.innerHTML = `
    <span>${icons[type] || icons.info}</span>
    <span>${message}</span>
  `;
  
  document.body.appendChild(toast);
  
  // Fokus ke toast untuk screen reader
  toast.focus();
  
  // Hapus toast setelah 3 detik
  setTimeout(() => {
    if (toast.parentNode) {
      toast.remove();
    }
  }, 3000);
}

// Fungsi untuk menampilkan loading spinner
function showLoading() {
  const existingSpinner = document.querySelector('.loading-spinner');
  if (existingSpinner) {
    existingSpinner.style.display = 'flex';
    return;
  }

  const spinner = document.createElement('div');
  spinner.className = 'loading-spinner';
  spinner.setAttribute('role', 'status');
  spinner.setAttribute('aria-label', 'Loading...');
  
  spinner.innerHTML = `
    <div class="spinner-wrapper">
      <div class="spinner-circle">
        <div class="spinner-dot"></div>
        <div class="spinner-dot dot-2"></div>
        <div class="spinner-dot dot-3"></div>
      </div>
      <p class="loading-text">Memproses...</p>
    </div>
  `;
  
  document.body.appendChild(spinner);
}

function hideLoading() {
  const spinner = document.querySelector('.loading-spinner');
  if (spinner) {
    spinner.style.display = 'none';
  }
}

// Fungsi untuk safe API call dengan loading dan error handling
async function safeApiCall(url, options = {}) {
  try {
    showLoading();
    const response = await fetch(url, {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        ...options.headers
      }
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    return { success: true, data };
  } catch (error) {
    console.error('API Error:', error);
    showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
    return { success: false, error: error.message };
  } finally {
    hideLoading();
  }
}

// Fungsi untuk trap focus dalam elemen modal
function trapFocus(element) {
  const focusableElements = element.querySelectorAll(
    'a[href], button, textarea, input[type="text"], input[type="radio"], input[type="checkbox"], select, [tabindex]:not([tabindex="-1"])'
  );
  
  const firstElement = focusableElements[0];
  const lastElement = focusableElements[focusableElements.length - 1];

  element.addEventListener('keydown', function(e) {
    if (e.key === 'Tab') {
      if (e.shiftKey) {
        if (document.activeElement === firstElement) {
          lastElement.focus();
          e.preventDefault();
        }
      } else {
        if (document.activeElement === lastElement) {
          firstElement.focus();
          e.preventDefault();
        }
      }
    }
  });
}

// Fungsi untuk membuat ripple effect pada sentuhan
function addRippleEffect(element) {
  element.classList.add('ripple-effect');
  
  element.addEventListener('click', function(e) {
    let ripple = document.createElement("span");
    ripple.classList.add("ripple");
    
    // Posisi ripple
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top - size / 2;
    
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    
    element.appendChild(ripple);
    
    setTimeout(() => {
      ripple.remove();
    }, 600);
  });
}

// Fungsi untuk validasi input accessibility
function setupAccessibleInputs() {
  // Fokus management untuk form inputs
  document.addEventListener('focus', function(event) {
    if (event.target.matches('input, select, textarea, button, a')) {
      event.target.classList.add('focused');
    }
  }, true);

  document.addEventListener('blur', function(event) {
    if (event.target.matches('input, select, textarea, button, a')) {
      event.target.classList.remove('focused');
    }
  }, true);
}

// Fungsi untuk setup touch target optimization
function setupTouchTargets() {
  // Tambahkan area sentuhan tambahan ke elemen-elemen interaktif
  const touchTargets = document.querySelectorAll('button, a, .item-check, .touch-optimized');
  
  touchTargets.forEach(target => {
    // Pastikan semua tombol dan link memiliki ukuran sentuhan minimum
    const computedStyle = window.getComputedStyle(target);
    const minHeight = parseInt(computedStyle.minHeight);
    const minWidth = parseInt(computedStyle.minWidth);
    
    if (minHeight < 44 && target.tagName === 'BUTTON') {
      target.style.minHeight = '44px';
    }
    
    if (minWidth < 44 && target.tagName === 'BUTTON') {
      target.style.minWidth = '44px';
    }
  });
}

// Inisialisasi saat DOM siap
document.addEventListener('DOMContentLoaded', function() {
  console.log('Customer DOM loaded with enhanced utilities');

  // Setup semua fungsi
  setupAccessibleInputs();
  setupTouchTargets();

  // Cari elemen search jika ada
  const searchInput = document.getElementById('navbar-search');

  if (searchInput) {
    // Tambahkan event listener untuk menangani pencarian
    searchInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault(); // Mencegah submit form default
        performSearch(searchInput.value.trim());
      }
    });

    // Tambahkan event listener untuk tombol pencarian jika ada
    // Mencari elemen tombol pencarian
    const searchButtons = document.querySelectorAll('.search-icon');
    searchButtons.forEach(button => {
      button.addEventListener('click', function() {
        const searchValue = searchInput.value.trim();
        if (searchValue) {
          performSearch(searchValue);
        } else {
          searchInput.focus();
        }
      });
    });
  }

  // Tambahkan event listener untuk elemen-elemen umum jika diperlukan
  // Misalnya, untuk menu hamburger, dropdown, dll.
});

// Buat fungsi-fungsi tersedia secara global
window.formatPrice = formatPrice;
window.showToast = showToast;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.safeApiCall = safeApiCall;
window.trapFocus = trapFocus;
window.addRippleEffect = addRippleEffect;

// Fungsi untuk melakukan pencarian
function performSearch(query) {
  if (!query) {
    alert('Silakan masukkan kata kunci pencarian terlebih dahulu.');
    return;
  }

  // Redirect ke halaman hasil pencarian
  window.location.href = `/produk/search?q=${encodeURIComponent(query)}`;
}