<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'invoice' => 'App\Invoice',
]);

class Reminder extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'days_after', 'type', 'can_sms', 'can_email', 'email', 'phone', 'trackable_id', 'trackable_type', 'company_branch_id', 'company_id',
    ];

    /**
     * Get associated resource.
     */
    public function trackable()
    {
        return $this->morphTo();
    }
}
