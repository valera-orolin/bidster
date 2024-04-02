<?php

namespace Database\Seeders;

use App\Models\Lot;
use App\Models\User;
use App\Models\LotImage;
use App\Models\AuctionRequest;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AuctionRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        Lot::factory()
            ->count(50)
            ->create()
            ->each(function ($lot) use ($users) {
                if (rand(0, 1)) {
                    AuctionRequest::factory()
                        ->for($lot, 'lot')
                        ->for($users->random())
                        ->state(['type' => 'Create'])
                        ->create();
                    
                    $randomImageNumber = rand(1, 50);
                    LotImage::create([
                        'lot_id' => $lot->id,
                        'image_path' => "/storage/images/lot_images/{$randomImageNumber}.jpg"
                    ]);
                } else {
                    $old_lot = Lot::factory()->create();

                    AuctionRequest::factory()
                        ->for($lot, 'lot')
                        ->for($old_lot, 'old_lot')
                        ->for($users->random())
                        ->state(['type' => 'Edit'])
                        ->create();

                    $randomImageNumber = rand(1, 50);
                    LotImage::create([
                        'lot_id' => $lot->id,
                        'image_path' => "/storage/images/lot_images/{$randomImageNumber}.jpg"
                    ]);
                    LotImage::create([
                        'lot_id' => $old_lot->id,
                        'image_path' => "/storage/images/lot_images/{$randomImageNumber}.jpg"
                    ]);
                }
            });
    }
}
