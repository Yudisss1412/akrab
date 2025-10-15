<header class="ak-navbar">
  <div class="ak-navbar__inner">
    <a href="{{ route('welcome') }}" aria-label="Ke beranda">
      <img class="ak-logo" src="{{ asset('src/Logo_UMKM.png') }}" alt="AKRAB" />
    </a>
    <div class="header-right">
      @if(auth()->check() && auth()->user()->role && auth()->user()->role->name === 'admin')
        <a class="profile-ico" href="{{ route('profil.admin') }}" aria-label="Profil Admin">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 10C14.2091 10 16 8.20914 16 6C16 3.79086 14.2091 2 12 2C9.79086 2 8 3.79086 8 6C8 8.20914 9.79086 10 12 10Z" stroke="#006E5C" stroke-width="1.5"/>
            <path d="M20 17.5C20 19.985 20 22 12 22C4 22 4 19.985 4 17.5C4 15.015 7.582 13 12 13C16.418 13 20 15.015 20 17.5Z" stroke="#006E5C" stroke-width="1.5"/>
          </svg>
        </a>
      @elseif(auth()->check() && auth()->user()->role && auth()->user()->role->name === 'seller')
        <a class="profile-ico" href="{{ route('profil.penjual') }}" aria-label="Profil Penjual">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 10C14.2091 10 16 8.20914 16 6C16 3.79086 14.2091 2 12 2C9.79086 2 8 3.79086 8 6C8 8.20914 9.79086 10 12 10Z" stroke="#006E5C" stroke-width="1.5"/>
            <path d="M20 17.5C20 19.985 20 22 12 22C4 22 4 19.985 4 17.5C4 15.015 7.582 13 12 13C16.418 13 20 15.015 20 17.5Z" stroke="#006E5C" stroke-width="1.5"/>
          </svg>
        </a>
      @else
        <a class="profile-ico" href="{{ route('profil.penjual') }}" aria-label="Profil Penjual">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 10C14.2091 10 16 8.20914 16 6C16 3.79086 14.2091 2 12 2C9.79086 2 8 3.79086 8 6C8 8.20914 9.79086 10 12 10Z" stroke="#006E5C" stroke-width="1.5"/>
            <path d="M20 17.5C20 19.985 20 22 12 22C4 22 4 19.985 4 17.5C4 15.015 7.582 13 12 13C16.418 13 20 15.015 20 17.5Z" stroke="#006E5C" stroke-width="1.5"/>
          </svg>
        </a>
      @endif
    </div>
  </div>
</header>