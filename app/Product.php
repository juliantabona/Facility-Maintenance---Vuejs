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

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'variants' => 'array',
        'variant_attributes' => 'array',

        //  Return the following 1/0 as true/false
        'allow_inventory' => 'boolean',
        'auto_track_inventory' => 'boolean',
        'allow_variants' => 'boolean',
        'allow_downloads' => 'boolean',
        'show_on_store' => 'boolean',
    ];

    protected $table = 'products_and_services';

    protected $with = ['categories', 'tags', 'taxes', 'galleryImages'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'type', 'cost_per_item', 'unit_price', 'unit_sale_price',
        'sku', 'barcode', 'stock_quantity', 'allow_inventory', 'auto_track_inventory', 'variants', 'variant_attributes', 'allow_variants',
        'downloads', 'allow_downloads', 'show_on_store', 'company_branch_id', 'company_id',
    ];

    protected $allowedFilters = [
        'id', 'name', 'description', 'type', 'cost_per_item', 'unit_price', 'created_at',

        // nested filters
        //  'taxes.id', 'taxes.name',
    ];

    protected $orderable = [
        'id', 'name', 'description', 'cost_per_item', 'unit_price', 'created_at',
    ];

    /*  Get the documents relating to this product. These are various files such as images, downloadable documents,
     *  and so on. Basically any file/image the user wants to save to this product is stored in this relation
     */

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    public function galleryImages()
    {
        return $this->documents()->where('type', 'gallery');
    }

    /**
     * Get all of the categories for the jobcard.
     */
    public function categories()
    {
        return $this->morphToMany('App\Category', 'trackable', 'category_allocations');
    }

    /**
     * Get all of the categories for the jobcard.
     */
    public function tags()
    {
        return $this->morphToMany('App\Tag', 'trackable', 'tag_allocations');
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

    /* ATTRIBUTES */

    protected $appends = [
        'primary_image'
    ];

    public function getPrimaryImageAttribute()
    {
        return $this->documents()->where('type', 'primary')->first();
    }

    public function setAllowInventoryAttribute($value)
    {
        $this->attributes['allow_inventory'] = ( ($value === 'true' || $value === '1') ? 1 : 0);
    }

    public function setAutoTrackInventoryAttribute($value)
    {
        $this->attributes['auto_track_inventory'] = ( ($value === 'true' || $value === '1') ? 1 : 0);
    }

    public function setAllowVariantsAttribute($value)
    {
        $this->attributes['allow_variants'] = ( ($value === 'true' || $value === '1') ? 1 : 0);
    }

    public function setAllowDownloadsAttribute($value)
    {
        $this->attributes['allow_downloads'] = ( ($value === 'true' || $value === '1') ? 1 : 0);
    }

    public function setShowOnStoreAttribute($value)
    {
        $this->attributes['show_on_store'] = ( ($value === 'true' || $value === '1') ? 1 : 0);
    }
}
