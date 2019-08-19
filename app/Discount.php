<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'product' => 'App\Product',
    'shop' => 'App\Shop',
]);

class Discount extends Model
{
    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'rate', 'meta'
    ];

    /**
     * Get all of the owning refund models.
     */
    public function discountable()
    {
        return $this->morphTo();
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->where('trackable_type', 'discount')
                    ->orderBy('created_at', 'desc');
    }
}
