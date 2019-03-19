<?php

namespace App;

use DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Traits\QuotationTraits;

Relation::morphMap([
    'jobcard' => 'App\Jobcard',
]);

class Quotation extends Model
{
    use Dataviewer;
    use QuotationTraits;

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
        'currency_type', 'calculated_taxes', 'quotation_to_title', 'customized_company_details', 'customized_client_details', 'client_id',
        'table_columns', 'items', 'notes', 'colors', 'footer', 'company_branch_id', 'company_id',
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

    public function invoices()
    {
        return $this->hasMany('App\Invoice', 'quotation_id');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->orderBy('created_at', 'desc');
    }

    public function createdActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('type', 'created');
    }

    public function approvedActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('type', 'approved');
    }

    public function sentActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where(function ($q) {
            $q->where('type', 'sent email')->orWhere('type', 'sent sms');
        });
    }

    public function skippedSendingActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('type', 'skipped sending');
    }

    public function convertedActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('type', 'converted');
    }

    public function client()
    {
        return $this->belongsTo('App\Company', 'client_id');
    }

    /**
     * Get all of the quotation reminders.
     */
    public function reminders()
    {
        return $this->morphMany('App\Reminder', 'trackable');
    }

    protected $appends = ['last_approved_activity', 'last_sent_activity', 'last_skipped_sending_activity', 'last_converted_activity',
                          'has_approved', 'has_sent', 'has_skipped_sending', 'has_converted', 'has_expired',
                          'current_activity_status', 'activity_count', 'sent_quotation_activity_count', 'converted_activity_count',
                        ];

    public function getLastApprovedActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('type', 'approved')->first();
    }

    public function getLastSentActivityAttribute()
    {
        return $this->recentActivities()->select('id', 'type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where(function ($q) {
            $q->where('type', 'sent email')->orWhere('type', 'sent sms');
        })->first();
    }

    public function getLastSkippedSendingActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('type', 'skipped sending')->first();
    }

    public function getLastConvertedActivityAttribute()
    {
        return $this->recentActivities()->select('type', 'created_by', 'created_at')->where('trackable_id', $this->id)->where('type', 'converted')->first();
    }

    public function gethasApprovedAttribute()
    {
        return $this->last_approved_activity ? true : false;
    }

    public function gethasSentAttribute()
    {
        return $this->last_sent_activity ? true : false;
    }

    public function gethasSkippedSendingAttribute()
    {
        return $this->last_skipped_sending_activity ? true : false;
    }

    public function gethasConvertedAttribute()
    {
        return $this->last_converted_activity ? true : false;
    }

    public function gethasExpiredAttribute()
    {
        $expiryDate = $this->expiry_date_value->getTimestamp();
        $now = Carbon::now()->getTimestamp();

        return ($now > $expiryDate) ? true : false;
    }

    public function getCurrentActivityStatusAttribute()
    {
        //  If converted
        if ($this->has_converted) {
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

    public function getActivityCountAttribute()
    {
        $count = $this->recentActivities()->where('trackable_id', $this->id)
                                          ->select(DB::raw('count(*) as total'))
                                          ->groupBy('trackable_type')
                                          ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    public function getSentQuotationActivityCountAttribute()
    {
        $count = $this->recentActivities()->where('trackable_id', $this->id)->where(function ($q) {
            $q->where('type', 'sent email')->orWhere('type', 'sent sms');
        })
                                          ->select(DB::raw('count(*) as total'))
                                          ->groupBy('type')
                                          ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }

    public function getConvertedActivityCountAttribute()
    {
        $count = $this->recentActivities()->select(DB::raw('count(*) as total'))
                                          ->where('trackable_id', $this->id)
                                          ->where('type', 'converted')
                                          ->groupBy('type')
                                          ->first();

        return $count ? $count->only(['total']) : ['total' => 0];
    }
}
