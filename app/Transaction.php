<?php

namespace App;

use App\Traits\TransactionTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'invoice' => 'App\Invoice',
]);

class Transaction extends Model
{
    use Dataviewer, TransactionTraits;

    protected $casts = [
        'meta' => 'array',
        'automatic' => 'boolean', //  Return the following 1/0 as true/false
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Transaction Details  */
        'type', 'status', 'automatic', 'payment_type', 'payment_amount',

        /*  Ownership Information  */
        'owner_id', 'owner_type',

        /*  Meta Data  */
        'meta'

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Returns the owner of the transaction
     *  In most cases this returns an invoice
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /* 
     *  Returns recent activities owned by this transaction
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

    public function setAutomaticAttribute($value)
    {
        $this->attributes['automatic'] = ( ($value == 'true' || $value == '1') ? 1 : 0);
    }

}
