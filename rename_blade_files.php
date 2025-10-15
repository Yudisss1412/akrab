<?php

// Script untuk mengganti ekstensi semua file .blade.php menjadi .blade.view
// Kecuali file di direktori vendor

function renameBladeFiles($dir) {
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') continue;
        
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        
        if (is_dir($path)) {
            // Jika direktori bukan vendor, lanjutkan rekursif
            if (basename($path) !== 'vendor') {
                renameBladeFiles($path);
            }
        } else {
            // Jika file berakhiran .blade.php, ganti menjadi .blade.view
            if (substr($file, -10) === '.blade.php') {
                $newPath = substr($path, 0, -10) . '.blade.view';
                echo "Mengganti: $path\n";
                echo "Menjadi: $newPath\n";
                rename($path, $newPath);
            }
        }
    }
}

// Jalankan fungsi untuk direktori views
$viewsDir = 'C:\xampp\htdocs\ecommerce-akrab\resources\views';
renameBladeFiles($viewsDir);

echo "Proses penggantian selesai.\n";