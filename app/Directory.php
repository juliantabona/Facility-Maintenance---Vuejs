<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;

class Directory extends Model
{
    use Dataviewer;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'type', 'model_id', 'model_type', 'owning_branch_id', 'owning_company_id',
    ];

    protected $allowedFilters = [
        'id', 'type', 'model_id', 'model_type', 'owning_branch_id', 'owning_company_id', 'created_at',
    ];

    protected $allowedOrderableColumns = [
        'id', 'type', 'model_id', 'model_type', 'owning_branch_id', 'owning_company_id', 'created_at',
    ];
}
