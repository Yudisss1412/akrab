// Subcategory filter based on main category
document.addEventListener('DOMContentLoaded', function() {
  console.log('Subcategory filter script loaded');
  
  // Define subcategories for each main category based on the 7 main categories
  const subcategories = {
    'kuliner': [
      { value: 'makanan-berat', label: 'Makanan Berat' },
      { value: 'camilan', label: 'Camilan' },
      { value: 'minuman', label: 'Minuman' },
      { value: 'bumbu-bahan-masak', label: 'Bumbu & Bahan Masak' },
      { value: 'kue-kering', label: 'Kue & Kering' },
      { value: 'makanan-ringan', label: 'Makanan Ringan' },
      { value: 'produk-olahan-susu', label: 'Produk Olahan Susu' }
    ],
    'fashion': [
      { value: 'pakaian-pria', label: 'Pakaian Pria' },
      { value: 'pakaian-wanita', label: 'Pakaian Wanita' },
      { value: 'pakaian-anak', label: 'Pakaian Anak' },
      { value: 'aksesoris', label: 'Aksesoris' },
      { value: 'tas', label: 'Tas' },
      { value: 'sepatu', label: 'Sepatu' },
      { value: 'perhiasan', label: 'Perhiasan' }
    ],
    'kerajinan': [
      { value: 'kerajinan-logam', label: 'Kerajinan Logam' },
      { value: 'kerajinan-kayu', label: 'Kerajinan Kayu' },
      { value: 'kerajinan-kertas', label: 'Kerajinan Kertas' },
      { value: 'kerajinan-kain', label: 'Kerajinan Kain' },
      { value: 'kerajinan-tanah-liat', label: 'Kerajinan Tanah Liat' },
      { value: 'souvenir-hadiah', label: 'Souvenir & Hadiah' },
      { value: 'alat-tulis-kerajinan', label: 'Alat Tulis Kerajinan' }
    ],
    'berkebun': [
      { value: 'tanaman-hias', label: 'Tanaman Hias' },
      { value: 'tanaman-buah', label: 'Tanaman Buah' },
      { value: 'tanaman-sayur', label: 'Tanaman Sayur' },
      { value: 'tanaman-obat', label: 'Tanaman Obat' },
      { value: 'pupuk-nutrisi', label: 'Pupuk & Nutrisi Tanaman' },
      { value: 'peralatan-berkebun', label: 'Peralatan Berkebun' },
      { value: 'pot-media-tanam', label: 'Pot & Media Tanam' }
    ],
    'kesehatan': [
      { value: 'vitamin-suplemen', label: 'Vitamin & Suplemen' },
      { value: 'obat-herbal', label: 'Obat Herbal' },
      { value: 'alat-kesehatan', label: 'Alat Kesehatan' },
      { value: 'produk-perawatan-diri', label: 'Produk Perawatan Diri' },
      { value: 'produk-terapi', label: 'Produk Terapi' },
      { value: 'diet-nutrisi', label: 'Produk Diet & Nutrisi' },
      { value: 'alat-bantu-kesehatan', label: 'Alat Bantu Kesehatan' }
    ],
    'mainan': [
      { value: 'mainan-edukatif', label: 'Mainan Edukatif' },
      { value: 'mainan-bayi', label: 'Mainan Bayi' },
      { value: 'mainan-anak', label: 'Mainan Anak' },
      { value: 'mainan-outdoor', label: 'Mainan Outdoor' },
      { value: 'boneka-figure', label: 'Boneka & Action Figure' },
      { value: 'permainan-tradisional', label: 'Permainan Tradisional' },
      { value: 'puzzle-permainan-meja', label: 'Puzzle & Permainan Meja' }
    ],
    'hampers': [
      { value: 'hampers-makanan', label: 'Hampers Makanan' },
      { value: 'hampers-minuman', label: 'Hampers Minuman' },
      { value: 'hampers-kecantikan', label: 'Hampers Kecantikan' },
      { value: 'hampers-fashion', label: 'Hampers Fashion' },
      { value: 'hampers-bayi', label: 'Hampers Bayi' },
      { value: 'hampers-kesehatan', label: 'Hampers Kesehatan' },
      { value: 'hampers-buah-sayur', label: 'Hampers Buah & Sayur' },
      { value: 'hampers-hari-raya', label: 'Hampers Hari Raya' }
    ]
  };

  // Determine current main category based on route or category title
  const currentPath = window.location.pathname;
  let currentMainCategory = null;

  // First, try to detect from URL
  if (currentPath.includes('/kategori/kuliner')) {
    currentMainCategory = 'kuliner';
  } else if (currentPath.includes('/kategori/fashion')) {
    currentMainCategory = 'fashion';
  } else if (currentPath.includes('/kategori/kerajinan')) {
    currentMainCategory = 'kerajinan';
  } else if (currentPath.includes('/kategori/berkebun')) {
    currentMainCategory = 'berkebun';
  } else if (currentPath.includes('/kategori/kesehatan')) {
    currentMainCategory = 'kesehatan';
  } else if (currentPath.includes('/kategori/mainan')) {
    currentMainCategory = 'mainan';
  } else if (currentPath.includes('/kategori/hampers')) {
    currentMainCategory = 'hampers';
  }

  // If not detected from URL, try to detect from category title element
  if (!currentMainCategory) {
    const categoryTitleElement = document.getElementById('kategori-title');
    if (categoryTitleElement) {
      const categoryTitle = categoryTitleElement.textContent.trim();
      switch(categoryTitle) {
        case 'Kuliner':
          currentMainCategory = 'kuliner';
          break;
        case 'Fashion':
          currentMainCategory = 'fashion';
          break;
        case 'Kerajinan':
        case 'Kerajinan Tangan':
          currentMainCategory = 'kerajinan';
          break;
        case 'Produk Berkebun':
          currentMainCategory = 'berkebun';
          break;
        case 'Produk Kesehatan':
          currentMainCategory = 'kesehatan';
          break;
        case 'Mainan':
          currentMainCategory = 'mainan';
          break;
        case 'Hampers':
          currentMainCategory = 'hampers';
          break;
      }
    }
  }

  console.log('Current path:', currentPath);
  console.log('Category title:', document.getElementById('kategori-title')?.textContent);
  console.log('Detected main category:', currentMainCategory);

  // Update subcategory checkboxes based on current main category
  if (currentMainCategory && subcategories[currentMainCategory]) {
    console.log('Updating subcategories for:', currentMainCategory);
    console.log('Subcategories list:', subcategories[currentMainCategory]);
    
    const subcategoryGroup = document.querySelector('.filter-checkbox-group');
    if (subcategoryGroup) {
      // Get all existing checkbox labels to remove them
      const existingCheckboxes = subcategoryGroup.querySelectorAll('label.checkbox-label');
      console.log('Removing', existingCheckboxes.length, 'existing subcategory checkboxes');
      
      // Remove all existing subcategory checkboxes
      existingCheckboxes.forEach(checkbox => {
        if (checkbox.querySelector('input[name="subkategori[]"]')) {
          checkbox.remove();
        }
      });
      
      // Add only the relevant subcategories
      subcategories[currentMainCategory].forEach(subcat => {
        const label = document.createElement('label');
        label.className = 'checkbox-label';
        label.innerHTML = `
          <input type="checkbox" name="subkategori[]" value="${subcat.value}"> ${subcat.label}
        `;
        subcategoryGroup.appendChild(label);
        console.log('Added subcategory:', subcat.label);
      });
    }
  } else {
    console.log('No matching main category found or subcategories not defined for:', currentMainCategory);
    
    // If we're on a category page but didn't detect it correctly, 
    // we might want to hide the subcategory section or show a message
    if (currentPath.includes('/kategori/')) {
      console.warn('Could not detect main category for subcategory filtering');
    }
  }
});