<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'jobcard' => 'App\Jobcard',
]);

class Quotation extends Model
{
    use Dataviewer;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'details' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'details', 'company_branch_id', 'company_id', 'trackable_type', 'trackable_id',
    ];

    /**
     * Get all of the owning trackable models.
     */
    public function trackable()
    {
        return $this->morphTo();
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->orderBy('created_at', 'desc');
    }
}
