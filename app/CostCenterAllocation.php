<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'jobcard' => 'App\Jobcard',
]);
class CostCenterAllocation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'costcenter_allocations';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'costcenter_id',
    ];

    public function trackable()
    {
        return $this->morphTo();
    }
}
