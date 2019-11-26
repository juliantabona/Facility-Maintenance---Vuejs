<?php

namespace App;

use DB;
use App\Traits\ProductTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'store' => 'App\Store',
    'account' => 'App\Account',
    'ussd_interface' => 'App\UssdInterface',
]);

class Product extends Model
{
    use Dataviewer;
    use ProductTraits;

    /*
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
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

    protected $with = ['gallery', 'taxes', 'discounts', 'coupons', 'categories', 'tags', 'variables'];

    /*
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        /*  Product Details  */
        'name', 'description', 'type', 'cost_per_item', 'unit_regular_price', 'unit_sale_price',
        'sku', 'barcode', 'stock_quantity', 'allow_stock_management', 'auto_manage_stock',
        'variant_attributes', 'allow_variants', 'allow_downloads', 'show_on_store',
        'is_new', 'is_featured', 'parent_product_id',

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
     *  Returns the parent product that this variation belongs to
     */
    public function parentProduct()
    {
        return $this->belongsTo('App\Product', 'parent_product_id');
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
        'primary_image', 'discount_total', 'tax_total', 'sub_total', 'grand_total', 'on_sale',
        'has_price', 'has_prices_on_all_variations', 'stock_status', 'has_enough_stock_on_all_variations',
        'currency', 'rating_count', 'average_rating', 'parent_variant_attributes', 'resource_type',
    ];

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
     *  Returns the product price status
     */
    public function getHasPriceAttribute()
    {
        //  If this is a simple product and we have a price provided
        if (!$this->allow_variants && $this->unit_regular_price) {
            //  True meaning this is a simple product and it has a price
            return true;

        //  If this is a simple product and we don't have a price provided
        } elseif (!$this->allow_variants && !$this->unit_regular_price) {
            //  False meaning this is a simple product and does not have a price
            return false;
        } else {
            //  Its not a simple product, therefore price status does not apply
            return null;
        }
    }

    /*
     *  Returns the product variation price status
     *  Returns false if any variation has no price
     */
    public function getHasPricesOnAllVariationsAttribute()
    {
        //  If this product supports variations
        if ($this->allow_variants) {
            //  Get the product variations
            $variations = $this->variations ?? [];

            //  If this product has variations
            if (count($variations)) {
                //  Foreach variation
                foreach ($this->variations as $variation) {
                    //  If the current variation has nested variations
                    if ($variation->allow_variants) {
                        /*  Since the current variation has nested variations, we need to check if the
                         *  nested variations have prices.
                         */
                        return $variation->has_prices_on_all_variations;

                    //  If the current variation has no nested variations
                    } else {
                        //  If the current variation has a price
                        if ($variation->has_price === true) {
                            //  Return true to indicate that we have a price on this variation
                            return true;

                        //  If the current variation does not have a price
                        } elseif ($variation->has_price === false) {
                            //  Return false to indicate that we don't have a price on this variation
                            return false;

                            //  Stop the loop
                            break;
                        }
                    }
                }
            } else {
                /*  We return false since the product supports variations but no variations were found
                 *  This means that the variation prices do not exist therefore we return false to
                 *  indicate that this product does not have prices for its variations.
                 */
                return false;
            }
        }

        //  Its not a variable product, therefore variation prices do not apply
        return null;
    }

    /*
     *  Returns the product stock status
     *
     */
    public function getStockStatusAttribute()
    {
        $in_stock = [
            'name' => 'In stock',
            'description' => 'This product has enough stock',
            'type' => 'in_stock',
        ];

        $low_stock = [
            'name' => 'Low stock',
            'description' => 'This product does not have enough stock',
            'type' => 'low_stock',
        ];

        $out_of_stock = [
            'name' => 'Out of stock',
            'description' => 'This product does not have stock',
            'type' => 'out_of_stock',
        ];

        //  If this is a variable product then the stock status does not apply
        if ($this->allow_variants) {
            return null;

        //  If this is a simple product then the stock status applies
        } else {
            //  If this product takes stock
            if ($this->allow_stock_management) {
                //  If the current product owner is a store
                if ($this->owner->resource_type == 'store') {
                    //  Get the stores minimum stock quantity
                    $minimum_stock_quantity = $this->owner->minimum_stock_quantity;

                //  If the current owner is not a store
                } else {
                    //  Default to minimum stock quantity of 10
                    $minimum_stock_quantity = 10;
                }

                //  If we allow stock and the quantity is greater than the minimum allowed quantity
                if ($this->allow_stock_management && $this->stock_quantity > $minimum_stock_quantity) {
                    return $in_stock;

                //  If we allow stock and the quantity is greater than 0
                } elseif ($this->allow_stock_management && $this->stock_quantity > 0) {
                    return $low_stock;
                } else {
                    return $out_of_stock;
                }

                //  If this product does not take stock
            } else {
                //  This product always has stock
                return $in_stock;
            }
        }
    }

    /*
     *  Returns the product variation price status
     *  Returns false if any variation has no price
     */
    public function getHasEnoughStockOnAllVariationsAttribute()
    {
        //  If this product supports variations
        if ($this->allow_variants) {
            //  Get the product variations
            $variations = $this->variations ?? [];

            //  If this product has variations
            if (count($variations)) {
                //  Foreach variation
                foreach ($this->variations as $variation) {
                    //  If the current variation has nested variations
                    if ($variation->allow_variants) {
                        /*  Since the current variation has nested variations, we need to check if the
                         *  nested variations have enough stock.
                         */
                        return $variation->has_enough_stock_on_all_variations;

                    //  If the current variation has no nested variations
                    } else {
                        //  If the current variation has stock
                        if ($variation->stock_status['type'] == 'in_stock') {
                            //  Return true to indicate that we have stock on this variation
                            return true;

                        //  If the current variation does not have stock or has low stock
                        } else {
                            //  Return false to indicate that we don't have stock or th stock is too low
                            return false;

                            //  Stop the loop
                            break;
                        }
                    }
                }
            } else {
                /*  We return false since the product supports variations but no variations were found
                 *  This means that the variations stock does not exists therefore we return false to
                 *  indicate that this product does not have stock for its variations.
                 */
                return false;
            }
        }

        //  Its not a variable product, therefore variation stock statuses do not apply
        return null;
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
     *  Returns the parent product variant attributes
     */
    public function getParentVariantAttributesAttribute()
    {
        return $this->parentProduct->variant_attributes ?? null;
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

    //  ON DELETE EVENT
    public static function boot()
    {
        parent::boot();

        // before delete() method call this
        static::deleting(function ($product) {
            //  Delete all reviews
            $product->reviews()->delete();

            //  Delete all settings
            $product->settings()->delete();

            //  Delete all variables
            $product->variables()->delete();

            //  Delete all documents
            foreach ($product->documents as $document) {
                $document->delete();
            }

            //  Delete all variations
            foreach ($product->variations as $variation) {
                $variation->delete();
            }

            //  Delete all activities
            $product->recentActivities()->delete();

            //  Delete all product allocations
            DB::table('product_allocations')->where(['product_id' => $product->id])->delete();

            // do the rest of the cleanup...
        });
    }
}
