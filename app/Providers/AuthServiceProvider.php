<?php

namespace App\Providers;

use App\Tax;
use App\User;
use App\Order;
use App\Phone;
use App\Store;                                                                                                        
use App\Coupon;   
use App\Account;
use App\Product;                                                                                                        
use App\Invoice;
use App\Discount;                                                                                                     
use App\Quotation;                                                                                                        
use App\UssdInterface;                                                                                                        
use App\Policies\TaxPolicy;
use App\Policies\UserPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PhonePolicy;
use App\Policies\StorePolicy;
use App\Policies\CouponPolicy;
use Laravel\Passport\Passport;
use App\Policies\AccountPolicy;
use App\Policies\ProductPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\DiscountPolicy;
use App\Policies\QuotationPolicy;
use App\Policies\UssdInterfacePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Tax::class => TaxPolicy::class,
        User::class => UserPolicy::class,
        Order::class => OrderPolicy::class,
        Phone::class => PhonePolicy::class,
        Store::class => StorePolicy::class,
        Coupon::class => CouponPolicy::class,
        Account::class => AccountPolicy::class,
        Product::class => ProductPolicy::class,
        Invoice::class => InvoicePolicy::class,
        Discount::class => DiscountPolicy::class,
        Quotation::class => QuotationPolicy::class,
        UssdInterface::class => UssdInterfacePolicy::class,
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
