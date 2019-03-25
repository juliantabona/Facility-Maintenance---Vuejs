<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'user' => 'App\User',
    'company' => 'App\Company',
    'jobcard' => 'App\Jobcard',
]);

class Lifecycle extends Model
{
    use Dataviewer;

    protected $casts = [
        'stages' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stages', 'selected', 'template', 'company_branch_id', 'company_id',
    ];

    /**
     * Get all of the trackable models.
     */
    public function trackable()
    {
        return $this->morphTo();
    }
}
