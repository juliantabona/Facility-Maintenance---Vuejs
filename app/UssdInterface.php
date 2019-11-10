<?php

namespace App;

use DB;
use App\AdvancedFilter\Dataviewer;
use App\Traits\UssdInterfaceTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'store' => 'App\Store',
]);

class UssdInterface extends Model
{
    use Dataviewer;
    use UssdInterfaceTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Basic Info  */
        'name', 'about_us', 'contact_us', 'call_to_action', 'code',

        /*  Ownership Info  */
        'owner_id', 'owner_type'

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Returns the owner of the ussd interface
     */
    public function owner()
    {
        return $this->morphTo();
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
 
 }
 