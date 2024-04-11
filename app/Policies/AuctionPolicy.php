<?php

namespace App\Policies;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AuctionPolicy
{
    /**
     * Determine whether the user can declare auctions as failed.
     */
    public function declareFailure(User $user, Auction $auction): bool
    {
        return $auction->seller()->is($user) || $user->isAdmin();
    }

    /**
     * Determine whether the user can declare auctions as finished.
     */
    public function declareFinish(User $user, Auction $auction): bool
    {
        return $auction->seller()->is($user);
    }
}
