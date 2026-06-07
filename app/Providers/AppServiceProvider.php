<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- WAJIB IMPORT INI

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // FIX: Memaksa Laravel menggunakan HTTPS saat berjalan di environment production (Railway)
        if (config('app.env') === 'production' || isset($_SERVER['HTTPS']) || (env('APP_URL') && str_contains(env('APP_URL'), 'https://'))) {
            URL::forceScheme('https');
        }
    }
}