<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'jobcard' => 'App\Jobcard',
]);

class CostCenter extends Model
{
    use Dataviewer;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'company_id', 'type',
    ];

    /**
     * Get all of the jobcards that are assigned this costcenter.
     */
    public function jobcards()
    {
        return $this->morphedByMany('App\Jobcard', 'trackable', 'costcenter_allocations');
    }
}
