<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\AdvancedFilter\Dataviewer;
use App\Traits\CompanyTraits;

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
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'date_of_incorporation', 'type', 'address', 'country', 'provience', 'city', 'postal_or_zipcode',
        'email', 'additional_email', 'website_link', 'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link',
        'bio', 'currency_type',
    ];

    protected $allowedFilters = [
        'id', 'name', 'description', 'date_of_incorporation', 'type', 'address', 'country', 'provience', 'city', 'postal_or_zipcode',
        'email', 'additional_email', 'website_link', 'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link',
        'bio', 'created_at',
    ];

    protected $orderable = [
        'id', 'name', 'description', 'date_of_incorporation', 'type', 'address', 'country', 'provience', 'city', 'postal_or_zipcode',
        'email', 'additional_email', 'website_link', 'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link',
        'bio', 'created_at',
    ];

    /*  Get the jobcards created by this company, get them in relation to the company branches that created them
     *  A jobcard is a documentation of work to be done for a client. This documentation is made up of details
     *  describing the job, the client, the supplier, the contacts of both the client and supplier, as well
     *  as the history (Recent Activities) describing the series of events building the jobcard
     */

    /**
     * Get the company associated lifecycle.
     */
    public function lifecycles()
    {
        return $this->hasMany('App\Lifecycle');
    }

    public function jobcards()
    {
        return $this->hasManyThrough('App\Jobcard', 'App\CompanyBranch', 'company_id', 'company_branch_id', 'id')
                //  Select everything and make the jobcard id the primary id
               ->select('*', 'jobcards.id as id');
    }

    public function clientJobcards()
    {
        return $this->morphOne('App\Jobcard', 'client');
    }

    /*  Get the process forms related to this company.  A process form in basically a staged process in which
     *  a company can follow to achieve a set of tasks. These processes involve collecting and monitoring data.
     *  When we ask for a process form, we are asking the database to get the main tree that holds all the steps
     *  of how data is going to be stored for that company
    */

    public function formTemplate()
    {
        return $this->hasMany('App\FormTemplate');
    }

    public function companyDirectory()
    {
        return $this->belongsToMany('App\Company', 'company_directory', 'owning_company_id', 'company_id')
                    ->withPivot('id', 'type', 'owning_branch_id', 'owning_company_id')
                    //  Select everything and make the jobcard id the primary id
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

    public function productsOrServices()
    {
        return $this->hasMany('App\ProductOrService');
    }

    public function taxes()
    {
        return $this->hasMany('App\Tax');
    }

    public function quotations()
    {
        return $this->hasMany('App\Quotation');
    }

    public function clientQuotations()
    {
        return $this->hasMany('App\Quotation', 'client_id');
    }

    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }

    public function clientInvoices()
    {
        return $this->hasMany('App\Invoice', 'client_id');
    }

    public function phones()
    {
        return $this->morphMany('App\Phone', 'trackable')
                    ->orderBy('created_at', 'desc');
    }

    public function wallets()
    {
        return $this->phones()->has('mobileMoneyAccount')->with('mobileMoneyAccount', 'createdBy');
    }

    /**
     * Get the companies settings.
     */
    public function settings()
    {
        return $this->morphOne('App\Setting', 'trackable');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->orderBy('created_at', 'desc');
    }

    public function createdActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('type', 'created');
    }

    public function approvedActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('type', 'approved');
    }

    protected $appends = [
                            'model_type', 'phone_list',
                            'last_approved_activity',
                            'has_approved',
                            'current_activity_status', 'activity_count', 'quotation_count', 'invoice_count',
                        ];

    //  Getter for the type of model
    public function getModelTypeAttribute()
    {
        return 'company';
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
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('type', 'approved')->first();
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
        $count = $this->recentActivities()->where('trackable_id', $this->id)
                                          ->select(DB::raw('count(*) as total'))
                                          ->groupBy('trackable_type')
                                          ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    public function getQuotationCountAttribute()
    {
        $count = $this->clientQuotations()->select(DB::raw('count(*) as total'))
                                          ->where('client_id', $this->id)
                                          ->groupBy('client_id')
                                          ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    public function getInvoiceCountAttribute()
    {
        $count = $this->clientInvoices()->select(DB::raw('count(*) as total'))
                                        ->where('client_id', $this->id)
                                        ->groupBy('client_id')
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

    /*  Get the documents relating to this company. These are various files such as company profiles,
     *  scanned files, images and so on. Basically any file the user wants to save to this company is
     *  stored in this relation
     */

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
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
        return $this->hasMany('App\Category');
    }

    public function branches()
    {
        return $this->hasMany('App\CompanyBranch');
    }

    public function staff()
    {
        return $this->userDirectory()
                    ->where('user_directory.type', 'staff');
    }

    public function assignedJobcards()
    {
        return $this->belongsToMany('App\Jobcard', 'jobcard_suppliers', 'supplier_id', 'jobcard_id')
                    ->withPivot('id', 'jobcard_id', 'supplier_id', 'amount', 'quotation_doc_url')
                    ->withTimestamps();
    }

    public function logos()
    {
        return $this->documents()->where('type', 'logo');
    }

    public function logo()
    {
        return $this->logos()->take(1);
    }

    public function quotation()
    {
        return $this->documents()->where('type', 'quotation');
    }
}
