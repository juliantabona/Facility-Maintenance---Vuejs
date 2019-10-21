<?php

namespace App;

use DB;
use App\AdvancedFilter\Dataviewer;
use App\Traits\UssdInterfaceTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'company' => 'App\Company',
]);

class UssdInterface extends Model
{
    use Dataviewer;
    use UssdInterfaceTraits;

    protected $casts = [
        
        'default' => 'boolean', //  Return the following 1/0 as true/false

    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Basic Info  */
        'name', 'description', 'live_mode',

        /*  Ownership Info  */
        'store_id'

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Returns the owner of the ussd interface
     */
     public function owner()
     {
         return $this->belongsTo('App\Store', 'store_id');
     }
 
    /* 
     *  Returns products allocated to this ussd interface
     */
     public function products()
     {
        return $this->morphToMany('App\Product', 'owner', 'product_allocations');
     }
 
     /* 
      *  Returns recent activities owned by this phone
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
     
     public function setLiveModeAttribute($value)
     {
         $this->attributes['live_mode'] = ( ($value == 'true' || $value == '1') ? 1 : 0);
     }
 
 }
 