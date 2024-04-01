<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function index()
    {
        $users = User::latest()->paginate(10);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function show(User $user)
    {
        $auctions = Auction::with(['lot', 'lot.images'])->where('seller_id', $user->id)->latest()->paginate(10);
        $bids = Bid::with(['user', 'auction.lot'])->where('user_id', $user->id)->latest()->paginate(10);

        return Inertia::render('Profile/Show', [
            'user' => $user,
            'auctions' => $auctions,
            'bids' => $bids,
        ]);
    }

    public function editAdmin(User $user): Response
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
        ]);
    }
}
