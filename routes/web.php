<?php

use App\Http\Controllers\AuctionRequestController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\LotController;
use App\Http\Controllers\ProfileController;

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Admin/Welcome');
    })->name('admin.welcome');

    Route::get('/requests', [AuctionRequestController::class, 'index'])->name('admin.requests.index');

    Route::get('/requests/create-auction/{auctionRequest}', [AuctionRequestController::class, 'createAuction'])->name('admin.requests.create-auction');

    Route::post('/requests/store-auction/{auctionRequest}', [AuctionRequestController::class, 'storeAuction'])->name('admin.requests.store-auction');

    Route::post('/requests/decline-auction/{auctionRequest}', [AuctionRequestController::class, 'declineAuction'])->name('admin.requests.decline-auction');
});

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::get('/dashboard', [LotController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('lots', LotController::class)
    ->only(['index', 'create', 'store', 'show'])
    ->middleware(['auth', 'verified']);

Route::get('/lots/show/{auction}', [LotController::class, 'show'])->middleware(['auth', 'verified'])->name('lots.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';