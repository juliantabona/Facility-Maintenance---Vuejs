<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use App\Traits\CommentTraits;

Relation::morphMap([
    'store' => 'App\Store',
    'product' => 'App\Product',
    'order' => 'App\Order',
]);

class Comment extends Model
{
    use Dataviewer;
    use CommentTraits;

    protected $casts = [
        'from_customer' => 'boolean',
        'from_staff' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text', 'type', 'from_customer', 'from_staff', 'user_id', 'trackable_id', 'trackable_type',
    ];
    
    protected $allowedFilters = [
        'id', 'text', 'user_id', 'created_at',
    ];

    protected $orderable = [
        'id', 'text', 'user_id', 'created_at',
    ];

    protected $with = ['user'];

    /**
     * Get all of the orders that are assigned this tag.
     */
    public function orders()
    {
        return $this->morphedByMany('App\Order', 'taggable');
    }

    /**
     * Get all of the stores that are assigned this comment.
     */
    public function stores()
    {
        return $this->morphedByMany('App\Store', 'taggable');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function rating()
    {
        return $this->morphOne('App\Rating', 'trackable');
    }

    /* ATTRIBUTES */

    protected $appends = ['rating_value'];

    public function getRatingValueAttribute()
    {
        $rating = $this->rating;

        if( $rating ){
            return intval($rating->value);
        }
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->where('trackable_type', 'comment')
                    ->orderBy('created_at', 'desc');
    }

    public function setFromCustomerAttribute($value)
    {
        $this->attributes['from_customer'] = ( ($value === 'true' || $value === '1') ? 1 : 0);
    }

    public function setFromStaffAttribute($value)
    {
        $this->attributes['from_staff'] = ( ($value === 'true' || $value === '1') ? 1 : 0);
    }
}
