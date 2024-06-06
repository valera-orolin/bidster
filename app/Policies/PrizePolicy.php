<?php

namespace App\Policies;

use App\Models\Prize;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PrizePolicy
{
    /**
     * Determine whether the user can receive a prize.
     */
    public function receive(User $user, Prize $prize)
    {
        return $user->id === $prize->bid->user_id;
    }
}
