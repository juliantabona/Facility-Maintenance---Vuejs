<?php

namespace App;

use App\Traits\ProductTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'store' => 'App\Store',
    'account' => 'App\Account',
    'ussd_interface' => 'App\UssdInterface',
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
        'allow_variants' => 'boolean',
        'allow_downloads' => 'boolean',
        'auto_manage_stock' => 'boolean',
        'allow_stock_management' => 'boolean',
    ];

    protected $with = ['gallery', 'taxes', 'discounts', 'coupons', 'categories', 'tags'];

    /*
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        /*  Product Details  */
        'name', 'description', 'type', 'cost_per_item', 'unit_regular_price', 'unit_sale_price',
        'sku', 'barcode', 'stock_quantity', 'allow_stock_management', 'auto_manage_stock', 'variants',
        'variant_attributes', 'allow_variants', 'allow_downloads', 'show_on_store',
        'is_new', 'is_featured',

        /*  Ownership Information  */
        'owner_id', 'owner_type',
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
     *  Returns the product variations. Variations are different
     *  versions of this product such as when this product is
     *  available in different sizes, colors or materials, then
     *  it will have products with different variables.
     */
    public function variations()
    {
        return $this->hasMany('App\Product', 'parent_product_id');
    }

    /* 
     *  Scope:
     *  Returns products that are not variables of another product
     */
    public function scopeIsNotVariation($query)
    {
        return $query->whereNull('parent_product_id');
    }

    /*
     *  Returns the product variables. These are properties that
     *  make this product a variation e.g size=small, color=blue,
     *  and material=cotton are all variables that make this
     *  product different from all other variable products.
     */
    public function variables()
    {
        return $this->hasMany('App\Variable', 'product_id');
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
        return $this->morphToMany('App\Tax', 'owner', 'tax_allocations')->withTimestamps();
    }

    /*
     *  Returns the product discounts
     */
    public function discounts()
    {
        return $this->morphToMany('App\Discount', 'owner', 'discount_allocations')->withTimestamps();
    }

    /*
     *  Returns the product coupons
     */
    public function coupons()
    {
        return $this->morphToMany('App\Coupon', 'owner', 'coupon_allocations')->withTimestamps();
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
        return $this->morphToMany('App\Category', 'owner', 'category_allocations')->withTimestamps();
    }

    /*
     *  Returns the product tags
     */
    public function tags()
    {
        return $this->morphToMany('App\Tag', 'owner', 'tag_allocations')->withTimestamps();
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
        'variations', 'variables',
        'primary_image', 'discount_total', 'tax_total', 'sub_total', 'grand_total',
        'on_sale', 'stock_status', 'currency', 'rating_count', 'average_rating', 'resource_type',
    ];

    

    /*
     *  Returns the product variations
     */
    public function getVariationsAttribute()
    {
        if($this->allow_variants){
            
            return $this->variations()->get();

        }
    }

    /*
     *  Returns the product variables
     */
    public function getVariablesAttribute()
    {
        if($this->parent_product_id){
            
            return $this->variables()->get();

        }
    }

    /*
     *  Returns the product primary image
     */
    public function getPrimaryImageAttribute()
    {
        return $this->documents()->whereType('primary')->first();
    }

    /*
     *  Returns the product price for one unit
     *
     *  This is the total price of the product based on the regular
     *  price and the sale price.
     */
    public function getUnitPriceAttribute()
    {
        //  If the product is on sale, use the sale price otherwise the regular price
        return $this->unit_sale_price ?? $this->unit_regular_price;
    }

    /*
     *  Returns the product total discount
     */
    public function getDiscountTotalAttribute()
    {
        $discount_total = 0;

        //  Foreach discount
        foreach ($this->discounts as $discount) {
            //  If its a percentage rate based discount
            if ($discount['type'] == 'rate') {
                //  Calculate the percentage discount amount and add to the total discount
                $discount_total += $discount['rate'] * $this->unit_regular_price;

            //  If its a fixed rate based discount
            } elseif ($discount['type'] == 'fixed') {
                //  Add the fixed discount to the total discount
                $discount_total += $discount['amount'];
            }
        }

        return $discount_total;
    }

    /*
     *  Returns the product total tax
     */
    public function getTaxTotalAttribute()
    {
        $tax_total = 0;

        //  Foreach tax
        foreach ($this->taxes as $tax) {
            //  Calculate the percentage tax amount and add to the total tax
            $tax_total += $tax['rate'] * $this->unit_regular_price;
        }

        return $tax_total;
    }

    /*
     *  Returns the product sub-total price
     *
     *  This is the total price of the product without the tax total and discount
     *  total included. It is the total price of the product based on a quantity
     *  of 1. It is exactly the same as the unit price since its only a quantity
     *  of one, but when the quantity increases the sub-total will change
     *  according to the quantity increase.
     */
    public function getSubTotalAttribute()
    {
        return $this->unit_regular_price;
    }

    /*
     *  Returns the product grand-total price
     *
     *  This is the total price of the product with the tax total
     *  and discount total included. It shows how much the product
     *  costs after taxing and discounting the product
     */
    public function getGrandTotalAttribute()
    {
        //  Calculate total price with taxes applied
        $total = $this->unit_regular_price + $this->tax_total;

        //  Calculate total price with discounts applied
        $total = $total - $this->discount_total;

        //  Return the grand total price
        return $total;
    }

    /*
     *  Returns true if the product is on sale
     */
    public function getOnSaleAttribute()
    {
        return isset($this->unit_sale_price) ? true : false;
    }

    /*
     *  Returns the product stock status
     */
    public function getStockStatusAttribute()
    {
        //  If we allow stock and the quantity is greater than 0
        if ($this->allow_stock_management && $this->stock_quantity > 0) {
            return 'In stock';
        } else {
            return 'Out of stock';
        }
    }

    /*
     *  Returns the product owner's currency
     */
    public function getCurrencyAttribute()
    {
        //  Get the store currency
        return $this->owner->currency;
    }

    /*
     *  Returns the product's total number of ratings
     */
    public function getRatingCountAttribute()
    {
        //  Count the product reviews
        $reviews = $this->reviews()->count();
    }

    /*
     *  Returns the product average rating
     */
    public function getAverageRatingAttribute()
    {
        //  Get the product reviews
        $reviews = $this->reviews ?? [];

        //  If we have any reviews
        if ($reviews) {
            //  Return the average of the ratings combined
            return collect($reviews)->avg('rating');
        }
    }

    /*
     *  Returns the resource type
     */
    public function getResourceTypeAttribute()
    {
        return strtolower(class_basename($this));
    }

    public function setAllowStockManagementAttribute($value)
    {
        $this->attributes['allow_stock_management'] = (($value == 'true' || $value == '1') ? 1 : 0);
    }

    public function setAutoManageStockAttribute($value)
    {
        $this->attributes['auto_manage_stock'] = (($value == 'true' || $value == '1') ? 1 : 0);
    }

    public function setAllowVariantsAttribute($value)
    {
        $this->attributes['allow_variants'] = (($value == 'true' || $value == '1') ? 1 : 0);
    }

    public function setAllowDownloadsAttribute($value)
    {
        $this->attributes['allow_downloads'] = (($value == 'true' || $value == '1') ? 1 : 0);
    }

    public function setShowOnStoreAttribute($value)
    {
        $this->attributes['show_on_store'] = (($value == 'true' || $value == '1') ? 1 : 0);
    }

    public function setIsNewAttribute($value)
    {
        $this->attributes['is_new'] = (($value == 'true' || $value == '1') ? 1 : 0);
    }

    public function setIsFeaturedAttribute($value)
    {
        $this->attributes['is_featured'] = (($value == 'true' || $value == '1') ? 1 : 0);
    }
    
}
