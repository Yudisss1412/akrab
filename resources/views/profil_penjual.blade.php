<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Profil Penjual — AKRAB</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/profil_penjual.css">
</head>
<body>
  <!-- NAVBAR minimal ala AKRAB -->
  <header class="header" role="banner">
    <div class="header-left">
      <a href="{{ route('welcome') }}" aria-label="Ke beranda">
        <img class="logo" src="/src/Logo_UMKM.png" alt="AKRAB">
      </a>
    </div>

    <div class="header-right">
      <a class="profile-ico" href="{{ route('profil.penjual') }}" aria-label="Profil Penjual">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M12 10c2.21 0 4-1.79 4-4s-1.79-4-4-4S8 3.79 8 6s1.79 4 4 4Z" stroke="#006E5C" stroke-width="1.5"/>
          <path d="M20 17.5C20 20 20 22 12 22S4 20 4 17.5C4 15 7.58 13 12 13s8 2 8 4.5Z" stroke="#006E5C" stroke-width="1.5"/>
        </svg>
      </a>
    </div>
  </header>

  <main class="content" role="main">
    <!-- Profil Toko -->
    <section class="card card-profile" aria-labelledby="sellerTitle">
      <div class="seller-hero">
        <!-- kiri: identitas -->
        <div class="seller-identity">
          <div class="avatar" aria-hidden="true">
            <span>K</span>
            <i class="dot online"></i>
          </div>
          <div class="seller-meta">
            <h1 id="sellerTitle" class="seller-name">Kristin Watson</h1>
            <div class="seller-mail">kristin@akrab.gg</div>
            <div class="seller-since">Bergabung sejak <strong>2012</strong></div>
          </div>
        </div>
        <!-- kanan: aksi -->
        <div class="profile-actions">
          <a href="{{ route('edit.profil') }}" id="btnEditProfile" class="btn btn-primary btn-sm">
            Edit Profil
          </a>
        </div>
      </div>

      <dl class="info-list" aria-label="Info Toko">
        <div>
          <dt>Nama Toko</dt>
          <dd>Shoppy.gg</dd>
        </div>
        <div>
          <dt>Email</dt>
          <dd>kristin@akrab.gg</dd>
        </div>
        <div>
          <dt>No. HP</dt>
          <dd>+62 812-3456-7890</dd>
        </div>
        <div>
          <dt>Alamat</dt>
          <dd>Jl. Melati No. 12, Banyuwangi</dd>
        </div>
        <div>
          <dt>Deskripsi Toko</dt>
          <dd>Toko perlengkapan rumah & aksesoris. Buka setiap hari 09.00–21.00.</dd>
        </div>
        <div>
          <dt>Bank Penerima</dt>
          <dd>BCA • 1234567890 • a.n. Kristin Watson</dd>
        </div>
      </dl>
    </section>

    <!-- Riwayat Penjualan -->
    <section class="card reviews-section" aria-labelledby="ordersTitle">
      <div class="orders-head">
        <h2 id="ordersTitle" class="card-title">Riwayat Penjualan</h2>
        <div class="orders-head-hint">Penjualan terbaru dari toko ini</div>
      </div>

      <!-- viewport: item dipasang DI DALAM elemen ini -->
      <div id="ordersViewport" class="orders-viewport" aria-live="polite">
        <!-- contoh item -->
        <article class="order-card" data-order-id="INV-001">
          <div class="order-head">
            <div class="shop-name">Shoppy.gg</div>
            <div class="order-meta">
              <span>Tanggal:
                <time datetime="2025-09-02">02 Sep 2025</time>
              </span>
              • <span>Status: <strong>Selesai</strong></span>
            </div>
          </div>

          <div class="order-items">
            <div class="order-item">
              <img class="item-img" src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=400&auto=format&fit=crop" alt="Mini Projector">
              <div class="item-body">
                <div class="item-row">
                  <div class="item-name">Mini Projector</div>
                  <div class="item-qty">Qty 1</div>
                </div>
                <div class="item-desc scrollable">
                  Proyektor mini untuk kebutuhan presentasi & hiburan.
                </div>
                <div class="item-subtotal">Rp 1.250.000</div>
              </div>
            </div>
          </div>

          <div class="order-actions">
            <div class="total-price" aria-label="Total">Total: Rp 1.250.000</div>
            <div class="row-actions">
              <a href="#" class="btn btn-ghost btn-detail">Detail</a>
              <a href="{{ route('invoice') }}?no=INV-001" class="btn btn-primary">Cetak</a>
            </div>
          </div>
        </article>
      </div>
    </section>

    <!-- Riwayat Ulasan -->
    <section class="card reviews-section" aria-labelledby="reviewsTitle">
      <div class="card-head">
        <h2 id="reviewsTitle" class="card-title">Riwayat Ulasan</h2>
      </div>
      <div class="reviews-viewport">
        <article class="review-card">
          <div class="rev-top" style="padding:.8rem 1rem;">
            <div><strong>Budi</strong> • <time datetime="2025-08-29">29 Agu 2025</time></div>
            <div class="rev-stars" aria-label="3 dari 5 bintang">
              <svg class="star filled" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1l2.6 5.3L18 7l-4 3.9L15 18l-5-2.6L5 18l1-7.1L2 7l5.4-.7L10 1z"/></svg>
              <svg class="star filled" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1l2.6 5.3L18 7l-4 3.9L15 18l-5-2.6L5 18l1-7.1L2 7l5.4-.7L10 1z"/></svg>
              <svg class="star filled" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1l2.6 5.3L18 7l-4 3.9L15 18l-5-2.6L5 18l1-7.1L2 7l5.4-.7L10 1z"/></svg>
              <svg class="star" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1l2.6 5.3L18 7l-4 3.9L15 18l-5-2.6L5 18l1-7.1L2 7l5.4-.7L10 1z"/></svg>
              <svg class="star" viewBox="0 0 20 20" aria-hidden="true"><path d="M10 1l2.6 5.3L18 7l-4 3.9L15 18l-5-2.6L5 18l1-7.1L2 7l5.4-.7L10 1z"/></svg>
            </div>
          </div>
          <div class="rev-body" style="padding:0 1rem 1rem;">
            <div class="rev-text">Produk sesuai deskripsi, pengiriman cepat. Mantap!</div>
            <div class="rev-actions">
              <div class="rev-actions">
                <button class="btn btn-ghost btn-sm btn-reply">Balas</button>
              </div>
            </div>
          </div>
        </article>
      </div>
    </section>
  </main>

  <footer class="ak-footer">
    <div class="ak-footer__inner">
      <span class="ak-footer__icon" aria-hidden="true">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M4.848 5.32966C4.878 5.13115 4.944 4.9567 5.028 4.81233C5.208 4.47547 5.514 4.30102 5.928 4.29501C6.198 4.29501 6.444 4.41531 6.618 4.58976C6.786 4.77624 6.9 5.0349 6.9 5.29356H7.98C7.968 5.01084 7.914 4.75218 7.8 4.51156C7.71 4.28297 7.572 4.07845 7.392 3.91002C6.522 3.10395 4.908 3.21825 4.17 4.13259C3.396 5.13716 3.378 6.89366 4.164 7.89824C4.89 8.79454 6.48 8.92086 7.344 8.12081C7.53 7.97042 7.68 7.78395 7.8 7.56739C7.896 7.35084 7.962 7.12225 7.968 6.87562H6.9C6.9 7.00194 6.858 7.11623 6.804 7.2185C6.75 7.33279 6.678 7.42302 6.6 7.50122C6.402 7.65762 6.168 7.74184 5.916 7.74184C5.7 7.73582 5.52 7.69371 5.382 7.60348C5.22619 7.51613 5.1022 7.38128 5.028 7.2185C4.728 6.67711 4.776 5.92518 4.848 5.32966ZM6 0C2.7 0 0 2.70694 0 6.01541C0.318 13.9979 11.7 13.9919 12 6.01541C12 2.70694 9.3 0 6 0ZM6 10.8277C3.354 10.8277 1.2 8.66821 1.2 6.01541C1.464 -0.36694 10.536 -0.36694 10.8 6.01541C10.8 8.66821 8.646 10.8277 6 10.8277Z" fill="#006E5C"/>
        </svg>
      </span>
      <span class="ak-footer__brand">Akrab Banyuwangi</span>
    </div>
  </footer>

  <script src="/js/profil_penjual.js"></script>
</body>
</html>
