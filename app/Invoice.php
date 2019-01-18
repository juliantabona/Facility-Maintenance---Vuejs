<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AdvancedFilter\Dataviewer;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'jobcard' => 'App\Jobcard',
]);

class Invoice extends Model
{
    use Dataviewer;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $casts = [
        'status' => 'array',
        'currency_type' => 'array',
        'calculated_taxes' => 'array',
        'customized_company_details' => 'array',
        'customized_client_details' => 'array',
        'table_columns' => 'array',
        'items' => 'array',
        'notes' => 'array',
        'colors' => 'array',
    ];

    protected $dates = ['created_date_value', 'expiry_date_value'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'heading', 'reference_no_title', 'reference_no_value', 'created_date_title', 'created_date_value',
        'expiry_date_title', 'expiry_date_value', 'sub_total_title', 'sub_total_value', 'grand_total_title', 'grand_total_value',
        'currency_type', 'calculated_taxes', 'invoice_to_title', 'customized_company_details', 'customized_client_details', 'client_id',
        'table_columns', 'items', 'notes', 'colors', 'footer', 'quotation_id', 'trackable_id', 'trackable_type', 'company_branch_id', 'company_id',
    ];

    protected $allowedFilters = [
        'id', 'reference_no_value', 'grand_total_value', 'created_date_value', 'expiry_date_value', 'created_at',

        //  Nested within JSON
        //  'notes > details',

        // Nested within relationhip
        'client.id', 'client.name', 'client.city', 'client.state_or_region', 'client.address', 'client.industry', 'client.type', 'client.website_link', 'client.phone_ext', 'client.phone_num', 'client.email', 'client.created_at',
    ];

    protected $orderable = [
        'id', 'reference_no_value', 'grand_total_value', 'created_date_value', 'expiry_date_value', 'created_at',
    ];

    /**
     * Get all of the owning trackable models.
     */
    public function trackable()
    {
        return $this->morphTo();
    }

    public function quotation()
    {
        return $this->belongsTo('App\Quotation', 'quotation_id');
    }

    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->orderBy('created_at', 'desc');
    }

    public function client()
    {
        return $this->belongsTo('App\Company', 'client_id');
    }
}
