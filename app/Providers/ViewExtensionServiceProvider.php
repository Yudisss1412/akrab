<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ViewExtensionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Tambahkan ekstensi .blade.view ke daftar ekstensi view (for backward compatibility)
        $this->app['view']->addExtension('blade.view', 'blade');

        // Standard .blade.php extension is already handled by Laravel by default
    }
}