<?php

namespace App\Policies;

use App\Models\Auction;
use App\Models\Lot;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LotPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lot $lot): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lot $lot): bool
    {
        $auction = Auction::where('lot_id', $lot->id)->first();
        return $auction->seller()->is($user) && $auction->status == 'Active';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lot $lot): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lot $lot): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lot $lot): bool
    {
        //
    }
}
