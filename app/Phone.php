<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Traits\PhoneTraits;

Relation::morphMap([
    'user' => 'App\User',
    'company' => 'App\Company',
    'store' => 'App\Store',
]);

class Phone extends Model
{
    use PhoneTraits;

    //  protected $with = ['createdBy'];

    protected $casts = [
        'calling_code' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'calling_code', 'type', 'provider', 'company_branch_id', 'company_id', 'created_by',
    ];

    /**
     * Get all of the owning documentable models.
     */
    public function trackable()
    {
        return $this->morphTo();
    }

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function mobileMoneyAccount()
    {
        return $this->hasOne('App\Wallet', 'phone_id');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->where('trackable_id', $this->id)
                    ->where('trackable_type', 'phone')
                    ->orderBy('created_at', 'desc');
    }
}
