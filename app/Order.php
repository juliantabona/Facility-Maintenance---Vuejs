<?php

namespace App;

use Carbon\Carbon;
use App\Traits\OrderTraits;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'store' => 'App\Store',
]);

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
        'created_date',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        /*  Basic Info  */
        'number', 'currency', 'created_date', 'manual_status',

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
        'customer_id', 'customer_note', 'billing_info', 'shipping_info',

        /*  Merchant Info  */
        'merchant_id', 'merchant_type', 'merchant_info',

        /*  Meta Data  */
        'metadata',
    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /*
     *  Scope orders by status
     */
    public function scopeOpen($query)
    {
        return $query->where('manual_status', 'open');
    }

    public function scopeCancelled($query)
    {
        return $query->where('manual_status', 'cancelled');
    }

    public function scopePendingPayment($query)
    {
        return $query->where('manual_status', 'pending payment');
    }

    public function scopePaid($query)
    {
        return $query->where('manual_status', 'paid');
    }

    public function scopePendingDelivery($query)
    {
        return $query->where('manual_status', 'pending delivery');
    }

    public function scopeDelivered($query)
    {
        return $query->where('manual_status', 'delivered');
    }

    public function scopeCompleted($query)
    {
        return $query->where('manual_status', 'closed');
    }

    /*
     *  Returns the merchant of the order
     *  This refers to the seller of the goods/services
     */
    public function merchant()
    {
        return $this->morphTo();
    }

    /*
     *  Returns the customer of the order
     *  This refers to the consumer of the goods/services
     */
    public function customer()
    {
        return $this->belongsTo('App\Contact', 'customer_id');
    }

    /*
     *  Returns the reference of the order
     *  This refers to the user who submitted/placed the order
     */
    public function reference()
    {
        return $this->belongsTo('App\Contact', 'reference_id');
    }

    /*
     *  Scope by type
     */
    public function scopeWhereType($query, $type)
    {
        return $query;
    }

    /*
     *  Returns documents associated with this order. These are various files such as images,
     *  videos, files and so on. Basically any file/image/video the user wants to save to
     *  this order is stored in this relation
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
     *  Returns the order taxes
     */
    public function taxes()
    {
        return $this->morphToMany('App\Tax', 'owner', 'tax_allocations')->withTimestamps();
    }

    /*
     *  Returns the order discounts
     */
    public function discounts()
    {
        return $this->morphToMany('App\Discount', 'owner', 'discount_allocations')->withTimestamps();
    }

    /*
     *  Returns the order coupons
     */
    public function coupons()
    {
        return $this->morphToMany('App\Coupon', 'owner', 'coupon_allocations')->withTimestamps();
    }

    /*
     *  Returns messages sent to this order
     */
    public function messages()
    {
        return $this->morphMany('App\Message', 'owner')->latest();
    }

    /*
     *  Returns reviews sent to this order
     */
    public function reviews()
    {
        return $this->morphMany('App\Review', 'owner')->latest();
    }

    /*
     *  Returns the invoiceS owned by this order
     */
    public function invoices()
    {
        return $this->morphMany('App\Invoice', 'owner');
    }

    /*
     *  Returns the lifecycle owned by this order
     */
    public function lifecycle()
    {
        return $this->morphToMany('App\Lifecycle', 'owner', 'lifecycle_allocations')->withTimestamps();
    }

    /*
     *  Returns recent activities owned by this store
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'owner')->orderBy('created_at', 'desc');
    }

    /*
     *  Returns store creation activity
     */
    public function createdActivities()
    {
        return $this->recentActivities()->whereType('created');
    }

    /*
     *  Returns store approval activity
     */
    public function approvedActivities()
    {
        return $this->recentActivities()->whereType('approved');
    }

    /*
     *  Returns order lifecycle progress update activity
     */
    public function updatedLifecycleStagesActivities()
    {
        return $this->recentActivities()->whereType('updated lifecycle stage');
    }

    /*
     *  Returns order lifecycle progress reversal activity
     */
    public function reversedLifecycleStagesActivities()
    {
        return $this->recentActivities()->whereType('reversed lifecycle stage');
    }

    protected $appends = [
        'resource_type', 'transaction_total', 'refund_total', 'outstanding_balance',
        'created_at_format', 'lifecycle_status_title', 'lifecycle_status_description',
        'lifecycle_history', 'lifecycle_flow',
    ];

    /*
     *  Returns the resource type
     */
    public function getResourceTypeAttribute()
    {
        return strtolower(class_basename($this));
    }

    /*
     *  Returns the total payment made to this order
     */
    public function getTransactionTotalAttribute()
    {
        $total = 0;

        foreach ($this->invoices as $invoice) {
            $total += $invoice->transaction_total;
        }

        return $total;
    }

    /*
     *  Returns the total refund paid to this order
     */
    public function getRefundTotalAttribute()
    {
        $total = 0;

        foreach ($this->invoices as $invoice) {
            $total += $invoice->refund_total;
        }

        return $total;
    }

    /*
     *  Returns the outstanding balance after all payments
     */
    public function getOutstandingBalanceAttribute()
    {
        $total = 0;

        foreach ($this->invoices as $invoice) {
            $total += $invoice->outstanding_balance;
        }

        return $total;
    }

    /*
     *  Returns the created at formatted for readability
     */
    public function getCreatedAtFormatAttribute()
    {
        return $this->created_at->format('M d Y @ H:i');
    }

    /*
     *  Returns the lifecycle history to show all the activities that occured
     *  whilst the lifecycle kept changing states until current date and time.
     *  It gives insights by naming and describing all activities as well as
     *  revealing the user responsible for a given activity
     */
    public function getLifecycleHistoryAttribute()
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

            //  If this is a type of created
            if ($createdStatus) {
                $currType = 'open';
                $currInstance = 1;

                array_push($activityHistory, [
                    'title' => 'Open',
                    'description' => 'Received on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                    'status' => 'active',
                ]);

            //  If this is a type of updated lifecycle
            } else {
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
                $prevStage = $lifecycleStages[$key + 1];
                $prevType = $prevStage['activity']['type'] ?? null;
                $prevInstance = $prevStage['activity']['instance'] ?? null;
                $prevIsPending = (($prevStage['activity']['pending_status'] ?? null) == 'true');
                $prevIsSkipped = (($prevStage['activity']['skip_status'] ?? null) == 'true');
                $prevIsManualVerification = (($prevStage['activity']['manual_verification_status'] ?? null) == 'true');
                $prevIsCancelled = (($prevStage['activity']['cancelled_status'] ?? null) == 'true');

                //  If this is a type of updated lifecycle
                if ($updatedStatus) {
                    //  If the current stage is pending
                    if ($currIsPending == true) {
                        //  If it is pending payment
                        if ($currType == 'payment') {
                            array_push($activityHistory, [
                                'title' => 'Pending Payment',
                                'description' => 'Set to pending payment on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                                'status' => 'pending',
                                'reason' => $currIsPendingReason,
                            ]);
                        //  If it is pending delivery
                        } elseif ($currType == 'delivery') {
                            array_push($activityHistory, [
                                'title' => 'Pending Delivery',
                                'description' => 'Set to pending delivery on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                                'status' => 'pending',
                                'reason' => $currIsPendingReason,
                            ]);
                        }
                        //  If the current stage is skipped
                    } elseif ($currIsSkipped == true) {
                        //  If it is skipped payment
                        if ($currType == 'payment') {
                            array_push($activityHistory, [
                                'title' => 'Skipped Payment',
                                'description' => 'Payment was skipped on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                                'status' => 'skipped',
                                'reason' => $currIsSkippedReason,
                            ]);
                        //  If it is skipped delivery
                        } elseif ($currType == 'delivery') {
                            array_push($activityHistory, [
                                'title' => 'Skipped Delivery',
                                'description' => 'Delivery was skipped on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                                'status' => 'skipped',
                                'reason' => $currIsSkippedReason,
                            ]);
                        }
                        //  If it is cancelled
                    } elseif ($currIsCancelled == true) {
                        array_push($activityHistory, [
                            'title' => 'Cancelled',
                            'description' => 'Set to cancelled on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                            'status' => 'cancelled',
                            'reason' => $currIsCancelledReason,
                        ]);
                    //  If it was cancelled on the previous stage and not cancelled in the current stage
                    } elseif ($currIsCancelled == false && $prevIsCancelled == true) {
                        //  Then this is an undo cancellation
                        array_push($activityHistory, [
                            'title' => 'Undo Cancellation',
                            'description' => 'Reversed to undo cancellation. Updated on '.(new Carbon($currStage['created_at']))->format('M d Y @ H:iA'),
                            'status' => 'active',
                        ]);
                    //  Otherwise if it was not pending, skipped or cancelled
                    } else {
                        //  If it is payment
                        if ($currType == 'payment') {
                            array_push($activityHistory, [
                                'title' => 'Paid',
                                'description' => 'Paid amount of '.$currStage['activity']['meta']['payment_amount']
                                                .' via '.$currStage['activity']['meta']['payment_method']
                                                .' on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                                'meta_data' => $currStage['activity']['meta'],
                                'status' => 'active',
                            ]);
                        //  If it is delivery
                        } elseif ($currType == 'delivery') {
                            array_push($activityHistory, [
                                'title' => 'Delivered',
                                'description' => 'Delivered via '.$currStage['activity']['meta']['courier']
                                                .' on '.(new Carbon($currStage['activity']['meta']['date_delivered']))->format('M d Y')
                                                .' @ '.(new Carbon($currStage['activity']['meta']['time_delivered']))->format('H:iA'),
                                'meta_data' => $currStage['activity']['meta'],
                                'status' => 'active',
                            ]);
                        //  If it is close
                        } elseif ($currType == 'closed') {
                            array_push($activityHistory, [
                                'title' => 'Completed',
                                'description' => 'Completed on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                                'status' => 'active',
                            ]);
                        }
                    }

                    //  If this is a type of reversed lifecycle
                } elseif ($reversedStatus) {
                    if ($currIsPending == true) {
                        if ($currType == 'payment') {
                            //  Then this is an undo pending payment
                            array_push($activityHistory, [
                                'title' => 'Resumed',
                                'description' => 'Resumed order after pending payment on '.(new Carbon($currStage['created_at']))->format('M d Y @ H:iA'),
                                'status' => 'inactive',
                            ]);
                        } elseif ($currType == 'delivery') {
                            //  Then this is an undo pending delivery
                            array_push($activityHistory, [
                                'title' => 'Resumed',
                                'description' => 'Resumed order after pending delivery on '.(new Carbon($currStage['created_at']))->format('M d Y @ H:iA'),
                                'status' => 'inactive',
                            ]);
                        }
                    } elseif ($currIsSkipped == true) {
                        //  Then this is an undo skip payment
                        if ($currType == 'payment') {
                            array_push($activityHistory, [
                                'title' => 'Undo Skip Payment',
                                'description' => 'Reversed to undo skipping payment. Updated on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                                'status' => 'inactive',
                            ]);
                        //  Then this is an undo skip delivery
                        } elseif ($currType == 'delivery') {
                            array_push($activityHistory, [
                                'title' => 'Undo Skip Delivery',
                                'description' => 'Reversed to undo skipping payment. Updated on '.(new Carbon($currStage->created_at))->format('M d Y @ H:iA'),
                                'status' => 'inactive',
                            ]);
                        }
                    } else {
                        if ($currType == 'payment') {
                            //  Then this is an undo setting the status to paid
                            array_push($activityHistory, [
                                'title' => 'Undo Payment',
                                'description' => 'Reversed to undo payment. Updated on '.(new Carbon($currStage['created_at']))->format('M d Y @ H:iA'),
                                'status' => 'inactive',
                            ]);
                        } elseif ($currType == 'delivery') {
                            //  Then this is an undo setting the status to delivered
                            array_push($activityHistory, [
                                'title' => 'Undo Delivery',
                                'description' => 'Reversed to undo delivery. Updated on '.(new Carbon($currStage['created_at']))->format('M d Y @ H:iA'),
                                'status' => 'inactive',
                            ]);
                        } elseif ($currType == 'closed') {
                            //  Then this is an undo setting the status to delivered
                            array_push($activityHistory, [
                                'title' => 'Undo Completed',
                                'description' => 'Reversed to undo completed. Updated on '.(new Carbon($currStage['created_at']))->format('M d Y @ H:iA'),
                                'status' => 'inactive',
                            ]);
                        }
                    }
                }
            }

            //  Add extra details linked to this stage such as who created the activity
            if (isset($activityHistory[$key]['title']) && !empty($activityHistory[$key]['title'])) {
                $activityHistory[$key]['type'] = $currType;
                $activityHistory[$key]['instance'] = $currInstance;
                $activityHistory[$key]['user'] = $currStage->user->only(['id', 'first_name', 'last_name', 'full_name']);
            }
        }

        return $activityHistory;
    }

    /*
     *  Returns the lifecycle stages in their current state. It shows all the stages
     *  that the order must follow from begining to end and what their states are e.g active,
     *  pending, cancelled... Actions are also listed if available so that the user can change
     *  the stage of the lifecycle and further advance or undo past actions. Templates are
     *  provided to help the user know what form data is required when executing a given
     *  action e.g) Provide a data field called "reason" with a message to describe
     *  your reason for taking a particular action
     */
    public function getLifecycleFlowAttribute()
    {
        //  Get the lifecycle stages
        $lifecycle_stages = $this->lifecycle()->first()['stages'] ?? [];

        //  Get the lifecycle history
        $lifecycle_history_events = $this->lifecycle_history;

        //  If we have the lifecycle stages and history
        if (count($lifecycle_stages) && count($lifecycle_history_events)) {
            //  Foreach stage that we have
            foreach ($lifecycle_stages as $key => $current_stage) {
                //  Build the stage using the default stage data
                $lifecycle_stages[$key] = [
                    'name' => $current_stage['name'],
                    'description' => $current_stage['description'],
                    'type' => $current_stage['type'],
                    'instance' => $current_stage['instance'],
                    'status' => 'inactive',
                    'actions' => [],
                ];

                $current_stage = $lifecycle_stages[$key];

                //  Foreach history event
                foreach ($lifecycle_history_events as $current_event) {
                    //  If the current stage matches the current history event
                    if ($current_stage['type'] == $current_event['type'] &&
                        $current_stage['instance'] == $current_event['instance']) {
                        if ($current_event['status'] != 'inactive') {
                            //  Build the stage using the history data
                            $lifecycle_stages[$key]['name'] = $current_event['title'];
                            $lifecycle_stages[$key]['description'] = $current_event['description'];
                            $lifecycle_stages[$key]['type'] = $current_event['type'];
                            $lifecycle_stages[$key]['instance'] = $current_event['instance'];
                            $lifecycle_stages[$key]['status'] = $current_event['status'];
                            $lifecycle_stages[$key]['meta_data'] = $current_event['meta_data'] ?? null;
                        }

                        // Break out of the only this inner foreach loop
                        break 1;
                    }
                }
            }
        }

        $activeStages = collect($lifecycle_stages)->where('status', 'active');

        /*  Loop through each stage with a status set to active
         *  This means that the stage had already been updated
         *  to allow the lifecycle to proceed to the next stage
         */
        foreach ($activeStages as $key => $current_stage) {
            /*  Every stage here should have actions the user can
             *  perform to change the lifecycle progress. By default
             *  we need to make sure that all stages can be editable.
             *  After that we need to target the last stage and give
             *  it all the other actions such the ability to
             *  proceed, undo, cancel, skip, e.t.c
             */

            /*  Lets first attempt to get the next stage from the current stage.
             *  If we have a next stage then return it otherwise return nothing.
             */
            $next_stage = $lifecycle_stages[$key + 1] ?? null;

            if ($key < (count($activeStages) - 1)) {
                /*  This is the stage that should only have the edit option.
                 *  We call the getLifecycleActions() and pass the current stage
                 *  as well as the $config to instruct that we only want to add
                 *  the edit action only
                 */
                $config = ['can_only_edit' => true];
                $lifecycle_stages[$key]['actions'] = $this->getLifecycleActions($current_stage, $next_stage, $config);
            } else {
                /*  This is the stage that should allow the user to edit as well
                 *  as proceed, undo, cancel, skip, e.t.c depending on the actions
                 *  supported on the specified status on the stage. We call the
                 *  getLifecycleActions() which will require both the current
                 *  and next stage (if available) to determine the possible
                 *  actions that the user can run.
                 */

                $lifecycle_stages[$key]['actions'] = $this->getLifecycleActions($current_stage, $next_stage);
            }
        }

        //  Foreach stage remove the meta data
        foreach ($lifecycle_stages as $key => $stage) {
            //  Remove meta_data information
            unset($lifecycle_stages[$key]['meta_data']);
        }

        return $lifecycle_stages;
    }

    /*
     *  Returns an array of data the represents all the information required to make a
     *  lifecycle change. Actions define the names, icons and templates and hyperlinks
     *  that will be required for each action that can be executed at a given instance
     *  of a lifecycle
     */
    public function getLifecycleActions($current_stage = null, $next_stage = null, $config = [])
    {
        //  Default settings
        $defaults = array(
            //  Means that we should only return the edit option only
            'can_only_edit' => false,
        );

        //  Replace defaults with any provided config options (if any)
        $config = array_merge($defaults, $config);

        /*  First we need to make sure that the current stage has been provided. */
        if ($current_stage) {
            /*  lets determine the current stage statuses  */
            $isActiveStatus = ($current_stage['status'] == 'active') ? true : false;
            $isPendingStatus = ($current_stage['status'] == 'pending') ? true : false;
            $isCancelledStatus = ($current_stage['status'] == 'cancelled') ? true : false;
            $isSkippedStatus = ($current_stage['status'] == 'skipped') ? true : false;
            $isManualVerificationStatus = ($current_stage['status'] == 'manual_verification') ? true : false;

            $current_stage_name = ($current_stage['name'] ?? null);

            $current_stage_meta_data = ($current_stage['meta_data'] ?? null);

            $notifyClientText = '';
            $options = [];

            $paymentTemplate = [
                'invoice_id' => null,
                'comment' => null,
            ];

            $deliveryTemplate = [
                'date_delivered' => null,
                'time_delivered' => null,
                'courier' => ['DHL', 'FedEx', 'Sprint Couries', 'Pickup by customer', 'Other'],
                'other_courier' => null,
                'comment' => null,
            ];

            $reasonTemplate = [
                'reason' => null,
            ];

            //  If we are instructed to only return the edit option
            if ($config['can_only_edit']) {
                /******************************************
                *   EDIT (UPDATE) OPTION                  *
                /*****************************************/

                //   If the current stage is not of type open or closed then show the edit option
                if ($current_stage['type'] == 'payment') {
                    //  Overide the template to add the required fields for payment update
                    array_push($options, $this->createAction('Edit Payment Details', 'update', 'ios-create-outline', false, $paymentTemplate, $current_stage_meta_data));
                } elseif ($current_stage['type'] == 'delivery') {
                    array_push($options, $this->createAction('Edit Delivery Details', 'update', 'ios-create-outline', false, $deliveryTemplate, $current_stage_meta_data));
                } elseif ($current_stage['type'] == 'job_started') {
                    array_push($options, $this->createAction('Edit Job Started Details', 'update', 'ios-create-outline', false, null, $current_stage_meta_data));
                }

                //  Return the actions options at this point so that we do not get anymore possible actions
                return $options;
            }

            //  If the current stage is pending, cancelled or requires manual verification
            if ($isPendingStatus || $isCancelledStatus || $isManualVerificationStatus) {
                /**************************************************************************
                *   CURRENT STEP -  PENDING, CANCELLED, OR MANUAL VERIFICATION OPTIONS    *
                /*************************************************************************/

                //  If the current stage is not cancelled but is pending
                if (!$isCancelledStatus && $isPendingStatus) {
                    //  Option to set to pending stage
                    $notifyClientText = 'Notify Client (Pending)';

                    array_push($options, $this->createAction('Resume '.$this->resource_type, 'resume', 'ios-repeat', false, null));
                }

                //  If the current stage is cancelled
                if ($isCancelledStatus) {
                    //  Option to reverse a cancelled stage
                    $notifyClientText = 'Notify Client (Cancelled)';

                    array_push($options, $this->createAction('Re-open '.$this->resource_type, 'resume', 'ios-repeat', true, null));
                }

                //  If the current stage requires manual verification
                if ($isManualVerificationStatus) {
                    //  Option to verify a stage
                    $notifyClientText = 'Notify Client (Verified)';

                    if ($current_stage['type'] == 'payment') {
                        array_push($options, $this->createAction('Approve Payment', 'approve', 'ios-checkmark', false, null));
                        array_push($options, $this->createAction('Decline Payment', 'decline', 'ios-close', true, null));
                    } elseif ($current_stage['type'] == 'delivery') {
                        array_push($options, $this->createAction('Approve Delivery', 'approve', 'ios-checkmark', false, null));
                        array_push($options, $this->createAction('Decline Delivery', 'decline', 'ios-close', true, null));
                    }
                }
            } else {
                /******************************************
                *   PROCEED OPTION                        *
                /*****************************************/

                $next_stage_name = ($next_stage['name'] ?? null);

                //   If the current stage is not of type closed then show the proceed option
                if ($current_stage['type'] != 'closed') {
                    $proceedText = $next_stage_name ? 'Proceed to '.$next_stage_name : 'Proceed';

                    if ($next_stage['type'] == 'payment') {
                        //  Overide the template to add the required fields for payment update
                        array_push($options, $this->createAction($proceedText, 'proceed', 'ios-redo-outline', false, $paymentTemplate));
                    } elseif ($next_stage['type'] == 'delivery') {
                        //  Overide the template to add the required fields for delivery update
                        array_push($options, $this->createAction($proceedText, 'proceed', 'ios-redo-outline', false, $deliveryTemplate));
                    }
                }

                /******************************************
                *   UNDO OPTION                           *
                /******************************************/

                //   If the current stage is not of type open then show the undo option
                if ($current_stage['type'] != 'open') {
                    $undoText = $isSkippedStatus ? 'Undo Skip' : 'Undo '.$current_stage_name;

                    //  Overide the template to add the required fields for payment update
                    array_push($options, $this->createAction($undoText, 'undo', 'ios-undo-outline', true, null));
                }

                /******************************************
                *   EDIT (UPDATE) OPTION                  *
                /*****************************************/

                //   If the current stage is not of type open or closed then show the edit option
                if ($current_stage['type'] == 'payment') {
                    //  Overide the template to add the required fields for payment update
                    array_push($options, $this->createAction('Edit Payment Details', 'update', 'ios-create-outline', false, $paymentTemplate));
                } elseif ($current_stage['type'] == 'delivery') {
                    array_push($options, $this->createAction('Edit Delivery Details', 'update', 'ios-create-outline', false, $deliveryTemplate));
                } elseif ($current_stage['type'] == 'job_started') {
                    array_push($options, $this->createAction('Edit Job Started Details', 'update', 'ios-create-outline', false, null));
                }

                /******************************************
                *   PENDING OR SKIP NEXT STAGE OPTIONS    *
                /*****************************************/

                //  If the next stage is a payment stage
                if ($next_stage['type'] == 'payment') {
                    //  Show the set to pending payment option
                    array_push($options, $this->createAction('Set As Pending Payment', 'pending', 'ios-time-outline', true, $reasonTemplate));

                    //  Show the skip payment option
                    array_push($options, $this->createAction('Skip Payment', 'skip', 'ios-fastforward-outline', false, $reasonTemplate));

                //  If the next stage is a delivery stage
                } elseif ($next_stage['type'] == 'delivery') {
                    //  Show the set to pending delivery option
                    array_push($options, $this->createAction('Set As Pending Delivery', 'pending', 'ios-time-outline', true, $reasonTemplate));

                    //  Show the skip delivery option
                    array_push($options, $this->createAction('Skip Delivery', 'skip', 'ios-fastforward-outline', false, $reasonTemplate));

                //  If the next stage is a job started stage
                } elseif ($next_stage['type'] == 'job_started') {
                    //  Show the set to pending delivery option
                    array_push($options, $this->createAction('Set As Pending Job', 'pending', 'ios-time-outline', true, $reasonTemplate));

                    //  Show the skip delivery option
                    array_push($options, $this->createAction('Skip Job Started', 'skip', 'ios-fastforward-outline', false, $reasonTemplate));
                }

                /******************************************
                /   CANCEL OPTION                         *
                ******************************************/

                //   If the current stage is not of type closed then show the cancel option
                if ($current_stage['type'] != 'closed') {
                    array_push($options, $this->createAction('Set As Cancelled', 'cancel', 'ios-hand-outline', false, $reasonTemplate));
                }
            }

            /******************************************
            /   NOTIFY CLIENT OPTION                  *
            ******************************************/
            if ($notifyClientText) {
                array_push($options, $this->createAction($notifyClientText, 'notify', 'ios-chatboxes-outline', false, null));
            }

            return $options;
        }
    }

    /*
     *  Returns an single action in proper array format
     */
    public function createAction($name, $trigger_name, $icon, $divider, $template, $meta_data = null)
    {
        /*  Example Of Expected outputs:
         *
         *  $href = route('order-lifecycle-resume', ['order_id' => 1])
         *      converts to: http://oqcloud.co.bw/api/orders/1/lifecycle/resume
         *
         *  $href = route('order-lifecycle-approve', ['order_id' => 1]);
         *      converts to: http://oqcloud.co.bw/api/orders/1/lifecycle/approve
         *  ...
         */
        $href = route($this->resource_type.'-lifecycle-'.$trigger_name, [$this->resource_type.'_id' => $this->id]);

        $action = [
            'name' => $name, 'trigger_name' => $trigger_name, 'icon' => $icon,
            'divider' => $divider, 'href' => $href, 'template' => $template,
        ];

        $meta_data ? ($action['data'] = $meta_data) : null;

        return $action;
    }

    /*
     *  Returns the title of the must recent lifecycle history event
     */
    public function getLifecycleStatusTitleAttribute()
    {
        //  If we have lifecycle activity
        if (count($this->lifecycle_history)) {
            //  Get the last stage title
            return $this->lifecycle_history[0]['title'] ?? null;
        }
    }

    /*
     *  Returns the description of the must recent lifecycle history event
     */
    public function getLifecycleStatusDescriptionAttribute()
    {
        //  If we have lifecycle activity
        if (count($this->lifecycle_history)) {
            //  Get the last stage description
            return $this->lifecycle_history[0]['description'] ?? null;
        }
    }

    public function getManualStatusAttribute($value)
    {
        switch($value) {
            case 'Pending Payment':
                $status_description = 'The order has not been paid (unpaid)';
                break;
            case 'Failed Payment':
                $status_description = 'The order payment failed or was declined (unpaid).';
                break;
            case 'Paid':
                $status_description = 'The order has been paid';
                break;
            case 'Processing':
                $status_description = 'Payment received (paid) and stock has been reduced. The order is now awaiting fulfillment.';
                break;
            case 'On Hold':
                $status_description = 'Awaiting payment – stock is reduced, but payment requires confirmation.';
                break;
            case 'Cancelled':
                $status_description = 'Order fulfilled and complete – requires no further action';
                break;
            case 'Completed':
                $status_description = 'Order fulfilled and complete – requires no further action';
                break;
            default:
                $status_description = 'Status is unknown.';
        }

        return [
            'name' => ucwords($value),
            'description' => $status_description
        ];

    }
}
