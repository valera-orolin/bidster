<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\AuctionRequest;

class AuctionRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requests = AuctionRequest::with('lot')->oldest()->paginate(10);

        return Inertia::render('Admin/AuctionRequests/Index', [
            'requests' => $requests,
        ]);
    }

    public function createAuction(AuctionRequest $auctionRequest)
    {
        $auctionRequest->load(['lot', 'user']);
        
        return Inertia::render('Admin/AuctionRequests/CreateAuction', [
            'request' => $auctionRequest,
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
