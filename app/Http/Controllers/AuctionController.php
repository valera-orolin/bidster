<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use Inertia\Inertia;
use App\Models\Auction;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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

    public function bids(Auction $auction)
    {
        $bids = Bid::with(['user'])->where('auction_id', $auction->id)->latest()->paginate(10);

        return Inertia::render('Auctions/Bids', [
            'bids' => $bids,
            'lot_title' => $auction->lot->title,
        ]);
    }

    public function indexAdmin()
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

    public function editAdmin(Auction $auction)
    {
        $auction->load(['lot', 'lot.images']);
        $auction->loadCount(['bids']);
        $auction->loadMax('bids', 'bid_size');

        return Inertia::render('Admin/Auctions/Edit', [
            'auction' => $auction,
        ]);
    }

    public function bidsAdmin(Auction $auction)
    {
        $bids = Bid::with(['user'])->where('auction_id', $auction->id)->latest()->paginate(10);

        return Inertia::render('Admin/Auctions/Bids', [
            'bids' => $bids,
            'lot_title' => $auction->lot->title,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
