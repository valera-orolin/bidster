<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use App\Services\SmartContractService;

class AuctionController extends Controller
{
    /**
     * Display a listing of the auctions that belong to the authenticated user.
     *
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        $auctions = Auction::with(['lot', 'lot.images'])
            ->withCount('bids')
            ->withMax('bids', 'bid_size')
            ->where('seller_id', auth()->id())
            ->latest()
            ->paginate(10);

        return Inertia::render('Auctions/Index', [
            'auctions' => $auctions,
        ]);
    }

    /**
     * Display the bids for the specified auction.
     *
     * @param  Auction $auction
     * @return \Inertia\Response
     */
    public function bids(Auction $auction): \Inertia\Response
    {
        $bids = Bid::with(['user'])->where('auction_id', $auction->id)->latest()->paginate(10);

        return Inertia::render('Auctions/Bids', [
            'bids' => $bids,
            'lot_title' => $auction->lot->title,
        ]);
    }

    /**
     * Display a listing of all auctions for admin.
     *
     * @return \Inertia\Response
     */
    public function indexAdmin(): \Inertia\Response
    {
        $auctions = Auction::with(['lot', 'lot.images'])
            ->withCount('bids')
            ->withMax('bids', 'bid_size')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Auctions/Index', [
            'auctions' => $auctions,
        ]);
    }

    /**
     * Show the form for editing (managing) the specified auction in admin panel.
     *
     * @param  Auction $auction
     * @return \Inertia\Response
     */
    public function editAdmin(Auction $auction): \Inertia\Response
    {
        $auction->load([
            'lot',
            'lot.images',
            'bids' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }, 
            'bids.user'
        ]);
        $auction->loadCount(['bids']);
        $auction->loadMax('bids', 'bid_size');

        return Inertia::render('Admin/Auctions/Edit', [
            'auction' => $auction,
        ]);
    }

    /**
     * Display the bids for the specified auction in admin panel.
     *
     * @param  Auction $auction
     * @return \Inertia\Response
     */
    public function bidsAdmin(Auction $auction): \Inertia\Response
    {
        $bids = Bid::with(['user'])->where('auction_id', $auction->id)->latest()->paginate(10);

        return Inertia::render('Admin/Auctions/Bids', [
            'bids' => $bids,
            'lot_title' => $auction->lot->title,
        ]);
    }

    /**
     * Declare the specified auction as failed in admin panel.
     *
     * @param  Auction $auction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function declareFailureAdmin(Request $request, Auction $auction): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('declareFailure', $auction);

        if ($auction->contract_id  != null) {
            $validated = $request->validate([
                'address' => 'required|size:42',
            ]);

            /** @var \App\Models\User $user */
            $user = auth()->user();
            if (!$user->isAddressCorrect($validated['address'])) {
                return response('Wrong address.', 500);
            }
            
            try {
                SmartContractService::cancelAuction($validated['address'], $auction->contract_id);
                
                $user->contract_address = Hash::make($validated['address']);
                $user->save();
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        }

        $auction->status = 'Failed';
        $auction->save();

        return redirect()->back();
    }

    /**
     * Declare the specified auction as failed.
     *
     * @param  Auction $auction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function declareFailure(Request $request, Auction $auction): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('declareFailure', $auction);

        if ($auction->contract_id  != null) {
            $validated = $request->validate([
                'address' => 'required|size:42',
            ]);

            /** @var \App\Models\User $user */
            $user = auth()->user();
            if (!$user->isAddressCorrect($validated['address'])) {
                return response('Wrong address.', 500);
            }
            
            try {
                SmartContractService::cancelAuction($validated['address'], $auction->contract_id);
                
                $user->contract_address = Hash::make($validated['address']);
                $user->save();
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        }

        $auction->status = 'Failed';
        $auction->save();

        return redirect(route('auctions.index'));
    }

    /**
     * Declare the specified auction as finished.
     *
     * @param  Auction $auction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function declareFinish(Request $request, Auction $auction): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('declareFinish', $auction);

        if ($auction->contract_id  != null) {
            $validated = $request->validate([
                'address' => 'required|size:42',
            ]);

            /** @var \App\Models\User $user */
            $user = auth()->user();
            if (!$user->isAddressCorrect($validated['address'])) {
                return response('Wrong address.', 500);
            }
            
            try {
                SmartContractService::finishAuction($validated['address'], $auction->contract_id);
                
                $user->contract_address = Hash::make($validated['address']);
                $user->save();
            } catch (\Exception $e) {
                return response($e->getMessage(), 500);
            }
        }

        $request = new Request();
        $request->merge(['auction_id' => $auction->id]);
        $prizeController = new PrizeController();
        $prizeController->store($request);

        $auction->status = 'Finished';
        $auction->save();

        return redirect(route('auctions.index'));
    }
}
