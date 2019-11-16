<?php

namespace App;

use DB;
use App\Traits\StoreTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'account' => 'App\Account',
]);

class Store extends Model
{
    use Dataviewer;
    use StoreTraits;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'currency' => 'array',
        'support_ussd' => 'boolean', //  Return the following 1/0 as true/false
    ];

    protected $with = ['phones', 'emails', 'addresses'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Basic Info  */
        'name', 'abbreviation', 'description', 'type', 'industry',  
        
        /*  Account Info  */
        'setup', 
        
        /*  Social Info  */
        'website_link', 'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link', 'youtube_link',

        /*  Currency Info  */
        'currency',

        /*  Access Attributes  */
        'support_ussd',

        /*  Ownership Info  */
        'owner_id', 'owner_type'

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Scope:
     *  Return stores that support USSD access (Accessible by 2G Devices via USSD)
     */
    public function scopeSupportUssd($query)
    {
        return $query->whereSupportUssd(1);
    }

    /* 
     *  Scope:
     *  Return stores that don't support USSD access (Accessible by 2G Devices via USSD)
     */
    public function scopeDontSupportUssd($query)
    {
        return $query->whereSupportUssd(0);
    }

    /* 
     *  Scope:
     *  Return stores that don't support USSD access (Accessible by 2G Devices via USSD)
     */
    public function scopePopular($query)
    {
        return $query->withCount('orders')->orderByDesc('orders_count');
    }

    /* 
     *  Scope:
     *  Return stores that match a given name or contain similar tags
     */
    public function scopeSearch($query, $name)
    {
        return $query->orWhere('name', $name)->orWhere('abbreviation', $name);
    }

    /* 
     *  Returns the owner of the store. In this case it returns the
     *  Account that owns this store
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*  
     *  Returns documents associated with this store. These are various files such as images,
     *  videos, files and so on. Basically any file/image/video the user wants to save to 
     *  this store is stored in this relation
     */

    public function documents()
    {
        return $this->morphMany('App\Document', 'owner');
    }

    /* 
     *  Returns documents categorized as files
     */
    public function files()
    {
        return $this->documents()->whereType('file');
    }

    /* 
     *  Returns phones associated with this store. This includes all
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
     *  Returns addresses associated with this store
     */
    public function addresses()
    {
        return $this->morphMany('App\Address', 'owner')->orderBy('created_at', 'desc');
    }

    /* 
     *  Returns emails associated with this store
     */
    public function emails()
    {
        return $this->morphMany('App\Email', 'owner')->orderBy('created_at', 'desc');
    }

    /* 
     *  Returns all the contacts that are associated with this store. We can filter our 
     *  results to be more specific (using a scope) e.g) Get all contacts that are 
     *  customers, vendors or that use a particular number or email.
     */
    public function contacts()
    {
        return $this->morphToMany('App\Contact', 'owner', 'contact_allocations');
    }

    public function contactsWithMobilePhone($mobile = null)
    {
        return $this->contacts()->withMobilePhone($mobile);
    }

    public function customerContacts()
    {
        return $this->contacts()->where('is_customer', 1);
    }

    public function customerContactsWithMobilePhone($mobile = null)
    {
        return $this->customerContacts()->withMobilePhone($mobile);
    }

    public function vendorContacts()
    {
        return $this->contacts()->where('is_vendor', 1);
    }

    public function vendorContactsWithMobilePhone($mobile = null)
    {
        return $this->vendorContacts()->withMobilePhone($mobile);
    }

    /* 
     *  Returns all the users that are associated with this store. This includes associations
     *  were the user as admin, staff, customer, vendor e.t.c. Any association to this store 
     *  will pass as a valid user to retrieve on this relationship. We can then filter our 
     *  results to be more specific (using a scope) e.g) Get all users where the user 
     *  is an admin.
     */
    public function users()
    {
        return $this->morphToMany('App\User', 'owner', 'user_allocations');
    }

    /* 
     *  Scope the users by type
     */
    public function scopeWhereUserType($query, $type)
    {
        //  If multiple type provided
        if( is_array($type) ){

            return $query->whereIn('user_allocations.type', $type);

        //  If single type provided
        }else{

            return $query->where('user_allocations.type', $type);
        }

        //  Otherwise return query as is
        return $query;
    }

    /* 
     *  Scope the users by id
     */
    public function scopeWhereUserId($query, $id)
    {
        return $query->where('user_allocations.user_id', '=', $id);
    }

    /* 
     *  Returns users where the user is an admin
     */
    public function admins()
    {
        return $this->users()->whereUserType('admin');
    }

    /* 
     *  Returns users where the user is a staff member
     */
    public function staff()
    {
        return $this->users()->whereUserType('staff');
    }

    /* 
     *  Checks if a given user is an admin to the store
     */
    public function isAdmin($user_id)
    {
        return ($this->admins()->whereUserId($user_id)->count()) ? true : false;
    }

    /* 
     *  Checks if a given user is a staff member to the store
     */
    public function isStaff($user_id)
    {
        return ($this->staff()->whereUserId($user_id)->count()) ? true : false;
    }

    /* 
     *  Checks if a given user is an admin or staff member to the store
     */
    public function isAdminOrStaff($user_id)
    {
        return ($this->isAdmin($user_id) || $this->isStaff($user_id));
    }

    /* 
     *  Returns the USSD Interface owned by this store
     */
    public function ussdInterface()
    {
        return $this->morphOne('App\UssdInterface', 'owner');
    }

    /* 
     *  Returns products owned by this store. This includes both
     *  products available and not available for stores that 
     *  support USSD accessibility. Basically this will 
     *  return ALL the products linked to this store
     */
    public function products()
    {
        return $this->morphMany('App\Product', 'owner');
    }

    /* 
     *  Return products available for USSD access (Accessible by 2G Devices via USSD)
     */
    public function ussdProducts()
    {
        return $this->products()->where('available_on_ussd', 1);
    }

    /* 
     *  Returns orders owned by this store
     */
    public function orders()
    {
        return $this->morphMany('App\Order', 'merchant');
    }
    
    /* 
     *  Returns orders owned by this store that belong 
     *  to a specific contact id
     */
    public function contactOrders($contact_id = null)
    {
        return $this->orders()->where(function ($query) use ($contact_id) {
            $query->orWhere('customer_id', $contact_id)
                  ->orWhere('reference_id', $contact_id);
        });
    }

    /* 
     *  Returns taxes owned by this store
     */
    public function taxes()
    {
        return $this->morphMany('App\Tax', 'owner');
    }

    /* 
     *  Returns discounts owned by this store
     */
    public function discounts()
    {
        return $this->morphMany('App\Discount', 'owner');
    }

    /* 
     *  Returns coupons owned by this store
     */
    public function coupons()
    {
        return $this->morphMany('App\Coupon', 'owner');
    }

    /* 
     *  Returns messages sent to this store
     */
    public function messages()
    {
        return $this->morphMany('App\Message', 'owner')->latest();
    }

    /* 
     *  Returns reviews sent to this store
     */
    public function reviews()
    {
        return $this->morphMany('App\Review', 'owner')->latest();
    }

    /* 
     *  Returns the store settings
     */
    public function settings()
    {
        return $this->morphOne('App\Setting', 'owner');
    }

    /*************************************/
    /*  BILLING RELATED RELATIONSHIPS    */
    /*************************************/

    /* 
     *  Returns invoices owned by this store
     */
    public function invoices()
    {
        return $this->morphMany('App\Invoice', 'owner');
    }


    /*************************************/
    /*  MISCELLANEOUS RELATIONSHIPS      */
    /*************************************/

    /* 
     *  Returns lifecycles owned by this store
     */
    public function availableLifecycles()
    {
        return $this->morphMany('App\Lifecycle', 'owner');
    }

    /* 
     *  Returns lifecycles owned by this store for managing orders
     */
    public function orderLifecycles()
    {
        return $this->availableLifecycles()->where('type', 'order');
    }

    /* 
     *  Returns categories owned by this store.
     *  Examples are "Electrical", "Mechanical", "Construction", "Renovation"
     *  Note: Categories can be used by multiple resources and are categorized 
     *  using the type attribute to identify and distinguish the relevant resources.
     */
    public function availableCategories()
    {
        return $this->morphMany('App\Category', 'owner')->whereNull('parent_category_id');
    }

    /* 
     *  Returns categories owned by this store for managing products
     */
    public function productCategories()
    {
        return $this->availableCategories()->where('type', 'product');
    }

    /* 
     *  Returns recent activities owned by this store
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }

    /* 
     *  Returns store creation activity
     */
    public function createdActivities()
    {
        return $this->recentActivities()->whereType('created');
    }

    /* 
     *  Returns store approval activity
     */
    public function approvedActivities()
    {
        return $this->recentActivities()->whereType('approved');
    }


    /* ATTRIBUTES */

    protected $appends = [
        'logo', 'phone_list', 'default_mobile', 'default_email', 'default_address', 'average_rating', 
        'resource_type', 'phone_list', 'last_approved_activity', 'is_approved', 
        'current_activity_status', 'activity_count'
    ];

    /* 
     *  Returns the store logo
     */
    public function getLogoAttribute()
    {
        return $this->documents()->where('type', 'logo')->first();
    }

    /* 
     *  Returns the store phones separated with commas
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
     *  Returns the store default mobile phone
     */
    public function getDefaultMobileAttribute()
    {
        return $this->mobiles()->where('default', 1)->first();
    }

    /* 
     *  Returns the store default email
     */
    public function getDefaultEmailAttribute()
    {
        return $this->emails()->where('default', 1)->first();
    }

    /* 
     *  Returns the store default address
     */
    public function getDefaultAddressAttribute()
    {
        return $this->addresses()->where('default', 1)->first();
    }

    /* 
     *  Returns the store average rating
     */
    public function getAverageRatingAttribute()
    {
        //  Get the store reviews
        $reviews = $this->reviews ?? [];

        //  If we have any reviews
        if( $reviews ){
            
            //  Return the average of the ratings combined
            return collect( $reviews )->avg('rating');

        }
    }

    /* 
     *  Returns the resource type
     */
    public function getResourceTypeAttribute()
    {
        return strtolower(class_basename($this));
    }

    /* 
     *  Returns the last approved activity
     */
    public function getLastApprovedActivityAttribute()
    {
        return $this->approvedActivities()->select('type', 'user_id', 'created_at')->first();
    }

    /* 
     *  Returns true/false if the store was approved
     */
    public function getIsApprovedAttribute()
    {
        return $this->last_approved_activity ? true : false;
    }

    /* 
     *  Returns the status of the store
     */
    public function getCurrentActivityStatusAttribute()
    {
        return $this->is_approved ? 'Approved' : 'Draft';
    }

    /* 
     *  Returns the total number of activities
     */
    public function getActivityCountAttribute()
    {
        $count = $this->recentActivities()->select(DB::raw('count(*) as total'))->groupBy('owner_type')->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    public function setSupportUssdAttribute($value)
    {
        $this->attributes['support_ussd'] = ( ($value == 'true' || $value == '1') ? 1 : 0);
    }

}
