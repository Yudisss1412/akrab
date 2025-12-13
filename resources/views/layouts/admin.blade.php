<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - UMKM AKRAB')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .custom-alert {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.8rem 1rem;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .alert-content {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .alert-icon {
            font-size: 1.2rem;
        }

        .alert-message {
            flex: 1;
        }
    </style>

    @stack('styles')
</head>
<body class="{{ request()->routeIs('dashboard.admin') || request()->routeIs('admin.dashboard') ? 'dashboard-page' : '' }}">
    <div class="container-fluid">
        @if(!(request()->routeIs('dashboard.admin') || request()->routeIs('admin.dashboard')))
        <nav class="navbar navbar-expand navbar-light bg-light mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="javascript:history.back()" title="Kembali ke halaman sebelumnya">
                    <i class="fas fa-arrow-left me-2"></i>UMKM AKRAB Admin
                </a>
            </div>
        </nav>
        @endif
        
        <!-- Success and Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
                <div class="alert-content">
                    <span class="alert-icon">✅</span>
                    <span class="alert-message">{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
                <div class="alert-content">
                    <span class="alert-icon">❌</span>
                    <span class="alert-message">{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        <!-- Main Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>