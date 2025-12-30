@extends('layouts.admin')

@section('title', 'Profil Admin - AKRAB')

@push('styles')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/penjual/profil_penjual.css') }}">
  <link rel="stylesheet" href="{{ asset('css/admin_penjual/style.css') }}">
@endpush

@section('content')
  @include('components.admin_penjual.header')
    <div class="main-layout">
      <div class="content-wrapper">
        <main class="content admin-page-content" role="main">
          <!-- Profil Admin -->
          <section class="card card-profile admin-profile" aria-labelledby="adminTitle">
            <div class="seller-hero">
              <!-- kiri: identitas -->
              <div class="seller-identity">
                <div class="avatar" aria-hidden="true">
                  <span>A</span>
                  <i class="dot online"></i>
                </div>
                <div class="seller-meta">
                  <h1 id="adminTitle" class="seller-name">{{ $admin->name }}</h1>
                  <div class="seller-mail">{{ $admin->email }}</div>
                  <div class="seller-since">Bergabung sejak <strong>{{ $admin->created_at->year }}</strong></div>
                </div>
              </div>
              <!-- kanan: aksi -->
              <div class="profile-actions">
                <a href="{{ route('edit.profil.admin') }}" id="btnEditProfile" class="btn btn-primary btn-sm">
                  Edit Profil
                </a>
                <a href="{{ route('logout') }}" class="btn btn-outline-secondary btn-sm"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  Keluar
                </a>
              </div>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>

            <dl class="info-list" aria-label="Info Admin">
              <div>
                <dt>Nama Lengkap</dt>
                <dd>{{ $admin->name }}</dd>
              </div>
              <div>
                <dt>Email</dt>
                <dd>{{ $admin->email }}</dd>
              </div>
              <div>
                <dt>No. HP</dt>
                <dd>{{ $admin->phone ?? '+62 812-3456-7890' }}</dd>
              </div>
              <div>
                <dt>Jabatan</dt>
                <dd>{{ $admin->role->name ?? 'Administrator' }}</dd>
              </div>
              <div>
                <dt>Level Akses</dt>
                <dd>Super Admin</dd>
              </div>
              <div>
                <dt>Status Akun</dt>
                <dd style="color: #10b981; font-weight: 600;">Aktif</dd>
              </div>
            </dl>
          </section>



          <!-- Log Personal -->
          <section class="card reviews-section" aria-labelledby="logTitle">
            <div class="card-head">
              <h2 id="logTitle" class="card-title">Log Personal</h2>
            </div>
            <div class="admin-orders-viewport">
              @if($activityLogs->count() > 0)
                @foreach($activityLogs as $log)
                  <article class="admin-order-card" data-order-id="LOG-{{ $log->id }}">
                    <div class="order-head">
                      <div class="shop-name">Anda</div>
                      <div class="order-meta">
                        <span>Tanggal:
                          <time datetime="{{ $log->created_at->format('Y-m-d') }}">{{ $log->created_at->format('d M Y') }}</time>
                        </span>
                        • <span>Status: <strong>{{ ucfirst($log->status) }}</strong></span>
                      </div>
                    </div>

                    <div class="order-items">
                      <div class="order-item">
                        <div class="item-body">
                          <div class="item-row">
                            <div class="item-name">{{ $log->activity }}</div>
                          </div>
                          <div class="item-desc scrollable">
                            {{ $log->description }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </article>
                @endforeach
              @else
                <div class="empty-state">
                  <p>Tidak ada aktivitas tercatat</p>
                </div>
              @endif
            </div>

            @if($activityLogs->hasPages())
              <div class="pagination">
                {{ $activityLogs->links() }}
              </div>
            @endif
          </section>
        </main>
      </div>
    </div>
  @include('components.admin_penjual.footer')
@endsection