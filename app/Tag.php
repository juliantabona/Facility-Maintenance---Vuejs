<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Traits\TagTraits;

Relation::morphMap([
    'jobcard' => 'App\Jobcard',
    'appointment' => 'App\Appointment',
    'product' => 'App\Product',
]);

class Tag extends Model
{
    use Dataviewer;
    use TagTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'company_id'
    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /**
     * Get all of the products that are assigned this Tag.
     */
    public function products()
    {
        return $this->morphedByMany('App\Products', 'trackable', 'tag_allocations');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->where('trackable_type', 'tag')
                    ->orderBy('created_at', 'desc');
    }

}
