<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use App\Traits\StoreTraits;

Relation::morphMap([
    'company' => 'App\Company',
]);

class Store extends Model
{
    use Dataviewer;
    use StoreTraits;

    protected $with = ['phones'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        /*  Basic Info  */
        'name', 'abbreviation', 'description', 'type', 'industry',  
        
        /*  Account Info  */
        'email', 'additional_email', 'setup', 
        
        /*  Social Info  */
        'website_link', 'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link', 'youtube_link',

        /*  Ownership Info  */
        'owner_id', 'owner_type'
    ];

    protected $allowedFilters = [
        
    ];

    protected $allowedOrderableColumns = [
        
    ];

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
     *  Scope the documents by type
     */
    public function scopeWhereDocumentType($query, $type)
    {
        return $query->where('type', '=', $type);
    }

    /* 
     *  Returns documents categorized as files
     */
    public function files()
    {
        return $this->documents()->documentType('file');
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
     *  Scope the phones by type
     */
    public function scopeWherePhoneType($query, $type)
    {
        return $query->where('type', '=', $type);
    }

    /* 
     *  Returns phones categorized as mobile phones
     */
    public function mobiles()
    {
        return $this->phones()->phoneType('mobile');
    }

    /* 
     *  Returns phones categorized as telephones
     */
    public function telephones()
    {
        return $this->phones()->phoneType('tel');
    }

    /* 
     *  Returns phones categorized as fax numbers
     */
    public function fax()
    {
        return $this->phones()->phoneType('fax');
    }

    /* 
     *  Returns addresses associated with this store
     */
    public function addresses()
    {
        return $this->morphMany('App\Address', 'owner')->orderBy('created_at', 'desc');
    }

    /* 
     *  Returns the store settings
     */
    public function settings()
    {
        return $this->morphOne('App\Setting', 'owner');
    }

    /* 
     *  Returns all the users that are associated with this store. This includes associations
     *  were the user as admin, staff, client, vendor e.t.c. Any association to this company 
     *  will pass as a valid user to retrieve on this relationship. We can then filter our 
     *  results to be more specific (using a scope) e.g) Get all users where the user 
     *  is an admin.
     */
    public function users()
    {
        return $this->morphToMany('App\User', 'allocatable', 'user_allocations');
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
     *  Returns users where the user is a client
     */
    public function userClients()
    {
        return $this->users()->whereUserType('client');
    }

    /* 
     *  Returns users where the user is a vendor
     */
    public function userVendors()
    {
        return $this->users()->whereUserType('vendor');
    }

    /* 
     *  Checks if a given user is an admin to the store
     */
    public function isAdmin($user_id)
    {
        return ($this->admins()->whereUserId($user_id)->count()) ? true : false;
    }

    /* 
     *  Checks if a given user is a staff member to the company
     */
    public function isStaff($user_id)
    {
        return ($this->staff()->whereUserId($user_id)->count()) ? true : false;
    }

    /* 
     *  Checks if a given user is an admin or staff member to the company
     */
    public function isAdminOrStaff($user_id)
    {
        return ($this->isAdmin($user_id) || $this->isStaff($user_id));
    }

    /* 
     *  Returns the owner of the store
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /* 
     *  Returns products owned by this store
     */
    public function products()
    {
        return $this->morphMany('App\Product', 'owner');
    }

    /* 
     *  Returns orders owned by this store
     */
    public function orders()
    {
        return $this->morphMany('App\Order', 'owner');
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
        return $this->morphMany('App\Message', 'owner')->orderBy('messages.created_at', 'asc');
    }

    /* 
     *  Returns reviews made to this store
     */
    public function reviews()
    {
        return $this->morphMany('App\Review', 'owner')->orderBy('reviews.created_at', 'asc');
    }

    /* 
     *  Returns reviews made to this store
     */
    public function availableLifecycles()
    {
        return $this->morphMany('App\Lifecycle', 'lifecycleable');
    }

    public function interests()
    {
        return $this->hasMany('App\StoreInterests');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')->orderBy('created_at', 'desc');
    }

    /* ATTRIBUTES */

    protected $appends = [
        'logo', 'address', 'resource_type'
    ];

    /* 
     *  Returns the store logo
     */
    public function getLogoAttribute()
    {
        return $this->documents()->where('type', 'logo')->first();
    }

    /* 
     *  Returns the users default address
     */
    public function getAddressAttribute()
    {
        return $this->addresses()->where('default', 1)->first();
    }

    /* 
     *  Returns the resource type
     */
    public function getResourceTypeAttribute()
    {
        return str_to_lower(class_basename($this));
    }

}
