<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Traits\CategoryTraits;

Relation::morphMap([
    'jobcard' => 'App\Jobcard',
    'appointment' => 'App\Appointment',
    'product' => 'App\Product',
]);

class Category extends Model
{
    use Dataviewer;
    use CategoryTraits;

    protected $with = ['childCategories'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'company_id', 'parent_category_id', 'type',
    ];

    protected $allowedFilters = [];

    protected $orderable = [];

    /**
     * Get all of the jobcards that are assigned this category.
     */
    public function childCategories()
    {
        return $this->hasMany('App\Category', 'parent_category_id');
    }

    /**
     * Get all of the jobcards that are assigned this category.
     */
    public function jobcards()
    {
        return $this->morphedByMany('App\Jobcard', 'trackable', 'category_allocations');
    }

}
