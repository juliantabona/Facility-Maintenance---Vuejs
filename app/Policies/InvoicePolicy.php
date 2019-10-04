<?php

namespace App\Policies;

use App\User;
use App\Invoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
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
     * Determine whether the user can view all invoices.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function viewAll(User $user)
    {
        //  Only the Super Admin can view all invoices
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the invoice.
     *
     * @param  \App\User  $user
     * @param  \App\Invoice  $invoice
     * @return mixed
     */
    public function view(User $user, Invoice $invoice)
    {
        //  Only an Admin or Staff member of the merchant account can view this invoice
        return $invoice->merchant->isAdminOrStaff($user->id);
    }

    /**
     * Determine whether the user can create invoices.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //  Any Authenticated user can create an invoice
    }

    /**
     * Determine whether the user can update the invoice.
     *
     * @param  \App\User  $user
     * @param  \App\Invoice  $invoice
     * @return mixed
     */
    public function update(User $user, Invoice $invoice)
    {
        //  Only an Admin or Staff member of the merchant account can update this invoice
        return $invoice->merchant->isAdminOrStaff($user->id);
    }

    /**
     * Determine whether the user can delete the invoice.
     *
     * @param  \App\User  $user
     * @param  \App\Invoice  $invoice
     * @return mixed
     */
    public function delete(User $user, Invoice $invoice)
    {
        //  Only an Admin of the merchant account can delete this invoice
        return $invoice->merchant->isAdmin($user->id);
    }

    /**
     * Determine whether the user can restore the invoice.
     *
     * @param  \App\User  $user
     * @param  \App\Invoice  $invoice
     * @return mixed
     */
    public function restore(User $user, Invoice $invoice)
    {
        //  Only an Admin of the merchant account can restore this invoice
        return $invoice->merchant->isAdmin($user->id);
    }

    /**
     * Determine whether the user can permanently delete the invoice.
     *
     * @param  \App\User  $user
     * @param  \App\Invoice  $invoice
     * @return mixed
     */
    public function forceDelete(User $user, Invoice $invoice)
    {
        //  Only an Admin of the merchant account can force delete this invoice
        return $invoice->merchant->isAdmin($user->id);
    }
}
