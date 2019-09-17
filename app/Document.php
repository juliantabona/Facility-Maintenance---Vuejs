<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Traits\DocumentTraits;

Relation::morphMap([
    'company' => 'App\Company',
    'product' => 'App\Product',
    'store' => 'App\Store',
    'order' => 'App\Order',
    'user' => 'App\User',
]);

class Document extends Model
{
    use Dataviewer;
    use DocumentTraits;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'name', 'mime', 'size', 'url', 'who_created_id'
    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /**
     *  Get the owner from the morphTo relationship.
     *  Documents can be assigned to multiple types of
     *  owning resources e.g companies, stores, users,
     *  products, e.t.c
     */
    public function owner()
    {
        return $this->morphTo();
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }
}
