<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use App\Traits\StoreTraits;

class Store extends Model
{
    use Dataviewer;
    use StoreTraits;

    protected $with = ['phones'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        /*  Basic Info  */
        'name', 'abbreviation', 'description', 'type', 'industry', 
        
        /*  Address Info  */
        'address_1', 'address_2', 'country', 'province', 'city', 'postal_or_zipcode', 
        
        /*  Address Info  */
        'email', 'additional_email', 'verified', 'setup', 
        
        /*  Social Info  */
        'website_link', 'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link', 'youtube_link',

        /*  Currency Info  */
        'currency_type',

        /*  Payment Gateway Info  */
        'vcs_terminal_id',

        /*  Company Info  */
        'company_branch_id', 'company_id'
    ];

    protected $allowedFilters = [
        'id', 'name', 'abbreviation', 'description', 'type', 'industry',
        'address_1', 'address_2', 'country', 'province', 'city', 'postal_or_zipcode', 
        'email', 'additional_email', 'verified', 'setup', 'facebook_link', 'twitter_link',
        'linkedin_link', 'instagram_link', 'youtube_link', 'currency_type', 'vcs_terminal_id',
        'company_branch_id', 'company_id', 'created_at',
    ];

    protected $orderable = [
        'id', 'name', 'abbreviation', 'description', 'type', 'industry',
        'address_1', 'address_2', 'country', 'province', 'city', 'postal_or_zipcode', 
        'email', 'additional_email', 'verified', 'setup', 'facebook_link', 'twitter_link',
        'linkedin_link', 'instagram_link', 'youtube_link', 'currency_type', 'vcs_terminal_id',
        'company_branch_id', 'company_id', 'created_at',
    ];

    /**
     * Get the ecommerce orders
     */
    public function orders()
    {
        return $this->hasMany('App\Order', 'store_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function companyBranch()
    {
        return $this->belongsTo('App\CompanyBranch', 'company_branch_id');
    }

    public function interests()
    {
        return $this->hasMany('App\StoreInterests');
    }

    public function productAndServices()
    {
        return $this->hasMany('App\Product');
    }

    /*  Get the documents relating to this store. These are various files such as logos, store profiles,
     *  scanned files, images and so on. Basically any file/image the user wants to save to this store is
     *  stored in this relation
     */

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    public function files()
    {
        return $this->documents()->where('type', 'file');
    }

    public function phones()
    {
        return $this->morphMany('App\Phone', 'trackable')
                    ->orderBy('created_at', 'desc');
    }

    public function comments()
    {
        return $this->morphToMany('App\Comment', 'trackable', 'comment_allocations')->orderBy('comment_allocations.created_at', 'asc');
    }

    public function messages()
    {
        return $this->comments()->where('type', 'message');
    }

    public function reviews()
    {
        return $this->comments()->where('type', 'review');
    }

    public function taxes()
    {
        return $this->morphToMany('App\Tax', 'taxable', 'tax_allocations');
    }

    public function discounts()
    {
        return $this->morphToMany('App\Discount', 'discountable', 'discount_allocations');
    }

    public function coupons()
    {
        return $this->morphToMany('App\Coupon', 'couponable', 'tax_allocations');
    }

    /**
     * Get the store settings.
     */
    public function settings()
    {
        return $this->morphOne('App\Setting', 'trackable');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->where('trackable_type', 'store')
                    ->orderBy('created_at', 'desc');
    }

    /* ATTRIBUTES */

    protected $appends = [
        'logo'
    ];

    public function getLogoAttribute()
    {
        return $this->documents()->where('type', 'logo')->first();
    }

}
