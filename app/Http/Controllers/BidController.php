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
     * Display a listing of the resource.
     */
    public function index()
    {
        $bids = Bid::with(['user', 'auction.lot'])->where('user_id', auth()->id())->latest()->paginate(10);

        return Inertia::render('Bids/Index', [
            'bids' => $bids,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Auction $auction)
    {
        $auction->load('lot');

        return Inertia::render('Bids/Create', [
            'auction' => $auction,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Auction $auction)
    {
        $validated = $request->validate([
            'bid_size' => 'required|numeric|min:0',
        ]);

        $user = auth()->user();

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
     * Display the specified resource.
     */
    public function show(Bid $bid)
    {
        $bid->load(['user', 'auction.lot']);

        return Inertia::render('Bids/Show', [
            'bid' => $bid,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bid $bid)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bid $bid)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bid $bid)
    {
        //
    }
}
