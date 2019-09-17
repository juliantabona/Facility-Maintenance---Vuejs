<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DiscountTraits;

Relation::morphMap([
    'product' => 'App\Product'
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
        'name', 'description', 'type', 'rate', 'meta'
    ];

    /**
     * Get all of the owning refund models.
     */
    public function discountable()
    {
        return $this->morphTo();
    }

    /**
     * Get the owner from the morphTo relationship
     * This method returns a company/store
     */
    public function owner()
    {
        return $this->discountable();
    }

    /**
     * Get all of the products that are assigned this discount.
     */
    public function products()
    {
        return $this->morphedByMany('App\Product', 'discountable');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }
}
