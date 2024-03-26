<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
