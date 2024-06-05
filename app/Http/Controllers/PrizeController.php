<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Prize;
use App\Models\Auction;
use Illuminate\Http\Request;

class PrizeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'auction_id' => 'required|exists:auctions,id',
        ]);
    
        $auction = Auction::find($validated['auction_id']);
    
        if ($auction->status != Auction::STATUS_ACTIVE) {
            return response()->json(['error' => 'Auction is not active'], 400);
        }
    
        $bid = Bid::where('auction_id', $validated['auction_id'])->orderBy('bid_size', 'desc')->first();

        if (!$bid) {
            return response()->json(['error' => 'No bids for this auction'], 400);
        }
    
        $prize = Prize::create([
            'auction_id' => $validated['auction_id'],
            'bid_id' => $bid->id,
            'is_received' => false,
        ]);
    
        return response()->json($prize, 201);
    }    
}
