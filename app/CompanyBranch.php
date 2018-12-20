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

    public function companyDirectory()
    {
        return $this->belongsToMany('App\Company', 'company_directory', 'owning_branch_id', 'company_id')
                    ->withPivot('id', 'type', 'owning_branch_id', 'owning_company_id')
                    //  Select everything and make the jobcard id the primary id
                    ->select('*', 'companies.id as id',  'companies.type as type',
                            'companies.deleted_at as deleted_at', 'companies.created_at as created_at',
                            'companies.updated_at as updated_at')
                    ->withTimestamps();
    }

    public function clients()
    {
        return $this->companyDirectory()
                    ->where('company_directory.type', 'client');
    }

    public function suppliers()
    {
        return $this->companyDirectory()
                    ->where('company_directory.type', 'supplier');
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

    public function userDirectory()
    {
        return $this->belongsToMany('App\User', 'user_directory', 'owning_branch_id', 'user_id')
                    ->withPivot('id', 'type', 'owning_branch_id', 'owning_company_id')
                    ->withTimestamps();
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

    /**
     *   Get the recent activities that belong to the branch.
     */
    public function recentActivities()
    {
        return $this->morphMany('App\RecentActivity', 'trackable')
                    ->orderBy('created_at', 'desc');
    }
}
