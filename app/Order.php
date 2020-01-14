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

    protected $with = ['fulfillments'];

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
        'discount_lines' => 'array'
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
        'number', 'currency', 'created_date',

        /*  Status / Payment Status / Fulfillment Status  */
        'status', 'payment_status', 'fulfillment_status',

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
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
    
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePartiallyPaid($query)
    {
        return $query->where('payment_status', 'partially paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePendingPayment($query)
    {
        return $query->where('payment_status', 'pending payment');
    }

    public function scopeFailedPayment($query)
    {
        return $query->where('payment_status', 'failed payment');
    }

    //  Return only open/archieved orders with payments
    public function scopeWithPayments($query)
    {
        return $query->whereIn('status', ['open', 'archieved'])->whereIn('payment_status', ['paid', 'partially paid']);
    }

    public function scopeFulfilled($query)
    {
        return $query->where('fulfillment_status', 'fulfilled');
    }

    public function scopeUnfulfilled($query)
    {
        return $query->where('fulfillment_status', 'unfulfilled');
    }

    public function scopePartiallyFulfilled($query)
    {
        return $query->where('fulfillment_status', 'partially fulfilled');
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
     *  Returns the invoices owned by this order
     */
    public function invoices()
    {
        return $this->morphMany('App\Invoice', 'owner');
    }
 
    /*
     *  Returns the transactions of invoices owned by this order
     */
    public function transactions()
    {
        /** Polymorphic hasManyThrough relationships are the same as any others, but 
         *  with an added constraint on the owner_type, which can be retrieved from
         *  the Relation::morphMap() array, or by using the class name directly.
         * 
         *  Refer to this stackoverflow quetion: 
         *  https://stackoverflow.com/questions/43285779/laravel-polymorphic-relations-has-many-through
         * 
         *  The array_search() function will search an array for a value and returns the key.
         * 
         *  static::class = App\Order
         * 
         *  Relation::morphMap() = [
         *      "order"  => "App\Order"
         *      "store"  => "App\Store"
         *      "user"   => App\User"
         *      "account => App\Account"
         *      "contact => App\Contact"
         *  ]
         * 
         *  Therefore array_search(static::class, Relation::morphMap()) will return "order"
         */
        return $this->hasManyThrough(
                    'App\Transaction',                      //  What we want (transations)
                    'App\Invoice',                          //  The relationship we have (invoices)
                    'owner_id',                             //  Foreign key on invoices table
                    'owner_id'                              //  Foreign key on transactions table
                )->where('invoices.owner_type', array_search(static::class, Relation::morphMap()) ?: static::class)
                //  Select all the transation details and make sure the owner id reflects the (invoice id) not the (order id)
                ->select(
                    'transactions.type', 'transactions.status', 'transactions.automatic', 'transactions.payment_type',  
                    'transactions.payment_amount', 'transactions.meta', 'invoices.owner_id as order_id', 'invoices.id as invoice_id',
                    'transactions.created_at', 'transactions.updated_at'
                );
    }

    /*
     *  Returns the order fulfillments
     */
    public function fulfillments()
    {
        return $this->morphMany('App\Fulfillment', 'owner')->orderBy('created_at', 'desc');
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

    protected $appends = [
        'resource_type', 'unfulfilled_item_lines', 'quantity_of_unfulfilled_item_lines', 'quantity_of_fulfilled_item_lines', 
        'quantity_of_unpaid_item_lines', 'quantity_of_paid_item_lines', 'paid_item_lines','unpaid_item_lines', 'transaction_total', 
        'refund_total', 'outstanding_balance'
    ];

    /*
     *  Returns the resource type
     */
    public function getResourceTypeAttribute()
    {
        return strtolower(class_basename($this));
    }

    /*
     *  Returns the order unfulfilled item lines
     */
    public function getUnfulfilledItemLinesAttribute()
    {
        $unfulfilled_item_lines = [];        

        if( $this->item_lines ){

            //  Foreach order item line
            foreach( $this->item_lines as $item_line ){
    
                //  Lets get the current order item line quantity value
                $item_quantity = intval($item_line['quantity']);
    
                //  Foreach fulfillment instance [Since we can have multiple fulfillment instances]
                foreach ($this->fulfillments as $fulfillment) {
    
                    //  Foreach item line of the current fulfillment instance
                    foreach ($fulfillment->item_lines as $fulfillment_item_line) {
    
                        //  Lets get the current fulfillment item line quantity value
                        $fulfillment_item_quantity = intval($fulfillment_item_line['quantity']);
    
                        //  Lets check if the current fulfillment item line matches the current order item line
                        if( $fulfillment_item_line['id'] == $item_line['id'] ){
    
                            /** Calculate if we have any remaining quantities of the matching item that are not yet fulfilled.
                             *  Assumiing that:
                             * 
                             *  $item_quantity = 5 and
                             *  $fulfillment_item_quantity = 2
                             * 
                             *  This means that if we subtract $fulfillment_item_quantity (2) from $item_quantity (5) we will get the
                             *  number of remaining unfulfilled items (3) for the same matching item.
                             * 
                             *  $item_quantity (3) = $item_quantity (5) - $fulfillment_item_quantity (2)
                             */
                            $item_quantity = $item_quantity - $fulfillment_item_quantity;
    
                        }
    
                    }
    
                }
    
                //  If we have any remaining quantities that haven't yet been fulfilled for this item line
                if($item_quantity > 0){
                    
                    //  Get the unfulfilled/partially fulfilled item line
                    $unfulfilled_item_line = $item_line;
    
                    //  Update the remaining quantities that require fulfillment for this item line
                    $unfulfilled_item_line['quantity'] = $item_quantity;
                    
                    //  Push the unfulfilled item
                    array_push($unfulfilled_item_lines, $unfulfilled_item_line);
    
                }
    
            }
    
            return $unfulfilled_item_lines;

        }
    }

    /*
     *  Returns the order paid item lines
     */
    public function getPaidItemLinesAttribute()
    {
        $paid_item_lines = [];        

        if($this->item_lines){

            //  Foreach order item line
            foreach( $this->item_lines as $item_line ){

                //  Lets get the current order item line quantity value
                $paid_item_quantity = 0;

                //  Get all the paid invoices of this order
                $invoices = collect($this->invoices()->get())->where('has_paid.status', true);

                //  Foreach invoice instance [Since we can have multiple invoice instances]
                foreach ($invoices as $invoice) {

                    //  Foreach item line of the current invoice instance
                    foreach ($invoice->item_lines as $invoice_item_line) {

                        //  Lets get the current invoice item line quantity value
                        $invoice_item_quantity = intval($invoice_item_line['quantity']);

                        //  Lets check if the current invoice item line matches the current order item line
                        if( $invoice_item_line['id'] == $item_line['id'] ){

                            /** Calculate if we have any additional quantities of the matching item that are paid.
                             *  Assumiing that:
                             * 
                             *  $paid_item_quantity = 0 and
                             *  $invoice_item_quantity = 2
                             * 
                             *  This means that if we add $invoice_item_quantity (2) to the $paid_item_quantity (0) we will get the
                             *  number of the total item quantity paid (2) for the same matching item.
                             * 
                             *  $paid_item_quantity (2) = $paid_item_quantity (0) + $invoice_item_quantity (2)
                             */
                            $paid_item_quantity = $paid_item_quantity + $invoice_item_quantity;

                        }

                    }

                }

                //  If we have any remaining quantities that haven't yet been paid for this item line
                if($paid_item_quantity > 0){
                    
                    //  Get the unpaid/partially paid item line
                    $paid_item_line = $item_line;

                    //  Update the remaining quantities that require to be paid for this item line
                    $paid_item_line['quantity'] = $paid_item_quantity;
                    
                    //  Push the paid item
                    array_push($paid_item_lines, $paid_item_line);

                }

            }

        }

        return $paid_item_lines;
    }

    /*
     *  Returns the order unpaid item lines
     */
    public function getUnPaidItemLinesAttribute()
    {
        $unpaid_item_lines = [];        

        if($this->item_lines){

            //  Foreach order item line
            foreach( $this->item_lines as $item_line ){

                //  Lets get the current order item line quantity value
                $item_quantity = intval($item_line['quantity']);

                //  Get all the paid invoices of this order
                $invoices = collect($this->invoices()->get())->where('has_paid.status', true);

                //  Foreach invoice instance [Since we can have multiple invoice instances]
                foreach ($invoices as $invoice) {

                    //  Foreach item line of the current invoice instance
                    foreach ($invoice->item_lines as $invoice_item_line) {

                        //  Lets get the current invoice item line quantity value
                        $invoice_item_quantity = intval($invoice_item_line['quantity']);

                        //  Lets check if the current invoice item line matches the current order item line
                        if( $invoice_item_line['id'] == $item_line['id'] ){

                            /** Calculate if we have any remaining quantities of the matching item that are not yet paid.
                             *  Assumiing that:
                             * 
                             *  $item_quantity = 5 and
                             *  $invoice_item_quantity = 2
                             * 
                             *  This means that if we subtract $invoice_item_quantity (2) from $item_quantity (5) we will get the
                             *  number of remaining unpaid items (3) for the same matching item.
                             * 
                             *  $item_quantity (3) = $item_quantity (5) - $invoice_item_quantity (2)
                             */
                            $item_quantity = $item_quantity - $invoice_item_quantity;

                        }

                    }

                }

                //  If we have any remaining quantities that haven't yet been paid for this item line
                if($item_quantity > 0){
                    
                    //  Get the unpaid/partially paid item line
                    $unpaid_item_line = $item_line;

                    //  Update the remaining quantities that require to be paid for this item line
                    $unpaid_item_line['quantity'] = $item_quantity;
                    
                    //  Push the unpaid/partially paid item
                    array_push($unpaid_item_lines, $unpaid_item_line);

                }

            }

        }

        return $unpaid_item_lines;
    }

    /*
     *  Returns the order unfulfilled item lines
     */
    public function getQuantityOfUnfulfilledItemLinesAttribute()
    {
        $quantity = 0;        

        if($this->unfulfilled_item_lines){

            //  Foreach item line
            foreach( $this->unfulfilled_item_lines as $unfulfilled_item_line ){                    
                
                //  Lets get the current fulfillment item line quantity value
                $quantity = $quantity + intval($unfulfilled_item_line['quantity']);

            }

        }

        return $quantity;
    }

    /*
     *  Returns the order unfulfilled item lines
     */
    public function getQuantityOfFulfilledItemLinesAttribute()
    {
        $quantity = 0;        

        //  Foreach item line
        foreach ($this->fulfillments as $fulfillment) {                 
             
            //  Foreach item line of the current fulfillment instance
            foreach ($fulfillment->item_lines as $fulfillment_item_line) {              
            
                //  Lets get the current fulfillment item line quantity value
                $quantity = $quantity + intval($fulfillment_item_line['quantity']);

            }

        }

        return $quantity;
    }
    /*
     *  Returns the order unpaid item lines
     */
    public function getQuantityOfUnpaidItemLinesAttribute()
    {
        $quantity = 0;        

        //  Foreach item line
        foreach( $this->unpaid_item_lines as $unpaid_item_line ){                    
            
            //  Lets get the current unpaid item line quantity value
            $quantity = $quantity + intval($unpaid_item_line['quantity']);

        }

        return $quantity;
    }

    /*
     *  Returns the order paid item lines
     */
    public function getQuantityOfPaidItemLinesAttribute()
    {
        $quantity = 0;        

        //  Foreach item line
        foreach ($this->paid_item_lines as $paid_item_line) {                 
             
            //  Lets get the current paid item line quantity value
            $quantity = $quantity + intval($paid_item_line['quantity']);

        }

        return $quantity;
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
        $total = $this->grand_total;

        //  Get all the paid invoices of this order
        $invoices = collect($this->invoices()->get())->where('has_paid.status', true);

        //  If we have any paid invoices
        if( count( $invoices ) ){

            //  Foreach paid invoice
            foreach ($this->invoices as $invoice) {

                //  Remove the amount paid by customer from the current outstanding total
                $total = $total - $invoice->transaction_total;

            }

        }

        return $total;
    }

    /*
     *  Returns the current status name and description of the order
     */
    public function getStatusAttribute($value)
    {
        switch (ucwords($value)) {
            case 'Open':
                $status_description = 'The order is open for processing';
                break;
            case 'Archieved':
                $status_description = 'The order has been archieved';
                break;
            case 'Cancelled':
                $status_description = 'The order has been cancelled and no longer available for processing';
                break;
            case 'Draft':
                $status_description = 'The order is currently a draft and not yet available for processing.';
                break;
            default:
                $status_description = 'Status is unknown.';
        }

        return [
            'name' => ucwords($value),
            'description' => $status_description,
        ];
    }

    /*
     *  Returns the current payment status name and description of the order
     */
    public function getPaymentStatusAttribute($value)
    {
        switch (ucwords($value)) {
            case 'Authorized':
                $status_description = 'The order has not been authourized';
                break;
            case 'Paid':
                $status_description = 'The order has been paid';
                break;
            case 'Partially Paid':
                $status_description = 'The order has been partially paid';
                break;
            case 'Refunded':
                $status_description = 'The order has been refunded';
                break;
            case 'Partially Refunded':
                $status_description = 'The order has been partially refunded';
                break;
            case 'Pending':
                $status_description = 'The order is awaiting payment – stock is reduced, but payment requires confirmation';
                break;
            case 'Failed Payment':
                $status_description = 'The order payment failed or was declined (unpaid)';
                break;
            case 'Unpaid':
                $status_description = 'The order has not been paid';
                break;
            case 'Voided':
                $status_description = 'Not sure what this means...';
                break;
            default:
                $status_description = 'Status is unknown';
        }

        return [
            'name' => ucwords($value),
            'description' => $status_description,
        ];
    }

    /*
     *  Returns the current fulfillmnt status name and description of the order
     */
    public function getFulfillmentStatusAttribute($value)
    {
        switch (ucwords($value)) {
            case 'Unfulfilled':
                $status_description = 'The order is still awaiting fulfillment';
                break;
            case 'Partially Fulfilled':
                $status_description = 'The order has been partially fulfilled';
                break;
            case 'Fulfilled':
                $status_description = 'The order has been fulfilled';
                break;
            default:
                $status_description = 'Status is unknown';
        }

        return [
            'name' => ucwords($value),
            'description' => $status_description,
        ];
    }
    
}
