<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\AdvancedFilter\Dataviewer;
use App\Traits\RecentActivityTraits;

Relation::morphMap([
    'jobcard' => 'App\Jobcard',
    'quotation' => 'App\Quotation',
    'invoice' => 'App\Invoice',
    'appointment' => 'App\Appointment',
    'company' => 'App\Company',
    'companybranch' => 'App\CompanyBranch',
    'category' => 'App\Category',
    'priority' => 'App\Priority',
    'costcenter' => 'App\CostCenter',
    'document' => 'App\Document',
    'product' => 'App\Product',
    'tag' => 'App\Tag',
    'phone' => 'App\Phone',
    'user' => 'App\User',
    'store' => 'App\Store',
    'order' => 'App\Order',
    'refund' => 'App\Refund',
    'transaction' => 'App\Transaction',
    'rating' => 'App\Rating',   
    'comment' => 'App\Comment',   
    'billing_and_shipping_address' => 'App\BillingAndShippingAddress',   
]);

class RecentActivity extends Model
{
    use Dataviewer;
    use RecentActivityTraits;

    protected $table = 'recent_activities';

    protected $casts = [
        'activity' => 'array',
    ];

    protected $with = ['createdBy'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'activity', 'company_branch_id', 'company_id', 'created_by',
    ];

    public function creator()
    {
        return $this->morphMany('App\Creator', 'creatable');
    }

    protected $allowedFilters = [
        'id', 'company_branch_id', 'created_at',
    ];

    protected $orderable = [
        'id', 'company_branch_id', 'created_at',
    ];

    /**
     * Get all of the owning documentable models.
     */
    public function trackable()
    {
        return $this->morphTo();
    }

    public function jobcard()
    {
        return $this->belongsTo('App\Jobcard', 'trackable_id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    protected $appends = ['details'];

    public function getDetailsAttribute()
    {
        //  DETAILS FOR INVOICES
        if ($this->type == 'created' && $this->trackable_type == 'invoice') {
            $title = 'Created';
            $description = 'Invoice was created';
        } elseif ($this->type == 'updated' && $this->trackable_type == 'invoice') {
            $title = 'Updated';
            $description = 'Invoice was updated';
        } elseif ($this->type == 'approved' && $this->trackable_type == 'invoice') {
            $title = 'Approved';
            $description = 'Invoice was approved';
        } elseif ($this->type == 'sent' && $this->trackable_type == 'invoice') {
            //  Mail information
            $emailReceipient = (($this->activity ?? [])['mail'] ?? [])['email'] ?? 'unknown';
            $emailSubject = (($this->activity ?? [])['mail'] ?? [])['subject'] ?? null;

            $title = 'Sent';
            $description = 'Invoice was sent to "'.$emailReceipient.'"';
            $description .= $emailSubject ? ' with the subject "'.$emailSubject.'"' : '';
        } elseif ($this->type == 'skip send' && $this->trackable_type == 'invoice') {
            $title = 'Skipped Sending';
            $description = 'Invoice was not sent via email/sms';
        } elseif ($this->type == 'sent receipt' && $this->trackable_type == 'invoice') {
            //  Mail information
            $emailReceipient = (($this->activity ?? [])['mail'] ?? [])['email'] ?? 'unknown';
            $emailSubject = (($this->activity ?? [])['mail'] ?? [])['subject'] ?? null;

            $title = 'Sent Receipt';
            $description = 'Invoice rececipt was sent to "'.$emailReceipient.'"';
            $description .= $emailSubject ? ' with the subject "'.$emailSubject.'"' : '';
        } elseif ($this->type == 'payment reminder' && $this->trackable_type == 'invoice') {
            $title = 'Updated Reminders';
            $description = 'Invoice payment reminders were updated';
        } elseif ($this->type == 'paid' && $this->trackable_type == 'invoice') {
            $title = 'Paid';
            $description = 'Invoice was recorded as paid';
        } elseif ($this->type == 'payment cancelled' && $this->trackable_type == 'invoice') {
            $title = 'Payment Cancelled';
            $description = 'Invoice payment was cancelled';
        } else {
            $title = $this->type;
            $description = 'limited information...';
        }

        return ['title' => $title, 'description' => $description];
    }
}
