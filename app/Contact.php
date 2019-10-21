<?php

namespace App;

use App\Traits\ContactTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'store' => 'App\Store',
    'account' => 'App\Account',
]);

class Contact extends Model
{
    use Dataviewer, ContactTraits;
    
    protected $casts = [
        'is_vendor' => 'boolean', //  Return the following 1/0 as true/false
        'is_customer' => 'boolean', //  Return the following 1/0 as true/false
        'is_individual' => 'boolean', //  Return the following 1/0 as true/false
    ];

    protected $with = ['phones'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Basic Info  */
        'name', 'is_vendor', 'is_customer',  'is_individual'

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /*
     *  Returns all stores that this contacts has been allocated to
     */
    public function stores()
    {
        return $this->morphedByMany('App\Store', 'owner', 'contact_allocations');
    }

    /* 
     *  Returns phones associated with this contact. This includes all
     *  types of phones such as telephones, mobiles and fax numbers.
     *  We can then filter our results to be more specific (using a scope) 
     *  e.g) Get only mobile phones
     */
    public function phones()
    {
        return $this->morphMany('App\Phone', 'owner')->orderBy('created_at', 'desc');
    }
    /* 
     *  Returns phones categorized as mobile phones
     */
    public function mobiles()
    {
        return $this->phones()->whereType('mobile');
    }

    /* 
     *  Returns phones categorized as telephones
     */
    public function telephones()
    {
        return $this->phones()->whereType('tel');
    }

    /* 
     *  Returns phones categorized as fax numbers
     */
    public function fax()
    {
        return $this->phones()->whereType('fax');
    }

    /* 
     *  Returns addresses associated with this contact
     */
    public function addresses()
    {
        return $this->morphMany('App\Address', 'owner')->orderBy('created_at', 'desc');
    }

    /* 
     *  Returns emails associated with this contact
     */
    public function emails()
    {
        return $this->morphMany('App\Email', 'owner')->orderBy('created_at', 'desc');
    }

    /* 
     *  Returns the contacts settings
     */
    public function settings()
    {
        return $this->morphOne('App\Setting', 'owner');
    }

    /* 
     *  Checks if a given contact has subcribed to receive targeted
     *  marketing from the owner.
     */
    public function isSubscriber()
    {
        return ($this->settings['has_subscribed'] == 'true') ? true : false;
    }

    /* 
     *  Returns recent activities owned by this contact
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }

    /*  Attributes */

    protected $appends = ['default_mobile', 'default_email', 'default_address', 'resource_type'];

    /* 
     *  Returns the contacts default mobile phone
     */
    public function getDefaultMobileAttribute()
    {
        return $this->mobiles()->where('default', 1)->first();
    }

    /* 
     *  Returns the contacts default email
     */
    public function getDefaultEmailAttribute()
    {
        return $this->emails()->where('default', 1)->first();
    }

    /* 
     *  Returns the contacts default address
     */
    public function getDefaultAddressAttribute()
    {
        return $this->addresses()->where('default', 1)->first();
    }

    /* 
     *  Returns the resource type
     */
    public function getResourceTypeAttribute()
    {
        return strtolower(class_basename($this));
    }

    public function setIsVendorAttribute($value)
    {
        $this->attributes['is_vendor'] = ( ($value == 'true' || $value === '1') ? 1 : 0);
    }

    public function setIsCustomerAttribute($value)
    {
        $this->attributes['is_customer'] = ( ($value == 'true' || $value === '1') ? 1 : 0);
    }

    public function setIsIndividualAttribute($value)
    {
        $this->attributes['is_individual'] = ( ($value == 'true' || $value == '1') ? 1 : 0);
    }


}
