<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\AdvancedFilter\Dataviewer;

class Tax extends Model
{
    use SoftDeletes;
    use Dataviewer;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'abbreviation', 'rate', 'company_branch_id', 'company_id',
    ];

    protected $allowedFilters = [
        'id', 'name', 'abbreviation', 'rate', 'created_at',

        // nested filters
        //  'taxes.id', 'taxes.name',
    ];

    protected $orderable = [
        'id', 'name', 'abbreviation', 'rate', 'created_at',
    ];

    /**
     * Get all of the tax associated products and services.
     */
    public function productsOrServices()
    {
        return $this->belongsToMany('App\ProductOrService', 'products_and_services_taxes', 'tax_id', 'product_service_id');
    }

    public function products()
    {
        return productsOrServices()->where('type', 'product');
    }

    public function services()
    {
        return productsOrServices()->where('type', 'service');
    }
}
