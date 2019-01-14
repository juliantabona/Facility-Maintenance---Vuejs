<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\AdvancedFilter\Dataviewer;

class ProductOrService extends Model
{
    use SoftDeletes;
    use Dataviewer;

    protected $table = 'products_and_services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'type', 'price', 'buy', 'sell', 'company_branch_id', 'company_id',
    ];

    protected $allowedFilters = [
        'id', 'name', 'description', 'type', 'price', 'created_at',

        // nested filters
        //  'taxes.id', 'taxes.name',
    ];

    protected $orderable = [
        'id', 'name', 'description', 'type', 'price', 'created_at',
    ];

    /**
     * Get all of the product/service associated taxes.
     */
    public function taxes()
    {
        return $this->belongsToMany('App\Tax', 'products_and_services_taxes', 'product_service_id', 'tax_id');
    }
}
