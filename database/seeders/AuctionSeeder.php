<?php

namespace Database\Seeders;

use App\Models\Lot;
use App\Models\User;
use App\Models\Auction;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AuctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = User::all();

        Lot::factory()
            ->count(50)
            ->create()
            ->each(function ($lot) use ($users) {
                Auction::factory()
                    ->for($lot)
                    ->for($users->random(), 'seller')
                    //->state(['status' => 'Active'])
                    ->create();
            });
    }
}
