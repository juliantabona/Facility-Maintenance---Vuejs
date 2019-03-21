<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Traits\PriorityTraits;

Relation::morphMap([
    'jobcard' => 'App\Jobcard',
]);

class Priority extends Model
{
    use Dataviewer;
    use PriorityTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'color_code', 'company_id', 'type',
    ];

    protected $allowedFilters = [];

    protected $orderable = [];

    /**
     * Get all of the jobcards that are assigned this category.
     */
    public function jobcards()
    {
        return $this->morphedByMany('App\Jobcard', 'trackable', 'priority_allocations');
    }
}
