<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('ADMIN', function ($user) {
            return $user->roles === 'ADMIN';
        });
        Gate::define('SUPERVISOR', function ($user) {
            return $user->roles === 'SUPERVISOR';
        });
        Gate::define('USER', function ($user) {
            return $user->roles === 'USER';
        });
        Gate::define('PINCAB', function ($user) {
            return $user->roles === 'PINCAB';
        });
        Gate::define('KADIV', function ($user) {
            return $user->roles === 'KADIV';
        });
        Gate::define('DIRUT', function ($user) {
            return $user->roles === 'DIRUT';
        });
        Gate::define('DIRBIS', function ($user) {
            return $user->roles === 'DIRBIS';
        });
        Gate::define('PATUH', function ($user) {
            return $user->roles === 'PATUH';
        });
        Gate::define('ADMIN_SDM', function ($user) {
            return $user->roles === 'ADMIN_SDM';
        });
    }
}
