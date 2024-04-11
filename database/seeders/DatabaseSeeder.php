<?php

namespace Database\Seeders;

use App\Models\Lot;
use App\Models\User;
use App\Models\Auction;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
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
        // User::factory(10)->create();

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
            'role' => 'Manager',
        ]);

        for ($i = 2; $i <= 15; $i++) {
            User::factory()->create([
                'avatar' => "/storage/images/avatar_images/{$i}.jpg"
            ]);
        }

        $this->call([
            CategorySeeder::class,
            AuctionSeeder::class,
            BidSeeder::class,
            AuctionRequestSeeder::class,
        ]);
    }
}
