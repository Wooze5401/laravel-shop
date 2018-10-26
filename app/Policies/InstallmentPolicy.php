<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Installment;
use Illuminate\Auth\Access\HandlesAuthorization;

class InstallmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user is the user of the installment.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Installment  $installment
     * @return boolean
     */
    public function own(User $user, Installment $installment)
    {
        return $installment->user_id == $user->id;
    }
}
