<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use App\Helpers\AuthHelper;

class AuthHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Helper function untuk mengecek role pengguna
        view()->composer('*', function ($view) {
            $view->with('currentUserRole', AuthHelper::getUserRole());
        });
    }
}
