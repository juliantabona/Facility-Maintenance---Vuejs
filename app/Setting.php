<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'user' => 'App\User',
    'company' => 'App\Company',
]);

class Setting extends Model
{
    use Dataviewer;

    protected $casts = [
        'details' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'details', 'trackable_id', 'trackable_type',
    ];

    /**
     * Get all of the trackable models.
     */
    public function trackable()
    {
        return $this->morphTo();
    }
}
