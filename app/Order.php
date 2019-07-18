<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use App\Traits\OrderTraits;

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
        'line_items' => 'array',
        'shipping_lines' => 'array',
        'tax_lines' => 'array',
        'billing' => 'array',
        'shipping' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        /*  Basic Info  */
        'parent_id', 'number', 'order_key', 'status', 'currency', 'cart_hash', 'meta_data', 'date_completed',

        /*  Item Info  */
        'line_items',

        /*  Shipping Info  */
        'shipping_lines',

        /*  Grand Total, Subtotal, Shipping Total, Discount Total  */
        'cart_total', 'shipping_total', 'discount_total', 'grand_total',

        /*  Tax Info  */
        'cart_tax', 'shipping_tax', 'discount_tax', 'grand_total_tax', 'prices_include_tax', 'tax_lines',

        /*  Customer Info  */
        'customer_id', 'customer_ip_address', 'customer_user_agent', 'customer_note', 'billing', 'shipping',

        /*  Payment Info  */
        'payment_method', 'payment_method', 'payment_method_title', 'transaction_id', 'date_paid',

        /*  Store, Company & Branch Info  */
        'store_id', 'company_branch_id', 'company_id',
    ];

    protected $allowedFilters = [
        'id', 'parent_id', 'number', 'order_key', 'status', 'currency', 'cart_hash', 'meta_data', 'date_completed',
        'line_items', 'shipping_lines', 'cart_total', 'shipping_total', 'discount_total', 'grand_total',
        'cart_tax', 'shipping_tax', 'discount_tax', 'grand_total_tax', 'prices_include_tax', 'tax_lines',
        'payment_method', 'payment_method', 'payment_method_title', 'transaction_id', 'date_paid',
        'store_id', 'company_branch_id', 'company_id', 'created_at',
    ];

    protected $orderable = [
        'id', 'parent_id', 'number', 'order_key', 'status', 'currency', 'cart_hash', 'meta_data', 'date_completed',
        'line_items', 'shipping_lines', 'cart_total', 'shipping_total', 'discount_total', 'grand_total',
        'cart_tax', 'shipping_tax', 'discount_tax', 'grand_total_tax', 'prices_include_tax', 'tax_lines',
        'payment_method', 'payment_method', 'payment_method_title', 'transaction_id', 'date_paid',
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

    public function customer()
    {
        return $this->belongsTo('App\User', 'customer_id');
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

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->orderBy('created_at', 'desc');
    }

    protected $appends = ['created_at_format', 'status_title', 'status_description'];

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
        } elseif ($status == 'unpaid') {
            $description = 'Order has not received payment';
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
}
