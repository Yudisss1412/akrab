<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Penilaian Saya — Riwayat Ulasan</title>
  <link rel="stylesheet" href="css/halaman_ulasan.css" />
  <link rel="icon" href="data:," />
</head>
<body>
  <!-- App Header -->
  <header class="appbar">
    <button class="icon-btn" id="btnBack" aria-label="Kembali">
      <!-- back arrow -->
      <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M15.41 7.41 14 6l-6 6 6 6 1.41-1.41L10.83 12z" fill="currentColor"/></svg>
    </button>
    <h1 class="appbar__title">Penilaian Saya</h1>
    <div class="appbar__right"></div>
  </header>

  <!-- Stat bar -->
  <section class="stats">
    <div class="stat">
      <div class="stat__value" id="statUlasan">0</div>
      <div class="stat__label">Ulasan</div>
    </div>
    <div class="stat">
      <div class="stat__value" id="statTerbantu">0</div>
      <div class="stat__label">Terbantu</div>
    </div>
    <div class="stat">
      <div class="stat__value" id="statDilihat">0</div>
      <div class="stat__label">Dilihat</div>
    </div>
  </section>

  <!-- Tabs -->
  <nav class="tabs" role="tablist" aria-label="Kategori penilaian">
    <button role="tab" class="tab" aria-selected="false" data-tab="belum">Belum Dinilai</button>
    <button role="tab" class="tab is-active" aria-selected="true" data-tab="dinilai">Penilaian Saya</button>
    <span class="tab__indicator"></span>
  </nav>

  <!-- Toolbar -->
  <section class="toolbar">
    <div class="search">
      <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M15.5 14h-.79l-.28-.27a6.471 6.471 0 001.57-4.23 6.5 6.5 0 10-6.5 6.5 6.471 6.471 0 004.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor"/></svg>
      <input id="searchInput" type="search" placeholder="Cari produk/ulasan…" />
    </div>

    <div class="filters">
      <label class="select">
        <select id="sortSelect">
          <option value="latest">Terbaru</option>
          <option value="oldest">Terlama</option>
          <option value="ratingHigh">Rating tertinggi</option>
          <option value="ratingLow">Rating terendah</option>
        </select>
        <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M7 10l5 5 5-5z" fill="currentColor"/></svg>
      </label>
    </div>
  </section>

  <!-- Content Lists -->
  <main class="content">

    <!-- List: Belum Dinilai -->
    <section id="listBelum" class="review-list" hidden aria-label="Belum Dinilai">
      <!-- JS akan render item yang belum diberi ulasan -->
    </section>

    <!-- List: Sudah Dinilai -->
    <section id="listDinilai" class="review-list" aria-label="Penilaian Saya">
      <!-- JS akan render ulasan di sini -->
    </section>

    <!-- Empty state -->
    <template id="tplEmpty">
    <div class="empty">
        <img alt="" 
        src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='140' height='100' viewBox='0 0 140 100'><rect width='140' height='100' rx='12' fill='%23f2f3f5'/><path d='M35 65h70M45 50h50M50 35h40' stroke='%23c4c7cf' stroke-width='6' stroke-linecap='round'/></svg>">
        <p>Belum ada data untuk kategori ini.</p>
    </div>
    </template>

    <!-- Template card -->
    <template id="tplCard">
      <article class="card" data-id="">
        <header class="card__head">
          <div class="user">
            <div class="avatar" aria-hidden="true"></div>
            <div class="user__col">
              <div class="user__name">Nama Pengguna</div>
              <div class="stars" aria-label="Rating">
                <!-- JS inject bintang -->
              </div>
            </div>
          </div>
          <time class="card__time" datetime=""></time>
        </header>

        <div class="card__body">
          <ul class="kv">
            <!-- JS inject baris Fungsi/Fitur/Desain kalau ada -->
          </ul>

          <a class="product" href="#" target="_blank" rel="noopener">
            <div class="product__thumb" aria-hidden="true"></div>
            <div class="product__meta">
              <div class="product__title"></div>
              <div class="product__variant"></div>
            </div>
          </a>
        </div>

        <footer class="card__foot">
          <button class="btn ghost btn-help">
            <!-- thumbs up -->
            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M1 21h4V9H1v12zM23 10c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14 1 7.59 7.41C7.22 7.78 7 8.3 7 8.86V19c0 1.1.9 2 2 2h7c.82 0 1.54-.5 1.85-1.22l3.02-6.76c.08-.18.13-.38.13-.59v-2.43z" fill="currentColor"/></svg>
            Membantu
          </button>

          <button class="btn primary btn-update">
            Perbarui
          </button>
        </footer>
      </article>
    </template>
  </main>

  <script src="js/halaman_ulasan.js"></script>
</body>
</html>
