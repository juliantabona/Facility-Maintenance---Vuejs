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
        return $user->isSuperAdmin();
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
        return $user->isSuperAdmin();
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
        return $company->isAdminOrStaff($user->id);
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
        return $company->isAdminOrStaff($user->id);
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
        //  Only an Admin can delete this company
        return $company->isAdmin($user->id);
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
        //  Only an Admin can restore this company
        return $company->isAdmin($user->id);
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
        return $company->isAdmin($user->id);
    }
}
