<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UMKM AKRAB')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/layouts/app-layout.css') }}">
    
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/order-detail.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    @stack('styles')
    
    <!-- CSS khusus untuk komponen penjual -->
    @if(request()->routeIs('penjual.*'))
        <link rel="stylesheet" href="{{ asset('css/penjual/components.css') }}">
    @endif
</head>
<body>
    <div class="main-layout">
        <!-- Header -->
        @yield('header')
        
        <!-- Main Content -->
        <main class="content">
            @yield('content')
        </main>
        
        <!-- Footer -->
        @yield('footer')
    </div>

    <script src="{{ asset('js/customer/helpers/csrfHelper.js') }}"></script>
    @stack('scripts')
    <script src="{{ asset('js/customer/script.js') }}"></script>

    <script src="{{ asset('js/layouts/app-layout.js') }}"></script>
</body>
</html>