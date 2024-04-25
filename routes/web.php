<?php

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckIsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckIsBanned;
use Illuminate\Foundation\Application;
use App\Http\Controllers\BidController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckIsDirector;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuctionRequestController;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::get('/ban', function () {
    $user = Auth::user();
    return Inertia::render('Profile/Ban', [
        'user' => $user,
    ]);
});


Route::middleware(['auth', 'verified', CheckIsAdmin::class, CheckIsBanned::class])->prefix('admin')->group(function () {

    Route::get('/', function () {
        $user = Auth::user();
        return Inertia::render('Admin/Welcome', [
            'user' => $user,
        ]);
    })->name('admin.welcome');

    Route::get('/requests', [AuctionRequestController::class, 'index'])->name('admin.requests.index');

    Route::get('/requests/create-auction/{auctionRequest}', [AuctionRequestController::class, 'createAuction'])->name('admin.requests.create-auction');

    Route::post('/requests/store-auction/{auctionRequest}', [AuctionRequestController::class, 'storeAuction'])->name('admin.requests.store-auction');

    Route::post('/requests/decline-auction/{auctionRequest}', [AuctionRequestController::class, 'declineAuction'])->name('admin.requests.decline-auction');

    Route::get('/requests/edit-auction/{auctionRequest}', [AuctionRequestController::class, 'editAuction'])->name('admin.requests.edit-auction');

    Route::put('/requests/update-auction/{auctionRequest}', [AuctionRequestController::class, 'updateAuction'])->name('admin.requests.update-auction');

    Route::get('/auctions', [AuctionController::class, 'indexAdmin'])->name('admin.auctions.index');

    Route::post('/auctions/declare-failure/{auction}', [AuctionController::class, 'declareFailureAdmin'])->name('admin.auctions.declare-failure');

    Route::get('/auctions/edit/{auction}', [AuctionController::class, 'editAdmin'])->name('admin.auctions.edit');

    Route::get('/auctions/bids/{auction}', [AuctionController::class, 'bidsAdmin'])->name('admin.auctions.bids');

    Route::get('/users', [ProfileController::class, 'index'])->name('admin.users.index');
    
    Route::post('/users/manage/make-manager/{user}', [ProfileController::class, 'makeManager'])->name('admin.users.make-manager')->middleware(CheckIsDirector::class);

    Route::post('/users/manage/make-user/{user}', [ProfileController::class, 'makeUser'])->name('admin.users.make-user')->middleware(CheckIsDirector::class);

    Route::post('/users/manage/make-banned/{user}', [ProfileController::class, 'makeBanned'])->name('admin.users.make-banned');

    Route::post('/users/manage/make-active/{user}', [ProfileController::class, 'makeActive'])->name('admin.users.make-active');

    Route::get('/users/edit/{user}', [ProfileController::class, 'editAdmin'])->name('admin.users.edit');

    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store']);
});


Route::middleware(['auth', 'verified', CheckIsBanned::class])->group(function () {

    Route::get('/dashboard', [LotController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

    Route::resource('lots', LotController::class)
        ->only(['index', 'create', 'store'])
        ->middleware(['auth', 'verified']);

    Route::get('/likes', [LikeController::class, 'index'])->name('likes.index');

    Route::post('/likes/store/{auction}', [LikeController::class, 'store'])->name('likes.store')->middleware(['auth', 'verified']);

    Route::get('/lots/show/{auction}', [LotController::class, 'show'])->middleware(['auth', 'verified'])->name('lots.show');

    Route::get('/lots/edit/{auction}', [LotController::class, 'edit'])->middleware(['auth', 'verified'])->name('lots.edit');

    Route::put('/lots/update/{auction}', [LotController::class, 'update'])->middleware(['auth', 'verified'])->name('lots.update');

    Route::get('/bids', [BidController::class, 'index'])->middleware(['auth', 'verified'])->name('bids.index');

    Route::get('/bids/show/{bid}', [BidController::class, 'show'])->middleware(['auth', 'verified'])->name('bids.show');

    Route::get('/bids/create/{auction}', [BidController::class, 'create'])->middleware(['auth', 'verified'])->name('bids.create');

    Route::post('/bids/store/{auction}', [BidController::class, 'store'])->middleware(['auth', 'verified'])->name('bids.store');

    Route::get('/auctions', [AuctionController::class, 'index'])->middleware(['auth', 'verified'])->name('auctions.index');

    Route::get('/auctions/bids/{auction}', [AuctionController::class, 'bids'])->middleware(['auth', 'verified'])->name('auctions.bids');

    Route::post('/auctions/declare-failure/{auction}', [AuctionController::class, 'declareFailure'])->name('auctions.declare-failure');

    Route::post('/auctions/declare-finish/{auction}', [AuctionController::class, 'declareFinish'])->name('auctions.declare-finish');
});


Route::middleware(['auth', CheckIsBanned::class])->group(function () {
    Route::get('/profile/show/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
