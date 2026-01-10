<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Facade;

/**
 * Service Provider Aplikasi
 *
 * Service provider ini digunakan untuk mendaftarkan dan menginisialisasi
 * layanan-layanan utama dalam aplikasi e-commerce AKRAB.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Mendaftarkan layanan-layanan aplikasi
     */
    public function register(): void
    {
        //
    }

    /**
     * Menginisialisasi layanan-layanan aplikasi
     */
    public function boot(): void
    {
        // Menambahkan ekstensi blade.view untuk file tampilan
        View::addExtension('blade.view', 'blade');
    }

    /**
     * Mendaftarkan facade-facade aplikasi setelah loading
     */
    public function registerFacades(): void
    {
        // Mendaftarkan Facade Intervention Image jika diperlukan
        Facade::clearResolvedInstances();
    }
}
