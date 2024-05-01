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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class LotController extends Controller
{
    /**
     * Display a listing of the lots (active auctions).
     *
     * @return \Inertia\Response
     */
    public function index(): \Inertia\Response
    {
        $auctions = Auction::with(['lot', 'seller', 'lot.images', 'lot.subcategory.category'])
            ->where('status', 'Active')
            ->withCount('bids')
            ->withMax('bids', 'bid_size')
            ->latest()
            ->paginate(10);

        return Inertia::render('Lots/Index', [
            'auctions' => $auctions,
        ]);
    }

    /**
     * Show the form for creating a new lot.
     *
     * @return \Inertia\Response
     */
    public function create(): \Inertia\Response
    {
        $categories = Category::with('subcategories')->get();

        return Inertia::render('Lots/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created lot and auction request.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): \Illuminate\Http\Response
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'address' => 'required|max:255',
            'description' => 'required|max:3000',
            'end_date' => 'required|date|after_or_equal:tomorrow',
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
     * Display the specified lot (auction).
     *
     * @param  Auction $auction
     * @return \Inertia\Response
     */
    public function show(Auction $auction): \Inertia\Response
    {
        $auction->load(['lot', 'seller', 'lot.images', 'lot.characteristics', 'lot.subcategory.category']);
        $auction->loadCount(['bids']);
        $auction->loadMax('bids', 'bid_size');
        $auction->seller->loadCount(['auctions' => function ($query) {
            $query->where('status', 'Finished');
        }]);
        $auction->isLikedByUser = $auction->isLikedByUser();

        $messages = $auction->messages()->get();
        foreach ($messages as $message) {
            $message->load('user', 'auction');
        }

        return Inertia::render('Lots/Show', [
            'auction' => $auction,
            'messages' => $messages,
        ]);
    }

    /**
     * Show the form for editing the specified lot (auction).
     *
     * @param  Auction $auction
     * @return \Inertia\Response
     */
    public function edit(Auction $auction): \Inertia\Response
    {
        Gate::authorize('update', $auction->lot);

        $auction->load([
            'lot', 
            'lot.images', 
            'lot.characteristics',
            'bids' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }, 
            'bids.user'
        ]);
        $auction->loadCount(['bids']);
        $auction->loadMax('bids', 'bid_size');
        $categories = Category::with('subcategories')->get();

        return Inertia::render('Lots/Edit', [
            'auction' => $auction,
            'categories' => $categories,
        ]);
    }

    /**
     * Store an auction request for updating the specified lot (auction).
     *
     * @param  Request $request
     * @param  Auction $auction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Auction $auction): \Illuminate\Http\Response
    {
        Gate::authorize('update', $auction->lot);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'address' => 'required|max:255',
            'description' => 'required|max:3000',
            'end_date' => 'required|date|after_or_equal:tomorrow',
            'starting_price' => 'required|numeric|min:0',
            'characteristics' => 'array',
            'characteristics.*.name' => 'required|string',
            'characteristics.*.value' => 'required|string',
            'subcategory_id' => 'nullable|exists:subcategories,id'
        ]);

        $lot = new Lot($validated);
        $lot->save();
        
        if ($request->has('images')) {
            foreach ($request->images as $image) {
                if ($image instanceof \Illuminate\Http\UploadedFile) {
                    $path = '/storage/' . $image->store('images', 'public');
                    LotImage::create([
                        'lot_id' => $lot->id,
                        'image_path' => $path
                    ]);
                } else {
                    $storagePath = str_replace('/storage/', '', $image);
                    $contents = Storage::disk('public')->get($storagePath);
                    $newFileName = time() . '_' . basename($storagePath);
                    Storage::disk('public')->put('images/' . $newFileName, $contents);
                    LotImage::create([
                        'lot_id' => $lot->id,
                        'image_path' => '/storage/images/' . $newFileName
                    ]);
                }
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
}
