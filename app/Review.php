<?php

namespace App;

use App\Traits\CommonTraits;
use App\Traits\ReviewTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'store' => 'App\Store',
    'order' => 'App\Order',
    'product' => 'App\Product'
]);

class Review extends Model
{
    use Dataviewer, CommonTraits, ReviewTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Review Details  */
        'rating', 'text', 'user_id',

        /*  Ownership Information  */
        'owner_id', 'owner_type',
        
    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    protected $with = ['user'];

    /* 
     *  Returns the recipient of the review
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /* 
     *  Returns the sender of the review
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /* 
     *  Returns recent activities owned by this message
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
