<?php

namespace App\Http\Controllers;

use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\Contract;
use App\Models\Bid;
use Inertia\Inertia;
use App\Models\Auction;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'bid_size' => 'required|numeric|min:0',
        ]);

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

        $bid = new Bid;
        $bid->user_id = $user->id;
        $bid->auction_id = $auction->id;
        $bid->bid_size = $validated['bid_size'];

        $bid->save();

        return response(null, 200);
    }

    /*
        try {
            $validated = $request->validate([
                'bid_size' => 'required|numeric|min:0',
            ]);
    
            $web3 = new Web3('http://172.17.0.1:8545');
            $contractAddress = '0xA12f522368e96D7aFCe957bAB7aBeC5Bf694bc97';
            $path = __DIR__.'/ContractABI.json';
            $abi = json_decode(file_get_contents($path), true);
    
            $contract = new Contract($web3->provider, $abi);
            $contract->at($contractAddress);
    
            $fromAddress = '0x099d429889A29fBb6014CcC56CAa599997568376';
            $valueInEther = $validated['bid_size'];
            $valueInWei = bcadd(bcmul($valueInEther, bcpow('10', '18')), '0');
        
            $contract->at($contractAddress)->send('payForItem', ['from' => $fromAddress, 'value' => 9999999999], function($err,$result) use($contract) {
                if ($err !== null) {
                    throw $err;
                }
                if ($result) {
                    var_dump($result);
                }
            });
        } catch (\Exception $e) {
            return response('Caught exception: '.  $e->getMessage(), 500);
        }

        return response(null, 200);
    */

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
