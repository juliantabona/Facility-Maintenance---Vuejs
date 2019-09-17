<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use App\Traits\ProductTraits;

Relation::morphMap([
    'store' => 'App\Store',
]);

class Product extends Model
{
    use SoftDeletes;
    use Dataviewer;
    use ProductTraits;

    /*
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'variants' => 'array',
        'variant_attributes' => 'array',

        //  Return the following 1/0 as true/false
        'has_inventory' => 'boolean',
        'auto_track_inventory' => 'boolean',
        'allow_variants' => 'boolean',
        'allow_downloads' => 'boolean',
        'show_on_store' => 'boolean',
        'is_new' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected $with = ['categories', 'tags', 'taxes', 'galleryImages'];

    /*
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'type', 'cost_per_item', 'unit_price', 'unit_sale_price',
        'sku', 'barcode', 'stock_quantity', 'has_inventory', 'auto_track_inventory', 
        'variants', 'variant_attributes', 'allow_variants', 'downloads', 'allow_downloads', 
        'show_on_store', 'is_new', 'is_featured', 'productable_id', 'productable_id'
    ];

    protected $allowedFilters = [
        'id', 'name', 'description', 'type', 'cost_per_item', 'unit_price', 'created_at',

        // nested filters
        //  'taxes.id', 'taxes.name',
    ];

    protected $allowedOrderableColumns = [
        'id', 'name', 'description', 'cost_per_item', 'unit_price', 'created_at',
    ];

    /*  
     *  Returns documents associated with this store. These are various files such as logos,
     *  store profiles,  scanned files, images and so on. Basically any file/image the user 
     *  wants to save to this store is stored in this relation
     */

    public function documents()
    {
        return $this->morphMany('App\Document', 'owner');
    }

    /* 
     *  Returns documents categorized as gallery images
     */
    public function galleryImages()
    {
        return $this->documents()->where('type', 'gallery');
    }

    /* 
     *  Returns documents categorized as downloads
     */
    public function downloads()
    {
        return $this->documents()->where('type', 'download');
    }

    /* 
     *  Get the product settings
     */
    public function settings()
    {
        return $this->morphOne('App\Setting', 'owner');
    }

    /**
     * Product can be assigned to company/store
     */
    public function productable()
    {
        return $this->morphTo();
    }

    /**
     * Get the owner from the morphTo relationship
     * This method returns a company/store
     */
    public function owner()
    {
        return $this->productable();
    }

    /* 
     *  Get the product taxes
     */
    public function taxes()
    {
        return $this->morphToMany('App\Tax', 'taxable', 'tax_allocations');
    }

    /* 
     *  Get the product discounts
     */
    public function discounts()
    {
        return $this->morphToMany('App\Discount', 'discountable', 'discount_allocations');
    }

    /* 
     *  Get the product coupons
     */
    public function coupons()
    {
        return $this->morphToMany('App\Coupon', 'couponable', 'coupon_allocations');
    }

    /* 
     *  Get the product messages
     */
    public function messages()
    {
        return $this->morphMany('App\Message', 'messageable')->orderBy('messages.created_at', 'asc');
    }

    /* 
     *  Get the product reviews
     */
    public function reviews()
    {
        return $this->morphMany('App\Review', 'reviewable')->orderBy('reviews.created_at', 'asc');
    }

    /*
     * Get the product categories
     */
    public function categories()
    {
        return $this->morphToMany('App\Category', 'allocatable', 'category_allocations');
    }

    /*
     * Get the product tags
     */
    public function tags()
    {
        return $this->morphToMany('App\Tag', 'allocatable', 'tag_allocations');
    }

    /*
     * Get the product recent activties
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }

    /* ATTRIBUTES */

    protected $appends = [
        'primary_image', 'store_currency_symbol', 'average_rating'
    ];

    public function getAverageRatingAttribute()
    {
        $reviews = $this->reviews ?? [];

        if( $reviews ){
            return collect( $reviews )->avg('rating');
        }
    }

    public function getPrimaryImageAttribute()
    {
        return $this->documents()->where('type', 'primary')->first();
    }

    public function getStoreCurrencySymbolAttribute()
    {
        //  Get the owning model settings
        $settings = $this->owner->settings ?? null;

        //  If we have the settings
        if($settings){
            //  Return the currency symbol if exists
            return $settings['details']['general']['currency_type']['currency']['symbol'] ?? null;
        }
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

    public function setIsNewAttribute($value)
    {
        $this->attributes['is_new'] = ( ($value === 'true' || $value === '1') ? 1 : 0);
    }

    public function setIsFeaturedAttribute($value)
    {
        $this->attributes['is_featured'] = ( ($value === 'true' || $value === '1') ? 1 : 0);
    }

}
