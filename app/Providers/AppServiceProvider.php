<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Advertisement;
use App\Models\Gong;
use App\Observers\AdvertisementObserver;
use App\Observers\GongObserver;

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
        // Register model observers
        Advertisement::observe(AdvertisementObserver::class);
        Gong::observe(GongObserver::class);
    }
}
