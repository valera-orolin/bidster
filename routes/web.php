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

    Route::get('/auctions', [AuctionController::class, 'indexAdmin'])->name('admin.auctions.index');

    Route::get('/auctions/edit/{auction}', [AuctionController::class, 'editAdmin'])->name('admin.auctions.edit');

    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
});


Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [LotController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

    Route::resource('lots', LotController::class)
        ->only(['index', 'create', 'store', 'show', 'edit'])
        ->middleware(['auth', 'verified']);

    Route::get('/lots/show/{auction}', [LotController::class, 'show'])->middleware(['auth', 'verified'])->name('lots.show');

    Route::get('/bids', [BidController::class, 'index'])->middleware(['auth', 'verified'])->name('bids.index');

    Route::get('/bids/show/{bid}', [BidController::class, 'show'])->middleware(['auth', 'verified'])->name('bids.show');

    Route::get('/bids/create/{auction}', [BidController::class, 'create'])->middleware(['auth', 'verified'])->name('bids.create');

    Route::post('/bids/store/{auction}', [BidController::class, 'store'])->middleware(['auth', 'verified'])->name('bids.store');

    Route::get('/auctions', [AuctionController::class, 'index'])->middleware(['auth', 'verified'])->name('auctions.index');

    Route::get('/auctions/bids/{auction}', [AuctionController::class, 'bids'])->middleware(['auth', 'verified'])->name('auctions.bids');

});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
