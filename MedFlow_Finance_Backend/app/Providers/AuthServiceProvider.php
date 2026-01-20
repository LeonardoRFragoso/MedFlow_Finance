<?php

namespace App\Providers;

use App\Models\Upload;
use App\Models\Record;
use App\Models\Report;
use App\Policies\UploadPolicy;
use App\Policies\RecordPolicy;
use App\Policies\ReportPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Upload::class => UploadPolicy::class,
        Record::class => RecordPolicy::class,
        Report::class => ReportPolicy::class,
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
