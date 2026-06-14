<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Mahasiswa;
use App\Observers\MahasiswaObserver;

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

        // Registrasi Observer untuk Real-time Activity Log
        Mahasiswa::observe(MahasiswaObserver::class);
    }
}