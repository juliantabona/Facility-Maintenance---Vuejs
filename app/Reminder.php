<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'invoice' => 'App\Invoice',
    'appointment' => 'App\Appointment',
]);

class Reminder extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'days_after', 'type', 'can_sms', 'can_email', 'email', 'phone', 'owner_id', 'owner_type'
    ];

    /**
     * Get associated resource.
     */
    public function owner()
    {
        return $this->morphTo();
    }
}
