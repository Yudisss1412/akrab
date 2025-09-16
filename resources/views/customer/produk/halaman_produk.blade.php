<!DOCTYPE html>

<html lang="id">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<meta content="{{ csrf_token() }}" name="csrf-token"/>
<title>Daftar Produk — AKRAB</title>

  {{-- Font (sama seperti cust_welcome) --}}
  <link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&amp;display=swap" rel="stylesheet"/>

  {{-- CSS khusus halaman produk (berisi style navbar+footer yang match cust_welcome) --}}
  <link href="{{ asset('css/customer/produk/halaman_produk.css') }}?v=3" rel="stylesheet"/>
<style>
/* Scoped header/footer normalizer (drop-in) */
.header img.logo, header .logo, .brand img, .navbar img.logo { max-height: 40px; height:auto; width:auto; }
footer { margin-top: 40px; }
</style><link href="/css/cust_welcome.css" rel="stylesheet"/></head>
<body><header class="header">
<div class="header-left">
<a href="{{ route('cust.welcome') }}">
<img alt="Logo UMKM" class="logo" src="{{ asset('src/Logo_UMKM.png') }}"/>
</a>
</div>
<div class="header-center">
<div class="search-bar">
<svg class="search-icon" viewbox="0 0 24 24"><path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" fill="currentColor"></path></svg>
<input id="navbar-search" placeholder="Cari Di Akrab..." type="text"/>
</div>
</div>
<div class="header-right">
<a aria-label="Profil" class="profile-ico" href="{{ route('profil.pembeli') }}">
<svg fill="none" height="24" viewbox="0 0 24 24" width="24">
<path d="M12 10C14.2091 10 16 8.20914 16 6C16 3.79086 14.2091 2 12 2C9.79086 2 8 3.79086 8 6C8 8.20914 9.79086 10 12 10Z" stroke="#006E5C" stroke-width="1.5"></path>
<path d="M20 17.5C20 19.985 20 22 12 22C4 22 4 19.985 4 17.5C4 15.015 7.582 13 12 13C16.418 13 20 15.015 20 17.5Z" stroke="#006E5C" stroke-width="1.5"></path>
</svg>
</a>
<a aria-label="Keranjang" class="keranjang" href="{{ route('keranjang') }}">
<svg fill="none" height="24" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
<path d="M17 18C17.5304 18 18.0391 18.2107 18.4142 18.5858C18.7893 18.9609 19 19.4696 19 20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22C16.4696 22 15.9609 21.7893 15.5858 21.4142C15.2107 21.0391 15 20.5304 15 20C15 18.89 15.89 18 17 18ZM1 2H4.27L5.21 4H20C20.2652 4 20.5196 4.10536 20.7071 4.29289C20.8946 4.48043 21 4.73478 21 5C21 5.17 20.95 5.34 20.88 5.5L17.3 11.97C16.96 12.58 16.3 13 15.55 13H8.1L7.2 14.63L7.17 14.75C7.17 14.8163 7.19634 14.8799 7.24322 14.9268C7.29011 14.9737 7.3537 15 7.42 15H19V17H7C6.46957 17 5.96086 16.7893 5.58579 16.4142C5.21071 16.0391 5 15.5304 5 15C5 14.65 5.09 14.32 5.24 14.04L6.6 11.59L3 4H1V2ZM7 18C7.53043 18 8.03914 18.2107 8.41421 18.5858C8.78929 18.9609 9 19.4696 9 20C9 20.5304 8.78929 21.0391 8.41421 21.4142C8.03914 21.7893 7.53043 22 7 22C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20C5 18.89 5.89 18 7 18ZM16 11L18.78 6H6.14L8.5 11H16Z" fill="#006E5C"></path>
</svg>
</a>
</div>
</header>

  {{-- NAVBAR — copy gaya &amp; struktur cust_welcome --}}
        

  {{-- KONTEN HALAMAN PRODUK --}}
  <main class="produk-page" role="main">
<div class="produk-header-row">
<h1 class="produk-title" id="produk-heading">Daftar Produk</h1>
<div aria-label="Filter produk" class="produk-filter-right" role="region">
<label for="filter-kategori">Kategori</label>
<select aria-controls="produk-grid" id="filter-kategori" name="kategori">
<option value="all">Semua</option>
<option value="Minuman">Minuman</option>
<option value="Camilan">Camilan</option>
<option value="Kerajinan">Kerajinan</option>
<option value="Kopi">Kopi</option>
<option value="Sabun">Sabun</option>
<option value="Kue">Kue</option>
<option value="Lainnya">Lainnya</option>
</select>
</div>
</div>
<div aria-busy="false" aria-live="polite" class="produk-grid" id="produk-grid"></div>
<nav aria-label="Navigasi halaman produk" class="produk-pagination" id="produk-pagination"></nav>
<section aria-labelledby="rekom-title" class="rekomendasi-section">
<div class="rekomendasi-header">
<h3 id="rekom-title">Rekomendasi Produk Terkait / Lainnya</h3>
<span class="rekomendasi-sub">Kamu mungkin juga suka</span>
</div>
<div aria-busy="false" aria-live="polite" class="rekom-grid" id="rekom-grid"></div>
<nav aria-label="Navigasi rekomendasi" class="produk-pagination" id="rekom-pagination"></nav>
</section>
<noscript><p>Aktifkan JavaScript untuk menampilkan daftar produk.</p></noscript>
</main>

  {{-- FOOTER — copy gaya cust_welcome (3 kolom) --}}


  {{-- JS khusus halaman ini --}}
  <script>
    // Pastikan script produk pakai input navbar (ID sama seperti di cust_welcome)
    window.__AKRAB_SEARCH_INPUT_ID__ = 'navbar-search';
  </script>
<script defer="" src="{{ asset('js/customer/produk/halaman_produk.js') }}?v=3"></script>
<footer class="footer">
<div class="footer-copyright">
<span>©</span> Akrab Banyuwangi
            </div>
<div class="social-icons-container">
<div class="social-icons-background">
<a aria-label="Instagram" class="social-icon" href="https://www.instagram.com/akrab_banyuwangi"><svg height="24" viewbox="0 0 24 24" width="24"><path d="M7.8 2H16.2C19.4 2 22 4.6 22 7.8V16.2C22 17.7383 21.3889 19.2135 20.3012 20.3012C19.2135 21.3889 17.7383 22 16.2 22H7.8C4.6 22 2 19.4 2 16.2V7.8C2 6.26174 2.61107 4.78649 3.69878 3.69878C4.78649 2.61107 6.26174 2 7.8 2ZM7.6 4C6.64522 4 5.72955 4.37928 5.05442 5.05442C4.37928 5.72955 4 6.64522 4 7.6V16.4C4 18.39 5.61 20 7.6 20H16.4C17.3548 20 18.2705 19.6207 18.9456 18.9456C19.6207 18.2705 20 17.3548 20 16.4V7.6C20 5.61 18.39 4 16.4 4H7.6ZM17.25 5.5C17.5815 5.5 17.8995 5.6317 18.1339 5.86612C18.3683 6.10054 18.5 6.41848 18.5 6.75C18.5 7.08152 18.3683 7.39946 18.1339 7.63388C17.8995 7.8683 17.5815 8 17.25 8C16.9185 8 16.6005 7.8683 16.3661 7.63388C16.1317 7.39946 16 7.08152 16 6.75C16 6.41848 16.1317 6.10054 16.3661 5.86612C16.6005 5.6317 16.9185 5.5 17.25 5.5ZM12 7C13.3261 7 14.5979 7.52678 15.5355 8.46447C16.4732 9.40215 17 10.6739 17 12C17 13.3261 16.4732 14.5979 15.5355 15.5355C14.5979 16.4732 13.3261 17 12 17C10.6739 17 9.40215 16.4732 8.46447 15.5355C7.52678 14.5979 7 13.3261 7 12C7 10.6739 7.52678 9.40215 8.46447 8.46447C9.40215 7.52678 10.6739 7 12 7ZM12 9C11.2044 9 10.4413 9.31607 9.87868 9.87868C9.31607 10.4413 9 11.2044 9 12C9 12.7956 9.31607 13.5587 9.87868 14.1213C10.4413 14.6839 11.2044 15 12 15C12.7956 15 13.5587 14.6839 14.1213 14.1213C14.6839 13.5587 15 12.7956 15 12C15 11.2044 14.6839 10.4413 14.1213 9.87868C13.5587 9.31607 12.7956 9 12 9Z" fill="#F5F5F5"></path></svg></a>
<a aria-label="Facebook" class="social-icon" href="https://web.facebook.com/akrabbanyuwangi/?_rdc=1&amp;_rdr#"><svg height="24" viewbox="0 0 24 24" width="24"><path d="M22 12C22 6.48 17.52 2 12 2C6.48 2 2 6.48 2 12C2 16.84 5.44 20.87 10 21.8V15H8V12H10V9.5C10 7.57 11.57 6 13.5 6H16V9H14C13.45 9 13 9.45 13 10V12H16V15H13V21.95C18.05 21.45 22 17.19 22 12Z" fill="#F5F5F5"></path></svg></a>
<a aria-label="WhatsApp" class="social-icon" href="wa.me/6285171558272"><svg height="24" viewbox="0 0 24 24" width="24"><path d="M19.05 4.91005C18.1331 3.98416 17.041 3.25002 15.8375 2.75042C14.634 2.25081 13.3431 1.99574 12.04 2.00005C6.57999 2.00005 2.12999 6.45005 2.12999 11.9101C2.12999 13.6601 2.58999 15.3601 3.44999 16.8601L2.04999 22.0001L7.29999 20.6201C8.74999 21.4101 10.38 21.8301 12.04 21.8301C17.5 21.8301 21.95 17.3801 21.95 11.9201C21.95 9.27005 20.92 6.78005 19.05 4.91005ZM12.04 20.1501C10.56 20.1501 9.10999 19.7501 7.83999 19.0001L7.53999 18.8201L4.41999 19.6401L5.24999 16.6001L5.04999 16.2901C4.22754 14.9771 3.79091 13.4593 3.78999 11.9101C3.78999 7.37005 7.48999 3.67005 12.03 3.67005C14.23 3.67005 16.3 4.53005 17.85 6.09005C18.6176 6.85392 19.2259 7.7626 19.6396 8.76338C20.0533 9.76417 20.2642 10.8371 20.26 11.9201C20.28 16.4601 16.58 20.1501 12.04 20.1501ZM16.56 13.9901C16.31 13.8701 15.09 13.2701 14.87 13.1801C14.64 13.1001 14.48 13.0601 14.31 13.3001C14.14 13.5501 13.67 14.1101 13.53 14.2701C13.39 14.4401 13.24 14.4601 12.99 14.3301C12.74 14.2101 11.94 13.9401 11 13.1001C10.26 12.4401 9.76999 11.6301 9.61999 11.3801C9.47999 11.1301 9.59999 11.0001 9.72999 10.8701C9.83999 10.7601 9.97999 10.5801 10.1 10.4401C10.22 10.3001 10.27 10.1901 10.35 10.0301C10.43 9.86005 10.39 9.72005 10.33 9.60005C10.27 9.48005 9.76999 8.26005 9.56999 7.76005C9.36999 7.28005 9.15999 7.34005 9.00999 7.33005H8.52999C8.35999 7.33005 8.09999 7.39005 7.86999 7.64005C7.64999 7.89005 7.00999 8.49005 7.00999 9.71005C7.00999 10.9301 7.89999 12.1101 8.01999 12.2701C8.13999 12.4401 9.76999 14.9401 12.25 16.0101C12.84 16.2701 13.3 16.4201 13.66 16.5301C14.25 16.7201 14.79 16.6901 15.22 16.6301C15.7 16.5601 16.69 16.0301 16.89 15.4501C17.1 14.8701 17.1 14.3801 17.03 14.2701C16.96 14.1601 16.81 14.1101 16.56 13.9901Z" fill="#F5F5F5"></path></svg></a>
<a aria-label="Email" class="social-icon" href="#"><svg height="24" viewbox="0 0 24 24" width="24"><path d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6ZM20 6L12 11L4 6H20ZM20 18H4V8L12 13L20 8V18Z" fill="#F5F5F5"></path></svg></a>
</div>
</div>
<div class="footer-privacy">
<a href="#">Kebijakan Privasi</a>
</div>
</footer></body>
</html>