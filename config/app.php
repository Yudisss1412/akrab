<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nama Aplikasi
    |--------------------------------------------------------------------------
    |
    | Nilai ini adalah nama aplikasi Anda, yang akan digunakan ketika framework
    | perlu menempatkan nama aplikasi dalam notifikasi atau elemen UI lainnya
    | di mana nama aplikasi perlu ditampilkan.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Lingkungan Aplikasi
    |--------------------------------------------------------------------------
    |
    | Nilai ini menentukan "lingkungan" tempat aplikasi Anda saat ini berjalan.
    | Ini dapat menentukan bagaimana Anda ingin mengkonfigurasi berbagai layanan
    | yang digunakan aplikasi. Atur ini di file ".env" Anda.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Mode Debug Aplikasi
    |--------------------------------------------------------------------------
    |
    | Ketika aplikasi Anda dalam mode debug, pesan kesalahan terperinci dengan
    | stack trace akan ditampilkan pada setiap kesalahan yang terjadi dalam
    | aplikasi Anda. Jika dinonaktifkan, halaman kesalahan generik sederhana ditampilkan.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | URL Aplikasi
    |--------------------------------------------------------------------------
    |
    | URL ini digunakan oleh konsol untuk menghasilkan URL dengan benar ketika
    | menggunakan alat baris perintah Artisan. Anda harus mengaturnya ke root
    | aplikasi sehingga tersedia dalam perintah Artisan.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Zona Waktu Aplikasi
    |--------------------------------------------------------------------------
    |
    | Di sinilah Anda dapat menentukan zona waktu default untuk aplikasi Anda,
    | yang akan digunakan oleh fungsi tanggal dan waktu PHP. Zona waktu
    | diatur ke "UTC" secara default karena cocok untuk sebagian besar kasus.
    |
    */

    'timezone' => 'Asia/Jakarta',

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Lokal Aplikasi
    |--------------------------------------------------------------------------
    |
    | Lokal aplikasi menentukan lokal default yang akan digunakan
    | oleh metode terjemahan / lokalisasi Laravel. Opsi ini dapat
    | diatur ke lokal apa pun yang memiliki string terjemahan.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
