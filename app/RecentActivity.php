<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\AdvancedFilter\Dataviewer;

Relation::morphMap([
    'user' => 'App\User',
    'company' => 'App\Company',
    'companybranch' => 'App\CompanyBranch',
    'jobcard' => 'App\Jobcard',
    'category' => 'App\Category',
    'priority' => 'App\Priority',
    'costcenter' => 'App\CostCenter',
    'document' => 'App\Document',
    'quotation' => 'App\Quotation',
    'invoice' => 'App\Invoice',
]);

class RecentActivity extends Model
{
    use Dataviewer;

    protected $table = 'recent_activities';

    protected $casts = [
        'activity' => 'array',
    ];

    protected $with = ['createdBy'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'activity', 'company_branch_id', 'company_id', 'created_by',
    ];

    public function creator()
    {
        return $this->morphMany('App\Creator', 'creatable');
    }

    protected $allowedFilters = [
        'id', 'company_branch_id', 'created_at',
    ];

    protected $orderable = [
        'id', 'company_branch_id', 'created_at',
    ];

    /**
     * Get all of the owning documentable models.
     */
    public function trackable()
    {
        return $this->morphTo();
    }

    public function jobcard()
    {
        return $this->belongsTo('App\Jobcard', 'trackable_id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
}
