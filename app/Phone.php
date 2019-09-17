<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Traits\PhoneTraits;

Relation::morphMap([
    'user' => 'App\User',
    'store' => 'App\Store',
    'company' => 'App\Company',
]);

class Phone extends Model
{
    use PhoneTraits;

    protected $casts = [
        'calling_code' => 'array',
        'default' => 'boolean'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Phone Details  */
        'type', 'calling_code', 'number', 'provider', 'default',

        /*  Ownership Information  */
        'owner_id', 'owner_type'

    ];

    /**
     *  Get the owner from the morphTo relationship.
     *  Phones can be assigned to multiple types of
     *  owning resources e.g users, companies, stores,
     *  e.t.c
     */
    public function owner()
    {
        return $this->morphTo();
    }

    public function mobileMoneyAccount()
    {
        return $this->hasOne('App\Wallet', 'phone_id');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }

    /* ATTRIBUTES */
    
    public function setDefaultAttribute($value)
    {
        $this->attributes['default'] = ( ($value === 'true' || $value === '1') ? 1 : 0);
    }

}
