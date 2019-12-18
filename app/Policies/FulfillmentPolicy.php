<?php

namespace App\Policies;

use App\User;
use App\Fulfillment;
use Illuminate\Auth\Access\HandlesAuthorization;

class FulfillmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the fulfillment.
     *
     * @param  \App\User  $user
     * @param  \App\Fulfillment  $fulfillment
     * @return mixed
     */
    public function view(User $user, Fulfillment $fulfillment)
    {
        return true;
    }

    /**
     * Determine whether the user can create fulfillments.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the fulfillment.
     *
     * @param  \App\User  $user
     * @param  \App\Fulfillment  $fulfillment
     * @return mixed
     */
    public function update(User $user, Fulfillment $fulfillment)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the fulfillment.
     *
     * @param  \App\User  $user
     * @param  \App\Fulfillment  $fulfillment
     * @return mixed
     */
    public function delete(User $user, Fulfillment $fulfillment)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the fulfillment.
     *
     * @param  \App\User  $user
     * @param  \App\Fulfillment  $fulfillment
     * @return mixed
     */
    public function restore(User $user, Fulfillment $fulfillment)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the fulfillment.
     *
     * @param  \App\User  $user
     * @param  \App\Fulfillment  $fulfillment
     * @return mixed
     */
    public function forceDelete(User $user, Fulfillment $fulfillment)
    {
        return true;
    }
}
