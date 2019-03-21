<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Traits\CostCenterTraits;

Relation::morphMap([
    'jobcard' => 'App\Jobcard',
]);

class CostCenter extends Model
{
    use Dataviewer;
    use CostCenterTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'company_id', 'type',
    ];

    protected $allowedFilters = [];

    protected $orderable = [];

    /**
     * Get all of the jobcards that are assigned this costcenter.
     */
    public function jobcards()
    {
        return $this->morphedByMany('App\Jobcard', 'trackable', 'costcenter_allocations');
    }
}
