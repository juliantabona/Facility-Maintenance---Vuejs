<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\AdvancedFilter\Dataviewer;

class Tax extends Model
{
    use SoftDeletes;
    use Dataviewer;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'abbreviation', 'rate', 'company_branch_id', 'company_id',
    ];

    protected $allowedFilters = [
        'id', 'name', 'abbreviation', 'rate', 'created_at'
    ];

    protected $orderable = [
        'id', 'name', 'abbreviation', 'rate', 'created_at',
    ];

    /**
     * Get all of the owning refund models.
     */
    public function taxable()
    {
        return $this->morphTo();
    }

    
}
