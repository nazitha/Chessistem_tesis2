<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Mail\BrevoMailer;
use App\Services\BrevoMailService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BrevoMailService::class, function ($app) {
            return new BrevoMailService();
        });

        $this->app->singleton('mail.brevo', function ($app) {
            return new BrevoMailer($app->make(BrevoMailService::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
