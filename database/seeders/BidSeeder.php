<?php

namespace Database\Seeders;

use App\Models\Bid;
use App\Models\User;
use App\Models\Auction;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $auctions = Auction::all();

        foreach ($auctions as $auction) {
            $bidSize = $auction->lot->starting_price;

            for ($i = 0; $i < 12; $i++) {
                Bid::factory()
                    ->for($users->random())
                    ->for($auction)
                    ->create(['bid_size' => $bidSize]);

                $bidSize = ceil($bidSize * 1.1);
            }
        }
    }
}
