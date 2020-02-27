<?php

namespace App;

use App\Traits\CommonTraits;
use App\Traits\DiscountTraits;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use CommonTraits, DiscountTraits;
    
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Discount Details  */
        'name', 'description', 'type', 'rate', 'metadata',

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
        return $this->morphedByMany('App\Product', 'owner', 'discount_allocations')->withTimestamps();
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
