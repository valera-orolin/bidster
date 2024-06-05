<?php

use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\PrizeController;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $auctions = Auction::where('status', 'Active')
        ->whereHas('lot', function ($query) {
            $query->where('end_date', '<=', now());
        })
        ->get();
        
    foreach ($auctions as $auction) {
        $request = new Request();
        $request->merge(['auction_id' => $auction->id]);
        $prizeController = new PrizeController();
        $prizeController->store($request);
        $auction->status = 'Finished';
        $auction->save();
    }
})->everyMinute();