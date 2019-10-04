<?php

namespace App\Policies;

use App\Tax;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxPolicy
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
     * Determine whether the user can view all taxes.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function viewAll(User $user)
    {
        //  Only the Super Admin can view all taxes
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the tax.
     *
     * @param  \App\User  $user
     * @param  \App\Tax  $tax
     * @return mixed
     */
    public function view(User $user, Tax $tax)
    {
        //  Only an Admin or Staff member of the tax owner can view this tax
        return $tax->owner->isAdminOrStaff($user->id);
    }

    /**
     * Determine whether the user can create taxes.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //  Any Authenticated user can create a tax
    }

    /**
     * Determine whether the user can update the tax.
     *
     * @param  \App\User  $user
     * @param  \App\Tax  $tax
     * @return mixed
     */
    public function update(User $user, Tax $tax)
    {
        //  Only an Admin or Staff member of the tax owner can update this tax
        return $tax->owner->isAdminOrStaff($user->id);
    }

    /**
     * Determine whether the user can delete the tax.
     *
     * @param  \App\User  $user
     * @param  \App\Tax  $tax
     * @return mixed
     */
    public function delete(User $user, Tax $tax)
    {
        //  Only an Admin or Staff member of the tax owner can delete this tax
        return $tax->owner->isAdmin($user->id);
    }

    /**
     * Determine whether the user can restore the tax.
     *
     * @param  \App\User  $user
     * @param  \App\Tax  $tax
     * @return mixed
     */
    public function restore(User $user, Tax $tax)
    {
        //  Only an Admin or Staff member of the tax owner can restore this tax
        return $tax->owner->isAdmin($user->id);
    }

    /**
     * Determine whether the user can permanently delete the tax.
     *
     * @param  \App\User  $user
     * @param  \App\Tax  $tax
     * @return mixed
     */
    public function forceDelete(User $user, Tax $tax)
    {
        //  Only an Admin or Staff member of the tax owner can force delete this tax
        return $tax->owner->isAdmin($user->id);
    }
}
