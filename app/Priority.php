<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'jobcard' => 'App\Jobcard',
]);

class Priority extends Model
{
    use Dataviewer;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'color_code', 'company_id', 'type',
    ];

    /**
     * Get all of the jobcards that are assigned this category.
     */
    public function jobcards()
    {
        return $this->morphedByMany('App\Jobcard', 'trackable', 'priority_allocations');
    }
}
