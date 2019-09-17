<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;

class StoreInterest extends Model
{
    use Dataviewer;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'store_id',
    ];

    protected $allowedFilters = [
        'id', 'type', 'store_id', 'created_at',
    ];

    protected $allowedOrderableColumns = [
        'id', 'type', 'store_id', 'created_at',
    ];

    public function store()
    {
        return $this->belongsTo('App\Store');
    }
}
