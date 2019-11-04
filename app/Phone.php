<?php

namespace App;

use App\Traits\PhoneTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'user' => 'App\User',
    'store' => 'App\Store',
    'account' => 'App\Account',
    'contact' => 'App\Contact',
]);

class Phone extends Model
{
    use PhoneTraits;

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

        /*  Phone Details  */
        'type', 'calling_code', 'number', 'provider', 'default', 'verified',

        /*  Ownership Information  */
        'owner_id', 'owner_type'

    ];

    /* 
     *  Scope to only return verified records e.g
     *  Returning only verified phones
     */
    public function scopeVerified($query)
    {
        return $query->where('verified', 1);
    }

    /* 
     *  Returns the owner of the phone
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /* 
     *  Returns the mobile money account associated with this phone
     */
    public function wallet()
    {
        return $this->hasOne('App\Wallet', 'phone_id');
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
