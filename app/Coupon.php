<?php

namespace App;

use App\Traits\CouponTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'store' => 'App\Store',
    'order' => 'App\Order',
    'product' => 'App\Product',
    'company' => 'App\Company',
    'invoice' => 'App\Invoice',
    'quotation' => 'App\Quotation',
]);

class Coupon extends Model
{
    use CouponTraits;

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date', 'end_date'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Discount Details  */
        'name', 'description', 'code', 'type', 'rate', 'meta', 'start_date', 'end_date',

        /*  Ownership Information  */
        'owner_id', 'owner_type',

    ];

    /* 
     *  Returns the owner of the coupon
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns all products that this coupon has been allocated to 
     */
    public function products()
    {
        return $this->morphedByMany('App\Product', 'owner', 'coupon_allocations')->withTimestamps();
    }

    /* 
     *  Returns recent activities owned by this coupon
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }

    /* ATTRIBUTES */

    protected $appends = [
        'resource_type'
    ];

    /* 
     *  Returns the resource type
     */
    public function getResourceTypeAttribute()
    {
        return strtolower(class_basename($this));
    }
    
}
