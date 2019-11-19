<?php

namespace App;

use App\Traits\PriorityTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'jobcard' => 'App\Jobcard',
]);

class Priority extends Model
{
    use Dataviewer;
    use PriorityTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Priority Details  */
        'name', 'description', 'color_code', 'type',

        /*  Ownership Information  */
        'owner_id', 'owner_type',
        
    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Returns the owner of the priority
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns all jobcards that this priority has been allocated to 
     */
    public function jobcards()
    {
        return $this->morphedByMany('App\Jobcard', 'owner', 'priority_allocations')->withTimestamps();
    }

    /* 
     *  Returns recent activities owned by this priority
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
