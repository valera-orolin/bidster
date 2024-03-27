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
        //
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
            $bidSizeWei = bcmul($validated['bid_size'], bcpow('10', '18'));

            $contract->call('owner', [], function ($err, $result) use (&$owner, &$error) {
                if ($err !== null) {
                    // Обработка ошибки
                    $error = $err->getMessage();
                } else {
                    // Обработка результата
                    $owner = $result[0];
                }
            });

            if ($error) {
                return response()->json(['error' => $error], 500);
            } else {
                return response()->json(['owner' => $owner], 200);
            }
    
            $contract->call('payForItem', [], ['from' => $fromAddress, 'value' => $bidSizeWei], function ($err, $result) {
                if ($err !== null) {
                    // Обработка ошибки
                    return response()->json(['error' => $err->getMessage()], 500);
                }
                // Обработка результата
                return response()->json(['result' => 'Payment successful'], 200);
            });
        } catch (\Exception $e) {
            return response('Caught exception: '.  $e->getMessage(), 500);
        }

        /*

        $user = auth()->user();

        $bid = new Bid;
        $bid->user_id = $user->id;
        $bid->auction_id = $auction->id;
        $bid->bid_size = $validated['bid_size'];

        $bid->save();

        return response(null, 200);
        */
    }

    /*
    try {
            $web3 = new Web3('http://172.17.0.1:8545');
            $contractAddress = '0xA12f522368e96D7aFCe957bAB7aBeC5Bf694bc97';
            $path = __DIR__.'/ContractABI.json';
            $abi = json_decode(file_get_contents($path), true);
        
            $contract = new Contract($web3->provider, $abi);
            $contract->at($contractAddress);
        
            $owner = '';
            $error = '';
        
            $contract->call('owner', [], function ($err, $result) use (&$owner, &$error) {
                if ($err !== null) {
                    // Обработка ошибки
                    $error = $err->getMessage();
                } else {
                    // Обработка результата
                    $owner = $result[0];
                }
            });
            
            if ($error) {
                return response()->json(['error' => $error], 500);
            } else {
                return response()->json(['owner' => $owner], 200);
            }
            
        } catch (\Exception $e) {
            return response('Caught exception: '.  $e->getMessage(), 500);
        }
    */

    /**
     * Display the specified resource.
     */
    public function show(Bid $bid)
    {
        //
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
