<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'UMKM AKRAB'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo e(asset('css/layouts/app-layout.css')); ?>">
    
    <link rel="stylesheet" href="<?php echo e(asset('css/customer/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/order-detail.css')); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <?php echo $__env->yieldPushContent('styles'); ?>
    
    <!-- CSS khusus untuk komponen penjual -->
    <?php if(request()->routeIs('penjual.*')): ?>
        <link rel="stylesheet" href="<?php echo e(asset('css/penjual/components.css')); ?>">
    <?php endif; ?>
</head>
<body>
    <div class="main-layout">
        <!-- Header -->
        <?php echo $__env->yieldContent('header'); ?>
        
        <!-- Main Content -->
        <main class="content">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
        
        <!-- Footer -->
        <?php echo $__env->yieldContent('footer'); ?>
    </div>

    <script src="<?php echo e(asset('js/customer/helpers/csrfHelper.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script src="<?php echo e(asset('js/customer/script.js')); ?>"></script>

    <script src="<?php echo e(asset('js/layouts/app-layout.js')); ?>"></script>
</body>
</html><?php /**PATH C:\xampp\htdocs\ecommerce-akrab\resources\views/layouts/app.blade.php ENDPATH**/ ?>