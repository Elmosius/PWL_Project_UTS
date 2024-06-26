<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

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
        Gate::define('admin', function (User $user) {
            return $user->id_role === '1';
        });

        Gate::define('kaprodi', function (User $user) {
            return ($user->id_role === '2');
        });

        Gate::define('adminorkaprodi', function (User $user) {
            return ($user->id_role !== '3');
        });

        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();
    }
}
