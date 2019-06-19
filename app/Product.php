<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\AdvancedFilter\Dataviewer;
use App\Traits\ProductTraits;

class Product extends Model
{
    use SoftDeletes;
    use Dataviewer;
    use ProductTraits;

    protected $table = 'products_and_services';

    protected $with = ['taxes'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'type', 'purchase_price', 'selling_price', 'company_branch_id', 'company_id',
    ];

    protected $allowedFilters = [
        'id', 'name', 'description', 'type', 'purchase_price', 'selling_price', 'created_at',

        // nested filters
        //  'taxes.id', 'taxes.name',
    ];

    protected $orderable = [
        'id', 'name', 'description', 'type', 'purchase_price', 'selling_price', 'created_at',
    ];

    /*  Get the documents relating to this product. These are various files such as images, downloadable documents,
     *  and so on. Basically any file/image the user wants to save to this product is stored in this relation
     */

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    public function primaryImage()
    {
        return $this->documents()->where('type', 'primary')->take(1);
    }

    public function secondaryImages()
    {
        return $this->documents()->where('type', 'secondary');
    }

    /**
     * Get all of the product/service associated taxes.
     */
    public function taxes()
    {
        return $this->belongsToMany('App\Tax', 'products_and_services_taxes', 'product_service_id', 'tax_id');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->where('trackable_type', 'product')
                    ->orderBy('created_at', 'desc');
    }

}
