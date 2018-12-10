<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\AdvancedFilter\Dataviewer;

class Company extends Model
{
    use SoftDeletes;
    use Dataviewer;

    protected $table = 'companies';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'city', 'state_or_region', 'address', 'industry', 'type', 'website_link',
        'profile_doc_url', 'phone_ext', 'phone_num', 'email', 'logo_url',
    ];

    protected $allowedFilters = [
        'id', 'name', 'description', 'city', 'state_or_region', 'address', 'industry', 'type', 'website_link',
        'profile_doc_url', 'phone_ext', 'phone_num', 'email', 'created_at',
    ];

    protected $orderable = [
        'id', 'name', 'description', 'city', 'state_or_region', 'address', 'industry', 'type', 'website_link',
        'profile_doc_url', 'phone_ext', 'phone_num', 'email', 'created_at',
    ];

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
     *  contractor contracts. Any user in the database is a profile
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
        return $this->morphMany('App\CostCenter', 'costcenter');
    }

    /*  Get the priorities related to this company Priorities are response time commitments to completing a task.
     *  Examples are "low", "medium", "high", "urgent", "emergency"
     */

    public function priorities()
    {
        return $this->morphMany('App\Priority', 'priority');
    }

    /*  Get the categories related to this company. Categories are job sorting classes that help organise
     *  scheduled work. They are meant of organise work based on the kind of work to be done. Examples are
     *  "Electrical", "Mechanical", "Construction", "Renovation", "Maintenance & Repair", "Heating",
     *  "Ventilation", "Air-conditioning", "Painting", "Plumbing", "Cleaning"
     */

    public function categories()
    {
        return $this->morphMany('App\Category', 'category');
    }

    /*  Get the company branches related to this company
     *
     */

    public function branches()
    {
        return $this->hasMany('App\CompanyBranch');
    }

    /*  Get the process forms related to this company.  A process form in basically a staged process in which
     *  a company can follow to achieve a set of tasks. These processes involve collecting and monitoring data.
     *  When we ask for a process form, we are asking the database to get the main tree that holds all the steps
     *  of how data is going to be stored for that company
    */

    public function processForms()
    {
        return $this->hasMany('App\ProcessForm');
    }

    /*  Get all the recent activities relating to this company, Get them in relation to the company branches that
     *  created them Recent activities are anything that happens that needs to be recorded mainly for accountability
     *  purposes. Examples are activities such as creating, updating, trashing, deleting reources that the company
     *  has a resource can be a user, client, contractor, jobcard, document, e.t.c
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->orderBy('created_at', 'desc');
    }

    public function companyDirectory()
    {
        return $this->belongsToMany('App\Company', 'company_directory', 'owning_company_id', 'company_id')
                    ->withPivot('id', 'type', 'owning_branch_id', 'owning_company_id')
                    ->withTimestamps();
    }

    public function clients()
    {
        return $this->companyDirectory()
                    ->where('company_directory.type', 'client');
    }

    public function contractors()
    {
        return $this->companyDirectory()
                    ->where('company_directory.type', 'contractor');
    }

    public function userDirectory()
    {
        return $this->belongsToMany('App\User', 'user_directory', 'owning_company_id', 'user_id')
                    ->withPivot('id', 'type', 'owning_branch_id', 'owning_company_id')
                    ->withTimestamps();
    }

    public function staff()
    {
        return $this->userDirectory()
                    ->where('user_directory.type', 'staff');
    }

    /*
    public function recentActivities()
    {
        return $this->hasManyThrough('App\RecentActivity', 'App\CompanyBranch');
    }
    */

    /*  Get the clients for this company. A client is basically another company that this company is doing work for.
     *  A client can be stored without necessary having work to be done for them, but stored for profilling purposes.
     *  This could be useful in the case of prospective clients.

    public function clients()
    {
        return $this->belongsToMany('App\Company', 'company_clients', 'company_id', 'client_id')
                    ->withPivot('client_id', 'company_id', 'who_created_id')
                    ->withTimestamps();
    }
    */

    /*  Get the contractors for this company. A contractor is basically another company that this company is
     *  subcontracting work. A contractor can be stored without necessary having work to be done by them, but stored
     *  for profilling purposes. This could be useful in the case of prospective contractors.


    public function contractors()
    {
        return $this->belongsToMany('App\Company', 'company_contractors', 'company_id', 'contractor_id')
                    ->withPivot('contractor_id', 'company_id', 'who_created_id')
                    ->withTimestamps();
    }
    */

    /*  Get the contacts for this company. A contact is basically users that this company is linked to. This link may
     *  be that the contact is a staff member, a client contact, a contractor contact, or just an individual on their own
     */

    /*  Get the jobcards created by this company, get them in relation to the company branches that created them
     *  A jobcard is a documentation of work to be done for a client. This documentation is made up of details
     *  describing the job, the client, the contractor, the contacts of both the client and contractor, as well
     *  as the history (Recent Activities) describing the series of events building the jobcard
     */

    public function jobcards()
    {
        return $this->hasManyThrough('App\Jobcard', 'App\CompanyBranch', 'company_id', 'company_branch_id', 'id')
                //  Select everything and make the jobcard id the primary id
               ->select('*', 'jobcards.id as id');
    }

    /*  Get the jobcards assigned to this contractor company, in this case this company is the one that has been
     *  sub-contracted for the works and we would like to get access to all the jobcards that it has been listed
     *  for. These assigned jobcards DO NOT mean that the contractor was eventually SELECTED for the job but
     *  means that they were atleast meantioned on the list of POTENTIAL contractors
     */

    public function assignedJobcards()
    {
        return $this->belongsToMany('App\Jobcard', 'jobcard_contractors', 'contractor_id', 'jobcard_id')
                    ->withPivot('id', 'jobcard_id', 'contractor_id', 'amount', 'quotation_doc_url')
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

    /*
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'who_created_id');
    }
    */

    /*

    public function jobcards()
    {
        return $this->belongsToMany('App\Jobcard', 'jobcard_contacts', 'contact_id', 'jobcard_id');
    }

    public function priorities()
    {
        return $this->hasMany('App\JobcardPriority');
    }

    public function costCenter()
    {
        return $this->hasMany('App\JobcardCostCenter');
    }

    public function category()
    {
        return $this->hasMany('App\JobcardCategory');
    }

    public function statuses()
    {
        return $this->hasMany('App\JobcardStatus');
    }
    */
}
