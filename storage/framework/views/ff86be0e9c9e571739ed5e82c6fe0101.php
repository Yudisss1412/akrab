<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Admin - UMKM AKRAB'); ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="<?php echo e(asset('css/layouts/admin-layout.css')); ?>">

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="<?php echo e(request()->routeIs('dashboard.admin') || request()->routeIs('admin.dashboard') ? 'dashboard-page' : ''); ?>">
    <div class="container-fluid">
        <?php if(!(request()->routeIs('dashboard.admin') || request()->routeIs('admin.dashboard'))): ?>
        <nav class="navbar navbar-expand navbar-light bg-light mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="javascript:history.back()" title="Kembali ke halaman sebelumnya">
                    <i class="fas fa-arrow-left me-2"></i>UMKM AKRAB Admin
                </a>
            </div>
        </nav>
        <?php endif; ?>
        
        <!-- Success and Error Messages -->
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
                <div class="alert-content">
                    <span class="alert-icon">✅</span>
                    <span class="alert-message"><?php echo e(session('success')); ?></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
                <div class="alert-content">
                    <span class="alert-icon">❌</span>
                    <span class="alert-message"><?php echo e(session('error')); ?></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Main Content -->
        <main>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\ecommerce-akrab\resources\views/layouts/admin.blade.php ENDPATH**/ ?>