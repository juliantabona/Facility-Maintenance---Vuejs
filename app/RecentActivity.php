<?php

namespace App;

use App\AdvancedFilter\Dataviewer;
use App\Traits\RecentActivityTraits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'tax' => 'App\Tax',
    'tag' => 'App\Tag',
    'user' => 'App\User',
    'order' => 'App\Order',
    'phone' => 'App\Phone',
    'store' => 'App\Store',
    'refund' => 'App\Review',
    'coupon' => 'App\Coupon',
    'review' => 'App\Rating',
    'comment' => 'App\Comment',
    'invoice' => 'App\Invoice',
    'jobcard' => 'App\Jobcard',
    'company' => 'App\Company',
    'product' => 'App\Product',
    'discount' => 'App\Discount',
    'category' => 'App\Category',
    'priority' => 'App\Priority',
    'document' => 'App\Document',
    'quotation' => 'App\Quotation',
    'costcenter' => 'App\CostCenter',
    'appointment' => 'App\Appointment',
    'transaction' => 'App\Transaction',
]);

class RecentActivity extends Model
{
    use Dataviewer, RecentActivityTraits;

    protected $table = 'recent_activities';

    protected $casts = [
        'activity' => 'array',
    ];

    protected $with = ['user'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        /*  Activity Details  */
        'type', 'activity', 'user_id',

        /*  Ownership Information  */
        'owner_id', 'owner_type',
        
    ];

    protected $allowedFilters = [];

    protected $allowedOrderableColumns = [];

    /*
     *  Returns the owner of the activity
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /*
     *  Returns the user responsible for the activity
     */
    public function user()
    {
        return $this->belongsTo('App\User');
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
