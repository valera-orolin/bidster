<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\AuctionRequest;
use Illuminate\Support\Facades\Auth;

class LotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Lots/Index', [

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Lots/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'address' => 'required|max:255',
            'description' => 'required|max:3000',
            'end_date' => 'required|date',
            'starting_price' => 'required|numeric|min:0',
        ]);

        $lot = new Lot($validated);
        $lot->save();

        $auctionRequest = new AuctionRequest([
            'lot_id' => $lot->id,
            'user_id' => Auth::id(),
            'type' => 'Add',
        ]);
        $auctionRequest->save();

        return response(null, 200);
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
