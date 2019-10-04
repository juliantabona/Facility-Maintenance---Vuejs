<?php

namespace App;

use App\Traits\DiscountTraits;
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

class Discount extends Model
{
    use DiscountTraits;
    
    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Discount Details  */
        'name', 'description', 'type', 'rate', 'meta',

        /*  Ownership Information  */
        'owner_id', 'owner_type',

    ];

    /* 
     *  Returns the owner of the discount
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns all products that this discount has been allocated to 
     */
    public function products()
    {
        return $this->morphedByMany('App\Product', 'owner', 'discount_allocations');
    }

    /* 
     *  Returns recent activities owned by this discount
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
