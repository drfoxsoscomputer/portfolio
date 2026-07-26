<?php

namespace App\Providers;

use App\Models\Profile;
use App\Observers\ProfileObserver;
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
        // Register ProfileObserver from the Observers namespace
        Profile::observe(ProfileObserver::class);

        // Note: Morph map for polymorphic relationships is handled
        // in the Image model itself via its morphTo() method
    }
}