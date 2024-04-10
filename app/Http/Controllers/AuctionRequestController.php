<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lot;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Auction;
use Illuminate\Http\Request;
use App\Models\AuctionRequest;

class AuctionRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requests = AuctionRequest::with(['lot', 'lot.images'])->oldest()->paginate(10);

        return Inertia::render('Admin/AuctionRequests/Index', [
            'requests' => $requests,
        ]);
    }

    public function createAuction(AuctionRequest $auctionRequest)
    {
        $auctionRequest->load(['lot', 'user', 'lot.images', 'lot.characteristics', 'lot.subcategory.category']);
        $auctionRequest->user->loadCount(['auctions' => function ($query) {
            $query->where('status', 'Finished');
        }]);
        
        return Inertia::render('Admin/AuctionRequests/CreateAuction', [
            'request' => $auctionRequest,
        ]);
    }

    public function storeAuction(Request $request, AuctionRequest $auctionRequest)
    {
        $validated = $request->validate([
            'lot' => 'required',
            'user' => 'required',
        ]);

        $lot = Lot::find($validated['lot']);
        $user = User::find($validated['user']);

        if ($lot->end_date < Carbon::tomorrow()) {
            return response('The end date of the lot cannot be earlier than tomorrow.', 500);
        }

        if (!$lot || !$user) {
            return response('Lot or User not found.', 500);
        }

        $auction = new Auction;
        $auction->status = 'Active';
        $auction->lot_id = $lot->id;
        $auction->seller_id = $user->id;
        $auction->save();

        $auctionRequest->delete();

        return response(null, 200);
    }

    public function declineAuction(Request $request, AuctionRequest $auctionRequest)
    {
        $validated = $request->validate([
            'lot' => 'required',
        ]);

        $auctionRequest->delete();

        $lot = Lot::find($validated['lot']);

        if (!$lot) {
            return response('Lot not found', 404);
        }

        $lot->delete();

        return response(null, 200);
    }

    public function editAuction(AuctionRequest $auctionRequest)
    {
        $auctionRequest->load(['lot', 'old_lot', 'user', 'lot.images', 'old_lot.images', 'lot.characteristics', 'old_lot.characteristics', 'lot.subcategory.category', 'old_lot.subcategory.category']);
        $auctionRequest->user->loadCount(['auctions' => function ($query) {
            $query->where('status', 'Finished');
        }]);
        
        return Inertia::render('Admin/AuctionRequests/EditAuction', [
            'request' => $auctionRequest,
        ]);
    }

    public function updateAuction(Request $request, AuctionRequest $auctionRequest)
    {
        $validated = $request->validate([
            'lot' => 'required',
            'old_lot' => 'required',
        ]);

        $lot = Lot::find($validated['lot']);
        $old_lot = Lot::find($validated['old_lot']);
        $auction = Auction::where('lot_id', $old_lot->id)->first();

        if ($lot->end_date < Carbon::tomorrow()) {
            return response('The end date of the lot cannot be earlier than tomorrow.', 500);
        }

        if (!$lot || !$old_lot || !$auction) {
            return response('Lot or Auction not found', 404);
        }

        $auction->lot_id = $lot->id;
        $auction->save();

        $auctionRequest->delete();
        $old_lot->delete();

        return response(null, 200);
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
    public function show(AuctionRequest $auctionRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AuctionRequest $auctionRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AuctionRequest $auctionRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AuctionRequest $auctionRequest)
    {
        //
    }
}
