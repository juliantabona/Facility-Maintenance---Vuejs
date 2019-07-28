<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use App\Traits\OrderTraits;
use Illuminate\Support\Str;

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
        'shipping_info' => 'array'
    ];

    protected $with = ['lifecycle', 'refunds', 'transactions'];

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

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->where('trackable_type', 'order')
                    ->orderBy('created_at', 'desc');
    }

    public function createdActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'order')->where('type', 'created');
    }

    public function approvedActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'order')->where('type', 'approved');
    }

    public function updatedLifecycleStagesActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'order')->where('type', 'updated lifecycle stage');
    }

    public function reversedLifecycleStagesActivities()
    {
        return $this->recentActivities()->where('trackable_id', $this->id)->where('trackable_type', 'order')->where('type', 'reversed lifecycle stage');
    }

    protected $appends = [
                            'model_type','created_at_format',
                            'transaction_total', 'refund_total', 'outstanding_balance', 
                            'status_title', 'status_description',
                            'current_lifecycle_stage', 'current_lifecycle_main_status', 'current_lifecycle_sub_status', 'lifecycle_stages'
                        ];

    //  Getter for the type of model
    public function getModelTypeAttribute()
    {
        return Str::snake( class_basename( $this ) );
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
        } elseif ($status == 'pending-delivery') {
            $description = 'Order has not been delivered';
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

    public function getStatusTitleAttribute()
    {
        return ucfirst(str_replace('-', ' ', $this->status));
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

    public function getCurrentLifecycleMainStatusAttribute()
    {
        if (count($this->current_lifecycle_stage)) {
            $availableStages = $this->lifecycle->first()['stages'];
            foreach ($availableStages as $availableStage) {
                if ($availableStage['type'] == $this->current_lifecycle_stage['activity']['type']
                    && $availableStage['instance'] == $this->current_lifecycle_stage['activity']['instance']) {
                    //  Current stage name and type
                    $stageName = $availableStage['name'];
                    $stageType = $availableStage['type'];

                    return ['name' => $stageName, 'type' => $stageType];
                }
            }
        } elseif ($this->has_approved) {
            return ['name' => 'Open', 'type' => 'open'];
        }
    }

    public function getCurrentLifecycleSubStatusAttribute()
    {
        if (count($this->current_lifecycle_stage)) {
            //  Check if this stage is cancelled
            if ($this->current_lifecycle_stage['activity']['cancelled_status'] ?? false) {
                return 'Cancelled';
            //  Check if this stage is pending
            } elseif ($this->current_lifecycle_stage['activity']['pending_status'] ?? false) {
                return 'Pending';
            }
        }
    }

    public function gethasLifecycleAttribute()
    {
        return count($this->lifecycle_stages) ? true : false;
    }

}
