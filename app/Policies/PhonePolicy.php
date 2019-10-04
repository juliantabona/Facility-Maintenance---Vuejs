<?php

namespace App\Policies;

use App\User;
use App\Phone;
use Illuminate\Auth\Access\HandlesAuthorization;

class PhonePolicy
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
     * Determine whether the user can view all phones.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function viewAll(User $user)
    {
        //  Only the Super Admin can view all phones
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the phone.
     *
     * @param  \App\User  $user
     * @param  \App\Phone  $phone
     * @return mixed
     */
    public function view(User $user, Phone $phone)
    {
        //  If the owner is a user
        if( $phone->owner->resource_type === 'user' ){
            
            //  Then only the user that owns the phone can view this phone
            return $phone->owner->isAccountOwner($user->id);

        //  Otherwise if the owner is a company/store
        }else{

            //  Only an Admin or Staff member of the phone owner can view this phone
            return $phone->owner->isAdminOrStaff($user->id);   

        }
    }

    /**
     * Determine whether the user can create phones.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //  Any Authenticated user can create a phone
    }

    /**
     * Determine whether the user can update the phone.
     *
     * @param  \App\User  $user
     * @param  \App\Phone  $phone
     * @return mixed
     */
    public function update(User $user, Phone $phone)
    {
        //  If the owner is a user
        if( $phone->owner->resource_type === 'user' ){
            
            //  Then only the user that owns the phone can update this phone
            return $phone->owner->isAccountOwner($user->id);

        //  Otherwise if the owner is a company/store
        }else{

            //  Only an Admin or Staff member of the phone owner can update this phone
            return $phone->owner->isAdminOrStaff($user->id);   

        }
    }

    /**
     * Determine whether the user can delete the phone.
     *
     * @param  \App\User  $user
     * @param  \App\Phone  $phone
     * @return mixed
     */
    public function delete(User $user, Phone $phone)
    {
        //  If the owner is a user
        if( $phone->owner->resource_type === 'user' ){
            
            //  Then only the user that owns the phone can delete this phone
            return $phone->owner->isAccountOwner($user->id);

        //  Otherwise if the owner is a company/store
        }else{

            //  Only an Admin or Staff member of the phone owner can delete this phone
            return $phone->owner->isAdmin($user->id);

        }
    }

    /**
     * Determine whether the user can restore the phone.
     *
     * @param  \App\User  $user
     * @param  \App\Phone  $phone
     * @return mixed
     */
    public function restore(User $user, Phone $phone)
    {
        //  If the owner is a user
        if( $phone->owner->resource_type === 'user' ){
            
            //  Then only the user that owns the phone can restore this phone
            return $phone->owner->isAccountOwner($user->id);

        //  Otherwise if the owner is a company/store
        }else{

            //  Only an Admin or Staff member of the phone owner can restore this phone
            return $phone->owner->isAdmin($user->id);

        }
    }

    /**
     * Determine whether the user can permanently delete the phone.
     *
     * @param  \App\User  $user
     * @param  \App\Phone  $phone
     * @return mixed
     */
    public function forceDelete(User $user, Phone $phone)
    {
        //  If the owner is a user
        if( $phone->owner->resource_type === 'user' ){
            
            //  Then only the user that owns the phone can force delete this phone
            return $phone->owner->isAccountOwner($user->id);

        //  Otherwise if the owner is a company/store
        }else{

            //  Only an Admin or Staff member of the phone owner can force delete this phone
            return $phone->owner->isAdmin($user->id);

        }
    }

}
