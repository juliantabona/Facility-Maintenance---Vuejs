<?php

namespace App\Policies;

use App\User;
use App\Discount;
use Illuminate\Auth\Access\HandlesAuthorization;

class DiscountPolicy
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
     * Determine whether the user can view all discounts.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function viewAll(User $user)
    {
        //  Only the Super Admin can view all discounts
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the discount.
     *
     * @param  \App\User  $user
     * @param  \App\Discount  $discount
     * @return mixed
     */
    public function view(User $user, Discount $discount)
    {
        //  Only an Admin or Staff member of the discount owner can view this discount
        return $discount->owner->isAdminOrStaff($user->id);
    }

    /**
     * Determine whether the user can create discounts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //  Any Authenticated user can create a discount
    }

    /**
     * Determine whether the user can update the discount.
     *
     * @param  \App\User  $user
     * @param  \App\Discount  $discount
     * @return mixed
     */
    public function update(User $user, Discount $discount)
    {
        //  Only an Admin or Staff member of the discount owner can update this discount
        return $discount->owner->isAdminOrStaff($user->id);
    }

    /**
     * Determine whether the user can delete the discount.
     *
     * @param  \App\User  $user
     * @param  \App\Discount  $discount
     * @return mixed
     */
    public function delete(User $user, Discount $discount)
    {
        //  Only an Admin or Staff member of the discount owner can delete this discount
        return $discount->owner->isAdmin($user->id);
    }

    /**
     * Determine whether the user can restore the discount.
     *
     * @param  \App\User  $user
     * @param  \App\Discount  $discount
     * @return mixed
     */
    public function restore(User $user, Discount $discount)
    {
        //  Only an Admin or Staff member of the discount owner can restore this discount
        return $discount->owner->isAdmin($user->id);
    }

    /**
     * Determine whether the user can permanently delete the discount.
     *
     * @param  \App\User  $user
     * @param  \App\Discount  $discount
     * @return mixed
     */
    public function forceDelete(User $user, Discount $discount)
    {
        //  Only an Admin or Staff member of the discount owner can force delete this discount
        return $discount->owner->isAdmin($user->id);
    }
}
