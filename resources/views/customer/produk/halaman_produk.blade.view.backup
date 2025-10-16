@extends('layouts.app')

@section('title', 'Daftar Produk — AKRAB')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link href="{{ asset('css/customer/produk/halaman_produk.css') }}?v=3" rel="stylesheet"/>
@endpush

@section('content')
  {{-- KONTEN HALAMAN PRODUK --}}
  <main class=\"produk-page\" role=\"main\">
    <div class=\"produk-header-row\">
      <h1 class=\"produk-title\" id=\"produk-heading\">Daftar Produk</h1>
      <div aria-label=\"Filter produk\" class=\"produk-filter-right\" role=\"region\">
        <label for=\"filter-kategori\">Kategori</label>
        <select aria-controls=\"produk-grid\" id=\"filter-kategori\" name=\"kategori\">
          <option value=\"all\">Semua</option>
          <option value=\"Minuman\">Minuman</option>
          <option value=\"Camilan\">Camilan</option>
          <option value=\"Kerajinan\">Kerajinan</option>
        </select>
      </div>
    </div>

    {{-- GRID PRODUK --}}
    <div class=\"produk-grid\" id=\"produk-grid\" role=\"feed\">
      {{-- PRODUK 1 --}}
      <div class=\"produk-item\" role=\"article\" aria-labelledby=\"produk-1-title\">
        <div class=\"produk-img\">
          <img alt=\"Kecap ABC Botol Kecil\" src=\"{{ asset('src/kecap_abc.png') }}\" />
        </div>
        <div class=\"produk-info\">
          <h2 id=\"produk-1-title\" class=\"produk-name\">Kecap ABC Botol Kecil</h2>
          <p class=\"produk-toko\">Toko Surya</p>
          <div class=\"produk-harga\">Rp 12.000</div>
          <div class=\"produk-rating\">
            <span aria-label=\"4 dari 5 bintang\" class=\"rating-stars\">★★★★☆</span>
            <span class=\"rating-angka\">4.0</span>
          </div>
          <div class=\"produk-aksi\">
            <button class=\"btn-produk btn-primary\">Beli</button>
            <button class=\"btn-produk btn-outline\">+ Keranjang</button>
          </div>
        </div>
      </div>

      {{-- PRODUK 2 --}}
      <div class=\"produk-item\" role=\"article\" aria-labelledby=\"produk-2-title\">
        <div class=\"produk-img\">
          <img alt=\"Sambal ABC Botol Kecil\" src=\"{{ asset('src/sambal_abc.png') }}\" />
        </div>
        <div class=\"produk-info\">
          <h2 id=\"produk-2-title\" class=\"produk-name\">Sambal ABC Botol Kecil</h2>
          <p class=\"produk-toko\">Toko Surya</p>
          <div class=\"produk-harga\">Rp 10.000</div>
          <div class=\"produk-rating\">
            <span aria-label=\"4.5 dari 5 bintang\" class=\"rating-stars\">★★★★★</span>
            <span class=\"rating-angka\">4.5</span>
          </div>
          <div class=\"produk-aksi\">
            <button class=\"btn-produk btn-primary\">Beli</button>
            <button class=\"btn-produk btn-outline\">+ Keranjang</button>
          </div>
        </div>
      </div>

      {{-- PRODUK 3 --}}
      <div class=\"produk-item\" role=\"article\" aria-labelledby=\"produk-3-title\">
        <div class=\"produk-img\">
          <img alt=\"Santan Kara Kemasan\" src=\"{{ asset('src/santan_kara.png') }}\" />
        </div>
        <div class=\"produk-info\">
          <h2 id=\"produk-3-title\" class=\"produk-name\">Santan Kara Kemasan</h2>
          <p class=\"produk-toko\">Toko Surya</p>
          <div class=\"produk-harga\">Rp 15.000</div>
          <div class=\"produk-rating\">
            <span aria-label=\"3.5 dari 5 bintang\" class=\"rating-stars\">★★★☆☆</span>
            <span class=\"rating-angka\">3.5</span>
          </div>
          <div class=\"produk-aksi\">
            <button class=\"btn-produk btn-primary\">Beli</button>
            <button class=\"btn-produk btn-outline\">+ Keranjang</button>
          </div>
        </div>
      </div>

      {{-- PRODUK 4 --}}
      <div class=\"produk-item\" role=\"article\" aria-labelledby=\"produk-4-title\">
        <div class=\"produk-img\">
          <img alt=\"Tepung Terigu Segitiga\" src=\"{{ asset('src/tepung_terigu.png') }}\" />
        </div>
        <div class=\"produk-info\">
          <h2 id=\"produk-4-title\" class=\"produk-name\">Tepung Terigu Segitiga</h2>
          <p class=\"produk-toko\">Toko Surya</p>
          <div class=\"produk-harga\">Rp 18.000</div>
          <div class=\"produk-rating\">
            <span aria-label=\"5 dari 5 bintang\" class=\"rating-stars\">★★★★★</span>
            <span class=\"rating-angka\">5.0</span>
          </div>
          <div class=\"produk-aksi\">
            <button class=\"btn-produk btn-primary\">Beli</button>
            <button class=\"btn-produk btn-outline\">+ Keranjang</button>
          </div>
        </div>
      </div>
    </div>

    {{-- REKOMENDASI PRODUK --}}
    <div class=\"produk-rekomendasi produk-section\" role=\"region\" aria-labelledby=\"rekomendasi-heading\">
      <div class=\"rekomendasi-header\">
        <h3 id=\"rekomendasi-heading\" class=\"produk-subtitle\">Rekomendasi Produk</h3>
        <a class=\"produk-see-all\" href=\"#\">Lihat Semua</a>
      </div>
      <div class=\"produk-grid\" role=\"feed\">
        {{-- PRODUK REKOMENDASI 1 --}}
        <div class=\"produk-item\" role=\"article\" aria-labelledby=\"rekomendasi-1-title\">
          <div class=\"produk-img\">
            <img alt=\"Gula Jawa Kemasan\" src=\"{{ asset('src/gula_jawa.png') }}\" />
          </div>
          <div class=\"produk-info\">
            <h2 id=\"rekomendasi-1-title\" class=\"produk-name\">Gula Jawa Kemasan</h2>
            <p class=\"produk-toko\">Toko Surya</p>
            <div class=\"produk-harga\">Rp 16.000</div>
            <div class=\"produk-rating\">
              <span aria-label=\"4 dari 5 bintang\" class=\"rating-stars\">★★★★☆</span>
              <span class=\"rating-angka\">4.0</span>
            </div>
            <div class=\"produk-aksi\">
              <button class=\"btn-produk btn-primary\">Beli</button>
              <button class=\"btn-produk btn-outline\">+ Keranjang</button>
            </div>
          </div>
        </div>

        {{-- PRODUK REKOMENDASI 2 --}}
        <div class=\"produk-item\" role=\"article\" aria-labelledby=\"rekomendasi-2-title\">
          <div class=\"produk-img\">
            <img alt=\"Minyak Goreng Bimoli\" src=\"{{ asset('src/minyak_goreng.png') }}\" />
          </div>
          <div class=\"produk-info\">
            <h2 id=\"rekomendasi-2-title\" class=\"produk-name\">Minyak Goreng Bimoli</h2>
            <p class=\"produk-toko\">Toko Surya</p>
            <div class=\"produk-harga\">Rp 25.000</div>
            <div class=\"produk-rating\">
              <span aria-label=\"4.5 dari 5 bintang\" class=\"rating-stars\">★★★★★</span>
              <span class=\"rating-angka\">4.5</span>
            </div>
            <div class=\"produk-aksi\">
              <button class=\"btn-produk btn-primary\">Beli</button>
              <button class=\"btn-produk btn-outline\">+ Keranjang</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- POPULER --}}
    <div class=\"produk-populer produk-section\" role=\"region\" aria-labelledby=\"populer-heading\">
      <div class=\"produk-populer-header\">
        <h3 id=\"populer-heading\" class=\"produk-subtitle\">Produk Paling Populer</h3>
        <a class=\"produk-see-all\" href=\"#\">Lihat Semua</a>
      </div>
      <div class=\"produk-grid\" role=\"feed\">
        {{-- PRODUK POPULER 1 --}}
        <div class=\"produk-item\" role=\"article\" aria-labelledby=\"populer-1-title\">
          <div class=\"produk-img\">
            <img alt=\"Kecap Manis ABC\" src=\"{{ asset('src/kecap_manis.png') }}\" />
          </div>
          <div class=\"produk-info\">
            <h2 id=\"populer-1-title\" class=\"produk-name\">Kecap Manis ABC</h2>
            <p class=\"produk-toko\">Toko Surya</p>
            <div class=\"produk-harga\">Rp 14.000</div>
            <div class=\"produk-rating\">
              <span aria-label=\"4.5 dari 5 bintang\" class=\"rating-stars\">★★★★★</span>
              <span class=\"rating-angka\">4.5</span>
            </div>
            <div class=\"produk-aksi\">
              <button class=\"btn-produk btn-primary\">Beli</button>
              <button class=\"btn-produk btn-outline\">+ Keranjang</button>
            </div>
          </div>
        </div>

        {{-- PRODUK POPULER 2 --}}
        <div class=\"produk-item\" role=\"article\" aria-labelledby=\"populer-2-title\">
          <div class=\"produk-img\">
            <img alt=\"Saus Tomat ABC\" src=\"{{ asset('src/saus_tomat.png') }}\" />
          </div>
          <div class=\"produk-info\">
            <h2 id=\"populer-2-title\" class=\"produk-name\">Saus Tomat ABC</h2>
            <p class=\"produk-toko\">Toko Surya</p>
            <div class=\"produk-harga\">Rp 12.000</div>
            <div class=\"produk-rating\">
              <span aria-label=\"4 dari 5 bintang\" class=\"rating-stars\">★★★★☆</span>
              <span class=\"rating-angka\">4.0</span>
            </div>
            <div class=\"produk-aksi\">
              <button class=\"btn-produk btn-primary\">Beli</button>
              <button class=\"btn-produk btn-outline\">+ Keranjang</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  {{-- JS khusus halaman ini --}}
  <script>
    // Pastikan script produk pakai input navbar (ID sama seperti di cust_welcome)
    window.__AKRAB_SEARCH_INPUT_ID__ = 'navbar-search';
  </script>
@endsection

@push('scripts')
  <script defer src="{{ asset('js/customer/produk/halaman_produk.js') }}?v=3"></script>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection