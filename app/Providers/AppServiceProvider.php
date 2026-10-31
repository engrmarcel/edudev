<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\User;
use App\Services\Audit\AuditLogger;
use App\Services\Branding\BrandManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuditLogger::class);
        $this->app->singleton(BrandManager::class);
    }

    public function boot(): void
    {
        // Catch lazy-loading (N+1) and silently-discarded attributes during
        // development and tests. Production behaviour is unchanged.
        Model::shouldBeStrict(! $this->app->isProduction());

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        // Super admins pass every permission check. All other roles are
        // checked against explicit permissions.
        Gate::before(function (User $user) {
            return $user->hasRole(Role::SuperAdmin->value) ? true : null;
        });

        View::composer('*', function ($view) {
            static $brand = null;
            $view->with('brand', $brand ??= app(BrandManager::class));
        });
    }
}
