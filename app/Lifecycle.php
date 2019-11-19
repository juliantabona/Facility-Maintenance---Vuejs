<?php

namespace App;

use App\Traits\LifecycleTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'store' => 'App\Store',
    'order' => 'App\Order',
    'company' => 'App\Company',
    'jobcard' => 'App\Jobcard',
]);

class Lifecycle extends Model
{
    use Dataviewer;
    use LifecycleTraits;

    protected $casts = [
        'stages' => 'array',

        'default' => 'boolean', //  Return the following 1/0 as true/false
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Lifecycle Details  */
        'stages', 'default', 'type',

        /*  Ownership Information  */
        'owner_id', 'owner_type',

    ];

    /* 
     *  Returns the owner of the lifecycle
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns all jobcards that this lifecycle has been allocated to 
     */
    public function jobcards()
    {
        return $this->morphedByMany('App\Jobcard', 'owner', 'lifecycle_allocations')->withTimestamps();
    }

    /*
     *  Returns all orders that this lifecycle has been allocated to 
     */
    public function orders()
    {
        return $this->morphedByMany('App\Order', 'owner', 'lifecycle_allocations')->withTimestamps();
    }

    /* 
     *  Returns recent activities owned by this lifecycle
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

    public function setDefaultAttribute($value)
    {
        $this->attributes['default'] = ( ($value == 'true' || $value == '1') ? 1 : 0);
    }

}
