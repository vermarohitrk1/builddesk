<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Interfaces\LeadRepositoryInterface::class,
            \App\Repositories\Eloquent\LeadRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\OrganisationRepositoryInterface::class,
            \App\Repositories\Eloquent\OrganisationRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\EmployeeRepositoryInterface::class,
            \App\Repositories\Eloquent\EmployeeRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
