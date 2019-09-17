<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;

class Sms extends Model
{
    use Dataviewer;

    public $defaultCredit = 25;

    protected $table = 'sms_records';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'count', 'company_id',
    ];

    protected $allowedFilters = [
        'id', 'count', 'company_id', 'created_at',
    ];

    protected $allowedOrderableColumns = [
        'id', 'count', 'company_id', 'created_at',
    ];

    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }
}
