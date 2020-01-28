<?php

namespace App;

use App\Traits\CommonTraits;
use App\Traits\MessageTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'user' => 'App\User',
    'store' => 'App\Store',
    'order' => 'App\Order',
]);

class Message extends Model
{
    use Dataviewer, CommonTraits, MessageTraits;

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Message Details  */
        'text', 'meta', 'user_id',

        /*  Ownership Information  */
        'owner_id', 'owner_type',
        
    ];
    
    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    protected $with = ['user'];

    /* 
     *  Returns the recipient of the message
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /* 
     *  Returns the sender of the message
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
