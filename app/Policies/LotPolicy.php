<?php

namespace App\Policies;

use App\Models\Auction;
use App\Models\Lot;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LotPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lot $lot): bool
    {
        $auction = Auction::where('lot_id', $lot->id)->first();
        return $auction->seller()->is($user) && $auction->status == 'Active';
    }
}
