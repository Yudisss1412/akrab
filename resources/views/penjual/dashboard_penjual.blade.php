<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Manajemen Produk — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/penjual/dashboard_penjual.css') }}">
</head>
<body>

  <!-- HEADER ala cust_welcome -->
  <header class="ak-navbar">
    <div class="ak-navbar__inner">
      <img class="ak-logo" src="{{ asset('src/Logo_UMKM.png') }}" alt="AKRAB" />
      <div class="header-right">
        <a class="profile-ico" href="{{ route('profil.penjual') }}" aria-label="Profil Penjual">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 10C14.2091 10 16 8.20914 16 6C16 3.79086 14.2091 2 12 2C9.79086 2 8 3.79086 8 6C8 8.20914 9.79086 10 12 10Z" stroke="#006E5C" stroke-width="1.5"/>
            <path d="M20 17.5C20 19.985 20 22 12 22C4 22 4 19.985 4 17.5C4 15.015 7.582 13 12 13C16.418 13 20 15.015 20 17.5Z" stroke="#006E5C" stroke-width="1.5"/>
          </svg>
        </a>
      </div>
    </div>
  </header>

  <!-- Toolbar -->
  <section class="page-toolbar shell" aria-label="Toolbar halaman">
    <div class="page-toolbar__left">
      <button id="btnAdd" class="btn btn-primary" type="button">+ Tambah Produk</button>
      <label class="search" aria-label="Cari produk">
        <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M21 21l-4.35-4.35m1.1-5.4A7.35 7.35 0 1 1 10.4 3a7.35 7.35 0 0 1 7.35 7.35z"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <input id="searchInput" type="search" placeholder="Cari Produk" />
      </label>
    </div>
    <h1 class="page-title">Manajemen Produk</h1>
  </section>

  <!-- Main -->
  <main class="shell" id="main">
    <div class="table-wrap">
      <table class="table" id="productTable">
        <thead>
          <tr>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Foto</th>
            <th style="width:92px;"></th>
          </tr>
        </thead>
        <tbody id="tbodyProducts"><!-- baris dirender JS --></tbody>
      </table>
    </div>

    <div class="table-footer" id="pager" aria-label="Navigasi halaman tabel">
      <div class="hint" id="pageHint"></div>
      <div class="nav">
        <button id="prevPage" class="link-btn" type="button">&lt; Sebelumnya</button>
        <button id="nextPage" class="link-btn" type="button">Berikutnya &gt;</button>
      </div>
    </div>
  </main>

  <!-- MODAL Tambah/Edit Produk (stok disatukan di sini) -->
  <div id="modal" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-card">
      <div class="modal-head">
        <h2 id="modalTitle">Produk</h2>
        <button class="modal-close" id="btnClose" aria-label="Tutup">✕</button>
      </div>

      <form id="productForm">
        <label for="fName">Nama Produk
          <input type="text" id="fName" required>
        </label>

        <label for="fPrice">Harga
          <input type="number" id="fPrice" min="0" step="100" required>
        </label>

        <label for="fCategory">Kategori
          <select id="fCategory" required>
            <option value="Pakaian">Pakaian</option>
            <option value="Aksesoris">Aksesoris</option>
            <option value="Elektronik">Elektronik</option>
            <option value="Lainnya">Lainnya</option>
          </select>
        </label>

        <label for="fDesc">Deskripsi
          <textarea id="fDesc" rows="3"></textarea>
        </label>

        <div class="grid two">
          <div class="field">
            <label for="fStockCur">Stok Saat Ini</label>
            <input type="number" id="fStockCur" min="0" value="0">
          </div>
          <div class="field">
            <label for="fStockMax">Stok Maksimum</label>
            <input type="number" id="fStockMax" min="0" value="0">
          </div>
        </div>

        <label for="fPhoto">Foto
          <div class="upload">
            <div class="upload-drop" id="photoDrop" title="Klik atau seret gambar ke sini">
              <input type="file" id="fPhoto" accept="image/*" aria-label="Pilih foto">
              <span class="ico" aria-hidden="true">
                <svg width="104" height="104" viewBox="0 0 104 104" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M21.6667 91C19.2833 91 17.2438 90.1521 15.548 88.4563C13.8522 86.7606 13.0029 84.7196 13 82.3333V21.6667C13 19.2833 13.8493 17.2438 15.548 15.548C17.2467 13.8522 19.2862 13.0029 21.6667 13H82.3333C84.7167 13 86.7577 13.8493 88.4563 15.548C90.155 17.2467 91.0029 19.2862 91 21.6667V82.3333C91 84.7167 90.1521 86.7577 88.4563 88.4563C86.7606 90.155 84.7196 91.0029 82.3333 91H21.6667ZM21.6667 82.3333H82.3333V21.6667H21.6667V82.3333ZM26 73.6667H78L61.75 52L48.75 69.3333L39 56.3333L26 73.6667Z" fill="black"/>
                </svg>
              </span>
            </div>
          </div>
        </label>

        <div class="modal-actions">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn" id="btnCancel">Batal</button>
        </div>
      </form>
    </div>
    <div class="modal-backdrop"></div>
  </div>

  <!-- Footer -->
  <footer class="ak-footer">
    <div class="ak-footer__inner">
      <span class="ak-footer__icon" aria-hidden="true">
        <!-- SVG © sesuai desain -->
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M4.848 5.32966C4.878 5.13115 4.944 4.9567 5.028 4.81233C5.208 4.47547 5.514 4.30102 5.928 4.29501C6.198 4.29501 6.444 4.41531 6.618 4.58976C6.786 4.77624 6.9 5.0349 6.9 5.29356H7.98C7.968 5.01084 7.914 4.75218 7.8 4.51156C7.71 4.28297 7.572 4.07845 7.392 3.91002C6.522 3.10395 4.908 3.21825 4.17 4.13259C3.396 5.13716 3.378 6.89366 4.164 7.89824C4.89 8.79454 6.48 8.92086 7.344 8.12081C7.53 7.97042 7.68 7.78395 7.8 7.56739C7.896 7.35084 7.962 7.12225 7.968 6.87562H6.9C6.9 7.00194 6.858 7.11623 6.804 7.2185C6.75 7.33279 6.678 7.42302 6.6 7.50122C6.402 7.65762 6.168 7.74184 5.916 7.74184C5.7 7.73582 5.52 7.69371 5.382 7.60348C5.22619 7.51613 5.1022 7.38128 5.028 7.2185C4.728 6.67711 4.776 5.92518 4.848 5.32966ZM6 0C2.7 0 0 2.70694 0 6.01541C0.318 13.9979 11.7 13.9919 12 6.01541C12 2.70694 9.3 0 6 0ZM6 10.8277C3.354 10.8277 1.2 8.66821 1.2 6.01541C1.464 -0.36694 10.536 -0.36694 10.8 6.01541C10.8 8.66821 8.646 10.8277 6 10.8277Z" fill="#006E5C"/>
        </svg>
      </span>
      <span class="ak-footer__brand">Akrab Banyuwangi</span>
    </div>
  </footer>

  <script src="{{ asset('js/penjual/dashboard_penjual.js') }}"></script>
</body>
</html>
