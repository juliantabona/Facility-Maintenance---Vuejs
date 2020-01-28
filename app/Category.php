<?php

namespace App;

use App\Traits\CommonTraits;
use App\Traits\CategoryTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'store' => 'App\Store',
    'product' => 'App\Product',
    'jobcard' => 'App\Jobcard',
    'appointment' => 'App\Appointment',
]);

class Category extends Model
{
    use Dataviewer, CommonTraits, CategoryTraits;

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
     *  Scope:
     *  Returns categories that are allocatable to stores by default.
     *  This means that they were created for every store to use in 
     *  order to categorise themselves. Basically we return any 
     *  category of type "store" that is not owned by any store
     */
    public function scopeDefaultForStores($query)
    {
        return $query->whereType('store')->whereNull('owner_id');
    }

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
    public function stores()
    {
        return $this->morphedByMany('App\Store', 'owner', 'category_allocations')->withTimestamps();
    }

    /*
     *  Returns all products that this category has been allocated to 
     */
    public function products()
    {
        return $this->morphedByMany('App\Product', 'owner', 'category_allocations')->withTimestamps();
    }

    /*
     *  Returns all jobcards that this category has been allocated to 
     */
    public function jobcards()
    {
        return $this->morphedByMany('App\Jobcard', 'owner', 'category_allocations')->withTimestamps();
    }

    /*
     *  Returns all appointments that this category has been allocated to 
     */
    public function appointments()
    {
        return $this->morphedByMany('App\Appointment', 'owner', 'category_allocations')->withTimestamps();
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
