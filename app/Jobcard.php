<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\AdvancedFilter\Dataviewer;
use App\Traits\JobcardTraits;

Relation::morphMap([
    'user' => 'App\User',
    'company' => 'App\Company',
]);

class Jobcard extends Model
{
    use SoftDeletes;
    use Dataviewer;
    use JobcardTraits;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date', 'end_date',
    ];

    protected $with = [
        'client.phones',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'start_date', 'end_date', 'company_branch_id', 'company_id', 'client_id', 'client_type', 'is_public',
    ];

    protected $allowedFilters = [
        'id', 'title', 'description', 'start_date', 'end_date', 'company_branch_id', 'company_id', 'client_id', 'is_public', 'created_at',

        // nested filters
        'priority.id', 'priority.name',
        'category.id', 'category.name',
        'costCenter.id', 'costCenter.name',
        'documents.count', 'documents.id', 'documents.name', 'documents.type', 'documents.mime', 'documents.size', 'documents.url', 'documents.created_at',
        'client.id', 'client.name', 'client.city', 'client.state_or_region', 'client.address', 'client.industry', 'client.type', 'client.website_link', 'client.phone_ext', 'client.phone_num', 'client.email', 'client.created_at',
        'suppliersList.count', 'suppliersList.id', 'suppliersList.name', 'suppliersList.city', 'suppliersList.state_or_region', 'suppliersList.address', 'suppliersList.industry', 'suppliersList.type', 'suppliersList.website_link', 'suppliersList.phone_ext', 'suppliersList.phone_num', 'suppliersList.email', 'suppliersList.created_at',
        'selectedSuppliers.id', 'selectedSuppliers.name', 'selectedSuppliers.city', 'selectedSuppliers.state_or_region', 'selectedSuppliers.address', 'selectedSuppliers.industry', 'selectedSuppliers.type', 'selectedSuppliers.website_link', 'selectedSuppliers.phone_ext', 'selectedSuppliers.phone_num', 'selectedSuppliers.email', 'selectedSuppliers.created_at',
    ];

    protected $orderable = [
        'id', 'title', 'description', 'start_date', 'end_date', 'created_at',
    ];

    /**
     * Get the lifecycle for the jobcard.
     */
    public function lifecycle()
    {
        return $this->morphToMany('App\Lifecycle', 'trackable', 'lifecycle_allocations');
    }

    /**
     * Get all of the categories for the jobcard.
     */
    public function categories()
    {
        return $this->morphToMany('App\Category', 'trackable', 'category_allocations');
    }

    /**
     * Get all of the cost centers for the jobcard.
     */
    public function costCenters()
    {
        return $this->morphToMany('App\CostCenter', 'trackable', 'costcenter_allocations');
    }

    /**
     * Get the priority for the jobcard.
     */
    public function priority()
    {
        return $this->morphToMany('App\Priority', 'trackable', 'priority_allocations');
    }

    /**
     * Get the priority for the jobcard.
     */
    public function assignedStaff()
    {
        return $this->morphToMany('App\User', 'trackable', 'staff_allocations');
    }

    /**
     * Get all of the jobcard's quotations.
     */
    public function quotations()
    {
        return $this->morphMany('App\Quotation', 'trackable');
    }

    public function suppliersList()
    {
        return $this->belongsToMany('App\Company', 'jobcard_suppliers', 'jobcard_id', 'supplier_id')
                    ->withPivot('id', 'jobcard_id', 'supplier_id', 'amount', 'quotation_doc_id', 'selected')
                    ->withTimestamps();
    }

    public function statusLifecycle()
    {
        return $this->morphMany('App\FormTemplateAllocation', 'trackable');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->where('trackable_type', 'jobcard')
                    ->orderBy('created_at', 'desc');
    }

    public function createdActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'jobcard')->where('type', 'created');
    }

    public function approvedActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'jobcard')->where('type', 'approved');
    }

    public function addedLifecycleStagesActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'jobcard')->where('type', 'added lifecycle stage');
    }

    public function updatedLifecycleStagesActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'jobcard')->where('type', 'updated lifecycle stage');
    }

    public function reversedLifecycleStagesActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'jobcard')->where('type', 'reversed lifecycle stage');
    }

    ///////////////////////////////////////////////////////////////////////////////////
    //                                                                              //
    //  EVERTHING BELOW THIS CAUTION IS NOT YET BEING USED BY THE SYSTEM            //
    //                                                                              //
    //////////////////////////////////////////////////////////////////////////////////

    public function owningCompany()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }

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
        return $this->morphTo();
    }

    public function selectedSuppliers()
    {
        return $this->suppliersList()->where('selected', 1);
    }

    /*  Get the total count of all the people who viewed this jobcard. Make sure that
     *  we have distinct viewers meaning that we are not counting repeated records.
    */
    public function distinctViewersCount()
    {
        return $this->recentActivities()->distinct('who_created_id')->count('who_created_id');
    }

    /*

    public function category()
    {
        return $this->belongsTo('App\JobcardCategory', 'category_id');
    }

    public function client()
    {
        return $this->belongsTo('App\Client', 'client');
    }

    public function clientContacts()
    {
        return $this->belongsToMany('App\ClientContact', 'jobcard_contacts', 'jobcard_id', 'contact_id');
    }
    */

    protected $appends = [
                    'createdBy', 'deadline', 'deadlineArray', 'deadlineInWords',
                    'last_approved_activity',
                    'has_approved', 'has_lifecycle',
                    'current_activity_status',
                    'current_lifecycle_stage', 'current_lifecycle_main_status', 'current_lifecycle_sub_status', 'lifecycle_stages',
                    'activity_count',
                ];

    public function getLastApprovedActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'jobcard')->where('type', 'approved')->first();
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

    public function getLifecycleStagesAttribute()
    {
        //$addedLifecycleStages = $this->addedLifecycleStagesActivities()->orderBy('created_at', 'asc')->get();
        $updatedLifecycleStages = $this->updatedLifecycleStagesActivities()->orderBy('created_at', 'asc')->get();
        $reversedLifecycleStages = $this->reversedLifecycleStagesActivities()->orderBy('created_at', 'asc')->get();

        $originalStages = collect($updatedLifecycleStages)->filter(function ($stage) {
            return !$stage['activity']['updated_stage_id'];
        });

        $updatedStages = collect($updatedLifecycleStages)->filter(function ($stage) {
            return $stage['activity']['updated_stage_id'];
        });

        $allStages = [];

        //  For each original stage
        foreach ($originalStages as $originalStage) {
            //  Check if we have any updated copies of the original
            foreach ($updatedStages as $updatedStage) {
                //  Check if the updated copy was created after the original copy
                if ($updatedStage->created_at->getTimestamp() > $originalStage->created_at->getTimestamp()) {
                    //  Check if the update target id is the same as the original stage id
                    if ($updatedStage['activity']['updated_stage_id'] == $originalStage['id']) {
                        //  This means the original has been updated, therefore we need to update the original copy
                        $originalStage = $updatedStage;
                    }
                }
            }

            $reversed = false;

            //  Check if we have reversed/undid this update
            foreach ($reversedLifecycleStages as $reversedStage) {
                //  Check if the reversed update was created after the original/updated copy
                if ($reversedStage->created_at->getTimestamp() > $originalStage->created_at->getTimestamp()) {
                    //  Check if the update target type and instance is the same as the original type and instance
                    if ($reversedStage['activity']['type'] == $originalStage['activity']['type'] &&
                        $reversedStage['activity']['instance'] == $originalStage['activity']['instance']) {
                        //  This means the original has been updated, therefore we need to update the original copy
                        $reversed = true;
                    }
                }
            }

            if (!$reversed) {
                array_push($allStages, $originalStage);
            }
        }

        return $allStages;
    }

    public function getCurrentLifecycleStageAttribute()
    {
        if (count($this->lifecycle_stages)) {
            //  Return the last lifecycle stage
            return $this->lifecycle_stages[0];
        }
    }

    public function getCurrentLifecycleMainStatusAttribute()
    {
        if (count($this->current_lifecycle_stage)) {
            $availableStages = $this->lifecycle->first()['stages'];
            foreach ($availableStages as $availableStage) {
                if ($availableStage['type'] == $this->current_lifecycle_stage['activity']['type']
                    && $availableStage['instance'] == $this->current_lifecycle_stage['activity']['instance']) {
                    //  Current stage name and type
                    $stageName = $availableStage['name'];
                    $stageType = $availableStage['type'];

                    return ['name' => $stageName, 'type' => $stageType];
                }
            }
        } elseif ($this->has_approved) {
            return ['name' => 'Open', 'type' => 'open'];
        }
    }

    public function getCurrentLifecycleSubStatusAttribute()
    {
        if (count($this->current_lifecycle_stage)) {
            //  Check if this stage is cancelled
            if ($this->current_lifecycle_stage['activity']['cancelled_status'] ?? false) {
                return 'Cancelled';
            //  Check if this stage is pending
            } elseif ($this->current_lifecycle_stage['activity']['pending_status'] ?? false) {
                return 'Pending';
            }
        }
    }

    public function gethasLifecycleAttribute()
    {
        return count($this->lifecycle_stages) ? true : false;
    }

    public function getActivityCountAttribute()
    {
        $count = $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'jobcard')
                                            ->select(DB::raw('count(*) as total'))
                                            ->groupBy('trackable_type')
                                            ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    //  Getter for calculating the deadline returned as array
    public function getCreatedByAttribute()
    {
        $publishingUser = $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'jobcard')->where('type', 'created')->first();

        if ($publishingUser) {
            return $publishingUser->createdBy;
        }
    }

    //  Getter for calculating the deadline returned as array
    public function getDeadlineAttribute()
    {
        if ($this->end_date) {
            return oq_jobcardDeadlineArray($this)[0].' '.oq_jobcardDeadlineArray($this)[1]
                .' '.(oq_jobcardDeadlineArray($this)[2] ? '' : 'ago');
        }
    }

    //  Getter for calculating the deadline returned as array
    public function getDeadlineArrayAttribute()
    {
        if ($this->deadline) {
            return oq_jobcardDeadlineArray($this);
        }
    }

    //  Getter for calculating the deadline returned as words
    public function getDeadlineInWordsAttribute()
    {
        if ($this->deadline) {
            return oq_jobcardDeadlineWords(oq_jobcardDeadlineArray($this));
        }
    }
}
