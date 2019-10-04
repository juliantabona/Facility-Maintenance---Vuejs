<?php

namespace App;

use App\Traits\CategoryTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'product' => 'App\Product',
    'jobcard' => 'App\Jobcard',
    'appointment' => 'App\Appointment',
]);

class Category extends Model
{
    use Dataviewer;
    use CategoryTraits;

    protected $with = ['sub_categories'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Category Details  */
        'name', 'description', 'parent_category_id', 'type',

        /*  Ownership Information  */
        'owner_id', 'owner_type',
        
    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Returns the sub-categories
     */
    public function sub_categories()
    {
        return $this->hasMany('App\Category', 'parent_category_id');
    }

    /* 
     *  Returns the owner of the category
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns all products that this category has been allocated to 
     */
    public function products()
    {
        return $this->morphedByMany('App\Product', 'owner', 'category_allocations');
    }

    /*
     *  Returns all jobcards that this category has been allocated to 
     */
    public function jobcards()
    {
        return $this->morphedByMany('App\Jobcard', 'owner', 'category_allocations');
    }

    /*
     *  Returns all appointments that this category has been allocated to 
     */
    public function appointments()
    {
        return $this->morphedByMany('App\Appointment', 'owner', 'category_allocations');
    }

    /* 
     *  Returns recent activities owned by this category
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
