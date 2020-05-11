<?php

namespace App\Policies;

use App\User;
use App\UssdService;
use Illuminate\Auth\Access\HandlesAuthorization;

class UssdServicePolicy
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
     * Determine whether the user can view all ussd services.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function viewAll(User $user)
    {
        //  Only the Super Admin can view all ussd services
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the ussd service.
     *
     * @param  \App\User  $user
     * @param  \App\UssdService  $ussdService
     * @return mixed
     */
    public function view(User $user, UssdService $ussdService)
    {
        //  Only an Admin or Staff member can view this ussd service
        return $ussdService->isAdminOrStaff($user->id);
    }

    /**
     * Determine whether the user can create ussd services.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //  Any Authenticated user can create a ussd service
        return true;
    }

    /**
     * Determine whether the user can update the ussd service.
     *
     * @param  \App\User  $user
     * @param  \App\UssdService  $ussdService
     * @return mixed
     */
    public function update(User $user, UssdService $ussdService)
    {
        //  Only an Admin or Staff member can update this ussd service
        return $ussdService->isAdminOrStaff($user->id);
    }

    /**
     * Determine whether the user can delete the ussd service.
     *
     * @param  \App\User  $user
     * @param  \App\UssdService  $ussdService
     * @return mixed
     */
    public function delete(User $user, UssdService $ussdService)
    {
        //  Only an Admin can delete this ussd service
        return $ussdService->isAdmin($user->id);
    }

    /**
     * Determine whether the user can restore the ussd service.
     *
     * @param  \App\User  $user
     * @param  \App\UssdService  $ussdService
     * @return mixed
     */
    public function restore(User $user, UssdService $ussdService)
    {
        //  Only an Admin can restore this ussd service
        return $ussdService->isAdmin($user->id);
    }

    /**
     * Determine whether the user can permanently delete the ussd service.
     *
     * @param  \App\User  $user
     * @param  \App\UssdService  $ussdService
     * @return mixed
     */
    public function forceDelete(User $user, UssdService $ussdService)
    {
        //  Only an Admin can force delete this ussd service
        return $ussdService->isAdmin($user->id);
    }
}
