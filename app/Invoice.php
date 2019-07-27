<?php

namespace App;

use DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Traits\InvoiceTraits;

Relation::morphMap([
    'jobcard' => 'App\Jobcard',
    'order' => 'App\Order',
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
        'currency_type' => 'array',
        'calculated_taxes' => 'array',
        'customized_company_details' => 'array',
        'customized_client_details' => 'array',
        'table_columns' => 'array',
        'items' => 'array',
        'notes' => 'array',
        'colors' => 'array',
        'recurring_settings' => 'array',
    ];

    protected $dates = ['created_date_value', 'expiry_date_value'];

    protected $with = ['reminders'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'heading', 'reference_no_title', 'reference_no_value', 'created_date_title', 'created_date_value',
        'expiry_date_title', 'expiry_date_value', 'sub_total_title', 'sub_total_value', 'grand_total_title', 'grand_total_value',
        'currency_type', 'calculated_taxes', 'invoice_to_title', 'customized_company_details', 'customized_client_details', 'client_id', 'client_type',
        'table_columns', 'items', 'notes', 'colors', 'footer', 'isRecurring', 'recurring_settings', 'invoice_parent_id', 'quotation_id',
        'company_branch_id', 'company_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //  Hide the recentActivities from the returned results
        //  since they can be way too many
        'recentActivities',
    ];

    protected $allowedFilters = [
        'id', 'reference_no_value', 'grand_total_value', 'created_date_value', 'expiry_date_value', 'created_at',

        //  Nested within JSON
        //  'notes > details',

        // Nested within relationhip
        'client.id', 'client.name', 'client.city', 'client.state_or_region', 'client.address', 'client.industry', 'client.type', 'client.website_link', 'client.phone_ext', 'client.phone_num', 'client.email', 'client.created_at',
    ];

    protected $orderable = [
        'id', 'reference_no_value', 'grand_total_value', 'created_date_value', 'expiry_date_value', 'created_at',
    ];

    /**
     * Get all of the owning trackable models.
     */
    public function trackable()
    {
        return $this->morphTo();
    }

    public function quotation()
    {
        return $this->belongsTo('App\Quotation', 'quotation_id');
    }

    public function childInvoices()
    {
        return $this->hasMany('App\Invoice', 'invoice_parent_id');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->where('trackable_type', 'invoice')
                    ->orderBy('created_at', 'desc');
    }

    public function approvedActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where('type', 'approved');
    }

    public function sentActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where(function ($q) {
            $q->where('type', 'sent email')->orWhere('type', 'sent sms');
        });
    }

    public function skippedSendingActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where('type', 'skipped sending');
    }

    public function sentReceiptActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where(function ($q) {
            $q->where('type', 'sent receipt email')->orWhere('type', 'sent receipt sms');
        });
    }

    public function paidActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where('type', 'paid')->limit(1);
    }

    public function paymentCanceledActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where('type', 'cancelled payment')->limit(1);
    }

    public function createdActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where('type', 'created');
    }

    public function client()
    {
        return $this->belongsTo('App\Company', 'client_id');
    }

    /**
     * Get all of the invoice reminders.
     */
    public function reminders()
    {
        return $this->morphMany('App\Reminder', 'trackable');
    }

    protected $appends = ['last_approved_activity', 'last_sent_activity', 'last_skipped_sending_activity', 'last_sent_receipt_activity',
                          'last_paid_activity', 'last_payment_cancelled_activity', 'last_recurring_schedule_plan_activity',
                          'last_recurring_delivery_plan_activity', 'last_recurring_payment_plan_activity', 'last_approved_recurring_settings_activity',

                          'has_paid', 'has_expired', 'has_cancelled', 'has_sent', 'has_skipped_sending', 'has_sent_receipt', 'has_approved',
                          'has_set_recurring_schedule_plan', 'has_set_recurring_delivery_plan', 'has_set_recurring_payment_plan',
                          'has_approved_recurring_settings',
                          'current_activity_status', 'activity_count', 'sent_invoice_activity_count', 'sent_receipt_activity_count',
                        ];

    public function getLastApprovedActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where('type', 'approved')->first();
    }

    public function getLastSentActivityAttribute()
    {
        return $this->recentActivities()->select('id', 'type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where(function ($q) {
            $q->where('type', 'sent email')->orWhere('type', 'sent sms');
        })->first();
    }

    public function getLastSkippedSendingActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where('type', 'skipped sending')->first();
    }

    public function getLastSentReceiptActivityAttribute()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where(function ($q) {
            $q->where('type', 'sent receipt email')->orWhere('type', 'sent receipt sms');
        })->first();
    }

    public function getLastPaidActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where('type', 'paid')->first();
    }

    public function getLastPaymentCancelledActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where('type', 'cancelled payment')->first();
    }

    public function getLastRecurringSchedulePlanActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where('type', 'updated recurring schedule')->first();
    }

    public function getLastRecurringDeliveryPlanActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where('type', 'updated recurring delivery')->first();
    }

    public function getLastRecurringPaymentPlanActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where('type', 'updated recurring payment')->first();
    }

    public function getlastApprovedRecurringSettingsActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where('type', 'approved recurring settings')->first();
    }

    public function getHasPaidAttribute()
    {
        //  Have we ever cancelled before
        if ($this->last_payment_cancelled_activity) {
            //  Then that means we have recorded payment before
            if ($this->last_paid_activity) {
                //  Then we can compare the two dates and see which was more recent
                $cancelledDate = strtotime($this->last_payment_cancelled_activity->created_at);
                $recordedPaymentDate = strtotime($this->last_paid_activity->created_at);

                if ($recordedPaymentDate > $cancelledDate) {
                    // The user has been confirmed as paid
                    return true;
                } else {
                    //  Payment record was cancelled
                    return false;
                }
            } else {
                return false;
            }
        } elseif ($this->last_paid_activity) {
            return true;
        } else {
            return false;
        }
    }

    public function gethasExpiredAttribute()
    {
        $expiryDate = $this->expiry_date_value->getTimestamp();
        $now = Carbon::now()->getTimestamp();

        return ($now > $expiryDate) ? true : false;
    }

    public function gethasCancelledAttribute()
    {
        return $this->last_payment_cancelled_activity ? true : false;
    }

    public function gethasSentAttribute()
    {
        return $this->last_sent_activity ? true : false;
    }

    public function gethasSkippedSendingAttribute()
    {
        return $this->last_skipped_sending_activity ? true : false;
    }

    public function gethasSentReceiptAttribute()
    {
        return $this->last_sent_receipt_activity ? true : false;
    }

    public function gethasApprovedAttribute()
    {
        return $this->last_approved_activity ? true : false;
    }

    public function gethasSetRecurringSchedulePlanAttribute()
    {
        return $this->last_recurring_schedule_plan_activity ? true : false;
    }

    public function gethasSetRecurringDeliveryPlanAttribute()
    {
        return $this->last_recurring_delivery_plan_activity ? true : false;
    }

    public function gethasSetRecurringPaymentPlanAttribute()
    {
        return $this->last_recurring_payment_plan_activity ? true : false;
    }

    public function gethasApprovedRecurringSettingsAttribute()
    {
        return $this->last_approved_recurring_settings_activity ? true : false;
    }

    public function getCurrentActivityStatusAttribute()
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

    public function getActivityCountAttribute()
    {
        $count = $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'invoice')
                                          ->select(DB::raw('count(*) as total'))
                                          ->groupBy('trackable_type')
                                          ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    public function getSentInvoiceActivityCountAttribute()
    {
        $count = $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'invoice')->where(function ($q) {
            $q->where('type', 'sent email')->orWhere('type', 'sent sms');
        })
                                          ->select(DB::raw('count(*) as total'))
                                          ->groupBy('type')
                                          ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    public function getSentReceiptActivityCountAttribute()
    {
        $count = $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'invoice')
                                          ->select(DB::raw('count(*) as total'))
                                          ->where('type', 'sent receipt')
                                          ->groupBy('type')
                                          ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }
}
