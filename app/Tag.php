<?php

namespace App;

use App\Traits\TagTraits;
use App\Traits\CommonTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'product' => 'App\Product',
]);

class Tag extends Model
{
    use Dataviewer, CommonTraits, TagTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Tag Details  */
        'name', 'type',

        /*  Ownership Information  */
        'owner_id', 'owner_type',

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Returns the owner of the tag
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns all products that this tag has been allocated to 
     */
    public function products()
    {
        return $this->morphedByMany('App\Product', 'owner', 'tag_allocations')->withTimestamps();
    }

    /* 
     *  Returns recent activities owned by this tag
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