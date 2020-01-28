<?php

namespace App;

use App\Traits\CommonTraits;
use App\Traits\TrackingDetailTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'fulfillment' => 'App\Fulfillment',
]);

class TrackingDetail extends Model
{
    use CommonTraits, TrackingDetailTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Tracking Details  */
        'url', 'number', 'carrier',

        /*  Ownership Information  */
        'owner_id', 'owner_type',

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Returns the owner of the tracking details
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /* 
     *  Returns recent activities owned by this tracking details
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
        return strtolower(Str::snake(class_basename($this)));
    }
}
