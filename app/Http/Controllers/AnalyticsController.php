<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\AnalyticsService;

class AnalyticsController extends Controller
{
    /**
     * Show analytics page
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function __invoke(Request $request)
    {
        $tab = $request->input('tab', 'today');
        $tab = $tab == '' ? 'today' : $tab;
        $from = Carbon::parse($request->input('from'));
        $to = Carbon::parse($request->input('to'));

        $analytics = new AnalyticsService($tab, $from, $to);

        $finished_auctions = $analytics->getFinishedAuctions();
        $placed_bids = $analytics->getPlacedBids();
        $bid_amount = $analytics->getBidAmount();
        $winning_bid_amount = $analytics->getWinningBidAmount();
        $completed_auctions_count = $analytics->getCompletedAuctionsCount();
        $keys = $analytics->getKeys();
        
        return Inertia::render('Admin/Analytics/Index', [
            'finished_auctions' => $finished_auctions,
            'placed_bids' => $placed_bids,
            'winning_bid_amount' => $winning_bid_amount,
            'bid_amount' => $bid_amount,
            'completed_auctions_count' => $completed_auctions_count,
            'keys' => $keys,
        ]);
    }
}
