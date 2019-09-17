<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use App\Traits\TaxTraits;

Relation::morphMap([
    'product' => 'App\Product'
]);
class Tax extends Model
{
    use TaxTraits;
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

    protected $allowedOrderableColumns = [
        'id', 'name', 'abbreviation', 'rate', 'created_at',
    ];

    /**
     * Get all of the owning refund models.
     */
    public function taxable()
    {
        return $this->morphTo();
    }

    /**
     * Get the owner from the morphTo relationship
     * This method returns a company/store
     */
    public function owner()
    {
        return $this->taxable();
    }

    /**
     * Get all of the products that are assigned this tax.
     */
    public function products()
    {
        return $this->morphedByMany('App\Product', 'taxable');
    }
    
}
