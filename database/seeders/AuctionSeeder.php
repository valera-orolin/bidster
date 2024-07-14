<?php

namespace Database\Seeders;

use App\Models\Lot;
use App\Models\User;
use App\Models\Auction;
use App\Models\Characteristic;
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
            ->count(150)
            ->create()
            ->each(function ($lot) use ($users) {
                Auction::factory()
                    ->for($lot)
                    ->for($users->random(), 'seller')
                    //->state(['status' => 'Active'])
                    ->state(['contract_id' => 0])
                    ->create();
                
                for ($i = 0; $i < 10; $i++) {
                    Characteristic::factory()
                        ->for($lot)
                        ->create();
                }

                $randomImageNumber = rand(1, 50);
                LotImage::create([
                    'lot_id' => $lot->id,
                    'image_path' => "/storage/images/lot_images/{$randomImageNumber}.jpg"
                ]);
            });
    }
}
