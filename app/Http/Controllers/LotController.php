<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use Inertia\Inertia;
use App\Models\Auction;
use App\Models\Category;
use App\Models\LotImage;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use App\Models\AuctionRequest;
use App\Models\Characteristic;
use Illuminate\Support\Facades\Auth;

class LotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $auctions = Auction::with(['lot', 'seller', 'lot.images', 'lot.subcategory.category'])
            ->withCount('bids')
            ->withMax('bids', 'bid_size')
            ->latest()
            ->paginate(10);

        return Inertia::render('Lots/Index', [
            'auctions' => $auctions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::with('subcategories')->get();

        return Inertia::render('Lots/Create', [
            'categories' => $categories,
        ]);
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
            'images' => 'array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'characteristics' => 'array',
            'characteristics.*.name' => 'required|string',
            'characteristics.*.value' => 'required|string',
            'subcategory_id' => 'nullable|exists:subcategories,id'
        ]);

        $lot = new Lot($validated);
        $lot->save();

        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $path = '/storage/' . $image->store('images', 'public');
                LotImage::create([
                    'lot_id' => $lot->id,
                    'image_path' => $path
                ]);
            }
        }

        if ($request->has('characteristics')) {
            foreach ($request->input('characteristics') as $characteristic) {
                Characteristic::create([
                    'lot_id' => $lot->id,
                    'name' => $characteristic['name'],
                    'value' => $characteristic['value']
                ]);
            }
        }

        $auctionRequest = new AuctionRequest([
            'lot_id' => $lot->id,
            'old_lot_id' => null,
            'user_id' => Auth::id(),
            'type' => 'Create',
        ]);
        $auctionRequest->save();

        return response(null, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Auction $auction)
    {
        $auction->load(['lot', 'seller', 'lot.images', 'lot.characteristics', 'lot.subcategory.category']);
        $auction->loadCount(['bids']);
        $auction->loadMax('bids', 'bid_size');
        $auction->seller->loadCount(['auctions' => function ($query) {
            $query->where('status', 'Finished');
        }]);

        return Inertia::render('Lots/Show', [
            'auction' => $auction,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Auction $auction)
    {
        $auction->load(['lot', 'lot.images', 'lot.characteristics']);
        $auction->loadCount(['bids']);
        $auction->loadMax('bids', 'bid_size');
        $categories = Category::with('subcategories')->get();

        return Inertia::render('Lots/Edit', [
            'auction' => $auction,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Auction $auction)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'address' => 'required|max:255',
            'description' => 'required|max:3000',
            'end_date' => 'required|date',
            'starting_price' => 'required|numeric|min:0',
            'images' => 'array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'characteristics' => 'array',
            'characteristics.*.name' => 'required|string',
            'characteristics.*.value' => 'required|string',
            'subcategory_id' => 'nullable|exists:subcategories,id'
        ]);

        $lot = new Lot($validated);
        $lot->save();

        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $path = '/storage/' . $image->store('images', 'public');
                LotImage::create([
                    'lot_id' => $lot->id,
                    'image_path' => $path
                ]);
            }
        }

        if ($request->has('characteristics')) {
            foreach ($request->input('characteristics') as $characteristic) {
                Characteristic::create([
                    'lot_id' => $lot->id,
                    'name' => $characteristic['name'],
                    'value' => $characteristic['value']
                ]);
            }
        }

        $auctionRequest = new AuctionRequest([
            'lot_id' => $lot->id,
            'old_lot_id' => $auction->lot->id,
            'user_id' => Auth::id(),
            'type' => 'Edit',
        ]);
        $auctionRequest->save();

        return response(null, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
