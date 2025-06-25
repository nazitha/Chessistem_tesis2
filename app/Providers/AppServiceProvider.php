<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Mail\BrevoMailer;
use App\Services\BrevoMailService;
use App\Helpers\PermissionHelper;

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
        // Hacer que PermissionHelper esté disponible globalmente en las vistas
        \Blade::directive('permission', function ($expression) {
            return "<?php if(\\App\\Helpers\\PermissionHelper::canViewModule($expression)): ?>";
        });

        \Blade::directive('endpermission', function () {
            return "<?php endif; ?>";
        });

        // Registrar PermissionHelper como una función global
        \Blade::directive('canCreate', function ($expression) {
            return "<?php if(\\App\\Helpers\\PermissionHelper::canCreate($expression)): ?>";
        });

        \Blade::directive('canUpdate', function ($expression) {
            return "<?php if(\\App\\Helpers\\PermissionHelper::canUpdate($expression)): ?>";
        });

        \Blade::directive('canDelete', function ($expression) {
            return "<?php if(\\App\\Helpers\\PermissionHelper::canDelete($expression)): ?>";
        });

        \Blade::directive('canViewModule', function ($expression) {
            return "<?php if(\\App\\Helpers\\PermissionHelper::canViewModule($expression)): ?>";
        });
    }
}
