<?php

namespace App;

use App\Traits\CommonTraits;
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
    use Dataviewer, ContactTraits, CommonTraits;
    
    protected $casts = [
        'is_vendor' => 'boolean', //  Return the following 1/0 as true/false
        'is_customer' => 'boolean', //  Return the following 1/0 as true/false
        'is_individual' => 'boolean', //  Return the following 1/0 as true/false
    ];

    protected $with = ['phones', 'emails', 'addresses'];

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
        return $this->morphedByMany('App\Store', 'owner', 'contact_allocations')->withTimestamps();
    }

    /*
     *  Returns all the orders where this contact is listed
     *  as a customer or reference
     */
    public function orders()
    {
        return $this->hasMany('App\Order', 'customer_id');
    }

    /* 
     *  Scope:
     *  Return contacts that have phones
     */
    public function scopeWithPhone($query, $phone = null)
    {
         /*  If we have a phone specified  */
         if($phone){
            
            /*  Return any contact using the provided phone number  */
            return $query->whereHas('phones', function (Builder $query) use( $phone ){
                        $query->where('calling_code', $phone['calling_code'])
                                ->where('number', $phone['number']);
                    });

        /*  If no phone was specified  */
        }else{
            
            /*  Otherwise only return contacts using phones  */
            return $query->whereHas('phones');

        }
    }

    /* 
     *  Scope:
     *  Return contacts that have mobile phones
     */
    public function scopeWithMobilePhone($query, $phone = null)
    {
        return $query->withPhone($phone)->whereHas('phones', function (Builder $query){
                    $query->where('type', 'mobile');
                });
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

    protected $appends = [
        'type', 'phone_list', 'default_mobile', 'default_email', 'default_address', 'resource_type'
    ];

    /* 
     *  Returns the contact type
     */
    public function getTypeAttribute()
    {
        return $this->is_individual ? 'Individual' : 'Company/Organization';
    }

    /* 
     *  Returns the contact phones separated with commas
     */
    public function getPhoneListAttribute()
    {
        $phoneList = '';
        $phones = $this->phones()->whereIn('type', ['mobile', 'tel'])->get();

        foreach ($phones as $key => $phone) {

            /*  Merge the calling code and phone number  */
            $phoneList .= ($key != 0 ? ', ' : '').'(+'.$phone['calling_code'].') '.$phone['number'];

            /*  If this is not the last item add "," otherwise nothing  */
            $phoneList .= (next($phones)) ? ', ' : '';

        }

        return $phoneList;
    }

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
