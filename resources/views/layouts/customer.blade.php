<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', config('app.name', 'AKRAB'))</title>

  {{-- Font --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

</head>
<body class="@yield('body_class')">
  {{-- NAVBAR (bisa di-override per halaman lewat @section('navbar')) --}}
  <header class="header" role="banner">
    @hasSection('navbar')
      @yield('navbar')
    @else
      @include('partials.navbar', ['showSearch' => true])
    @endif
  </header>

  {{-- KONTEN HALAMAN (dibungkus satu-satunya .main-layout) --}}
  <div class="main-layout">
    @yield('content')
  </div>

  {{-- FOOTER (di luar .main-layout agar tidak dobel padding/spacing) --}}
  @includeIf('partials.footer')

  {{-- JS global (fallback tanpa Vite): uncomment jika perlu --}}
  {{-- <script defer src="{{ asset('js/app.js') }}"></script> --}}

  {{-- JS khusus halaman --}}
  @stack('scripts')
</body>
</html>
