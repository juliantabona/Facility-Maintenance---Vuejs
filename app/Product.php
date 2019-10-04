<?php

namespace App;

use App\Traits\ProductTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'store' => 'App\Store',
    'company' => 'App\Company',
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
        'is_new' => 'boolean',
        'is_featured' => 'boolean',
        'show_on_store' => 'boolean',
        'has_inventory' => 'boolean',
        'allow_variants' => 'boolean',
        'allow_downloads' => 'boolean',
        'auto_track_inventory' => 'boolean',
    ];

    protected $with = ['gallery', 'taxes', 'discounts', 'coupons', 'categories', 'tags'];

    /*
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Product Details  */
        'name', 'description', 'type', 'cost_per_item', 'unit_price', 'unit_sale_price',
        'sku', 'barcode', 'stock_quantity', 'has_inventory', 'auto_track_inventory', 
        'variants', 'variant_attributes', 'allow_variants', 'allow_downloads', 
        'show_on_store', 'is_new', 'is_featured',

        /*  Ownership Information  */
        'owner_id', 'owner_type'
    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /* 
     *  Scope by type
     */
    public function scopeWhereType($query, $type)
    {
        return $query;
    }

    /*  
     *  Returns documents associated with this product. These are various files such as images,
     *  videos, files and so on. Basically any file/image/video the user wants to save to 
     *  this product is stored in this relation
     */

    public function documents()
    {
        return $this->morphMany('App\Document', 'owner');
    }

    /* 
     *  Returns documents categorized as gallery
     */
    public function gallery()
    {
        return $this->documents()->whereType('gallery');
    }

    /* 
     *  Returns documents categorized as downloads
     */
    public function downloads()
    {
        return $this->documents()->whereType('download');
    }

    /* 
     *  Returns the product settings
     */
    public function settings()
    {
        return $this->morphOne('App\Setting', 'owner');
    }

    /* 
     *  Returns the owner of the product
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /* 
     *  Returns the product taxes
     */
    public function taxes()
    {
        return $this->morphToMany('App\Tax', 'owner', 'tax_allocations');
    }

    /* 
     *  Returns the product discounts
     */
    public function discounts()
    {
        return $this->morphToMany('App\Discount', 'owner', 'discount_allocations');
    }

    /* 
     *  Returns the product coupons
     */
    public function coupons()
    {
        return $this->morphToMany('App\Coupon', 'owner', 'coupon_allocations');
    }

    /* 
     *  Returns reviews sent to this product
     */
    public function reviews()
    {
        return $this->morphMany('App\Review', 'owner')->latest();
    }

    /*
     *  Returns the product categories
     */
    public function categories()
    {
        return $this->morphToMany('App\Category', 'owner', 'category_allocations');
    }

    /*
     *  Returns the product tags
     */
    public function tags()
    {
        return $this->morphToMany('App\Tag', 'owner', 'tag_allocations');
    }

    /* 
     *  Returns recent activities owned by this product
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }

    /* ATTRIBUTES */

    protected $appends = [
        'primary_image', 'store_currency_symbol', 'average_rating', 'resource_type'
    ];

    /* 
     *  Returns the product primary image
     */
    public function getPrimaryImageAttribute()
    {
        return $this->documents()->whereType('primary')->first();
    }

    /* 
     *  Returns the product owner's currency symbol
     */
    public function getStoreCurrencySymbolAttribute()
    {
        //  Get the owning resource settings
        $settings = $this->owner->settings ?? null;

        //  If we have the settings
        if($settings){

            //  Return the currency symbol if exists
            return $settings['details']['general']['currency_type']['currency']['symbol'] ?? null;

        }
    }
    
    /* 
     *  Returns the product average rating
     */
    public function getAverageRatingAttribute()
    {
        //  Get the product reviews
        $reviews = $this->reviews ?? [];

        //  If we have any reviews
        if( $reviews ){
            
            //  Return the average of the ratings combined
            return collect( $reviews )->avg('rating');

        }
    }

    /* 
     *  Returns the resource type
     */
    public function getResourceTypeAttribute()
    {
        return strtolower(class_basename($this));
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
