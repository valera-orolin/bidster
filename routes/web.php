<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\BidController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuctionRequestController;

Route::get('/', function () {
    return Inertia::render('Welcome');
});


Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {

    Route::get('/', function () {
        return Inertia::render('Admin/Welcome');
    })->name('admin.welcome');

    Route::get('/requests', [AuctionRequestController::class, 'index'])->name('admin.requests.index');

    Route::get('/requests/create-auction/{auctionRequest}', [AuctionRequestController::class, 'createAuction'])->name('admin.requests.create-auction');

    Route::post('/requests/store-auction/{auctionRequest}', [AuctionRequestController::class, 'storeAuction'])->name('admin.requests.store-auction');

    Route::post('/requests/decline-auction/{auctionRequest}', [AuctionRequestController::class, 'declineAuction'])->name('admin.requests.decline-auction');

    Route::get('/requests/edit-auction/{auctionRequest}', [AuctionRequestController::class, 'editAuction'])->name('admin.requests.edit-auction');

    Route::put('/requests/update-auction/{auctionRequest}', [AuctionRequestController::class, 'updateAuction'])->name('admin.requests.update-auction');

    Route::get('/auctions', [AuctionController::class, 'indexAdmin'])->name('admin.auctions.index');

    Route::get('/auctions/edit/{auction}', [AuctionController::class, 'editAdmin'])->name('admin.auctions.edit');

    Route::get('/auctions/bids/{auction}', [AuctionController::class, 'bidsAdmin'])->name('admin.auctions.bids');

    Route::get('/users', [ProfileController::class, 'index'])->name('admin.users.index');

    Route::get('/users/edit/{user}', [ProfileController::class, 'editAdmin'])->name('admin.users.edit');
});


Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [LotController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

    Route::resource('lots', LotController::class)
        ->only(['index', 'create', 'store'])
        ->middleware(['auth', 'verified']);

    Route::get('/lots/show/{auction}', [LotController::class, 'show'])->middleware(['auth', 'verified'])->name('lots.show');

    Route::get('/lots/edit/{auction}', [LotController::class, 'edit'])->middleware(['auth', 'verified'])->name('lots.edit');

    Route::put('/lots/update/{auction}', [LotController::class, 'update'])->middleware(['auth', 'verified'])->name('lots.update');

    Route::get('/bids', [BidController::class, 'index'])->middleware(['auth', 'verified'])->name('bids.index');

    Route::get('/bids/show/{bid}', [BidController::class, 'show'])->middleware(['auth', 'verified'])->name('bids.show');

    Route::get('/bids/create/{auction}', [BidController::class, 'create'])->middleware(['auth', 'verified'])->name('bids.create');

    Route::post('/bids/store/{auction}', [BidController::class, 'store'])->middleware(['auth', 'verified'])->name('bids.store');

    Route::get('/auctions', [AuctionController::class, 'index'])->middleware(['auth', 'verified'])->name('auctions.index');

    Route::get('/auctions/bids/{auction}', [AuctionController::class, 'bids'])->middleware(['auth', 'verified'])->name('auctions.bids');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile/show/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
