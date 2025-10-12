@extends('layouts.app')

@section('title', 'Profil Pembeli — Wishlist & Riwayat Pesanan')

@section('header')
  @include('components.customer.header.header')
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/customer/profil/profil_pembeli.css') }}">
@endpush

@section('content')
  @php
    // FRONTEND-ONLY URL (tanpa route/backend):
    $u1 = url('/produk/101/' . \\Illuminate\\Support\\Str::slug('Cangkir Keramik 250ml'));
    $u2 = url('/produk/102/' . \\Illuminate\\Support\\Str::slug('Piring Kayu 18cm'));

    // CATATAN:
    // Kalau nanti backend siap & punya named route:
    // $u1 = route('produk.detail', ['id'=>101, 'slug'=>\\Illuminate\\Support\\Str::slug('Cangkir Keramik 250ml')]);
    // $u2 = route('produk.detail', ['id'=>102, 'slug'=>\\Illuminate\\Support\\Str::slug('Piring Kayu 18cm')]);
  @endphp

  <div class="main-layout">
    <!-- CONTENT -->
    <main class="content">
      <div class="grid-2col">
        <!-- LEFT COLUMN -->
        <div class="left-col">
          <!-- PROFILE CARD -->
          <div class="profile-card">
            <div class="profile-header">
              <div class="profile-avatar">
                <img src="{{ asset('src/profil_pembeli.png') }}" alt="Profil Pembeli">
              </div>
              <div class="profile-info">
                <h2>Anggara Putra</h2>
                <p class="member-since">Member sejak 2023</p>
              </div>
              <a href="{{ route('profil.edit') }}" class="btn-edit-profile">Edit Profil</a>
            </div>
            
            <!-- Profile Details -->
            <div class="profile-details">
              <div class="detail-item">
                <label>Email</label>
                <span>anggara.putra@example.com</span>
              </div>
              <div class="detail-item">
                <label>Telepon</label>
                <span>+62 812 3456 7890</span>
              </div>
              <div class="detail-item">
                <label>Alamat</label>
                <span>Jl. Contoh No. 123, Kota, Provinsi</span>
              </div>
            </div>
          </div>
          
          <!-- NAVIGATION -->
          <div class="nav-card">
            <nav class="profile-nav">
              <a href="#" class="nav-item active">
                <i class="bi bi-bag-check"></i>
                <span>Riwayat Pesanan</span>
              </a>
              <a href="{{ route('koleksi.wishlist') }}" class="nav-item">
                <i class="bi bi-heart"></i>
                <span>Wishlist</span>
              </a>
              <a href="{{ route('koleksi.ulasan') }}" class="nav-item">
                <i class="bi bi-chat-left-text"></i>
                <span>Ulasan Saya</span>
              </a>
              <a href="#" class="nav-item">
                <i class="bi bi-gear"></i>
                <span>Pengaturan Akun</span>
              </a>
              <a href="#" class="nav-item">
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar</span>
              </a>
            </nav>
          </div>
        </div>
        
        <!-- RIGTH COLUMN -->
        <div class="right-col">
          <!-- ====== RIWAYAT PESANAN ====== -->
          <section class="order-history">
            <div class="section-header">
              <h3>Riwayat Pesanan</h3>
              <a href="#" class="view-all">Lihat Semua</a>
            </div>
            
            <div class="order-list">
              <!-- Order 1 -->
              <article class="order-card">
                <div class="order-header">
                  <div class="order-id">INV-2023-001</div>
                  <div class="order-status status-completed">Selesai</div>
                </div>
                
                <div class="order-details">
                  <div class="order-date">15 Mar 2023</div>
                  <div class="order-amount">Rp 245.000</div>
                </div>
                
                <div class="order-items">
                  <div class="item-preview">
                    <img src="{{ asset('src/CangkirKeramik1.png') }}" alt="Cangkir Keramik">
                    <span class="item-count">+2 item lainnya</span>
                  </div>
                  <a href="{{ $u1 }}" class="btn btn-outline">Beli Lagi</a>
                </div>
              </article>
              
              <!-- Order 2 -->
              <article class="order-card">
                <div class="order-header">
                  <div class="order-id">INV-2023-002</div>
                  <div class="order-status status-shipping">Dikirim</div>
                </div>
                
                <div class="order-details">
                  <div class="order-date">22 Mar 2023</div>
                  <div class="order-amount">Rp 180.000</div>
                </div>
                
                <div class="order-items">
                  <div class="item-preview">
                    <img src="{{ asset('src/PiringKayu.png') }}" alt="Piring Kayu">
                    <span class="item-count">+1 item lainnya</span>
                  </div>
                  <a href="{{ $u2 }}" class="btn btn-outline">Beli Lagi</a>
                </div>
              </article>
            </div>
          </section>
          <!-- ====== /RIWAYAT PESANAN ====== -->
          
          <!-- ====== RIWAYAT ULASAN ====== -->
          <section class="review-history">
            <div class="section-header">
              <h3>Ulasan Saya</h3>
              <a href="{{ route('koleksi.ulasan') }}" class="view-all">Lihat Semua</a>
            </div>
            
            <div class="review-list">
              <!-- Review 1 -->
              <article class="review-card">
                <div class="review-product">
                  <img src="{{ asset('src/CangkirKeramik1.png') }}" alt="Cangkir Keramik">
                  <div class="product-info">
                    <h4>Cangkir Keramik 250ml</h4>
                    <div class="rating">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-half"></i>
                      <span>(4.5)</span>
                    </div>
                  </div>
                </div>
                
                <div class="review-content">
                  <p>Kualitas bagus, sesuai pesanan. Pengiriman cepat dan aman.</p>
                </div>
              </article>
              
              <!-- Review 2 -->
              <article class="review-card">
                <div class="review-product">
                  <img src="{{ asset('src/PiringKayu.png') }}" alt="Piring Kayu">
                  <div class="product-info">
                    <h4>Piring Kayu Jati</h4>
                    <div class="rating">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star"></i>
                      <span>(4.0)</span>
                    </div>
                  </div>
                </div>
                
                <div class="review-content">
                  <p>Desainnya unik dan elegan. Cocok untuk penggunaan sehari-hari.</p>
                </div>
              </article>
            </div>
          </section>
          <!-- ====== /RIWAYAT ULASAN ====== -->
        </div>
      </div>
    </main>
  </div>
@endsection

@push('scripts')
  <script defer src="{{ asset('js/customer/profil/profil_pembeli.js') }}"></script>
@endpush

@section('footer')
  @include('components.customer.footer.footer')
@endsection