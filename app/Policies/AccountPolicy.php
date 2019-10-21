<?php

namespace App\Policies;

use App\User;
use App\Account;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
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
     * Determine whether the user can view all accounts.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function viewAll(User $user)
    {
        //  Only the Super Admin can view all accounts
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the account.
     *
     * @param \App\User    $user
     * @param \App\Account $account
     *
     * @return mixed
     */
    public function view(User $user, Account $account)
    {
        //  Only an Admin or Staff member can view this account
        return $account->isAdminOrStaff($user->id);
    }

    /**
     * Determine whether the user can create accounts.
     *
     * @param \App\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        //  Any Authenticated user can create a account
    }

    /**
     * Determine whether the user can update the account.
     *
     * @param \App\User    $user
     * @param \App\Account $account
     *
     * @return mixed
     */
    public function update(User $user, Account $account)
    {
        //  Only an Admin or Staff member can update this account
        return $account->isAdminOrStaff($user->id);
    }

    /**
     * Determine whether the user can delete the account.
     *
     * @param \App\User    $user
     * @param \App\Account $account
     *
     * @return mixed
     */
    public function delete(User $user, Account $account)
    {
        //  Only an Admin can delete this account
        return $account->isAdmin($user->id);
    }

    /**
     * Determine whether the user can restore the account.
     *
     * @param \App\User    $user
     * @param \App\Account $account
     *
     * @return mixed
     */
    public function restore(User $user, Account $account)
    {
        //  Only an Admin can restore this account
        return $account->isAdmin($user->id);
    }

    /**
     * Determine whether the user can permanently delete the account.
     *
     * @param \App\User    $user
     * @param \App\Account $account
     *
     * @return mixed
     */
    public function forceDelete(User $user, Account $account)
    {
        //  Only an Admin can force delete this account
        return $account->isAdmin($user->id);
    }
}
