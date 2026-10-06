<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // Paksa semua URL yang di-generate pakai HTTPS kalau APP_URL memang https
        // (perlu untuk kasus tunneling via ngrok, dimana koneksi internal ke
        // php artisan serve tetap HTTP meskipun publiknya HTTPS)
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}