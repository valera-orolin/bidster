<?php

namespace Database\Seeders;

use App\Models\AuctionRequest;
use App\Models\Lot;
use App\Models\User;
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
                if (rand(0, 1)) { // случайно выбираем тип запроса
                    // для типа Create создаем только новый лот
                    AuctionRequest::factory()
                        ->for($lot, 'lot')
                        ->for($users->random())
                        ->state(['type' => 'Create'])
                        ->create();
                } else {
                    // для типа Edit создаем как новый, так и старый лот
                    AuctionRequest::factory()
                        ->for($lot, 'lot')
                        ->for(Lot::factory()->create(), 'old_lot')
                        ->for($users->random())
                        ->state(['type' => 'Edit'])
                        ->create();
                }
            });
    }
}
