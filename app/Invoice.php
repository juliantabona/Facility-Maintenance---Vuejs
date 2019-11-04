<?php

namespace App;

use DB;
use Carbon\Carbon;
use App\Traits\InvoiceTraits;
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

class Invoice extends Model
{
    use Dataviewer;
    use InvoiceTraits;

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
        'recurring_settings' => 'array',
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
        'number', 'currency', 'created_date', 'expiry_date', 'quotation_id',

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

        /*  Recurring Settings  */
        'invoice_parent_id', 'is_recurring', 'recurring_settings',

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

        //  Invoice Filter
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
     *  Returns the owner of the invoice e.g
     *  The invoice can be owned by a particular 
     *  order but have the store as the merchant
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns the merchant of the invoice 
     *  This refers to the seller of the goods/services
     */
    public function merchant()
    {
        return $this->morphTo();
    }

    /*
     *  Returns the customer of the invoice
     *  This refers to the consumer of the goods/services
     */
    public function customer()
    {
        return $this->belongsTo('App\Contact', 'customer_id');
    }

    /*
     *  Returns the reference of the invoice
     *  This refers to the user who submitted/placed the invoice
     */
    public function reference()
    {
        return $this->belongsTo('App\Contact', 'reference_id');
    }

    /*  
     *  Returns documents associated with this invoice. These are various files such as images,
     *  videos, files and so on. Basically any file/image/video the user wants to save to 
     *  this invoice is stored in this relation
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
     *  Returns documents categorized as proof of payment
     */
    public function proofOfPayment()
    {
        return $this->documents()->whereType('proof-of-payment');
    }

    /*
     *  Returns the quotation that this invoice belongs to
     */
    public function quotation()
    {
        return $this->belongsTo('App\Quotation', 'quotation_id');
    }

    /*
     *  Returns child invoices created as recurring invoices
     */
    public function childInvoices()
    {
        return $this->hasMany('App\Invoice', 'invoice_parent_id');
    }

    /*
     *  Returns child invoices created as recurring invoices
     */
    public function parentInvoice()
    {
        return $this->belongsTo('App\Invoice', 'invoice_parent_id');
    }

    /*
     *  Returns the transactions owned by this invoice
     */
    public function transactions()
    {
        return $this->morphMany('App\Transaction', 'owner')->latest();
    }

    /*
     *  Returns the transactions marked as payments
     */
    public function payments()
    {
        return $this->transactions()->whereType('payment');
    }

    /*
     *  Returns the transactions marked as refunds
     */
    public function refunds()
    {
        return $this->transactions()->whereType('refund');
    }

    /* 
     *  Returns the invoice taxes
     */
    public function taxes()
    {
        return $this->morphToMany('App\Tax', 'owner', 'tax_allocations');
    }

    /* 
     *  Returns the invoice discounts
     */
    public function discounts()
    {
        return $this->morphToMany('App\Discount', 'owner', 'discount_allocations');
    }

    /* 
     *  Returns the invoice coupons
     */
    public function coupons()
    {
        return $this->morphToMany('App\Coupon', 'owner', 'coupon_allocations');
    }

    /* 
     *  Returns recent activities owned by this invoice
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }

    /* 
     *  Returns invoice creation activity
     */
    public function createdActivities()
    {
        return $this->recentActivities()->whereType('created');
    }

    /* 
     *  Returns invoice approval activity
     */
    public function approvedActivities()
    {
        return $this->recentActivities()->whereType('approved');
    }

    /* 
     *  Returns invoice sent activity
     */
    public function sentActivities()
    {
        return $this->recentActivities()->whereType(['sent email', 'sent sms']);
    }

    /* 
     *  Returns invoice skipped sending activity
     */
    public function skippedSendingActivities()
    {
        return $this->recentActivities()->whereType('skipped sending');
    }

    /* 
     *  Returns invoice sent receipt activity
     */
    public function sentReceiptActivities()
    {
        return $this->recentActivities()->whereType(['sent receipt email', 'sent receipt sms']);
    }

    /* 
     *  Returns invoice paid activity
     */
    public function paidActivities()
    {
        return $this->recentActivities()->whereType('paid');
    }

    /* 
     *  Returns invoice payment cancelled activity
     */
    public function paymentCanceledActivities()
    {
        return $this->recentActivities()->whereType('cancelled payment');
    }

    /* 
     *  Returns invoice updated recurring schedule activity
     */
    public function recurringScheduleActivities()
    {
        return $this->recentActivities()->whereType('updated recurring schedule');
    }

    /* 
     *  Returns invoice updated recurring delivery activity
     */
    public function recurringDeliveryActivities()
    {
        return $this->recentActivities()->whereType('updated recurring delivery');
    }

    /* 
     *  Returns invoice updated recurring payment activity
     */
    public function recurringPaymentActivities()
    {
        return $this->recentActivities()->whereType('updated recurring payment');
    }

    /* 
     *  Returns invoice recurring approval activity
     */
    public function recurringApprovalActivities()
    {
        return $this->recentActivities()->whereType('approved recurring settings');
    }



    /*
     *  Returns all invoice reminders.
     
    public function reminders()
    {
        return $this->morphMany('App\Reminder', 'trackable');
    }
    */







    protected $appends = [
        'resource_type', 'transaction_total', 'refund_total', 'outstanding_balance', 'status', 
        'has_paid', 'has_expired', 'has_cancelled', 'has_sent', 'has_skipped_sending', 'has_sent_receipt', 
        'has_approved', 'has_set_recurring_schedule_plan', 'has_set_recurring_delivery_plan', 
        'has_set_recurring_payment_plan', 'has_approved_recurring_settings', 
    ];

    /* 
     *  Returns the resource type
     */
    public function getResourceTypeAttribute()
    {
        return strtolower(class_basename($this));
    }

    /* 
     *  Returns the total payment made to this invoice
     */
    public function getTransactionTotalAttribute()
    {
        return $this->payments()->sum('payment_amount') ?? 0;
    }

    /* 
     *  Returns the total refund paid to this invoice
     */
    public function getRefundTotalAttribute()
    {
        return $this->refunds()->sum('payment_amount') ?? 0;
    }

    /* 
     *  Returns the outstanding balance after all payments
     */
    public function getOutstandingBalanceAttribute()
    {
        $balance = $this->grand_total - $this->transaction_total;

        return $balance > 0 ? $balance : 0;
    }

    /*
     *  Returns a confirmation of payment
     */
    public function getHasPaidAttribute()
    {
        //  Have we ever cancelled before
        if ( $this->paymentCanceledActivities()->count() ) {

            //  Then that means we have recorded payment before
            if ( $this->paidActivities()->count() ) {

                //  Then we can compare the two dates and see which was more recent
                $cancelledDate = strtotime( $this->paymentCanceledActivities()->first()->created_at );
                $recordedPaymentDate = strtotime( $this->paidActivities()->first()->created_at );

                if ($recordedPaymentDate > $cancelledDate) {

                    // The user has been confirmed as paid
                    return [
                            'status' => true,
                            'last_paid_date' => $this->paidActivities()->first()->created_at,
                        ];

                }

            }

        //  Otherwise if we have ever paid before
        } elseif ( $this->paidActivities()->count() ) {

                    // The user has been confirmed as paid
                    return [
                        'status' => true,
                        'last_paid_date' => $this->paidActivities()->first()->created_at,
                    ];

        }
        
        return ['status' => false, 'last_paid_date' => null];

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
     *  Returns a confirmation of cancellation
     */
    public function gethasCancelledAttribute()
    {
        //  Check if we have cancelled before
        $status = $this->paymentCanceledActivities()->count() ? true : false;

        //  Return the status
        return ['status' => $status, 'last_cancelled_date' => $this->paymentCanceledActivities()->first()->created_date ?? null];
    }

    /*
     *  Returns a confirmation of sending the invoice via email/sms
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
     *  Returns a confirmation of avoiding to send the invoice via email/sms
     */
    public function gethasSkippedSendingAttribute()
    {
        //  Check if we have skipped sending before
        $status = $this->skippedSendingActivities()->count() ? true : false;

        //  Return the status
        return ['status' => $status, 'last_skipped_date' => $this->skippedSendingActivities()->first()->created_date ?? null];
    }

    /*
     *  Returns a confirmation of sending the invoice receipt via email/sms
     */
    public function gethasSentReceiptAttribute()
    {
        //  Check if we have skipped sending before
        $status = $this->sentReceiptActivities()->count() ? true : false;

        //  Return the status
        return ['status' => $status, 'last_sent_date' => $this->sentReceiptActivities()->first()->created_date ?? null, 
                'count' =>  $this->sentReceiptActivities()->count()];
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
     *  Returns a confirmation updating the recurring invoice schedule details
     */
    public function gethasSetRecurringSchedulePlanAttribute()
    {
        //  Check if we have updated the recurring schedule plan before
        $status = $this->recurringScheduleActivities()->count() ? true : false;

        //  Return the status
        return ['status' => $status, 'last_updated' => $this->recurringScheduleActivities()->first()->created_date ?? null];
    }

    /*
     *  Returns a confirmation updating the recurring invoice delivery details
     */
    public function gethasSetRecurringDeliveryPlanAttribute()
    {
        //  Check if we have updated the recurring delivery plan before
        $status = $this->recurringDeliveryActivities()->count() ? true : false;

        //  Return the status
        return ['status' => $status, 'last_updated' => $this->recurringDeliveryActivities()->first()->created_date ?? null];
    }

    /*
     *  Returns a confirmation updating the recurring invoice payment details
     */
    public function gethasSetRecurringPaymentPlanAttribute()
    {
        //  Check if we have updated the recurring payment plan before
        $status = $this->recurringPaymentActivities()->count() ? true : false;

        //  Return the status
        return ['status' => $status, 'last_updated' => $this->recurringPaymentActivities()->first()->created_date ?? null];
    }

    /*
     *  Returns a confirmation of the recurring invoice approval
     */
    public function gethasApprovedRecurringSettingsAttribute()
    {
        //  Check if we have approved recurring invoice before
        $status = $this->recurringApprovalActivities()->count() ? true : false;

        //  Return the status
        return ['status' => $status, 'last_approved_date' => $this->recurringApprovalActivities()->first()->created_date ?? null];
    }

    /*
     *  Returns the current invoice status
     */
    public function getStatusAttribute()
    {
        //  If paid
        if ($this->has_paid) {
            return 'Paid';

        //  If expired
        } elseif ($this->has_expired) {
            return 'Expired';

        //  If cancelled
        } elseif ($this->has_cancelled) {
            return 'Cancelled';

        //  If approved
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
