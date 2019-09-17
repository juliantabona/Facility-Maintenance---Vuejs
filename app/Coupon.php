<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CouponTraits;

Relation::morphMap([
    'product' => 'App\Product'
]);

class Coupon extends Model
{
    use CouponTraits;

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'code', 'type', 'rate', 'meta', 'start_date', 'end_date'
    ];

    /**
     *  Get the owner from the morphTo relationship.
     *  Coupons can be assigned to multiple types of
     *  owning resources e.g companies, stores,
     *  e.t.c
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     *  Coupons can be assigned to multiple types of
     *  resources e.g products
     */
    public function products()
    {
        return $this->morphedByMany('App\Product', 'allocatable', 'coupon_allocations');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }
}
