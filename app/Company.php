<?php

namespace App;

use DB;
use App\Traits\CompanyTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use Dataviewer;
    use SoftDeletes;
    use CompanyTraits;

    protected $table = 'companies';

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
        'website_link', 'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link', 'youtube_link'

    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Scope by type
     */
    public function scopeWhereType($query, $type)
    {
        return $query;
    }

    /*  
     *  Returns documents associated with this company. These are various files such as images,
     *  videos, files and so on. Basically any file/image/video the user wants to save to 
     *  this company is stored in this relation
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
     *  Returns phones associated with this company. This includes all
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
     *  Returns addresses associated with this company
     */
    public function addresses()
    {
        return $this->morphMany('App\Address', 'owner')->orderBy('created_at', 'desc');
    }

    /* 
     *  Returns the company settings
     */
    public function settings()
    {
        return $this->morphOne('App\Setting', 'owner');
    }

    /* 
     *  Returns all the users that are associated with this company. This includes associations
     *  were the user as admin, staff, customer, vendor e.t.c. Any association to this company 
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
     *  Returns users where the user is a customer
     */
    public function userCustomers()
    {
        return $this->users()->whereUserType('customer');
    }

    /* 
     *  Returns users where the user is a vendor
     */
    public function userVendors()
    {
        return $this->users()->whereUserType('vendor');
    }

    /* 
     *  Checks if a given user is an admin to the company
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


    /*************************************/
    /*  ECOMMERCE RELATED RELATIONSHIPS  */
    /*************************************/

    /* 
     *  Returns stores owned by this company
     */
    public function stores()
    {
        return $this->morphMany('App\Store', 'owner');
    }
    
    /* 
     *  Returns taxes owned by this company
     */
    public function taxes()
    {
        return $this->morphMany('App\Tax', 'owner');
    }

    /* 
     *  Returns discounts owned by this company
     */
    public function discounts()
    {
        return $this->morphMany('App\Discount', 'owner');
    }

    /* 
     *  Returns coupons owned by this company
     */
    public function coupons()
    {
        return $this->morphMany('App\Coupon', 'owner');
    }

    /* 
     *  Returns products owned by this company
     */
    public function products()
    {
        return $this->morphMany('App\Product', 'owner');
    }

    
    /*************************************/
    /*  BILLING RELATED RELATIONSHIPS    */
    /*************************************/

    /* 
     *  Returns quotations owned by this company
     */
    public function quotations()
    {
        return $this->morphMany('App\Quotation', 'owner');
    }

    /* 
     *  Returns quotations where this company is the customer
     */
    public function receivedQuotations()
    {
        return $this->morphMany('App\Quotation', 'customer');
    }

    /* 
     *  Returns invoices owned by this company
     */
    public function invoices()
    {
        return $this->morphMany('App\Invoice', 'owner');
    }

    /* 
     *  Returns invoices where this company is the customer
     */
    public function receivedInvoices()
    {
        return $this->morphMany('App\Invoice', 'customer');
    }


    /*************************************/
    /*  MISCELLANEOUS RELATIONSHIPS      */
    /*************************************/

    /* 
     *  Returns sms credits owned by this company
     */
    public function smsCredits()
    {
        return $this->morphMany('App\Sms', 'owner');
    }

    /* 
     *  Returns mobile phones owned by this company that are
     *  linked to mobile money accounts
     */
    public function wallets()
    {
        return $this->mobiles()->has('wallet')->with('wallet', 'createdBy');
    }

    /* 
     *  Returns lifecycles owned by this company
     */
    public function availableLifecycles()
    {
        return $this->morphMany('App\Lifecycle', 'owner');
    }

    /* 
     *  Returns lifecycles owned by this company for managing jobcards
     */
    public function jobcardLifecycles()
    {
        return $this->availableLifecycles()->where('type', 'jobcard');
    }

    /* 
     *  Returns priorities owned by this company.
     *  Examples are "low", "medium", "high", "urgent", "emergency"
     *  Note: Priorities can be used by multiple resources and are categorized 
     *  using the type attribute to identify and distinguish the relevant resources.
     */
    public function availablePriorities()
    {
        return $this->morphMany('App\Priority', 'owner');
    }

    /* 
     *  Returns priorities owned by this company for managing jobcards
     */
    public function jobcardPriorities()
    {
        return $this->availablePriorities()->where('type', 'jobcard');
    }

    /* 
     *  Returns cost centers owned by this company.
     *  Examples are "Manufacturing", "Contruction", "Maintenance"
     *  Note: Cost centers can be used by multiple resources and are categorized 
     *  using the type attribute to identify and distinguish the relevant resources.
     */
    public function availableCostCenters()
    {
        return $this->morphMany('App\CostCenter', 'owner');
    }

    /* 
     *  Returns cost centers owned by this company for managing jobcards
     */
    public function jobcardCostCenters()
    {
        return $this->availableCostCenters()->where('type', 'jobcard');
    }

    /* 
     *  Returns categories owned by this company.
     *  Examples are "Electrical", "Mechanical", "Construction", "Renovation"
     *  Note: Categories can be used by multiple resources and are categorized 
     *  using the type attribute to identify and distinguish the relevant resources.
     */
    public function availableCategories()
    {
        return $this->morphMany('App\Category', 'owner')->whereNull('parent_category_id');
    }

    /* 
     *  Returns categories owned by this company for managing jobcards
     */
    public function jobcardCategories()
    {
        return $this->availableCategories()->where('type', 'jobcard');
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
        'logo', 'address', 'resource_type', 'phone_list', 
        'last_approved_activity', 'is_approved', 'current_activity_status',
        'activity_count'
    ];

    /* 
     *  Returns the company logo
     */
    public function getLogoAttribute()
    {
        return $this->documents()->whereType('logo')->first();
    }

    /* 
     *  Returns the company default address
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
        return strtolower(class_basename($this));
    }

    /* 
     *  Returns the company phones separated with commas
     */
    public function getPhoneListAttribute()
    {
        $phoneList = '';
        $phones = $this->phones()->get();

        foreach ($phones as $key => $phone) {
            $phoneList .= ($key != 0 ? ', ' : '').'(+'.$phone['calling_code']['calling_code'].') '.$phone['number'];
        }

        return $phoneList;
    }

    /* 
     *  Returns the last approved activity
     */
    public function getLastApprovedActivityAttribute()
    {
        return $this->approvedActivities()->select('type', 'user_id', 'created_at')->first();
    }

    /* 
     *  Returns true/false if the company was approved
     */
    public function getIsApprovedAttribute()
    {
        return $this->last_approved_activity ? true : false;
    }

    /* 
     *  Returns the status of the company
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

}
