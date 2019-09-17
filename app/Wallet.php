<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;

class Wallet extends Model
{
    use Dataviewer;

    protected $table = 'mobile_wallets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_name', 'status', 'phone_id',
    ];

    protected $allowedFilters = [
        'id', 'account_name', 'status', 'phone_id', 'created_at',
    ];

    protected $allowedOrderableColumns = [
        'id', 'account_name', 'status', 'phone_id', 'created_at',
    ];

    /**
     * Get all of the tax associated products and services.
     */
    public function mobile()
    {
        return $this->belongsTo('App\Phone', 'phone_id');
    }
}
