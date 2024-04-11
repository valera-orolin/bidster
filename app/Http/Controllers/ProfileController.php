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
use Illuminate\Support\Facades\Storage;
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
        $validated = $request->validated();

        if (empty($validated['description'])) {
            $validated['description'] = null;
        }

        if ($request->hasFile('avatar')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $request->user()->avatar));
            $path = $request->file('avatar')->store('images', 'public');
            $validated['avatar'] = '/storage/' . $path;
        }

        $request->user()->fill($validated);

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
        $users = User::withCount('bids')->latest()->paginate(10);
        $users->loadCount(['auctions' => function ($query) {
            $query->where('status', 'Finished');
        }]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function show(User $user)
    {
        $user->loadCount(['auctions' => function ($query) {
            $query->where('status', 'Finished');
        }]);
        $auctions = Auction::with(['lot', 'lot.images'])
            ->withCount('bids')
            ->withMax('bids', 'bid_size')
            ->where('seller_id', $user->id)
            ->latest()
            ->paginate(10);
        $bids = Bid::with(['user', 'auction.lot', 'auction.lot.images'])->where('user_id', $user->id)->latest()->paginate(10);

        return Inertia::render('Profile/Show', [
            'user' => $user,
            'auctions' => $auctions,
            'bids' => $bids,
        ]);
    }

    public function editAdmin(User $user): Response
    {
        $user->loadCount(['auctions' => function ($query) {
            $query->where('status', 'Finished');
        }]);
        $user->loadCount('bids');

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'currentUserRole' => Auth::user()->role,
        ]);
    }

    public function makeManager(User $user)
    {
        if ($user->role == 'Director') {
            return response('You can\'t demote a director.', 500);
        }

        $user->role = 'Manager';
        $user->save();

        return redirect()->back();
    }

    public function makeUser(User $user)
    {
        if ($user->role == 'Director') {
            return response('You can\'t demote a director.', 500);
        }

        $user->role = 'User';
        $user->save();

        return redirect()->back();
    }

    public function makeBanned(User $user) 
    {
        if ($user->role == 'Director') {
            return response('You can\'t ban a director.');
        }

        $user->status = 'Banned';
        $user->save();

        return redirect()->back();
    }

    public function makeActive(User $user) 
    {
        $user->status = 'Active';
        $user->save();

        return redirect()->back();
    }
}
