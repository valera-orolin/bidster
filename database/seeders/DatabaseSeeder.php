<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Subcategory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AuctionRequest;
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

        User::factory()->create([
            'name' => "user1",
            'email' => "user1@example.com",
            'avatar' => "/storage/images/avatar_images/16.png"
        ]);

        /*
        for ($i = 1; $i <= 15; $i++) {
            User::factory()->create([
                'avatar' => "/storage/images/avatar_images/{$i}.jpg"
            ]);
        }

        $this->call([
            AuctionSeeder::class,
            BidSeeder::class,
            AuctionRequestSeeder::class,
        ]);*/

        Category::create([
            'name' => 'cat1',
        ]);
        Category::create([
            'name' => 'cat2',
        ]);

        Subcategory::create([
            'name' => 'subcat11',
            'category_id' => 1,
        ]);
        Subcategory::create([
            'name' => 'subcat21',
            'category_id' => 2,
        ]);
        Subcategory::create([
            'name' => 'subcat22',
            'category_id' => 2,
        ]);
    }
}
