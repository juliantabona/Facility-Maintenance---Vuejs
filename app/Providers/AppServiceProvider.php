<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
            To Help With Our HATEOAS (HAL) API - I include the following as suggested by Laravel
            ------------------------------------------------------------------------------------
            If you would like to disable the wrapping of the outer-most resource, you may use the  
            "withoutWrapping" method on the base resource class. Typically, you should call this 
            method from your AppServiceProvider or another service provider that is loaded on 
            every request to your application:
            Reference: https://laravel.com/docs/5.7/eloquent-resources#concept-overview
        */
        Resource::withoutWrapping();

        Relation::morphMap([
            'account' => 'App\Account',
            'address' => 'App\Address',
            'category' => 'App\Category',
            'contact' => 'App\Contact',
            'cost_center' => 'App\CostCenter',
            'coupon' => 'App\Coupon',
            'discount' => 'App\Discount',
            'document' => 'App\Document',
            'email' => 'App\Email',
            'fulfillment' => 'App\Fulfillment',
            'invoice' => 'App\Invoice',
            'message' => 'App\Message',
            'order' => 'App\Order',
            'phone' => 'App\Phone',
            'priority' => 'App\Priority',
            'product' => 'App\Product',
            'quotation' => 'App\Quotation',
            'recent_activity' => 'App\RecentActivity',
            'review' => 'App\Review',
            'review' => 'App\Review',
            'setting' => 'App\Setting',
            'store' => 'App\Store',
            'tag' => 'App\Tag',
            'tax' => 'App\Tax',
            'tracking_detail' => 'App\TrackingDetail',
            'transaction' => 'App\Transaction',
            'user' => 'App\User',
            'ussd_interface' => 'App\UssdInterface',
            'ussd_session' => 'App\UssdSession',
            'variable' => 'App\Variable',
            'verification' => 'App\Verification'
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
