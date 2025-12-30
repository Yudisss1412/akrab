
<header class="header compact">
  <div class="header-left">
    <a href="{{ route('cust.welcome') }}" aria-label="Ke beranda">
      <img class="logo" src="{{ asset('src/Logo_UMKM.png') }}" alt="AKRAB" />
    </a>
  </div>
  <div class="header-center">
    <!-- Tanpa search bar untuk header penjual -->
  </div>
  <div class="header-right">
    <!-- Tiket Bantuan untuk penjual -->
    <a href="{{ route('customer.tickets') }}" class="ticket-badge" aria-label="Tiket Bantuan">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#006E5C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M14 2V8H20" stroke="#006E5C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M16 13H8" stroke="#006E5C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M16 17H8" stroke="#006E5C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M10 9H9H8" stroke="#006E5C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      @php
          $openTicketCount = auth()->user() ? auth()->user()->openTickets()->count() : 0;
      @endphp
      @if($openTicketCount > 0)
        <span class="ticket-count">{{ $openTicketCount }}</span>
      @endif
    </a>
    
    <a class="profile-ico" href="{{ route('profil.penjual') }}" aria-label="Profil Penjual">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 10C14.2091 10 16 8.20914 16 6C16 3.79086 14.2091 2 12 2C9.79086 2 8 3.79086 8 6C8 8.20914 9.79086 10 12 10Z" stroke="#006E5C" stroke-width="1.5"/>
        <path d="M20 17.5C20 19.985 20 22 12 22C4 22 4 19.985 4 17.5C4 15.015 7.582 13 12 13C16.418 13 20 15.015 20 17.5Z" stroke="#006E5C" stroke-width="1.5"/>
      </svg>
    </a>
  </div>
</header>