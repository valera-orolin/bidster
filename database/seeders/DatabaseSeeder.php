<?php

namespace Database\Seeders;

use App\Models\AuctionRequest;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        for ($i = 1; $i <= 15; $i++) {
            User::factory()->create([
                'name' => "user$i",
                'email' => "user$i@example.com",
            ]);
        }

        $this->call([
            AuctionSeeder::class,
            BidSeeder::class,
            AuctionRequestSeeder::class,
        ]);
    }
}
