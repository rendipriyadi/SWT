<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        // Set locale default untuk Carbon
        Carbon::setLocale('id');
        
        // Pastikan format untuk bulan dan hari tersedia dalam bahasa Indonesia
        setlocale(LC_TIME, 'id_ID.utf8', 'id_ID', 'id');
        
        // Force HTTPS in production for correct email URLs
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
    }
}
