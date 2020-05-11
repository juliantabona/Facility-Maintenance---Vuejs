<?php

namespace App;

use Illuminate\Support\Str;
use App\Traits\CommonTraits;
use App\Traits\UssdServiceTraits;
use Illuminate\Database\Eloquent\Model;

class UssdService extends Model
{
    use UssdServiceTraits, CommonTraits;

    protected $with = ['serviceCode'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'builder' => 'array',
        'live_mode' => 'boolean', //  Return the following 1/0 as true/false
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Ussd Service Details  */
        'name', 'live_mode', 'offline_message',

        /*  Builder  */
        'builder',

        /*  Ownership Information  */
        'owner_id', 'owner_type'

    ];

    /*
     *  Returns the owner of the ussd session
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns ussd service code associated with this ussd service.
     */
    public function serviceCode()
    {
        return $this->morphOne('App\UssdServiceCode', 'owner');
    }

    /* ATTRIBUTES */

    protected $appends = [
        'resource_type',
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

}
