<?php

namespace App\Policies;

use App\User;
use App\Coupon;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouponPolicy
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
     * Determine whether the user can view all coupons.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function viewAll(User $user)
    {
        //  Only the Super Admin can view all coupons
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the coupon.
     *
     * @param  \App\User  $user
     * @param  \App\Coupon  $coupon
     * @return mixed
     */
    public function view(User $user, Coupon $coupon)
    {
        //  Only an Admin or Staff member of the coupon owner can view this coupon
        return $coupon->owner->isAdminOrStaff($user->id);
    }

    /**
     * Determine whether the user can create coupons.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //  Any Authenticated user can create a coupon
    }

    /**
     * Determine whether the user can update the coupon.
     *
     * @param  \App\User  $user
     * @param  \App\Coupon  $coupon
     * @return mixed
     */
    public function update(User $user, Coupon $coupon)
    {
        //  Only an Admin or Staff member of the coupon owner can update this coupon
        return $coupon->owner->isAdminOrStaff($user->id);
    }

    /**
     * Determine whether the user can delete the coupon.
     *
     * @param  \App\User  $user
     * @param  \App\Coupon  $coupon
     * @return mixed
     */
    public function delete(User $user, Coupon $coupon)
    {
        //  Only an Admin or Staff member of the coupon owner can delete this coupon
        return $coupon->owner->isAdmin($user->id);
    }

    /**
     * Determine whether the user can restore the coupon.
     *
     * @param  \App\User  $user
     * @param  \App\Coupon  $coupon
     * @return mixed
     */
    public function restore(User $user, Coupon $coupon)
    {
        //  Only an Admin or Staff member of the coupon owner can restore this coupon
        return $coupon->owner->isAdmin($user->id);
    }

    /**
     * Determine whether the user can permanently delete the coupon.
     *
     * @param  \App\User  $user
     * @param  \App\Coupon  $coupon
     * @return mixed
     */
    public function forceDelete(User $user, Coupon $coupon)
    {
        //  Only an Admin or Staff member of the coupon owner can force delete this coupon
        return $coupon->owner->isAdmin($user->id);
    }
}
