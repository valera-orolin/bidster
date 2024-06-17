<?php

namespace Database\Seeders;

use App\Models\Lot;
use App\Models\User;
use App\Models\Auction;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Contract;
use App\Models\LotImage;
use App\Models\Subcategory;
use App\Models\AuctionRequest;
use App\Models\Characteristic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $files = Storage::files('public/images');
        foreach($files as $file) {
            Storage::delete($file);
        }

        $user1 = User::factory()->create([
            'name' => "user1",
            'email' => "user1@example.com",
            'avatar' => "/storage/images/avatar_images/16.png",
            'role' => 'Director',
        ]);

        $user2 = User::factory()->create([
            'name' => "user2",
            'email' => "user2@example.com",
            'avatar' => "/storage/images/avatar_images/1.jpg",
            'role' => 'User',
        ]);

        $user3 = User::factory()->create([
            'name' => "user3",
            'email' => "user3@example.com",
            'avatar' => "/storage/images/avatar_images/2.jpg",
            'role' => 'User',
        ]);

        $user4 = User::factory()->create([
            'name' => "user4",
            'email' => "user4@example.com",
            'avatar' => "/storage/images/avatar_images/3.jpg",
            'role' => 'User',
        ]);

        $user5 = User::factory()->create([
            'name' => "user5",
            'email' => "user5@example.com",
            'avatar' => "/storage/images/avatar_images/4.png",
            'role' => 'User',
        ]);

        /*
        for ($i = 2; $i <= 15; $i++) {
            User::factory()->create([
                'avatar' => "/storage/images/avatar_images/{$i}.jpg"
            ]);
        }*/

        $this->call([
            CategorySeeder::class,
            AuctionSeeder::class,
            BidSeeder::class,
            //AuctionRequestSeeder::class,
        ]);

        Lot::factory()
            ->count(1)
            ->create()
            ->each(function ($lot) use ($user2) {
                AuctionRequest::factory()
                    ->for($lot, 'lot')
                    ->for($user2)
                    ->state(['type' => 'Create'])
                    ->create();
                
                $randomImageNumber = rand(1, 50);
                LotImage::create([
                    'lot_id' => $lot->id,
                    'image_path' => "/storage/images/lot_images/{$randomImageNumber}.jpg"
                ]);

                for ($i = 0; $i < 10; $i++) {
                    Characteristic::factory()
                        ->for($lot)
                        ->create();
                }
            });
        
        Contract::create(['address' => '0x']);

        /*
        Lot::factory()
            ->count(1)
            ->create()
            ->each(function ($lot) use ($user1, $user2) {
                Auction::factory()
                    ->for($lot)
                    ->for($user2, 'seller')
                    ->state(['status' => 'Active'])
                    ->create();
                
                for ($i = 0; $i < 10; $i++) {
                    Characteristic::factory()
                        ->for($lot)
                        ->create();
                }
            });*/
    }
}
