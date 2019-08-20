<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'order' => 'App\Order',
]);

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'payment_type', 'payment_amount', 'success_state',
    ];

    /**
     * Get all of the owning transaction models.
     */
    public function transactionable()
    {
        return $this->morphTo();
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->where('trackable_type', 'transaction')
                    ->orderBy('created_at', 'desc');
    }
}
