<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use App\Traits\CompanyTraits;
use DB;

class Company extends Model
{
    use SoftDeletes;
    use Dataviewer;
    use CompanyTraits;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'currency_type' => 'array',
    ];

    protected $table = 'companies';

    protected $with = ['phones'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Basic Info  */
        'name', 'abbreviation', 'description', 'date_of_incorporation', 'type', 'industry', 
        
        /*  Address Info  */
        'email', 'additional_email',
        
        /*  Social Info  */
        'website_link', 'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link', 'youtube_link',

        /*  Currency Info  */
        'currency_type'
    ];

    protected $allowedFilters = [
        'id', 'name', 'description', 'date_of_incorporation', 'type', 'address', 'country', 'province', 'city', 'postal_or_zipcode',
        'email', 'additional_email', 'website_link', 'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link',
        'bio', 'created_at',
    ];

    protected $allowedOrderableColumns = [
        'id', 'name', 'description', 'date_of_incorporation', 'type', 'address', 'country', 'province', 'city', 'postal_or_zipcode',
        'email', 'additional_email', 'website_link', 'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link',
        'bio', 'created_at',
    ];

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
        return $this->documents()->where('type', 'file');
    }

    /* 
     *  Returns phones associated with this company
     */
    public function phones()
    {
        return $this->morphMany('App\Phone', 'owner')->orderBy('created_at', 'desc');
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
        return $query->where('user_allocations.type', '=', $type);
    }

    /* 
     *  Returns users where the users by id
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

    /* 
     *  Returns products associated with this company
     */
    public function products()
    {
        return $this->morphMany('App\Product', 'productable');
    }

    /* 
     *  Get the company taxes
     */
    public function taxes()
    {
        return $this->morphMany('App\Tax', 'taxable');
    }

    /* 
     *  Get the company discounts
     */
    public function discounts()
    {
        return $this->morphMany('App\Discount', 'discountable');
    }

    /* 
     *  Get the company coupons
     */
    public function coupons()
    {
        return $this->morphMany('App\Coupon', 'owner');
    }

    /* 
     *  Returns stores associated with this company
     */
    public function stores()
    {
        return $this->hasMany('App\Store');
    }

    /**
     * Get the ecommerce orders
     */
    public function orders()
    {
        return $this->hasMany('App\Order', 'company_id');
    }

    /*  Get the jobcards created by this company, get them in relation to the company branches that created them
     *  A jobcard is a documentation of work to be done for a client. This documentation is made up of details
     *  describing the job, the client, the supplier, the contacts of both the client and supplier, as well
     *  as the history (Recent Activities) describing the series of events building the jobcard
     */

    public function smsCredits()
    {
        return $this->hasOne('App\Sms');
    }

    /**
     * Get the company associated lifecycle.
     */
    public function lifecycles()
    {
        return $this->hasMany('App\Lifecycle');
    }

    public function outgoingJobcards()
    {
        return $this->hasManyThrough('App\Jobcard', 'App\CompanyBranch', 'company_id', 'company_branch_id', 'id')
                //  Select everything and make the jobcard id the primary id
               ->select('*', 'jobcards.id as id');
    }

    public function incomingJobcards()
    {
        return $this->morphOne('App\Jobcard', 'client');
    }

    public function outgoingAppointments()
    {
        return $this->hasMany('App\Appointment', 'company_id');
    }

    public function companyDirectory()
    {
        return $this->belongsToMany('App\Company', 'company_directory', 'owning_company_id', 'company_id')
                    ->withPivot('id', 'type', 'owning_branch_id', 'owning_company_id')
                    //  Select everything and make the company id the primary id
                    ->select('*', 'companies.id as id', 'companies.type as type', 'company_directory.type as directory_type',
                             'companies.deleted_at as deleted_at', 'companies.created_at as created_at',
                             'companies.updated_at as updated_at')
                    ->withTimestamps();
    }

    public function userDirectory()
    {
        return $this->belongsToMany('App\User', 'user_directory', 'owning_company_id', 'user_id')
                    ->withPivot('id', 'type', 'owning_branch_id', 'owning_company_id')
                    //  Select everything and make the jobcard id the primary id
                    ->select('*', 'users.id as id', 'user_directory.type as directory_type',
                             'users.deleted_at as deleted_at', 'users.created_at as created_at',
                             'users.updated_at as updated_at')
                    ->withTimestamps();
    }

    public function companyClients()
    {
        return $this->companyDirectory()
                    ->where('company_directory.type', 'client');
    }

    public function companySuppliers()
    {
        return $this->companyDirectory()
                    ->where('company_directory.type', 'supplier');
    }
    /*
    public function userClients()
    {
        return $this->userDirectory()
                    ->where('user_directory.type', 'client');
    }

    public function userSuppliers()
    {
        return $this->userDirectory()
                    ->where('user_directory.type', 'supplier');
    }

    public function userStaff()
    {
        return $this->userDirectory()
                    ->where('user_directory.type', 'staff');
    }
    */

    public function quotations()
    {
        return $this->hasMany('App\Quotation');
    }

    //  Quotes where this company is the client
    public function incomingQuotations()
    {
        return $this->quotations()->where('client_id', $this->id);
    }

    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }

    //  Invoices where this company is the client
    public function incomingInvoices()
    {
        return $this->hasMany('App\Invoice', 'client_id');
    }

    public function wallets()
    {
        return $this->phones()->has('mobileMoneyAccount')->with('mobileMoneyAccount', 'createdBy');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }

    public function createdActivities()
    {
        return $this->recentActivities()->where('type', 'created');
    }

    public function approvedActivities()
    {
        return $this->recentActivities()->where('type', 'approved');
    }

    protected $appends = [
                            'logo', 'model_type', 'phone_list',
                            'last_approved_activity',
                            'has_approved',
                            'current_activity_status', 'activity_count',
                            //'incoming_quotation_count', 'outgoing_quotation_count',
                            //'incoming_invoice_count', 'outgoing_invoice_count',
                            'incoming_jobcard_count', 'outgoing_jobcard_count',
                            'outgoing_appointment_count',
                        ];

    //  Getter for the type of model
    public function getModelTypeAttribute()
    {
        return 'company';
    }

    public function getLogoAttribute()
    {
        return $this->documents()->where('type', 'logo')->first();
    }

    //  Getter for the phone list
    public function getPhoneListAttribute()
    {
        $phones = $this->phones()->get();
        $phoneList = '';

        foreach ($phones as $key => $phone) {
            $phoneList .= ($key != 0 ? ', ' : '').'(+'.$phone['calling_code']['calling_code'].') '.$phone['number'];
        }

        return $phoneList;
    }

    public function getLastApprovedActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('type', 'approved')->first();
    }

    public function gethasApprovedAttribute()
    {
        return $this->last_approved_activity ? true : false;
    }

    public function getCurrentActivityStatusAttribute()
    {
        //  If approved
        if ($this->has_approved) {
            return 'Approved';

        //  If draft
        } else {
            return 'Draft';
        }
    }

    public function getActivityCountAttribute()
    {
        $count = $this->recentActivities()->select(DB::raw('count(*) as total'))
                                          ->groupBy('owner_type')
                                          ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    public function getOutgoingQuotationCountAttribute()
    {
        $count = $this->quotations()->select(DB::raw('count(*) as total'))
                                    ->where('company_id', $this->id)
                                    ->groupBy('company_id')
                                    ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    public function getIncomingQuotationCountAttribute()
    {
        $count = $this->quotations()->select(DB::raw('count(*) as total'))
                                    ->where('client_id', $this->id)
                                    ->groupBy('client_id')
                                    ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    public function getOutgoingInvoiceCountAttribute()
    {
        $count = $this->invoices()->select(DB::raw('count(*) as total'))
                                    ->where('company_id', $this->id)
                                    ->groupBy('company_id')
                                    ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    public function getIncomingInvoiceCountAttribute()
    {
        $count = $this->invoices()->select(DB::raw('count(*) as total'))
                                  ->where('client_id', $this->id)
                                  ->groupBy('client_id')
                                  ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    public function getIncomingJobcardCountAttribute()
    {
        $count = $this->incomingJobcards()->select(DB::raw('count(*) as total'))
                                        ->where('client_id', $this->id)
                                        ->where('client_type', 'company')
                                        ->groupBy('client_id')
                                        ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    public function getOutgoingJobcardCountAttribute()
    {
        $count = $this->incomingJobcards()->select(DB::raw('count(*) as total'))
                                        ->where('company_id', $this->id)
                                        ->groupBy('company_id')
                                        ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    public function getOutgoingAppointmentCountAttribute()
    {
        $count = $this->outgoingAppointments()->select(DB::raw('count(*) as total'))
                                        ->where('company_id', $this->id)
                                        ->groupBy('company_id')
                                        ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    ///////////////////////////////////////////////////////////////////////////////////
    //                                                                              //
    //  EVERTHING BELOW THIS CAUTION IS NOT YET BEING USED BY THE SYSTEM            //
    //                                                                              //
    //////////////////////////////////////////////////////////////////////////////////

    public function creator()
    {
        return $this->morphMany('App\Creator', 'creatable');
    }

    /*  Get the profiles of users related to this company  Get them in relation to the company branches
     *  that created them. These profiles can be admins, staff members, company client contacts or company
     *  supplier contracts. Any user in the database is a profile
     */

    public function profiles()
    {
        return $this->hasMany('App\User');
    }

    /*  Get the cost centers related to this company. A cost center is a part of an organization to which
     *  costs may be charged for accounting purposes. Examples are "legal department", "accounting department",
     *  "research and development, "human resource", "quality assurance", "logistics", and "customer service"
     *  are considered as cost centers.
     */

    public function costCenters()
    {
        return $this->hasMany('App\CostCenter');
    }

    /*  Get the priorities related to this company Priorities are response time commitments to completing a task.
     *  Examples are "low", "medium", "high", "urgent", "emergency"
     */

    public function priorities()
    {
        return $this->hasMany('App\Priority');
    }

    /*  Get the categories related to this company. Categories are job sorting classes that help organise
     *  scheduled work. They are meant of organise work based on the kind of work to be done. Examples are
     *  "Electrical", "Mechanical", "Construction", "Renovation", "Maintenance & Repair", "Heating",
     *  "Ventilation", "Air-conditioning", "Painting", "Plumbing", "Cleaning"
     */

    public function categories()
    {
        return $this->hasMany('App\Category')->whereNull('parent_category_id');
    }
    /*  Get the categories related to this company. Categories are job sorting classes that help organise
     *  scheduled work. They are meant of organise work based on the kind of work to be done. Examples are
     *  "Electrical", "Mechanical", "Construction", "Renovation", "Maintenance & Repair", "Heating",
     *  "Ventilation", "Air-conditioning", "Painting", "Plumbing", "Cleaning"
     */

    public function tags()
    {
        return $this->hasMany('App\Tag');
    }

    public function branches()
    {
        return $this->hasMany('App\CompanyBranch');
    }

    public function assignedJobcards()
    {
        return $this->belongsToMany('App\Jobcard', 'jobcard_suppliers', 'supplier_id', 'jobcard_id')
                    ->withPivot('id', 'jobcard_id', 'supplier_id', 'amount', 'quotation_doc_url')
                    ->withTimestamps();
    }

    /**
     * Get the post's image.
     */
    public function image()
    {
        return $this->morphOne('App\Image', 'imageable');
    }
}
