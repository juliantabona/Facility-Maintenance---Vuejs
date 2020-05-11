<?php

namespace App;

use Illuminate\Support\Str;
use App\Traits\CommonTraits;
use App\Traits\UssdServiceCodeTraits;
use Illuminate\Database\Eloquent\Model;

class UssdServiceCode extends Model
{
    use UssdServiceCodeTraits, CommonTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Service Code Details  */
        'shared_code', 'dedicated_code',

        /*  Ownership Information  */
        'owner_id', 'owner_type'

    ];

    /*
     *  Returns the owner of the service code
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
        return strtolower(Str::snake(class_basename($this)));
    }

}
