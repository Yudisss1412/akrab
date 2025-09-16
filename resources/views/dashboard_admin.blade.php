<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/dashboard_admin.css') }}">
</head>
<body>
  <!-- NAVBAR -->
  <header class="ak-navbar" role="banner">
    <div class="ak-navbar__inner">
      <div class="cw-left">
        <a href="{{ route('welcome') }}" aria-label="Ke beranda">
          <img class="ak-logo" src="/src/Logo_UMKM.png" alt="AKRAB">
        </a>
        <h1 class="cw-title">Admin Dashboard</h1>
      </div>
      <!-- (profil icon dihapus) -->
    </div>
  </header>

  <main class="shell">
    <!-- Toolbar -->
    <div class="page-toolbar">
      <div class="page-toolbar__left">
        <button id="btnAddCategory" class="btn btn-primary">+ Tambah Kategori</button>
        <div class="search" role="search">
          <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79L20 20.49 21.49 19 15.5 14zM9.5 14C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="#333"/>
          </svg>
          <input id="searchCategory" type="text" placeholder="Cari Kategori" aria-label="Cari kategori">
        </div>
      </div>
      <p class="page-title m0">Kategori Produk</p>
    </div>

    <!-- LIST KATEGORI -->
    <section class="list-wrap" aria-label="Daftar Kategori">
      <div id="categoryList" class="category-list">
        <!-- Baris kategori dirender via JS -->
      </div>

      <!-- Footer / Pagination -->
      <div class="table-footer">
        <span id="pageHint">—</span>
        <div class="nav">
          <button id="prevPage" class="btn-link" type="button">‹ Sebelumnya</button>
          <button id="nextPage" class="btn-link" type="button">Berikutnya ›</button>
        </div>
      </div>
    </section>
  </main>

  <footer class="ak-footer" role="contentinfo">
    <div class="ak-footer__inner">
      <span class="ak-footer__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24"><path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 4v13l10 5 10-5V4l-10 5Z"/></svg>
      </span>
      <span class="ak-footer__brand">Akrab Banyuwangi</span>
    </div>
  </footer>

  <script src="{{ asset('js/dashboard_admin.js') }}"></script>
</body>
</html>
