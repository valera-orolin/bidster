<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Inertia\Inertia;
use App\Models\Auction;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Display a listing of the auctions liked by the authenticated user.
     *
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        $auctions = Auction::whereHas('likes', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->with(['lot', 'seller', 'lot.images', 'lot.subcategory.category'])
            ->withCount('bids')
            ->withMax('bids', 'bid_size')
            ->orderByDesc(
                Like::select('created_at')
                    ->whereColumn('auction_id', 'auctions.id')
                    ->latest()
            )
            ->paginate(10);

        return Inertia::render('Likes/Index', [
            'auctions' => $auctions,
        ]);
    }

    /**
     * Store a new like or delete an existing like for an auction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Auction  $auction
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Auction $auction): \Illuminate\Http\Response
    {
        $like = Like::where('user_id', $request->user()->id)
                ->where('auction_id', $auction->id)
                ->first();

        if ($like) {
            $like->delete();
        } else {
            $like = new Like();
            $like->auction()->associate($auction);
            $like->user()->associate($request->user());
            $like->save();
        }

        return response('', 200);
    }
}
