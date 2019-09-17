<?php

namespace App\Providers;

use App\User;
use App\Store;
use App\Company;
use App\Product;                                                                                                        
use App\Policies\UserPolicy;
use App\Policies\StorePolicy;
use App\Policies\CompanyPolicy;
use App\Policies\ProductPolicy;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Store::class => StorePolicy::class,
        Company::class => CompanyPolicy::class,
        Product::class => ProductPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes(function ($router) {
            $router->forAccessTokens();
        });
    }
}
