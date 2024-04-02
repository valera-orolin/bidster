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

        for ($i = 0; $i < 150; $i++) {
            Bid::factory()
                ->for($users->random())
                ->for($auctions->random())
                ->create();
        }
    }
}
