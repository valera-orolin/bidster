<?php

namespace App\Http\Controllers;

use Web3\Web3;
use Web3\Contract;
use App\Models\Bid;
use Inertia\Inertia;
use App\Models\Auction;
use Illuminate\Http\Request;
use Web3\Providers\HttpProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use App\Services\SmartContractService;

class BidController extends Controller
{
    /**
     * Display a listing of the bids that belong to the authenticated user.
     *
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        $bids = Bid::with(['user', 'auction.lot', 'auction.lot.images'])->where('user_id', auth()->id())->latest()->paginate(10);

        return Inertia::render('Bids/Index', [
            'bids' => $bids,
        ]);
    }

    /**
     * Show the form for creating a bid for the specified auction.
     *
     * @param  Auction $auction
     * @return \Inertia\Response
     */
    public function create(Auction $auction): \Inertia\Response
    {
        Gate::authorize('isPublished', $auction);
        
        $auction->load([
            'lot', 
            'bids' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }, 
            'bids.user'
        ]);
        $auction->loadCount(['bids']);
        $auction->loadMax('bids', 'bid_size');

        $max_bid = $auction->bids->max('bid_size');
        $min_bid = ceil($max_bid ?  $max_bid + $max_bid / 10 : $auction->lot->starting_price);

        return Inertia::render('Bids/Create', [
            'auction' => $auction,
            'min_bid_size' => $min_bid,
        ]);
    }

    /**
     * Store a newly created bid for the specified auction.
     *
     * @param  Request $request
     * @param  Auction $auction
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Auction $auction): \Illuminate\Http\Response
    {    
        Gate::authorize('isPublished', $auction);
        
        $validated = $request->validate([
            'bid_size' => 'required|numeric|min:0',
            'address' => 'required|size:42',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->id == $auction->seller->id) {
            return response('You can\'t place a bid on your own auction.', 500);
        }

        if ($auction->status != 'Active') {
            return response('Auction status must be active.', 500);
        }

        $max_bid = $auction->bids->max('bid_size');
        $min_bid = $max_bid ?  $max_bid + $max_bid / 10 : $auction->lot->starting_price;
        if ($validated['bid_size'] < $min_bid) {
            return response("New bid must be no less than $min_bid.", 500);
        }

        if (!$user->isAddressCorrect($validated['address'])) {
            return response('Wrong address.', 500);
        }

        try {
            $maxUserBid = $auction->bids->where('user_id', $user->id)->max('bid_size');
            $contractBidValue = $validated['bid_size'];
            if ($maxUserBid) {
                $contractBidValue -= $maxUserBid;
            }
            SmartContractService::bid($validated['address'], $contractBidValue, $auction->contract_id);

            $user->contract_address = Hash::make($validated['address']);
            $user->save();

            $bid = new Bid;
            $bid->user_id = $user->id;
            $bid->auction_id = $auction->id;
            $bid->bid_size = $validated['bid_size'];
            $bid->save();
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }

        return response(null, 200);
    }

    /**
     * Display the specified bid.
     *
     * @param  Bid $bid
     * @return \Inertia\Response
     */
    public function show(Bid $bid): \Inertia\Response
    {
        $bid->load(['user', 'auction.lot', 'auction.lot.images']);
        $bids = Bid::with(['user'])
            ->where('auction_id', $bid->auction->id)
            ->orderBy('created_at', 'asc')
            ->get();
        $bid->user->loadCount(['auctions' => function($query) {
            $query->where('status', 'Finished');
        }]);

        return Inertia::render('Bids/Show', [
            'bid' => $bid,
            'bids' => $bids,
            'max_bid_size' => $bids->max('bid_size'),
        ]);
    }
}
