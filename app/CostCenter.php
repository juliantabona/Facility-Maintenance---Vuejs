<?php

namespace App;

use App\Traits\CostCenterTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'jobcard' => 'App\Jobcard',
]);

class CostCenter extends Model
{
    use Dataviewer;
    use CostCenterTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Priority Details  */
        'name', 'description', 'type',

        /*  Ownership Information  */
        'owner_id', 'owner_type',
        
    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Returns the owner of the cost center
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns all jobcards that this cost center has been allocated to 
     */
    public function jobcards()
    {
        return $this->morphedByMany('App\Jobcard', 'owner', 'cost_center_allocations')->withTimestamps();
    }

    /* 
     *  Returns recent activities owned by this cost center
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
