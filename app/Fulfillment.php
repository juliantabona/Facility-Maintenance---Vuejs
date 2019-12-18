<?php

namespace App;

use App\Traits\FulfillmentTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'order' => 'App\Order',
]);

class Fulfillment extends Model
{
    use FulfillmentTraits;

    protected $with = ['trackingDetails'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'item_lines' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Fulfillment Details  */
        'notes', 
        
        /*  Item Info  */
        'item_lines', 
        
        /*  Recipient Info  */
        'recipient_name', 'recipient_contact',

        /*  Ownership Information  */
        'owner_id', 'owner_type',

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Returns the owner of the fulfillment
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns the fulfillment tracking details
     */
    public function trackingDetails()
    {
        return $this->morphOne('App\TrackingDetail', 'owner');
    }

    /* 
     *  Returns recent activities owned by this fulfillment
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
