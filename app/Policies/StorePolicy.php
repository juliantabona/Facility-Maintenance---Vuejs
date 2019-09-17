<?php

namespace App\Policies;

use App\User;
use App\Store;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
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
     * Determine whether the user can view all stores.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function viewAll(User $user)
    {
        //  Only the Super Admin can view all stores
        if( $user->isSuperAdmin() ){
            return true;
        }
    }

    /**
     * Determine whether the user can view the store.
     *
     * @param  \App\User  $user
     * @param  \App\Store  $store
     * @return mixed
     */
    public function view(User $user, Store $store)
    {
        //  Only an Admin or Staff member can view this store
        if( $store->isAdminOrStaff($user->id) ){
            return true;
        }
    }

    /**
     * Determine whether the user can create stores.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //  Any Authenticated user can create a store
    }

    /**
     * Determine whether the user can update the store.
     *
     * @param  \App\User  $user
     * @param  \App\Store  $store
     * @return mixed
     */
    public function update(User $user, Store $store)
    {
        //  Only an Admin or Staff member can update this store
        if( $store->isAdminOrStaff($user->id) ){
            return true;
        }
    }

    /**
     * Determine whether the user can delete the store.
     *
     * @param  \App\User  $user
     * @param  \App\Store  $store
     * @return mixed
     */
    public function delete(User $user, Store $store)
    {
        //  Only an Admin can delete this store
        if( $store->isAdmin($user->id) ){
            return true;
        }
    }

    /**
     * Determine whether the user can restore the store.
     *
     * @param  \App\User  $user
     * @param  \App\Store  $store
     * @return mixed
     */
    public function restore(User $user, Store $store)
    {
        //  Only an Admin can restore this store
        if( $store->isAdmin($user->id) ){
            return true;
        }
    }

    /**
     * Determine whether the user can permanently delete the store.
     *
     * @param  \App\User  $user
     * @param  \App\Store  $store
     * @return mixed
     */
    public function forceDelete(User $user, Store $store)
    {
        //  Only an Admin can force delete this store
        if( $store->isAdmin($user->id) ){
            return true;
        }
    }
}
