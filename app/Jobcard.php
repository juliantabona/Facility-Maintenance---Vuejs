<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\AdvancedFilter\Dataviewer;

class Jobcard extends Model
{
    use SoftDeletes;
    use Dataviewer;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'start_date', 'end_date', 'step_id', 'priority_id', 'cost_center_id', 'company_branch_id',
        'category_id', 'client_id', 'is_public', 'select_contractor_id', 'img_url',
    ];

    protected $allowedFilters = [
        'id', 'title', 'description', 'start_date', 'end_date', 'created_at',

        // nested filters
        'priority.id', 'priority.name',
        'category.id', 'category.name',
        'costCenter.id', 'costCenter.name',
        'documents.count', 'documents.id', 'documents.name', 'documents.type', 'documents.mime', 'documents.size', 'documents.url', 'documents.created_at',
        'client.id', 'client.name', 'client.city', 'client.state_or_region', 'client.address', 'client.industry', 'client.type', 'client.website_link', 'client.phone_ext', 'client.phone_num', 'client.email', 'client.created_at',
        'contractorsList.count', 'contractorsList.id', 'contractorsList.name', 'contractorsList.city', 'contractorsList.state_or_region', 'contractorsList.address', 'contractorsList.industry', 'contractorsList.type', 'contractorsList.website_link', 'contractorsList.phone_ext', 'contractorsList.phone_num', 'contractorsList.email', 'contractorsList.created_at',
        'selectedContractors.id', 'selectedContractors.name', 'selectedContractors.city', 'selectedContractors.state_or_region', 'selectedContractors.address', 'selectedContractors.industry', 'selectedContractors.type', 'selectedContractors.website_link', 'selectedContractors.phone_ext', 'selectedContractors.phone_num', 'selectedContractors.email', 'selectedContractors.created_at',
    ];

    protected $orderable = [
        'id', 'title', 'description', 'start_date', 'end_date', 'created_at',
    ];

    public function categories()
    {
        return $this->morphMany('App\Category', 'category');
    }

    public function costCenters()
    {
        return $this->morphMany('App\CostCenter', 'costcenter');
    }

    public function priorities()
    {
        return $this->morphMany('App\Priority', 'priority');
    }

    public function contractorsList()
    {
        return $this->belongsToMany('App\Company', 'jobcard_contractors', 'jobcard_id', 'contractor_id')
                    ->withPivot('id', 'jobcard_id', 'contractor_id', 'amount', 'quotation_doc_id', 'selected')
                    ->withTimestamps();
    }

    public function statusLifecycle()
    {
        return $this->morphMany('App\FormTemplateAllocation', 'trackable');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->orderBy('created_at', 'desc');
    }

    ///////////////////////////////////////////////////////////////////////////////////
    //                                                                              //
    //  EVERTHING BELOW THIS CAUTION IS NOT YET BEING USED BY THE SYSTEM            //
    //                                                                              //
    //////////////////////////////////////////////////////////////////////////////////

    public function owningBranch()
    {
        return $this->belongsTo('App\CompanyBranch', 'company_branch_id');
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    public function client()
    {
        return $this->belongsTo('App\Company', 'client_id');
    }

    public function selectedContractors()
    {
        return $this->contractorsList()->where('selected', 1);
    }

    public function processFormStep()
    {
        return $this->belongsTo('App\ProcessFormSteps', 'step_id');
    }

    /*  Get the total count of all the people who viewed this jobcard. Make sure that
     *  we have distinct viewers meaning that we are not counting repeated records.
    */
    public function distinctViewersCount()
    {
        return $this->recentActivities()->distinct('who_created_id')->count('who_created_id');
    }

    /*
        public function progressSteps()
        {
            return $this->morphToMany('App\ProgressStatusSteps', 'progressable', 'progress_status', 'progressable_id', 'step_id');
        }

    */

    /*

    public function category()
    {
        return $this->belongsTo('App\JobcardCategory', 'category_id');
    }

    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }

    public function clientContacts()
    {
        return $this->belongsToMany('App\ClientContact', 'jobcard_contacts', 'jobcard_id', 'contact_id');
    }
    */

    protected $appends = ['createdBy', 'authourizedBy', 'deadline', 'deadlineArray', 'deadlineInWords', 'statusSummary'];

    //  Getter for calculating the deadline returned as array
    public function getCreatedByAttribute()
    {
        $publishingUser = $this->recentActivities->where('activity.type', 'created')->first();

        if (count($publishingUser)) {
            return $publishingUser->createdBy;
        }
    }

    //  Getter for calculating the deadline returned as array
    public function getAuthourizedByAttribute()
    {
        $authourizingUser = $this->recentActivities->where('activity.type', 'authourized')->first();

        if (count($authourizingUser)) {
            return $authourizingUser->createdBy;
        }
    }

    //  Getter for calculating the deadline returned as array
    public function getDeadlineAttribute()
    {
        return oq_jobcardDeadlineArray($this)[0].' '.oq_jobcardDeadlineArray($this)[1]
              .' '.(oq_jobcardDeadlineArray($this)[2] ? '' : 'ago');
    }

    //  Getter for calculating the deadline returned as array
    public function getDeadlineArrayAttribute()
    {
        return oq_jobcardDeadlineArray($this);
    }

    //  Getter for calculating the deadline returned as words
    public function getDeadlineInWordsAttribute()
    {
        return oq_jobcardDeadlineWords(oq_jobcardDeadlineArray($this));
    }

    //  Getter for getting the jobcard lifecycle status name
    public function getStatusSummaryAttribute()
    {
        //  What we want the summary to contain
        $lookfor = array('name', 'description');

        //  Get the jobcard lifecycle details
        $jobcardStatusLifecycle = $this->statusLifecycle->first();

        //  Get only the details of the current jobcard status
        $status = $jobcardStatusLifecycle->template['sections'][$jobcardStatusLifecycle->step - 1];

        //  Filter the jobcard status and get only the status details we want to look for
        $filteredStatusDetails = array_filter($status, function ($key) use ($lookfor) {
            return in_array($key, $lookfor);
        }, ARRAY_FILTER_USE_KEY);

        return $filteredStatusDetails;
    }
}
