<?php

namespace App;

use App\Traits\TaxTraits;
use App\Traits\CommonTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use Dataviewer, CommonTraits, TaxTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Tax Details  */
        'name', 'abbreviation', 'description', 'rate',

        /*  Ownership Information  */
        'owner_id', 'owner_type',

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Returns the owner of the tax
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns all products that this tax has been allocated to 
     */
    public function products()
    {
        return $this->morphedByMany('App\Product', 'owner', 'tax_allocations')->withTimestamps();
    }

    /* 
     *  Returns recent activities owned by this tax
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
