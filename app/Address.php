<?php

namespace App;

use App\Traits\CommonTraits;
use App\Traits\AddressTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use Dataviewer, CommonTraits, AddressTraits;
    
    protected $casts = [
        'metadata' => 'array',
        'default' => 'boolean', //  Return the following 1/0 as true/false
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Address Details  */
        'address_1', 'address_2', 'country', 'province', 'city', 'postal_or_zipcode', 'default',

        /*  Ownership Information  */
        'owner_id', 'owner_type',

        /*  Meta Data  */
        'metadata'

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Returns the owner of the address
     */
    public function owner()
    {
        return $this->morphTo();
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

    public function setDefaultAttribute($value)
    {
        $this->attributes['default'] = ( ($value == 'true' || $value == '1') ? 1 : 0);
    }

}
