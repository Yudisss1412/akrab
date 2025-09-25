<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Manajemen Produk — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/penjual/dashboard_penjual.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
</head>
<body>
  @include('components.admin_penjual.header')
  
  <div class="main-layout">
    <div class="content-wrapper">
      <!-- Toolbar -->
      <section class="page-toolbar" aria-label="Toolbar halaman">
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
  <main class="admin-page-content" id="main">
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

  <!-- Footer -->
      </div> <!-- end of content-wrapper -->
    </div> <!-- end of main-layout -->
    
    <!-- MODAL Tambah/Edit Produk - dipindahkan setelah main layout -->
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

    @include('components.admin_penjual.footer')

    <script src="{{ asset('js/penjual/dashboard_penjual.js') }}"></script>
  </body>
</html>
