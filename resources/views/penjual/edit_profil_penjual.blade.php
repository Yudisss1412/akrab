<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Edit Profil Penjual — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/penjual/edit_profil_penjual.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>
  @include('components.admin_penjual.header')

  <div class="main-layout">
    <div class="content-wrapper">
      <main class="content admin-page-content" role="main">
        <div class="page">
          <section class="card form-card" aria-labelledby="title-edit-profile">
          <header class="form-head">
            <h1 id="title-edit-profile">Edit Profil Toko</h1>
            <div class="avatar-box">
              <!-- Foto bisa diklik -->
              <label for="avatarInput" class="avatar-label">
                <div class="avatar-preview" id="avatarPreview">
                  @if(auth()->user()->seller && auth()->user()->seller->profile_image)
                    <img src="{{ asset('storage/' . auth()->user()->seller->profile_image) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                  @else
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                      <path d="M12 10C14.2091 10 16 8.20914 16 6C16 3.79086 14.2091 2 12 2C9.79086 2 8 3.79086 8 6C8 8.20914 9.79086 10 12 10Z" stroke="#006E5C" stroke-width="1.5"/>
                      <path d="M20 17.5C20 19.985 20 22 12 22C4 22 4 19.985 4 17.5C4 15.015 7.582 13 12 13C16.418 13 20 15.015 20 17.5Z" stroke="#006E5C" stroke-width="1.5"/>
                    </svg>
                  @endif
                </div>
              </label>
              <input type="file" id="avatarInput" name="avatar" accept="image/*" hidden>
            </div>
          </header>

          <form class="edit-form" id="editSellerProfileForm" action="{{ route('profil.penjual.update') }}" method="POST" novalidate>
            @method('PUT')
            <div class="form-group field">
              <input type="text" id="shopName" name="shopName" value="{{ old('shopName', auth()->user()->name) }}" required placeholder=" ">
              <label for="shopName">Nama Toko</label>
              <p class="error-message" id="shopName-error"></p>
            </div>

            <div class="form-group field">
              <input type="text" id="ownerName" name="ownerName" value="{{ old('ownerName', auth()->user()->name) }}" required placeholder=" ">
              <label for="ownerName">Nama Pemilik</label>
              <p class="error-message" id="ownerName-error"></p>
            </div>

            <div class="form-group field">
              <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required placeholder=" ">
              <label for="email">Email</label>
              <p class="error-message" id="email-error"></p>
            </div>

            <div class="form-group field">
              <input type="tel" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone ?? '+62 812-3456-7890') }}" required placeholder=" ">
              <label for="phone">Nomor Telepon</label>
              <p class="error-message" id="phone-error"></p>
            </div>

            <div class="form-group field">
              <textarea id="address" name="address" rows="3" required placeholder=" ">{{ old('address', auth()->user()->address ?? 'Alamat belum diisi') }}</textarea>
              <label for="address">Alamat</label>
              <p class="error-message" id="address-error"></p>
              <div class="address-search-container">
                <button type="button" id="searchAddressBtn" class="btn btn-secondary">Cari Alamat</button>
                <div id="searchSuggestions" class="search-suggestions"></div>
              </div>
            </div>

            <div class="map-container">
              <label>Lokasi pada Peta</label>
              <div id="map"></div>
              <!-- Hidden fields to store coordinates -->
              <input type="hidden" id="lat" name="lat" value="{{ old('lat', auth()->user()->lat) }}">
              <input type="hidden" id="lng" name="lng" value="{{ old('lng', auth()->user()->lng) }}">
            </div>

            <div class="form-group field">
              <textarea id="shopDescription" name="shopDescription" rows="3" required placeholder=" ">{{ old('shopDescription', auth()->user()->shop_description ?? 'Toko belum memiliki deskripsi') }}</textarea>
              <label for="shopDescription">Deskripsi Toko</label>
              <p class="error-message" id="shopDescription-error"></p>
            </div>

            <div class="bank-info-section">
              <h3>Informasi Bank Penerima</h3>
              <div class="grid-two">
                <div class="form-group field">
                  <select id="bankName" name="bankName">
                    <option value="">Pilih Bank</option>
                    <option value="BCA" {{ old('bankName', auth()->user()->bank_name) == 'BCA' ? 'selected' : '' }}>BCA</option>
                    <option value="BNI" {{ old('bankName', auth()->user()->bank_name) == 'BNI' ? 'selected' : '' }}>BNI</option>
                    <option value="BRI" {{ old('bankName', auth()->user()->bank_name) == 'BRI' ? 'selected' : '' }}>BRI</option>
                    <option value="Mandiri" {{ old('bankName', auth()->user()->bank_name) == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                    <option value="BTN" {{ old('bankName', auth()->user()->bank_name) == 'BTN' ? 'selected' : '' }}>BTN</option>
                    <option value="CIMB Niaga" {{ old('bankName', auth()->user()->bank_name) == 'CIMB Niaga' ? 'selected' : '' }}>CIMB Niaga</option>
                    <option value="Danamon" {{ old('bankName', auth()->user()->bank_name) == 'Danamon' ? 'selected' : '' }}>Danamon</option>
                    <option value="Permata" {{ old('bankName', auth()->user()->bank_name) == 'Permata' ? 'selected' : '' }}>Permata</option>
                    <option value="OCBC NISP" {{ old('bankName', auth()->user()->bank_name) == 'OCBC NISP' ? 'selected' : '' }}>OCBC NISP</option>
                    <option value="Bank Central Asia" {{ old('bankName', auth()->user()->bank_name) == 'Bank Central Asia' ? 'selected' : '' }}>Bank Central Asia</option>
                    <option value="Bank Mandiri" {{ old('bankName', auth()->user()->bank_name) == 'Bank Mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                    <option value="Bank Rakyat Indonesia" {{ old('bankName', auth()->user()->bank_name) == 'Bank Rakyat Indonesia' ? 'selected' : '' }}>Bank Rakyat Indonesia</option>
                  </select>
                  <p class="error-message" id="bankName-error"></p>
                </div>

                <div class="form-group field">
                  <input type="text" id="accountNumber" name="accountNumber" value="{{ old('accountNumber', auth()->user()->bank_account_number ?? '1234567890') }}" required placeholder=" ">
                  <label for="accountNumber">Nomor Rekening</label>
                  <p class="error-message" id="accountNumber-error"></p>
                </div>
              </div>

              <div class="form-group field">
                <input type="text" id="accountHolder" name="accountHolder" value="{{ old('accountHolder', auth()->user()->bank_account_name ?? auth()->user()->name) }}" required placeholder=" ">
                <label for="accountHolder">Atas Nama</label>
                <p class="error-message" id="accountHolder-error"></p>
              </div>
            </div>

            @csrf
            <div class="form-actions">
              <a href="{{ route('profil.penjual') }}" class="btn btn-secondary">Batal</a>
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </form>
          <div id="formAlertContainer" class="form-alert-container"></div>
        </section>
        </div> <!-- end of page -->
      </main>
    </div> <!-- end of content-wrapper -->
  </div> <!-- end of main-layout -->

  @include('components.admin_penjual.footer')

  <!-- Leaflet JavaScript -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Inisialisasi peta
      const map = L.map('map').setView([-6.200000, 106.816666], 13); // Koordinat Jakarta sebagai default

      // Tambahkan layer peta dari OpenStreetMap
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);

      // Tambahkan marker kosong sebagai default
      let marker = L.marker([-6.200000, 106.816666]).addTo(map);

      // Ambil elemen dari form
      const addressInput = document.getElementById('address');
      const latInput = document.getElementById('lat');
      const lngInput = document.getElementById('lng');
      const searchBtn = document.getElementById('searchAddressBtn');
      const searchSuggestions = document.getElementById('searchSuggestions');

      // Fungsi untuk mencari alamat menggunakan Nominatim
      async function searchAddress(query) {
        if (!query.trim()) {
          searchSuggestions.classList.remove('show');
          return;
        }

        try {
          // Gunakan Nominatim API untuk mencari alamat
          const encodedQuery = encodeURIComponent(query);
          const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodedQuery}&limit=5&addressdetails=1`;

          const response = await fetch(url, {
            headers: {
              'User-Agent': 'EcommerceAkrab/1.0 (contact@ecommerceakrab.com)'
            }
          });

          const results = await response.json();

          // Tampilkan hasil pencarian
          displaySearchResults(results);
        } catch (error) {
          console.error('Error saat mencari alamat:', error);
          searchSuggestions.classList.remove('show');
        }
      }

      // Tampilkan hasil pencarian alamat
      function displaySearchResults(results) {
        searchSuggestions.innerHTML = '';

        if (results.length === 0) {
          const noResult = document.createElement('div');
          noResult.className = 'suggestion-item';
          noResult.textContent = 'Alamat tidak ditemukan';
          searchSuggestions.appendChild(noResult);
          searchSuggestions.classList.add('show');
          return;
        }

        results.forEach(result => {
          const suggestionItem = document.createElement('div');
          suggestionItem.className = 'suggestion-item';
          suggestionItem.innerHTML = `
            <span class="suggestion-title">${result.display_name || result.name || 'Lokasi'}</span>
            <span class="suggestion-address">${result.address?.road || result.address?.village || result.address?.city || result.address?.state || 'Alamat tidak lengkap'}</span>
          `;

          suggestionItem.addEventListener('click', () => {
            // Isi alamat ke form
            addressInput.value = result.display_name;

            // Update peta ke lokasi yang dipilih
            updateMapFromCoordinates(parseFloat(result.lat), parseFloat(result.lon), result.display_name);

            // Sembunyikan hasil pencarian
            searchSuggestions.classList.remove('show');
          });

          searchSuggestions.appendChild(suggestionItem);
        });

        searchSuggestions.classList.add('show');
      }

      // Fungsi untuk memperbarui peta berdasarkan koordinat
      function updateMapFromCoordinates(lat, lng, displayName) {
        // Hapus marker lama
        map.removeLayer(marker);

        // Set peta ke lokasi baru
        map.setView([lat, lng], 15);

        // Tambahkan marker baru
        marker = L.marker([lat, lng]).addTo(map);

        // Tambahkan popup dengan nama lokasi
        marker.bindPopup(displayName).openPopup();

        // Update hidden fields dengan koordinat baru
        latInput.value = lat;
        lngInput.value = lng;
      }

      // Fungsi untuk memperbarui peta berdasarkan alamat
      async function updateMapFromAddress() {
        const address = addressInput.value.trim();
        if (!address) return;

        try {
          // Panggil endpoint geocoding kita
          const response = await fetch('{{ route("seller.geocode.address") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ address: address })
          });

          const result = await response.json();

          if (result.success) {
            // Hapus marker lama
            map.removeLayer(marker);

            // Set peta ke lokasi baru
            map.setView([result.data.lat, result.data.lng], 15);

            // Tambahkan marker baru
            marker = L.marker([result.data.lat, result.data.lng]).addTo(map);

            // Tambahkan popup dengan nama lokasi
            marker.bindPopup(result.data.display_name).openPopup();

            // Update hidden fields dengan koordinat baru
            latInput.value = result.data.lat;
            lngInput.value = result.data.lng;
          } else {
            console.error('Geocoding gagal:', result.message);
          }
        } catch (error) {
          console.error('Error saat geocoding:', error);
        }
      }

      // Event listener untuk pencarian alamat
      let debounceTimer;
      addressInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
          searchAddress(addressInput.value);
        }, 500); // Tunggu 0.5 detik setelah user berhenti mengetik
      });

      // Sembunyikan hasil pencarian saat klik di luar
      document.addEventListener('click', function(event) {
        if (!searchSuggestions.contains(event.target) && event.target !== addressInput) {
          searchSuggestions.classList.remove('show');
        }
      });

      // Panggil fungsi update peta saat alamat berubah (dengan debounce)
      addressInput.addEventListener('blur', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(updateMapFromAddress, 1000); // Tunggu 1 detik setelah user selesai mengedit
      });

      // Jika ada alamat saat halaman dimuat, coba geocode
      if (addressInput.value.trim()) {
        setTimeout(updateMapFromAddress, 500); // Beri sedikit waktu untuk memuat data
      }

      // Tambahkan fitur drag and drop marker untuk akurasi lebih lanjut
      marker.on('dragend', function(event) {
        const newLatLng = event.target.getLatLng();
        map.setView(newLatLng, 15);

        // Update hidden fields dengan koordinat baru dari drag
        latInput.value = newLatLng.lat;
        lngInput.value = newLatLng.lng;

        // Update popup dengan informasi bahwa lokasi telah disesuaikan
        marker.bindPopup(`Lokasi disesuaikan: ${newLatLng.lat.toFixed(6)}, ${newLatLng.lng.toFixed(6)}`).openPopup();
      });

      // Buat marker bisa digeser
      marker.dragging.enable();
    });
  </script>

  <script src="{{ asset('js/penjual/edit_profil_penjual.js') }}"></script>
</body>
</html>