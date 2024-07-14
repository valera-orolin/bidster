<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use Inertia\Inertia;
use App\Models\Prize;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\SmartContractService;

class PrizeController extends Controller
{
    /**
     * Store a newly created prize in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
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

    /**
     * Display a listing of the prizes for the admin.
     *
     * @return \Inertia\Response
     */
    public function indexAdmin(): \Inertia\Response
    {
        $prizes = Prize::with('auction.seller', 'bid.user', 'auction.lot.images')->latest()->paginate(10);

        return Inertia::render('Admin/Prizes/Index', [
            'prizes' => $prizes,
        ]);
    }

    /**
     * Display a listing of the prizes for the authenticated user.
     *
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        $userId = Auth::user()->id;

        $prizes = Prize::with('auction.seller', 'bid.user', 'auction.lot.images')
            ->whereHas('auction', function ($query) use ($userId) {
                $query->where('seller_id', $userId);
            })
            ->orWhereHas('bid', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('Prizes/Index', [
            'prizes' => $prizes,
        ]);
    }

    /**
     * Mark the specified prize as received.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Prize $prize
     * @return \Illuminate\Http\JsonResponse
     */
    public function receivePrize(Request $request, Prize $prize): \Illuminate\Http\JsonResponse
    {
        if ($prize->is_received) {
            return response()->json('Prize was already received', 500);
        }

        if (Gate::denies('receive', $prize)) {
            return response()->json('Unauthorized', 403);
        }

        if ($prize->auction->contract_id  != null) {
            $validated = $request->validate([
                'address' => 'required|size:42',
            ]);
            SmartContractService::claimPrize($validated['address'], $prize->auction->contract_id);
        }

        $prize->is_received = true;
        $prize->save();

        return response()->json('Prize id received', 200);
    }
}
