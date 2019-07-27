<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use App\Traits\StoreTraits;

class Store extends Model
{
    use Dataviewer;
    use StoreTraits;

    protected $with = ['logo', 'phones'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        /*  Basic Info  */
        'name', 'abbreviation', 'description', 'type', 'industry', 
        
        /*  Address Info  */
        'address_1', 'address_2', 'country', 'provience', 'city', 'postal_or_zipcode', 
        
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
        'address_1', 'address_2', 'country', 'provience', 'city', 'postal_or_zipcode', 
        'email', 'additional_email', 'verified', 'setup', 'facebook_link', 'twitter_link',
        'linkedin_link', 'instagram_link', 'youtube_link', 'currency_type', 'vcs_terminal_id',
        'company_branch_id', 'company_id', 'created_at',
    ];

    protected $orderable = [
        'id', 'name', 'abbreviation', 'description', 'type', 'industry',
        'address_1', 'address_2', 'country', 'provience', 'city', 'postal_or_zipcode', 
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

    /*  Get the documents relating to this store. These are various files such as logos, store profiles,
     *  scanned files, images and so on. Basically any file/image the user wants to save to this store is
     *  stored in this relation
     */

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    public function logo()
    {
        return $this->documents()->where('type', 'logo')->take(1);
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

}
