<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use App\Traits\AddressTraits;

Relation::morphMap([
    'user' => 'App\User',
    'store' => 'App\Store',
    'company' => 'App\Company',
]);

class Address extends Model
{
    use Dataviewer;
    use AddressTraits;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'default' => 'boolean',
        'meta' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Address Details  */
        'address_1', 'address_2', 'country', 'province', 'city', 'postal_or_zipcode', 'postal_or_zipcode', 'default',

        /*  Ownership Information  */
        'owner_id', 'owner_type',

        /*  Meta Data  */
        'meta'

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /**
     *  Get the owner from the morphTo relationship.
     *  Addresses can be assigned to multiple types of
     *  owning resources e.g users, companies, stores,
     *  e.t.c
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /* ATTRIBUTES */

    public function setDefaultAttribute($value)
    {
        $this->attributes['default'] = ( ($value === 'true' || $value === '1') ? 1 : 0);
    }

}
