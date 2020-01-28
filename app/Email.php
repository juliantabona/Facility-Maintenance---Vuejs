<?php

namespace App;

use App\Traits\EmailTraits;
use App\Traits\CommonTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'user' => 'App\User',
    'store' => 'App\Store',
    'account' => 'App\Account',
    'contact' => 'App\Contact',
]);

class Email extends Model
{
    use Dataviewer, CommonTraits, EmailTraits;
    
    protected $casts = [
        
        'default' => 'boolean', //  Return the following 1/0 as true/false
        'verified' => 'boolean', //  Return the following 1/0 as true/false
        
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Email Details  */
        'email', 'default', 'verified',

        /*  Ownership Information  */
        'owner_id', 'owner_type'

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Scope to only return verified records e.g
     *  Returning only verified emails
     */
    public function scopeVerified($query)
    {
        return $query->where('verified', 1);
    }
    
    /*  
     *  Returns a verification token associated with this phone
     */

    public function verification()
    {
        return $this->morphOne('App\Verification', 'owner');
    }

    /* 
     *  Returns the owner of the email
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

    public function setVerifiedAttribute($value)
    {
        $this->attributes['verified'] = ( ($value == 'true' || $value == '1') ? 1 : 0);
    }

}
