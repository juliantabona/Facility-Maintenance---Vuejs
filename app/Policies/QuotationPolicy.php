<?php

namespace App\Policies;

use App\User;
use App\Quotation;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuotationPolicy
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
     * Determine whether the user can view all quotations.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function viewAll(User $user)
    {
        //  Only the Super Admin can view all quotations
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the quotation.
     *
     * @param  \App\User  $user
     * @param  \App\Quotation  $quotation
     * @return mixed
     */
    public function view(User $user, Quotation $quotation)
    {
        //  Only an Admin or Staff member of the merchant account can view this quotation
        return $quotation->merchant->isAdminOrStaff($user->id);
    }

    /**
     * Determine whether the user can create quotations.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //  Any Authenticated user can create an quotation
    }

    /**
     * Determine whether the user can update the quotation.
     *
     * @param  \App\User  $user
     * @param  \App\Quotation  $quotation
     * @return mixed
     */
    public function update(User $user, Quotation $quotation)
    {
        //  Only an Admin or Staff member of the merchant account can update this quotation
        return $quotation->merchant->isAdminOrStaff($user->id);
    }

    /**
     * Determine whether the user can delete the quotation.
     *
     * @param  \App\User  $user
     * @param  \App\Quotation  $quotation
     * @return mixed
     */
    public function delete(User $user, Quotation $quotation)
    {
        //  Only an Admin of the merchant account can delete this quotation
        return $quotation->merchant->isAdmin($user->id);
    }

    /**
     * Determine whether the user can restore the quotation.
     *
     * @param  \App\User  $user
     * @param  \App\Quotation  $quotation
     * @return mixed
     */
    public function restore(User $user, Quotation $quotation)
    {
        //  Only an Admin of the merchant account can restore this quotation
        return $quotation->merchant->isAdmin($user->id);
    }

    /**
     * Determine whether the user can permanently delete the quotation.
     *
     * @param  \App\User  $user
     * @param  \App\Quotation  $quotation
     * @return mixed
     */
    public function forceDelete(User $user, Quotation $quotation)
    {
        //  Only an Admin of the merchant account can force delete this quotation
        return $quotation->merchant->isAdmin($user->id);
    }
}
