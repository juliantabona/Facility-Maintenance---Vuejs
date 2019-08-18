<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use App\Traits\OrderTraits;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Order extends Model
{
    use Dataviewer;
    use OrderTraits;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'meta_data' => 'array',
        'currency_type' => 'array',
        'line_items' => 'array',
        'shipping_lines' => 'array',
        'tax_lines' => 'array',
        'billing_info' => 'array',
        'shipping_info' => 'array',
    ];

    protected $with = ['lifecycle'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        /*  Basic Info  */
        'parent_id', 'number', 'order_key', 'status', 'currency_type', 'cart_hash', 'meta_data', 'date_completed',

        /*  Item Info  */
        'line_items',

        /*  Shipping Info  */
        'shipping_lines',

        /*  Grand Total, Subtotal, Shipping Total, Discount Total  */
        'cart_total', 'shipping_total', 'discount_total', 'grand_total',

        /*  Tax Info  */
        'cart_tax', 'shipping_tax', 'discount_tax', 'grand_total_tax', 'prices_include_tax', 'tax_lines',

        /*  Customer Info  */
        'client_id', 'client_type', 'customer_ip_address', 'customer_user_agent', 'customer_note',
        'billing_info', 'shipping_info',

        /*  Payment Info  */
        'payment_method', 'payment_method_title', 'transaction_id', 'date_paid',

        /*  Store, Company & Branch Info  */
        'store_id', 'company_branch_id', 'company_id',
    ];

    protected $allowedFilters = [
        'id', 'parent_id', 'number', 'order_key', 'status', 'currency_type', 'cart_hash', 'meta_data', 'date_completed',
        'line_items', 'shipping_lines', 'cart_total', 'shipping_total', 'discount_total', 'grand_total',
        'cart_tax', 'shipping_tax', 'discount_tax', 'grand_total_tax', 'prices_include_tax', 'tax_lines',
        'payment_method', 'payment_method_title', 'transaction_id', 'date_paid',
        'store_id', 'company_branch_id', 'company_id', 'created_at',
    ];

    protected $orderable = [
        'id', 'parent_id', 'number', 'order_key', 'status', 'currency_type', 'cart_hash', 'meta_data', 'date_completed',
        'line_items', 'shipping_lines', 'cart_total', 'shipping_total', 'discount_total', 'grand_total',
        'cart_tax', 'shipping_tax', 'discount_tax', 'grand_total_tax', 'prices_include_tax', 'tax_lines',
        'payment_method', 'payment_method_title', 'transaction_id', 'date_paid',
        'store_id', 'company_branch_id', 'company_id', 'created_at',
    ];

    public function store()
    {
        return $this->belongsTo('App\Store', 'store_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function companyBranch()
    {
        return $this->belongsTo('App\CompanyBranch', 'company_branch_id');
    }

    public function client()
    {
        //  Get the dynamic class e.g \App\User or App\Company e.t.c
        $dynamicModel = oq_generateDynamicModel($this->client_type);

        //  Check if this is a valid dynamic class
        if (class_exists($dynamicModel)) {
            return $this->hasOne($dynamicModel, 'id', 'client_id');
        }
    }

    public function transactions()
    {
        return $this->morphMany('App\Transaction', 'trackable')
                    ->orderBy('created_at', 'desc');
    }

    public function refunds()
    {
        return $this->morphMany('App\Refund', 'trackable')
                    ->orderBy('created_at', 'desc');
    }

    /*  Get the documents relating to this order. These are various files such as order documents.
     *  Basically any file/image the user wants to save to this order is stored in this relation
     */

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    public function files()
    {
        return $this->documents()->where('type', 'file');
    }

    public function invoices()
    {
        return $this->morphToMany('App\Invoice', 'trackable', 'invoice_allocations');
    }

    public function lifecycle()
    {
        return $this->morphToMany('App\Lifecycle', 'trackable', 'lifecycle_allocations');
    }

    public function comments()
    {
        return $this->morphToMany('App\Comment', 'trackable', 'comment_allocations');
    }

    public function messages()
    {
        return $this->comments()->where('type', 'message');
    }

    public function reviews()
    {
        return $this->comments()->where('type', 'review');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->where('trackable_type', 'order');
    }

    public function createdActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'order')->where('type', 'created')
                    ->orderBy('created_at', 'desc');
    }

    public function approvedActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'order')->where('type', 'approved')
                    ->orderBy('created_at', 'desc');
    }

    public function updatedLifecycleStagesActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'order')->where('type', 'updated lifecycle stage')
                    ->orderBy('created_at', 'desc');
    }

    public function reversedLifecycleStagesActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'order')->where('type', 'reversed lifecycle stage')
                    ->orderBy('created_at', 'desc');
    }

    protected $appends = [
                            'average_rating', 'model_type', 'created_at_format',
                            'phone_list', 'transaction_total', 'refund_total', 'outstanding_balance',
                            'status_title', 'status_description', 'lifecycle_status_activity',
                            'current_lifecycle_stage', 'current_lifecycle_main_status', 'lifecycle_stages',
                        ];

    public function getAverageRatingAttribute()
    {
        $reviews = $this->reviews()->get() ?? [];

        if( count( $reviews ) ){
            return collect( $reviews )->avg('rating_value');
        }
    }

    //  Getter for the type of model
    public function getModelTypeAttribute()
    {
        return Str::snake(class_basename($this));
    }

    //  Getter for the phone list
    public function getPhoneListAttribute()
    {
        $phones = $this->billing_info['phones'] ?? [];
        $phoneList = '';

        foreach ($phones as $key => $phone) {
            $phoneList .= ($key != 0 ? ', ' : '').'(+'.$phone['calling_code']['calling_code'].') '.$phone['number'];
        }

        return $phoneList;
    }

    //  Get the refund total amount
    public function getTransactionTotalAttribute()
    {
        return $this->transactions()->sum('amount');
    }

    //  Get the refund total amount
    public function getRefundTotalAttribute()
    {
        return $this->refunds()->sum('amount');
    }

    public function getOutstandingBalanceAttribute()
    {
        $balance = $this->grand_total - $this->transaction_total;

        return $balance > 0 ? $balance : 0;
    }

    public function getCreatedAtFormatAttribute()
    {
        return $this->created_at->format('M d Y @ H:i');
    }

    public function getStatusTitleAttribute()
    {
        return ucfirst(str_replace('-', ' ', $this->status));
    }

    public function getStatusDescriptionAttribute()
    {
        $status = $this->status;

        if ($status == 'completed') {
            $description = 'Order has been completed';
        } elseif ($status == 'paid') {
            $description = 'Order has been paid';
        } elseif ($status == 'pending-payment') {
            $description = 'Order has not been paid';
        } elseif ($status == 'failed-payment') {
            $description = 'Order payment failed';
        } elseif ($status == 'verify-payment') {
            $description = 'Order payment needs to be verified manually';
        } elseif ($status == 'pending-delivery') {
            $description = 'Order has not been delivered';
        } elseif ($status == 'verify-delivery') {
            $description = 'Order delivery needs to be verified manually';
        } elseif ($status == 'delivered') {
            $description = 'Order has been delivered';
        } elseif ($status == 'pending-refund') {
            $description = 'Order requires a refund';
        } elseif ($status == 'refunded') {
            $description = 'Order has been refunded';
        } elseif ($status == 'cancelled') {
            $description = 'Order has been cancelled';
        } else {
            $description = 'unknown';
        }

        return $description;
    }

    public function getLifecycleStagesAttribute()
    {
        $updatedLifecycleStages = $this->updatedLifecycleStagesActivities()->orderBy('created_at', 'asc')->get();
        $reversedLifecycleStages = $this->reversedLifecycleStagesActivities()->orderBy('created_at', 'asc')->get();

        $originalStages = collect($updatedLifecycleStages)->filter(function ($stage) {
            return !$stage['activity']['updated_stage_id'];
        });

        $updatedStages = collect($updatedLifecycleStages)->filter(function ($stage) {
            return $stage['activity']['updated_stage_id'];
        });

        $allStages = [];

        //  For each original stage
        foreach ($originalStages as $originalStage) {
            //  Check if we have any updated copies of the original
            foreach ($updatedStages as $updatedStage) {
                //  Check if the updated copy was created after the original copy
                if ($updatedStage->created_at->getTimestamp() > $originalStage->created_at->getTimestamp()) {
                    //  Check if the update target id is the same as the original stage id
                    if ($updatedStage['activity']['updated_stage_id'] == $originalStage['id']) {
                        //  This means the original has been updated, therefore we need to update the original copy
                        $originalStage = $updatedStage;
                    }
                }
            }

            $reversed = false;

            //  Check if we have reversed/undid this update
            foreach ($reversedLifecycleStages as $reversedStage) {
                //  Check if the reversed update was created after the original/updated copy
                if ($reversedStage->created_at->getTimestamp() > $originalStage->created_at->getTimestamp()) {
                    //  Check if the update target type and instance is the same as the original type and instance
                    if ($reversedStage['activity']['type'] == $originalStage['activity']['type'] &&
                        $reversedStage['activity']['instance'] == $originalStage['activity']['instance']) {
                        //  This means the original has been updated, therefore we need to update the original copy
                        $reversed = true;
                    }
                }
            }

            if (!$reversed) {
                array_push($allStages, $originalStage);
            }
        }

        return $allStages;
    }

    public function getCurrentLifecycleStageAttribute()
    {
        if (count($this->lifecycle_stages)) {
            //  Return the last lifecycle stage
            return $this->lifecycle_stages[0];
        }
    }

    public function gethasLifecycleAttribute()
    {
        return count($this->lifecycle_stages) ? true : false;
    }

    public function getLifecycleStatusActivityAttribute()
    {
        //  Get all the lifecycle activities in ascending order from the oldest to the latest activity
        $lifecycleStages = $this->recentActivities()
                                ->orderBy('created_at', 'asc')
                                ->whereIn('type', ['created', 'updated lifecycle stage', 'reversed lifecycle stage'])
                                ->get();

        $activityHistory = [];

        foreach ($lifecycleStages as $key => $currStage) {
            
            $createdStatus = ($currStage['type'] == 'created');
            $updatedStatus = ($currStage['type'] == 'updated lifecycle stage');
            $reversedStatus = ($currStage['type'] == 'reversed lifecycle stage');

            //  Current stage details
            $currType = $currStage['activity']['type'];
            $currInstance = $currStage['activity']['instance'];
            $currIsPending = ($currStage['activity']['pending_status'] == 'true') ?? false;
            $currIsPendingReason = 
                //  Check if we have a pending_status_reason
                ((isset($currStage['activity']['pending_status_reason']) && !empty($currStage['activity']['pending_status_reason'])) ? (
                    //  Check if the pending_status_reason is equal to "Other"
                    ($currStage['activity']['pending_status_reason']) == 'Other' 
                        //  if the pending_status_reason is equal to "Other" then use the other_pending_status_reason
                        ? $currStage['activity']['other_pending_status_reason']
                        //  Otherwise use the pending_status_reason value
                        : $currStage['activity']['pending_status_reason'])
                //  If we don't have a pending_status_reason then set the value to null
                : null);

            $currIsSkipped = ($currStage['activity']['skip_status'] == 'true') ?? false;
            $currIsSkippedReason = 
                //  Check if we have a skip_status_reason
                ((isset($currStage['activity']['skip_status_reason']) && !empty($currStage['activity']['skip_status_reason'])) ? (
                    //  Check if the skip_status_reason is equal to "Other"
                    ($currStage['activity']['skip_status_reason']) == 'Other' 
                        //  if the skip_status_reason is equal to "Other" then use the other_skip_status_reason
                        ? $currStage['activity']['other_skip_status_reason']
                        //  Otherwise use the skip_status_reason value
                        : $currStage['activity']['skip_status_reason'])
                //  If we don't have a skip_status_reason then set the value to null
                : null);
            $currIsManualVerification = ($currStage['activity']['manual_verification_status'] == 'true') ?? false;
            $currIsCancelled = ($currStage['activity']['cancelled_status'] == 'true') ?? false;
            $currIsCancelledReason = 
                //  Check if we have a cancelled_status_reason
                ((isset($currStage['activity']['cancelled_status_reason']) && !empty($currStage['activity']['cancelled_status_reason'])) ? (
                    //  Check if the cancelled_status_reason is equal to "Other"
                    ($currStage['activity']['cancelled_status_reason']) == 'Other' 
                        //  if the cancelled_status_reason is equal to "Other" then use the other_cancelled_status_reason
                        ? $currStage['activity']['other_cancelled_status_reason']
                        //  Otherwise use the cancelled_status_reason value
                        : $currStage['activity']['cancelled_status_reason'])
                //  If we don't have a cancelled_status_reason then set the value to null
                : null);

            //  Previous stage details
            if ($key > 0) {
                $prevStage = $lifecycleStages[$key - 1];
                $prevType = $prevStage['activity']['type'] ?? null;
                $prevInstance = $prevStage['activity']['instance'] ?? null;
                $prevIsPending = ($prevStage['activity']['pending_status'] == 'true') ?? null;
                $prevIsSkipped = ($prevStage['activity']['skip_status'] == 'true') ?? null;
                $prevIsManualVerification = ($prevStage['activity']['manual_verification_status'] == 'true') ?? null;
                $prevIsCancelled = ($prevStage['activity']['cancelled_status'] == 'true') ?? null;
            } else {
                $prevType = null;
                $prevInstance = null;
            }

            //  If this is a type of created
            if ($createdStatus) {
                array_push($activityHistory, [
                    'title' => 'Open',
                    'description' => 'Received on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA')
                ]);

            //  If this is a type of updated lifecycle
            } elseif ($updatedStatus) {

                //  If the current stage is pending and is a payment stage
                if ($currIsPending == true) {
                    if ($currType == 'payment') {
                        array_push($activityHistory, [
                            'title' => 'Pending Payment',
                            'description' => 'Set to pending payment on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                            'reason' => $currIsPendingReason
                            
                        ]);
                    } elseif ($currType == 'delivery') {
                        array_push($activityHistory, [
                            'title' => 'Pending Delivery',
                            'description' => 'Set to pending delivery on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                            'reason' => $currIsPendingReason
                        ]);
                    }
                }elseif ($currIsSkipped == true) {
                    if ($currType == 'payment') {
                        array_push($activityHistory, [
                            'title' => 'Skipped Payment',
                            'description' => 'Payment was skipped on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                            'reason' => $currIsSkippedReason
                        ]);
                    } elseif ($currType == 'delivery') {
                        array_push($activityHistory, [
                            'title' => 'Skipped Delivery',
                            'description' => 'Delivery was skipped on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                            'reason' => $currIsSkippedReason
                        ]);
                    }
                }elseif ($currIsCancelled == true) {
                    array_push($activityHistory, [
                        'title' => 'Cancelled',
                        'description' => 'Set to cancelled on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                        'reason' => $currIsCancelledReason
                    ]);
                } elseif ($currIsCancelled == false && $prevIsCancelled == true) {
                    //  Then this is an undo setting the status to delivered
                    array_push($activityHistory, [
                        'title' => 'Undo Cancellation',
                        'description' => 'Reversed to undo cancellation. Updated on '.(new Carbon($currStage['created_at']))->format('M d Y @ H:iA'),
                    ]);
                } else{
                    if ($currType == 'payment') {
                        array_push($activityHistory, [
                            'title' => 'Paid',
                            'description' => 'Paid amount of '. $currStage['activity']['meta']['payment_amount']
                                            .' via ' . $currStage['activity']['meta']['payment_method']
                                            .' on ' . (new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                        ]);
                    } elseif ($currType == 'delivery') {
                        array_push($activityHistory, [
                            'title' => 'Delivered',
                            'description' => 'Delivered via '. $currStage['activity']['meta']['courier']
                                            .' on ' . (new Carbon($currStage['activity']['meta']['date_delivered']))->format('M d Y')
                                            .' @ ' . (new Carbon($currStage['activity']['meta']['time_delivered']))->format('H:iA'),
                        ]);
                    } elseif ($currType == 'closed') {
                        array_push($activityHistory, [
                            'title' => 'Completed',
                            'description' => 'Completed on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                        ]);
                    }
                }

            //  If this is a type of reversed lifecycle
            } elseif ($reversedStatus) {

                //  If the current stage is pending and is a payment stage
                if ($currIsPending == true) {
                    if ($currType == 'payment') {
                        //  Then this is an undo pending payment
                        array_push($activityHistory, [
                            'title' => 'Resumed',
                            'description' => 'Resumed order after pending payment on '.(new Carbon($currStage['created_at']))->format('M d Y @ H:iA'),
                        ]);
                    } elseif ($currType == 'delivery') {
                        //  Then this is an undo pending delivery
                        array_push($activityHistory, [
                            'title' => 'Resumed',
                            'description' => 'Resumed order after pending delivery on '.(new Carbon($currStage['created_at']))->format('M d Y @ H:iA'),
                        ]);
                    }
                }elseif ($currIsSkipped == true) {
                    if ($currType == 'payment') {
                        array_push($activityHistory, [
                            'title' => 'Undo Skip Payment',
                            'description' => 'Reversed to undo skipping payment. Updated on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                        ]);
                    } elseif ($currType == 'delivery') {
                        array_push($activityHistory, [
                            'title' => 'Undo Skip Delivery',
                            'description' => 'Reversed to undo skipping payment. Updated on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                        ]);
                    }
                } else{
                    if ($currType == 'payment') {
                        //  Then this is an undo setting the status to paid
                        array_push($activityHistory, [
                            'title' => 'Undo Payment',
                            'description' => 'Reversed to undo payment. Updated on '.(new Carbon($currStage['created_at']))->format('M d Y @ H:iA'),
                        ]);
                    } elseif ($currType == 'delivery') {
                        //  Then this is an undo setting the status to delivered
                        array_push($activityHistory, [
                            'title' => 'Undo Delivery',
                            'description' => 'Reversed to undo delivery. Updated on '.(new Carbon($currStage['created_at']))->format('M d Y @ H:iA'),
                        ]);
                    } elseif ($currType == 'closed') {
                        //  Then this is an undo setting the status to delivered
                        array_push($activityHistory, [
                            'title' => 'Undo Completed',
                            'description' => 'Reversed to undo completed. Updated on '.(new Carbon($currStage['created_at']))->format('M d Y @ H:iA'),
                        ]);
                    }
                }
            }

            //  Add extra details linked to this stage such as who created the activity
            if( isset($activityHistory[$key]['title']) && !empty($activityHistory[$key]['title']) ){
                $activityHistory[$key]['type'] = $currType ?? 'open';
                $activityHistory[$key]['instance'] =$currInstance ?? 1;
                $activityHistory[$key]['created_by'] = $currStage->createdBy->only(['id', 'first_name', 'last_name', 'full_name']);
            }

        }

        return $activityHistory;
    }

    public function getCurrentLifecycleMainStatusAttribute()
    {
        $lifecycleHistory = $this->getLifecycleStatusActivityAttribute() ?? [];

        if (count($lifecycleHistory)) {

            //  Get the last in ativity in the lifecycle history
            return end($lifecycleHistory);

        }

    }


}
