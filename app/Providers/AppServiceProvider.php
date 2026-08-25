<?php

namespace App\Providers;

use App\Contracts\ContactNotifierInterface;
use App\Services\NotificationContactNotifier;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ContactNotifierInterface::class, NotificationContactNotifier::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
