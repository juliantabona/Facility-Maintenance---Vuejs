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

}
