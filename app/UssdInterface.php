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
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'live_mode' => 'boolean', //  Return the following 1/0 as true/false
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Basic Info  */
        'name', 'about_us', 'contact_us', 'call_to_action', 'code', 'live_mode',

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
        return $this->morphToMany('App\Product', 'owner', 'product_allocations')->withTimestamps()->orderBy('product_allocations.arrangement');
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
        'customer_access_code', 'team_access_code', 'resource_type'
     ];
 
     /* 
      *  Returns the resource type
      */
     public function getCustomerAccessCodeAttribute()
     {
        return '*'.config('app.CUSTOMER_USSD_CODE').'*'.$this->code.'#';
     }
 
     /* 
      *  Returns the resource type
      */
     public function getTeamAccessCodeAttribute()
     {
        return '*'.config('app.MERCHANT_USSD_CODE').'*'.$this->code.'#';
     }
 
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
 