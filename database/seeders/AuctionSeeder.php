<?php

namespace Database\Seeders;

use App\Models\Lot;
use App\Models\User;
use App\Models\Auction;
use App\Models\LotImage;
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
                
                $randomImageNumber = rand(1, 50);
                LotImage::create([
                    'lot_id' => $lot->id,
                    'image_path' => "/storage/images/lot_images/{$randomImageNumber}.jpg"
                ]);
            });
    }
}
