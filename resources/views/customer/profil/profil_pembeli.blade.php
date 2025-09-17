@extends('layouts.app')

@section('title', 'Profil Pembeli — Wishlist & Riwayat Pesanan')

@section('header')
  @include('components.header_compact')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/profil/profil_pembeli.css') }}">
@endpush

@section('content')
  @php
    // FRONTEND-ONLY URL (tanpa route/backend):
    $u1 = url('/produk/101/' . \Illuminate\Support\Str::slug('Cangkir Keramik 250ml'));
    $u2 = url('/produk/102/' . \Illuminate\Support\Str::slug('Piring Kayu 18cm'));

    // CATATAN:
    // Kalau nanti backend siap & punya named route:
    // $u1 = route('produk.detail', ['id'=>101, 'slug'=>\Illuminate\Support\Str::slug('Cangkir Keramik 250ml')]);
    // $u2 = route('produk.detail', ['id'=>102, 'slug'=>\Illuminate\Support\Str::slug('Piring Kayu 18cm')]);
  @endphp

  <div class="main-layout">
    <!-- CONTENT -->
    <main class="content">
      <div class="grid-2col">
        <!-- LEFT COLUMN -->
        <div class="left-col">
          <section class="card card-profile">
            <div class="card-head">
              <h2 class="card-title">Profil</h2>
              <a href="{{ route('edit.profil') }}" class="btn btn-ghost js-edit_profil">Edit Profil</a>
            </div>

            <dl class="info-list">
              <div>
                <dt>Nama</dt>
                <dd>Andi Saputra</dd>
              </div>
              <div>
                <dt>Email</dt>
                <dd>andi@example.com</dd>
              </div>
              <div>
                <dt>Telepon</dt>
                <dd>0812-3456-7890</dd>
              </div>
              <div>
                <dt>Alamat</dt>
                <dd>Jl. Anggrek No. 12, Bandung</dd>
              </div>
            </dl>

            <div class="actions-2col">
              <a href="{{ route('welcome') }}" class="btn btn-ghost js-logout">Logout</a>
              <button class="btn btn-danger btn-block">Hapus Akun</button>
            </div>
          </section>

          <!-- Kartu: Wishlist -->
          <section class="card card-wishlist" id="wishlistCard">
            <div class="card-head">
              <h2 class="card-title">Wishlist</h2>
              <a href="{{ route('halaman_wishlist') }}" class="btn btn-ghost btn-sm">Lihat Semua Wishlist</a>
            </div>

            <div id="wishlistViewport" class="wishlist-viewport" tabindex="0" aria-label="Daftar wishlist">
              <!-- Item 1 -->
              <article class=\"wishlist-card\">
                <a href=\"{{ $u1 }}\">
                  <img src=\"https://picsum.photos/seed/cup/96/96\" alt=\"Cangkir Keramik 250ml\" />
                </a>
                <div class=\"w-body\">
                  <a href=\"{{ $u1 }}\" class=\"w-name\">Cangkir Keramik 250ml</a>
                  <div class=\"w-price\">Rp45.000 <span class=\"chip chip-warn\">Stok menipis</span></div>
                  <div class=\"produk-card-stars\" aria-label=\"Rating 4.6 dari 5\">
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M29.6691 32.9982L28.0534 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535V29.2285L29.6691 32.9982ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF700\"/>
                    </svg>
                  </div>
                  <div class=\"w-actions\">
                    <button class=\"btn btn-ghost btn-sm btn-wish\" aria-label=\"Suka\" aria-pressed=\"false\" data-state=\"off\">
                      <svg class=\"svg-off\" width=\"22\" height=\"22\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                        <path d=\"M23.6962 36.3271L23.5003 36.5229L23.2849 36.3271C13.9828 27.8867 7.83366 22.3054 7.83366 16.6458C7.83366 12.7292 10.7712 9.79167 14.6878 9.79167C17.7037 9.79167 20.6412 11.75 21.6791 14.4133H25.3216C26.3595 11.75 29.297 9.79167 32.3128 9.79167C36.2295 9.79167 39.167 12.7292 39.167 16.6458C39.167 22.3054 33.0178 27.8867 23.6962 36.3271ZM32.3128 5.875C28.9053 5.875 25.6349 7.46125 23.5003 9.94833C21.3657 7.46125 18.0953 5.875 14.6878 5.875C8.65616 5.875 3.91699 10.5946 3.91699 16.6458C3.91699 24.0287 10.5753 30.08 20.6607 39.2254L23.5003 41.8104L26.3399 39.2254C36.4253 30.08 43.0837 24.0287 43.0837 16.6458C43.0837 10.5946 38.3445 5.875 32.3128 5.875Z\" fill=\"#FF0000\"/>
                      </svg>
                      <svg class=\"svg-on\" width=\"22\" height=\"22\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\" style=\"display:none\">
                        <path d=\"M23.5003 41.8104L20.6607 39.2254C10.5753 30.08 3.91699 24.0287 3.91699 16.6458C3.91699 10.5946 8.65616 5.875 14.6878 5.875C18.0953 5.875 21.3657 7.46125 23.5003 9.94833C25.6349 7.46125 28.9053 5.875 32.3128 5.875C38.3445 5.875 43.0837 10.5946 43.0837 16.6458C43.0837 24.0287 36.4253 30.08 26.3399 39.2254L23.5003 41.8104Z\" fill=\"#F24822\"/>
                      </svg>
                    </button>

                    <a href=\"{{ $u1 }}\" class=\"btn\">Lihat Detail</a>
                    <button class=\"btn btn-primary btn-sm\">+ Keranjang</button>
                  </div>
                  <p class=\"w-desc\">Cangkir keramik dengan finishing glossy dan kapasitas 250ml.</p>
                </div>
              </article>

              <!-- Item 2 -->
              <article class=\"wishlist-card\">
                <a href=\"{{ $u2 }}\">
                  <img src=\"https://picsum.photos/seed/plate/96/96\" alt=\"Toples Rotan Mini\" />
                </a>
                <div class=\"w-body\">
                  <a href=\"{{ $u2 }}\" class=\"w-name\">Toples Rotan Mini</a>
                  <div class=\"w-price\">Rp35.000</div>
                  <div class=\"produk-card-stars\" aria-label=\"Rating 4.2 dari 5\">
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M17.3316 32.9493L23.5003 29.2285L29.6691 32.9982L28.0535 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535L20.6607 20.5628L13.5128 21.1993L18.9472 25.9482L17.3316 32.9493ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                  </div>
                  <div class=\"w-actions\">
                    <button class=\"btn btn-ghost btn-sm btn-wish\" aria-label=\"Suka\" aria-pressed=\"false\" data-state=\"off\">
                      <svg class=\"svg-off\" width=\"22\" height=\"22\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                        <path d=\"M23.6962 36.3271L23.5003 36.5229L23.2849 36.3271C13.9828 27.8867 7.83366 22.3054 7.83366 16.6458C7.83366 12.7292 10.7712 9.79167 14.6878 9.79167C17.7037 9.79167 20.6412 11.75 21.6791 14.4133H25.3216C26.3595 11.75 29.297 9.79167 32.3128 9.79167C36.2295 9.79167 39.167 12.7292 39.167 16.6458C39.167 22.3054 33.0178 27.8867 23.6962 36.3271ZM32.3128 5.875C28.9053 5.875 25.6349 7.46125 23.5003 9.94833C21.3657 7.46125 18.0953 5.875 14.6878 5.875C8.65616 5.875 3.91699 10.5946 3.91699 16.6458C3.91699 24.0287 10.5753 30.08 20.6607 39.2254L23.5003 41.8104L26.3399 39.2254C36.4253 30.08 43.0837 24.0287 43.0837 16.6458C43.0837 10.5946 38.3445 5.875 32.3128 5.875Z\" fill=\"#FF0000\"/>
                      </svg>
                      <svg class=\"svg-on\" width=\"22\" height=\"22\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\" style=\"display:none\">
                        <path d=\"M23.5003 41.8104L20.6607 39.2254C10.5753 30.08 3.91699 24.0287 3.91699 16.6458C3.91699 10.5946 8.65616 5.875 14.6878 5.875C18.0953 5.875 21.3657 7.46125 23.5003 9.94833C25.6349 7.46125 28.9053 5.875 32.3128 5.875C38.3445 5.875 43.0837 10.5946 43.0837 16.6458C43.0837 24.0287 36.4253 30.08 26.3399 39.2254L23.5003 41.8104Z\" fill=\"#F24822\"/>
                      </svg>
                    </button>

                    <a href=\"{{ $u2 }}\" class=\"btn\">Lihat Detail</a>
                    <button class=\"btn btn-primary btn-sm\">+ Keranjang</button>
                  </div>
                  <p class=\"w-desc\">Toples rotan mini dengan ukuran diameter 18cm, cocok untuk penyajian snack.</p>
                </div>
              </article>

              <!-- Item 3 -->
              <article class=\"wishlist-card\">
                <a href=\"{{ $u1 }}\">
                  <img src=\"https://picsum.photos/seed/basket/96/96\" alt=\"Batik Tulis Mini Frame\" />
                </a>
                <div class=\"w-body\">
                  <a href=\"{{ $u1 }}\" class=\"w-name\">Batik Tulis Mini Frame</a>
                  <div class=\"w-price\">Rp150.000</div>
                  <div class=\"produk-card-stars\" aria-label=\"Rating 4.8 dari 5\">
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                  </div>
                  <div class=\"w-actions\">
                    <button class=\"btn btn-ghost btn-sm btn-wish\" aria-label=\"Suka\" aria-pressed=\"false\" data-state=\"off\">
                      <svg class=\"svg-off\" width=\"22\" height=\"22\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                        <path d=\"M23.6962 36.3271L23.5003 36.5229L23.2849 36.3271C13.9828 27.8867 7.83366 22.3054 7.83366 16.6458C7.83366 12.7292 10.7712 9.79167 14.6878 9.79167C17.7037 9.79167 20.6412 11.75 21.6791 14.4133H25.3216C26.3595 11.75 29.297 9.79167 32.3128 9.79167C36.2295 9.79167 39.167 12.7292 39.167 16.6458C39.167 22.3054 33.0178 27.8867 23.6962 36.3271ZM32.3128 5.875C28.9053 5.875 25.6349 7.46125 23.5003 9.94833C21.3657 7.46125 18.0953 5.875 14.6878 5.875C8.65616 5.875 3.91699 10.5946 3.91699 16.6458C3.91699 24.0287 10.5753 30.08 20.6607 39.2254L23.5003 41.8104L26.3399 39.2254C36.4253 30.08 43.0837 24.0287 43.0837 16.6458C43.0837 10.5946 38.3445 5.875 32.3128 5.875Z\" fill=\"#FF0000\"/>
                      </svg>
                      <svg class=\"svg-on\" width=\"22\" height=\"22\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\" style=\"display:none\">
                        <path d=\"M23.5003 41.8104L20.6607 39.2254C10.5753 30.08 3.91699 24.0287 3.91699 16.6458C3.91699 10.5946 8.65616 5.875 14.6878 5.875C18.0953 5.875 21.3657 7.46125 23.5003 9.94833C25.6349 7.46125 28.9053 5.875 32.3128 5.875C38.3445 5.875 43.0837 10.5946 43.0837 16.6458C43.0837 24.0287 36.4253 30.08 26.3399 39.2254L23.5003 41.8104Z\" fill=\"#F24822\"/>
                      </svg>
                    </button>

                    <a href=\"{{ $u1 }}\" class=\"btn\">Lihat Detail</a>
                    <button class=\"btn btn-primary btn-sm\">+ Keranjang</button>
                  </div>
                  <p class=\"w-desc\">Frame batik tulis mini ukuran 18cm, cocok untuk dekorasi dinding atau pajangan.</p>
                </div>
              </article>

              <!-- Item 4 -->
              <article class=\"wishlist-card\">
                <a href=\"{{ $u2 }}\">
                  <img src=\"https://picsum.photos/seed/sambal/96/96\" alt=\"Sambal Bawang Rumahan\" />
                </a>
                <div class=\"w-body\">
                  <a href=\"{{ $u2 }}\" class=\"w-name\">Sambal Bawang Rumahan</a>
                  <div class=\"w-price\">Rp18.000</div>
                  <div class=\"produk-card-stars\" aria-label=\"Rating 4.5 dari 5\">
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF600\"/>
                    </svg>
                    <svg width=\"27\" height=\"27\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                      <path d=\"M29.6691 32.9982L28.0534 25.9482L33.4878 21.2482L26.3399 20.6118L23.5003 13.9535V29.2285L29.6691 32.9982ZM11.4076 41.1253L14.5899 27.368L3.91699 18.1149L18.017 16.891L23.5003 3.91699L28.9837 16.891L43.0837 18.1149L32.4107 27.368L35.593 41.1253L23.5003 33.8305L11.4076 41.1253Z\" fill=\"#FFF700\"/>
                    </svg>
                  </div>
                  <div class=\"w-actions\">
                    <button class=\"btn btn-ghost btn-sm btn-wish active\" aria-label=\"Suka\" aria-pressed=\"true\" data-state=\"on\">
                      <svg class=\"svg-off\" width=\"22\" height=\"22\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\" style=\"display:none\">
                        <path d=\"M23.6962 36.3271L23.5003 36.5229L23.2849 36.3271C13.9828 27.8867 7.83366 22.3054 7.83366 16.6458C7.83366 12.7292 10.7712 9.79167 14.6878 9.79167C17.7037 9.79167 20.6412 11.75 21.6791 14.4133H25.3216C26.3595 11.75 29.297 9.79167 32.3128 9.79167C36.2295 9.79167 39.167 12.7292 39.167 16.6458C39.167 22.3054 33.0178 27.8867 23.6962 36.3271ZM32.3128 5.875C28.9053 5.875 25.6349 7.46125 23.5003 9.94833C21.3657 7.46125 18.0953 5.875 14.6878 5.875C8.65616 5.875 3.91699 10.5946 3.91699 16.6458C3.91699 24.0287 10.5753 30.08 20.6607 39.2254L23.5003 41.8104L26.3399 39.2254C36.4253 30.08 43.0837 24.0287 43.0837 16.6458C43.0837 10.5946 38.3445 5.875 32.3128 5.875Z\" fill=\"#FF0000\"/>
                      </svg>
                      <svg class=\"svg-on\" width=\"22\" height=\"22\" viewBox=\"0 0 47 47\" xmlns=\"http://www.w3.org/2000/svg\">
                        <path d=\"M23.5003 41.8104L20.6607 39.2254C10.5753 30.08 3.91699 24.0287 3.91699 16.6458C3.91699 10.5946 8.65616 5.875 14.6878 5.875C18.0953 5.875 21.3657 7.46125 23.5003 9.94833C25.6349 7.46125 28.9053 5.875 32.3128 5.875C38.3445 5.875 43.0837 10.5946 43.0837 16.6458C43.0837 24.0287 36.4253 30.08 26.3399 39.2254L23.5003 41.8104Z\" fill=\"#F24822\"/>
                      </svg>
                    </button>

                    <a href=\"{{ $u2 }}\" class=\"btn\">Lihat Detail</a>
                    <button class=\"btn btn-primary btn-sm\">+ Keranjang</button>
                  </div>
                  <p class=\"w-desc\">Sambal bawang rumahan pedas dengan tekstur lembut, cocok untuk pendamping makan nasi.</p>
                </div>
              </article>
            </div>
          </section>
        </div>

        <!-- RIGHT COLUMN -->
        <section class=\"card\" id=\"ordersSection\">
          <div class=\"orders-head\">
            <h2 class=\"card-title\">Riwayat Pesanan</h2>
            <div class=\"orders-head-hint\">Menampilkan pesanan terbaru</div>
          </div>

          <div id=\"ordersViewport\" class=\"orders-viewport\" aria-live=\"polite\">
            <article class=\"order-card\" data-order-id=\"#A-10293\">
              <div class=\"order-head\">
                <div class=\"shop-name\">Toko Dapur Maju</div>
                <div class=\"order-meta\">
                  <span>18 Agu 2025</span>
                  <span>•</span>
                  <span>Sudah Dibayar</span>
                </div>
              </div>

              <div class=\"order-items\">
                <div class=\"order-item\">
                  <img class=\"item-img\" src=\"https://picsum.photos/seed/chop/128/128\" alt=\"Cangkir Keramik 250ml\" />
                  <div class=\"item-body\">
                    <div class=\"item-row\">
                      <div class=\"item-name\">Cangkir Keramik 250ml</div>
                      <div class=\"item-qty\">x1</div>
                    </div>
                    <div class=\"item-desc scrollable\">
                      Cangkir keramik dengan finishing glossy dan kapasitas 250ml.
                    </div>
                    <div class=\"item-subtotal\">Rp45.000</div>
                  </div>
                </div>
              </div>

              <div class=\"order-actions\">
                <div class=\"total-price\">Total: Rp45.000</div>
                <div class=\"order-buttons\">
                  <a href=\"{{ $u1 }}\" class=\"btn\">Lihat Detail</a>
                  <button class=\"btn btn-primary buy-again\">Beli Lagi</button>
                </div>
              </div>
            </article>

            <article class=\"order-card\" data-order-id=\"#A-10244\">
              <div class=\"order-head\">
                <div class=\"shop-name\">Toko Kerajinan Nusantara</div>
                <div class=\"order-meta\">
                  <span>10 Agu 2025</span>
                  <span>•</span>
                  <span>Selesai</span>
                </div>
              </div>

              <div class=\"order-items\">
                <div class=\"order-item\">
                  <img class=\"item-img\" src=\"https://picsum.photos/seed/basket/128/128\" alt=\"Keranjang Rotan\" />
                  <div class=\"item-body\">
                    <div class=\"item-row\">
                      <div class=\"item-name\">Keranjang Rotan</div>
                      <div class=\"item-qty\">x2</div>
                    </div>
                    <div class=\"item-desc scrollable\">
                      Keranjang rotan handmade untuk penyimpanan pakaian/aksesoris.
                    </div>
                    <div class=\"item-subtotal\">Rp200.000</div>
                  </div>
                </div>
              </div>

              <div class=\"order-actions\">
                <div class=\"total-price\">Total: Rp200.000</div>
                <div class=\"order-buttons\">
                  <a href=\"{{ $u2 }}\" class=\"btn\">Lihat Detail</a>
                  <button class=\"btn btn-primary buy-again\">Beli Lagi</button>
                </div>
              </div>
            </article>
          </div>
        </section>

        <!-- ====== RIWAYAT ULASAN ====== -->
        <section class=\"reviews-section\">
          <div class=\"card-head\">
            <h2 class=\"card-title\">Riwayat Ulasan</h2>
            <a href=\"{{ route('halaman_ulasan') }}\" class=\"btn primary\">Lihat Semua Ulasan</a>
          </div>

          <div class=\"reviews-viewport\" aria-label=\"Daftar riwayat ulasan\" aria-live=\"polite\">
            <!-- Review 1 -->
            <article class=\"review-card\">
              <div class=\"rev-body\">
                <div class=\"rev-top\">
                  <div class=\"user__name\">yudistiradwianggara</div>
                  <time class=\"rev-date\">12-06-2025</time>
                </div>
                <div class=\"rev-stars\" aria-label=\"Rating 4 dari 5\">
                  <svg class=\"star filled\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                  <svg class=\"star filled\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                  <svg class=\"star filled\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                  <svg class=\"star filled\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                  <svg class=\"star\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                </div>
                <p class=\"rev-text\">Rasa bold, aroma mantap. Pengiriman aman dan cepat. Kemasannya rapi.</p>
                <div class=\"rev-actions\">
                  <a href=\"{{ $u1 }}\" class=\"btn\">Lihat Produk</a>
                  <button class=\"btn primary\">Perbarui</button>
                </div>
              </div>
            </article>

            <!-- Review 2 -->
            <article class=\"review-card\">
              <div class=\"rev-body\">
                <div class=\"rev-top\">
                  <div class=\"user__name\">yudistiradwianggara</div>
                  <time class=\"rev-date\">09-05-2025</time>
                </div>
                <div class=\"rev-stars\" aria-label=\"Rating 5 dari 5\">
                  <svg class=\"star filled\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                  <svg class=\"star filled\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                  <svg class=\"star filled\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                  <svg class=\"star filled\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                  <svg class=\"star filled\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                </div>
                <p class=\"rev-text\">Manisnya pas, tidak eneg. Cocok untuk campuran teh dan roti.</p>
                <div class=\"rev-actions\">
                  <a href=\"{{ $u2 }}\" class=\"btn\">Lihat Produk</a>
                  <button class=\"btn primary\">Perbarui</button>
                </div>
              </div>
            </article>

            <!-- Review 3 -->
            <article class=\"review-card\">
              <div class=\"rev-body\">
                <div class=\"rev-top\">
                  <div class=\"user__name\">yudistiradwianggara</div>
                  <time class=\"rev-date\">22-03-2025</time>
                </div>
                <div class=\"rev-stars\" aria-label=\"Rating 3 dari 5\">
                  <svg class=\"star filled\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                  <svg class=\"star filled\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                  <svg class=\"star filled\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                  <svg class=\"star\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                  <svg class=\"star\" viewBox=\"0 0 20 20\"><path d=\"M10 1l2.6 5.3l5.9.9-4.2 4.1l1 5.8L10 14.8l-5.3 2.3l1-5.8L1.5 7.2l5.9-.9z\"/></svg>
                </div>
                <p class=\"rev-text\">Motifnya bagus, bahan agak tebal. Pengiriman tepat waktu.</p>
                <div class=\"rev-actions\">
                  <a href=\"{{ $u1 }}\" class=\"btn\">Lihat Produk</a>
                  <button class=\"btn primary\">Perbarui</button>
                </div>
              </div>
            </article>
          </div>
        </section>
        <!-- ====== /RIWAYAT ULASAN ====== -->
      </div>
    </main>
  </div>
@endsection

@push('scripts')
  <script defer src="{{ asset('js/customer/profil/profil_pembeli.js') }}"></script>
@endpush