<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Traits\UploadTraits;

Relation::morphMap([
    'user' => 'App\User',
    'company' => 'App\Company',
    'jobcard' => 'App\Jobcard',
    'product' => 'App\Product',
    'store' => 'App\Store',
    'order' => 'App\Order',
]);

class Document extends Model
{
    use Dataviewer;
    use UploadTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'name', 'mime', 'size', 'url', 'who_created_id'
    ];

    protected $allowedFilters = [];

    protected $orderable = [];

    /**
     * Get all of the document owning models.
     */
    public function documentable()
    {
        return $this->morphTo();
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->where('trackable_type', 'document')
                    ->orderBy('created_at', 'desc');
    }
}
