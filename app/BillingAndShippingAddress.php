<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'user' => 'App\User',
    'company' => 'App\Company'
]);

class BillingAndShippingAddress extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        /*  Basic Info  */
        'first_name', 'last_name',
        
        /*  Address Info  */
        'address_1', 'address_2', 'country', 'province', 'city', 'postal_or_zipcode', 
        
        /*  Account Info  */
        'email'
    ];

    /**
     * Get all of the owning documentable models.
     */
    public function trackable()
    {
        return $this->morphTo();
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->where('trackable_type', 'billing_and_shipping_address')
                    ->orderBy('created_at', 'desc');
    }
}
