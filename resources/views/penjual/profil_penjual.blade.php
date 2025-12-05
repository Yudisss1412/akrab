<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Profil Penjual — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/penjual/profil_penjual.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>
  @include('components.admin_penjual.header')

  <div class="main-layout">
    <div class="content-wrapper">
      <main class="content admin-page-content" role="main">
    <!-- Profil Toko -->
    <section class="card card-profile" aria-labelledby="sellerTitle">
      <div class="seller-hero">
        <!-- kiri: identitas -->
        <div class="seller-identity">
          <div class="avatar" aria-hidden="true">
            <span>{{ strtoupper(substr($seller->store_name ?? $user->name, 0, 1)) }}</span>
            <i class="dot online"></i>
          </div>
          <div class="seller-meta">
            <h1 id="sellerTitle" class="seller-name">{{ $seller->store_name ?? $user->name }}</h1>
            <div class="seller-mail">{{ $user->email }}</div>
            <div class="seller-since">Bergabung sejak <strong>{{ $user->created_at->year }}</strong></div>
          </div>
        </div>
        <!-- kanan: aksi -->
        <div class="profile-actions">
          <a href="{{ route('edit.profil.penjual') }}" id="btnEditProfile" class="btn btn-primary btn-sm">
            Edit Profil
          </a>
        </div>
      </div>

      <dl class="info-list" aria-label="Info Toko">
        <div>
          <dt>Nama Toko</dt>
          <dd>{{ $seller->store_name ?? $user->name }}</dd>
        </div>
        <div>
          <dt>Email</dt>
          <dd>{{ $user->email }}</dd>
        </div>
        <div>
          <dt>No. HP</dt>
          <dd>{{ $user->phone ?? '+62 812-3456-7890' }}</dd>
        </div>
        <div>
          <dt>Alamat</dt>
          <dd>{{ $user->address ?? 'Alamat belum diisi' }}</dd>
        </div>
        <div>
          <dt>Lokasi</dt>
          <dd>
            <div id="seller-map" style="height: 300px; width: 100%; border: 1px solid var(--ak-border); border-radius: 8px; margin-top: 10px;"></div>
          </dd>
        </div>
        <div>
          <dt>Deskripsi Toko</dt>
          <dd>{{ $user->shop_description ?? 'Toko belum memiliki deskripsi' }}</dd>
        </div>
        <div>
          <dt>Bank Penerima</dt>
          <dd>{{ $user->bank_name ?? 'Nama Bank' }} • {{ $user->bank_account_number ?? 'Nomor Rekening' }} • a.n. {{ $user->bank_account_name ?? $user->name }}</dd>
        </div>
      </dl>
    </section>

    <!-- Riwayat Penjualan -->
    <section class="card orders-section" aria-labelledby="ordersTitle">
      <div class="orders-head">
        <h2 id="ordersTitle" class="card-title">Riwayat Penjualan</h2>
        <div class="orders-head-hint">Penjualan terbaru dari toko ini</div>
      </div>
      
      <!-- Tombol untuk melihat semua riwayat penjualan -->
      <div class="view-all-btn" style="padding: 1rem; text-align: center; border-top: 1px solid var(--ak-border);">
        <a href="{{ route('penjual.riwayat.penjualan') }}" class="btn btn-primary" aria-label="Lihat dan kelola semua riwayat penjualan">
          Lihat & Kelola Semua Riwayat Penjualan →
        </a>
      </div>

      <!-- viewport: item dipasang DI DALAM elemen ini -->
      <div id="ordersViewport" class="orders-viewport" aria-live="polite">
        <!-- Data pesanan akan dimuat secara dinamis oleh JavaScript -->
      </div>
    </section>

    <!-- Riwayat Ulasan -->
    <section class="card reviews-section" aria-labelledby="reviewsTitle">
      <div class="card-head">
        <h2 id="reviewsTitle" class="card-title">Riwayat Ulasan</h2>
      </div>
      <div class="reviews-viewport">
        <!-- Tempat untuk ulasan dinamis akan dimuat oleh JavaScript -->
        <!-- Tombol untuk melihat semua ulasan -->
        <div class="view-all-btn" style="padding: 1rem; text-align: center; border-top: 1px solid var(--ak-border);">
          <a href="{{ route('seller.reviews.index') }}" class="btn btn-primary" aria-label="Lihat dan kelola semua ulasan">
            Lihat & Kelola Semua Ulasan →
          </a>
        </div>
      </div>
    </section>
  </main>
      </div> <!-- end of content-wrapper -->
    </div> <!-- end of main-layout -->

    @include('components.admin_penjual.footer')

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Ambil data dari server
        const address = `{{ $user->address ?? '' }}`;
        const lat = {{ $user->lat ? $user->lat : 'null' }};
        const lng = {{ $user->lng ? $user->lng : 'null' }};

        // Jika ada koordinat yang disimpan di database, gunakan langsung
        if (lat && lng) {
          const map = L.map('seller-map').setView([lat, lng], 15);

          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
          }).addTo(map);

          L.marker([lat, lng]).addTo(map)
            .bindPopup(address || 'Lokasi Penjual').openPopup();
        }
        // Jika tidak ada koordinat disimpan tapi ada alamat, lakukan geocoding
        else if (address) {
          fetch('{{ route("seller.geocode.address") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ address: address })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              const map = L.map('seller-map').setView([data.data.lat, data.data.lng], 15);

              L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
              }).addTo(map);

              L.marker([data.data.lat, data.data.lng]).addTo(map)
                .bindPopup(data.data.display_name).openPopup();
            } else {
              // Jika geocoding gagal, tampilkan peta default
              const map = L.map('seller-map').setView([-6.200000, 106.816666], 13);

              L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
              }).addTo(map);

              L.marker([-6.200000, 106.816666]).addTo(map)
                .bindPopup('Lokasi Penjual').openPopup();
            }
          })
          .catch(error => {
            console.error('Error saat mengambil koordinat:', error);

            // Jika terjadi error, tampilkan peta default
            const map = L.map('seller-map').setView([-6.200000, 106.816666], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
              attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            L.marker([-6.200000, 106.816666]).addTo(map)
              .bindPopup('Lokasi Penjual').openPopup();
          });
        }
        // Jika tidak ada alamat, tampilkan peta default
        else {
          const map = L.map('seller-map').setView([-6.200000, 106.816666], 13);

          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
          }).addTo(map);

          L.marker([-6.200000, 106.816666]).addTo(map)
            .bindPopup('Lokasi Penjual').openPopup();
        }
      });
    </script>

    <script src="{{ asset('js/penjual/profil_penjual.js') }}"></script>
  </body>
</html>
