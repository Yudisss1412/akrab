<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pembeli - UMKM AKRAB')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- CSS Produk untuk konsistensi tampilan -->
    <link href="{{ asset('css/customer/produk/halaman_produk.css') }}" rel="stylesheet"/>
    
    <style>
        body {
            overflow-x: hidden;
            font-family: 'Poppins', sans-serif;
        }
        .content-wrapper {
            margin-left: 280px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            background-color: #f0fdfa; /* Sesuaikan dengan warna background CSS produk */
        }
        .sidebar {
            top: 0;
            left: 0;
        }
        
        /* Penyesuaian untuk konten dalam layout customer */
        .produk-page {
            max-width: 100%;
            padding: 2.2rem 2.7rem 70px 2.7rem; /* Sesuaikan dengan layout utama */
            margin: 0 auto;
            flex: 1;
            background: var(--background-color, #f0fdfa);
        }
        
        /* Penyesuaian untuk card produk agar sesuai dengan layout customer */
        .produk-page .produk-item {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 12px;
            transition: box-shadow .15s, border-color .15s, transform .15s;
        }
        
        /* Penyesuaian untuk header produk agar konsisten */
        .produk-page .produk-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        
        /* Gaya untuk produk-info sesuai dengan struktur baru */
        .produk-page .produk-info {
            flex: 1;
        }
        
        /* Gaya untuk produk-img sesuai dengan struktur baru */
        .produk-page .produk-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 10px;
            background: var(--background-color, #f0fdfa);
            flex-shrink: 0;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <x-sidebar role="customer" />
    
    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Success and Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <!-- Main Content -->
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Customer main script -->
    <script src="{{ asset('js/customer/script.js') }}"></script>
    
    @stack('scripts')
</body>
</html>