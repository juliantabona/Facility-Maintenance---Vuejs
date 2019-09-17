<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ReviewTraits;

Relation::morphMap([
    'store' => 'App\Store',
    'order' => 'App\Order',
    'product' => 'App\Product'
]);

class Review extends Model
{
    use ReviewTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rating', 'text', 'user_id', 'reviewable_id', 'reviewable_type'
    ];

    protected $with = ['user'];

    /**
     * Review can be assigned to multiple resources including
     * Stores, Products, Orders, e.t.c
     */
    public function reviewable()
    {
        return $this->morphTo();
    }

    /**
     * Get the owner from the morphTo relationship
     * This method returns a company/store
     */
    public function owner()
    {
        return $this->reviewable();
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }
}
