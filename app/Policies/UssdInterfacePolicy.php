<?php

namespace App\Policies;

use App\User;
use App\UssdInterface;
use Illuminate\Auth\Access\HandlesAuthorization;

class UssdInterfacePolicy
{
    use HandlesAuthorization;

    /**
     * Authorize any action on this given policy if the user
     * is a super admin.
     */
     public function before($user, $ability)
     {
        //  return $user->isSuperAdmin();
     }
     
     /**
      * Determine whether the user can view all ussd stores.
      *
      * @param  \App\User  $user
      * @param  \App\User  $model
      * @return mixed
      */
     public function viewAll(User $user)
     {
        return true;
     }

    /**
     * Determine whether the user can view the ussd store.
     *
     * @param  \App\User  $user
     * @param  \App\UssdInterface  $UssdInterface
     * @return mixed
     */
    public function view(User $user, UssdInterface $UssdInterface)
    {
        return true;
    }

    /**
     * Determine whether the user can create ussd stores.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the ussd store.
     *
     * @param  \App\User  $user
     * @param  \App\UssdInterface  $UssdInterface
     * @return mixed
     */
    public function update(User $user, UssdInterface $UssdInterface)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the ussd store.
     *
     * @param  \App\User  $user
     * @param  \App\UssdInterface  $UssdInterface
     * @return mixed
     */
    public function delete(User $user, UssdInterface $UssdInterface)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the ussd store.
     *
     * @param  \App\User  $user
     * @param  \App\UssdInterface  $UssdInterface
     * @return mixed
     */
    public function restore(User $user, UssdInterface $UssdInterface)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the ussd store.
     *
     * @param  \App\User  $user
     * @param  \App\UssdInterface  $UssdInterface
     * @return mixed
     */
    public function forceDelete(User $user, UssdInterface $UssdInterface)
    {
        return true;
    }
}
