<?php

namespace App\Policies;

use App\User;
use App\Company;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Authorize any action on this given policy if the user
     * is a super admin.
     */
    public function before($user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }
    
    /**
     * Determine whether the user can view all companies.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function viewAll(User $user)
    {
        //  Only the Super Admin can view all companies
        if( $user->isSuperAdmin() ){
            return true;
        }
    }

    /**
     * Determine whether the user can view the company.
     *
     * @param \App\User    $user
     * @param \App\Company $company
     *
     * @return mixed
     */
    public function view(User $user, Company $company)
    {
        //  Only an Admin or Staff member can view this company
        if( $company->isAdminOrStaff($user->id) ){
            return true;
        }
    }

    /**
     * Determine whether the user can create companies.
     *
     * @param \App\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        //  Any Authenticated user can create a company
    }

    /**
     * Determine whether the user can update the company.
     *
     * @param \App\User    $user
     * @param \App\Company $company
     *
     * @return mixed
     */
    public function update(User $user, Company $company)
    {
        //  Only an Admin or Staff member can update this company
        if( $company->isAdminOrStaff($user->id) ){
            return true;
        }
    }

    /**
     * Determine whether the user can delete the company.
     *
     * @param \App\User    $user
     * @param \App\Company $company
     *
     * @return mixed
     */
    public function delete(User $user, Company $company)
    {
        //  Only an Admin delete this company
        if( $company->isAdmin($user->id) ){
            return true;
        }
    }

    /**
     * Determine whether the user can restore the company.
     *
     * @param \App\User    $user
     * @param \App\Company $company
     *
     * @return mixed
     */
    public function restore(User $user, Company $company)
    {
        //  Only an Admin restore this company
        if( $company->isAdmin($user->id) ){
            return true;
        }
    }

    /**
     * Determine whether the user can permanently delete the company.
     *
     * @param \App\User    $user
     * @param \App\Company $company
     *
     * @return mixed
     */
    public function forceDelete(User $user, Company $company)
    {
        //  Only an Admin can force delete this company
        if( $company->isAdmin($user->id) ){
            return true;
        }
    }
}
