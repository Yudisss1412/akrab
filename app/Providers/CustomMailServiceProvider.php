<?php

namespace App\Providers;

use Illuminate\Mail\MailServiceProvider;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class CustomMailServiceProvider extends MailServiceProvider
{
    public function boot()
    {
        // Override pengiriman email saat development jika konfigurasi tidak valid
        if (app()->environment('local', 'development')) {
            Mail::alwaysTo('debug@example.com', 'Debug User');
        }
    }
    
    public function register()
    {
        parent::register();
    }
}