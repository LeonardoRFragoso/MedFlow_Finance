<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Definir gates para permissões
        Gate::define('view-audit-logs', function ($user) {
            return $user->hasPermission('audit.read');
        });

        Gate::define('manage-users', function ($user) {
            return $user->hasPermission('user.create');
        });

        Gate::define('manage-settings', function ($user) {
            return $user->hasPermission('settings.update');
        });
    }
}
