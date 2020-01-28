<?php

namespace App;

use DB;
use Carbon\Carbon;
use App\Traits\CommonTraits;
use App\Traits\QuotationTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'user' => 'App\User',
    'order' => 'App\Order',
    'store' => 'App\Store',
    'company' => 'App\Company',
    'jobcard' => 'App\Jobcard',
]);

class Quotation extends Model
{
    use Dataviewer, CommonTraits, QuotationTraits;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'metadata' => 'array',
        'currency' => 'array',
        'tax_lines' => 'array',
        'item_lines' => 'array',
        'billing_info' => 'array',
        'coupon_lines' => 'array',
        'shipping_info' => 'array',
        'merchant_info' => 'array',
        'discount_lines' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_date', 'expiry_date'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Basic Info  */
        'number', 'currency', 'created_date', 'expiry_date',

        /*  Item Info  */
        'item_lines',

        /*  Taxes, Disounts & Coupon Info  */
        'tax_lines', 'discount_lines', 'coupon_lines',

        /*  Grand Total, Sub Total, Tax Total, Discount Total, Shipping Total  */
        'sub_total', 'item_tax_total', 'global_tax_total', 'grand_tax_total', 'item_discount_total',
        'global_discount_total', 'grand_discount_total', 'shipping_total', 'grand_total',

        /*  Reference Info  */
        'reference_id', 'reference_ip_address', 'reference_user_agent',

        /*  Customer Info  */
        'customer_id', 'billing_info', 'shipping_info',

        /*  Merchant Info  */
        'merchant_id', 'merchant_type', 'merchant_info',

        /*  Meta Data  */
        'meta',

        /*  Ownership Information  */
        'owner_id', 'owner_type',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        /*  Hide the recentActivities from the returned results since they can be way too many */
        'recentActivities',
    ];

    protected $allowedFilters = [

        //  Quotation Filter
        'id', 'reference_no_value', 'grand_total', 'created_date', 'expiry_date', 'created_at',

        //  Customer Filter
        'customer.id', 'customer.name', 'customer.city', 'customer.state_or_region', 'customer.address', 
        'customer.industry', 'customer.type', 'customer.website_link', 'customer.phone_ext', 'customer.phone_num', 
        'customer.email', 'customer.created_at',

        //  Nested within JSON
        //  'billing_info > name',

    ];

    protected $allowedOrderableColumns = [
        'id', 'reference_no_value', 'grand_total', 'created_date', 'expiry_date', 'created_at',
    ];

    /*
     *  Returns the owner of the quotation e.g
     *  The quotation can be owned by a particular 
     *  order but have the store as the merchant
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns the merchant of the quotation 
     *  This refers to the seller of the goods/services
     */
    public function merchant()
    {
        return $this->morphTo();
    }

    /*
     *  Returns the customer of the quotation
     *  This refers to the consumer of the goods/services
     */
    public function customer()
    {
        return $this->belongsTo('App\Contact', 'customer_id');
    }

    /*
     *  Returns the reference of the quotation
     *  This refers to the user who submitted/placed the quotation
     */
    public function reference()
    {
        return $this->belongsTo('App\Contact', 'reference_id');
    }

    /*  
     *  Returns documents associated with this quotation. These are various files such as images,
     *  videos, files and so on. Basically any file/image/video the user wants to save to 
     *  this quotation is stored in this relation
     */

    public function documents()
    {
        return $this->morphMany('App\Document', 'owner');
    }

    /* 
     *  Returns documents categorized as files
     */
    public function files()
    {
        return $this->documents()->whereType('file');
    }

    /*
     *  Returns the quotation invoices
     */
    public function invoices()
    {
        return $this->hasMany('App\Invoice', 'quotation_id');
    }

    /* 
     *  Returns the quotation taxes
     */
    public function taxes()
    {
        return $this->morphToMany('App\Tax', 'owner', 'tax_allocations')->withTimestamps();
    }

    /* 
     *  Returns the quotation discounts
     */
    public function discounts()
    {
        return $this->morphToMany('App\Discount', 'owner', 'discount_allocations')->withTimestamps();
    }

    /* 
     *  Returns the quotation coupons
     */
    public function coupons()
    {
        return $this->morphToMany('App\Coupon', 'owner', 'coupon_allocations')->withTimestamps();
    }

    /* 
     *  Returns recent activities owned by this quotation
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }

    /* 
     *  Returns quotation creation activity
     */
    public function createdActivities()
    {
        return $this->recentActivities()->whereType('created');
    }

    /* 
     *  Returns quotation approval activity
     */
    public function approvedActivities()
    {
        return $this->recentActivities()->whereType('approved');
    }

    /* 
     *  Returns quotation sent activity
     */
    public function sentActivities()
    {
        return $this->recentActivities()->whereType(['sent email', 'sent sms']);
    }

    /* 
     *  Returns quotation skipped sending activity
     */
    public function skippedSendingActivities()
    {
        return $this->recentActivities()->whereType('skipped sending');
    }

    /* 
     *  Returns quotation converted to invoice activity
     */
    public function convertedActivities()
    {
        return $this->recentActivities()->whereType('converted');
    }

    protected $appends = [
        'status', 'has_converted_to_invoice', 'has_expired', 
        'has_cancelled', 'has_sent', 'has_skipped_sending', 'has_approved'
    ];
    
    /*
     *  Returns a confirmation of conversion to invoice
     */
    public function gethasConvertedToInvoiceAttribute()
    {
        //  Check if we have converted before
        $status = $this->convertedActivities()->count() ? true : false;

        //  Return the status
        return ['status' => $status, 'last_converted_date' => $this->convertedActivities()->first()->created_date ?? null];
    }

    /*
     *  Returns a confirmation of expiration
     */
    public function gethasExpiredAttribute()
    {
        //  Get the expiry date (as a timestamp)
        $expiryDate = $this->expiry_date->getTimestamp();

        //  Get the current date (as a timestamp)
        $now = Carbon::now()->getTimestamp();

        //  Compare to see if the expiry date has been exceeded
        $status = ($now > $expiryDate) ? true : false;

        //  Return the status
        return ['status' => $status, 'expiry_date' => $this->expiry_date];
    }

    /*
     *  Returns a confirmation of sending the quotation via email/sms
     */
    public function gethasSentAttribute()
    {
        //  Check if we have sent before
        $status = $this->sentActivities()->count() ? true : false;

        //  Return the status
        return ['status' => $status, 'last_sent_date' => $this->sentActivities()->first()->created_date ?? null, 
                'count' =>  $this->sentActivities()->count()];
    }

    /*
     *  Returns a confirmation of avoiding to send the quotation via email/sms
     */
    public function gethasSkippedSendingAttribute()
    {
        //  Check if we have skipped sending before
        $status = $this->skippedSendingActivities()->count() ? true : false;

        //  Return the status
        return ['status' => $status, 'last_skipped_date' => $this->skippedSendingActivities()->first()->created_date ?? null];
    }

    /*
     *  Returns a confirmation of approval
     */
    public function gethasApprovedAttribute()
    {
        //  Check if we have approved before
        $status = $this->approvedActivities()->count() ? true : false;

        //  Return the status
        return ['status' => $status, 'last_approved_date' => $this->approvedActivities()->first()->created_date ?? null];
    }

    /*
     *  Returns the current quotation status
     */
    public function getStatusAttribute()
    {
        //  If converted to invoice
        if ($this->has_converted_to_invoice) {
            return 'Converted';

        //  If expired
        } elseif ($this->has_expired) {
            return 'Expired';

        //  If sent
        } elseif ($this->has_sent) {
            return 'Sent';

        //  If approved
        } elseif ($this->has_approved) {
            return 'Approved';

        //  If draft
        } else {
            return 'Draft';
        }
    }

}
