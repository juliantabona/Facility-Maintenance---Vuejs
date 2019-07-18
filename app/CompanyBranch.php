<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyBranch extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'destination', 'company_id',
    ];

    public function creator()
    {
        return $this->morphMany('App\Creator', 'creatable');
    }

        /**
     * Get the stores associated.
     */
    public function stores()
    {
        return $this->hasMany('App\Store');
    }

    /**
     * Get the ecommerce orders
     */
    public function orders()
    {
        return $this->hasMany('App\Order', 'company_branch_id');
    }

    public function companyDirectory()
    {
        return $this->belongsToMany('App\Company', 'company_directory', 'owning_company_id', 'company_id')
                    ->withPivot('id', 'type', 'owning_branch_id', 'owning_company_id')
                    //  Select everything and make the jobcard id the primary id
                    ->select('*', 'companies.id as id', 'companies.type as type', 'company_directory.type as directory_type',
                             'companies.deleted_at as deleted_at', 'companies.created_at as created_at',
                             'companies.updated_at as updated_at')
                    ->withTimestamps();
    }

    public function userDirectory()
    {
        return $this->belongsToMany('App\User', 'user_directory', 'owning_company_id', 'user_id')
                    ->withPivot('id', 'type', 'owning_branch_id', 'owning_company_id')
                    //  Select everything and make the jobcard id the primary id
                    ->select('*', 'users.id as id', 'user_directory.type as directory_type',
                             'users.deleted_at as deleted_at', 'users.created_at as created_at',
                             'users.updated_at as updated_at')
                    ->withTimestamps();
    }

    public function companyClients()
    {
        return $this->companyDirectory()
                    ->where('company_directory.type', 'client');
    }

    public function companySuppliers()
    {
        return $this->companyDirectory()
                    ->where('company_directory.type', 'supplier');
    }

    public function userClients()
    {
        return $this->userDirectory()
                    ->where('user_directory.type', 'client');
    }

    public function userSuppliers()
    {
        return $this->userDirectory()
                    ->where('user_directory.type', 'supplier');
    }

    public function userStaff()
    {
        return $this->userDirectory()
                    ->where('user_directory.type', 'staff');
    }

    public function productAndServices()
    {
        return $this->hasMany('App\Product');
    }

    public function onlyProducts()
    {
        return productAndServices()->where('type', 'product');
    }

    public function onlyServices()
    {
        return productAndServices()->where('type', 'service');
    }

    public function quotations()
    {
        return $this->hasMany('App\Quotation');
    }

    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }

    public function taxes()
    {
        return $this->hasMany('App\Tax');
    }

    ///////////////////////////////////////////////////////////////////////////////////
    //                                                                              //
    //  EVERTHING BELOW THIS CAUTION IS NOT YET BEING USED BY THE SYSTEM            //
    //                                                                              //
    //////////////////////////////////////////////////////////////////////////////////

    /**
     *   Get the company the branch belongs to. A branch must belong to a company
     *   to access more information. This can be company details, settings, permissions,
     *   global jobcards, staff, suppliers, clients, quotations, invoices, receipts, documents,
     *   e.t.c related to the company.
     */
    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }

    public function staff()
    {
        return $this->userDirectory()
                    ->where('user_directory.type', 'staff');
    }

    /**
     *   Get the jobcards that belong to the branch.
     */
    public function jobcards()
    {
        return $this->hasMany('App\Jobcard', 'company_branch_id');
    }

    public function appointments()
    {
        return $this->hasMany('App\Appointment', 'company_branch_id');
    }

    /**
     *   Get the recent activities that belong to the branch.
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->orderBy('created_at', 'desc');
    }
}
