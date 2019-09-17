<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use App\Traits\MessageTraits;

Relation::morphMap([
    'store' => 'App\Store',
    'product' => 'App\Product',
    'order' => 'App\Order',
]);

class Message extends Model
{
    use Dataviewer;
    use MessageTraits;

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text', 'meta', 'user_id', 'messageable_id', 'messageable_type'
    ];
    
    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    protected $with = ['user'];

    /**
     * Get all of the owning refund models.
     */
    public function messageable()
    {
        return $this->morphTo();
    }

    /**
     * Get the recipient from the morphTo relationship
     * This method returns any resource that the Message
     * Was being sent to e.g. A Message can be sent to a
     * Store, Order, User, e.t.c
     */
    public function recipient()
    {
        return $this->messageable();
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
