<?php

namespace App;

use Illuminate\Support\Str;
use App\Traits\CommonTraits;
use App\Traits\MobileStoreTraits;
use Illuminate\Database\Eloquent\Model;

class MobileStore extends Model
{
    use MobileStoreTraits, CommonTraits;

    protected $with = ['serviceCode'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'metadata' => 'array',
        'live_mode' => 'boolean',       //  Return the following 1/0 as true/false
        'allow_delivery' => 'boolean',  //  Return the following 1/0 as true/false
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Session Details  */
        'about_us', 'contact_us', 'call_to_action', 'allow_delivery',
        'delivery_policy', 'live_mode', 'offline_message',

        /*  Meta Data  */
        'metadata',

        /*  Ownership Information  */
        'owner_id', 'owner_type'

    ];

    /*
     *  Returns the owner of the mobile store
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /* 
     *  Returns products allocated to this mobile store
     */
    public function products()
    {
       return $this->morphToMany('App\Product', 'owner', 'product_allocations')->withTimestamps()->orderBy('product_allocations.arrangement');
    }

    /*
     *  Returns ussd service code associated with this mobile store.
     */
    public function serviceCode()
    {
        return $this->morphOne('App\UssdServiceCode', 'owner');
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

    public function setLiveModeAttribute($value)
    {
        $this->attributes['live_mode'] = ( ($value == 'true' || $value === '1') ? 1 : 0);
    }

    public function setAllowDeliveryAttribute($value)
    {
        $this->attributes['allow_delivery'] = ( ($value == 'true' || $value === '1') ? 1 : 0);
    }

}
