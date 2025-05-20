<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Craftsman;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // create 3 clients
        for ($i = 0; $i < 3; $i++){
            $user = User::factory()
                ->client()
                ->state([
                    'email' => 'c' . ($i + 1) . '@c' . ($i + 1) . '.c' . ($i + 1),
                ])
                ->create();

            $user->client()->create(Client::factory()->make()->toArray());
        }

        // create 3 craftsmen
        for ($i = 0; $i < 3; $i++){
            $user = User::factory()
                ->craftsman()
                ->state([
                    'email' => 'a' . ($i + 1) . '@a' . ($i + 1) . '.a' . ($i + 1),
                ])
                ->create();

            $user->craftsman()->create(Craftsman::factory()->make()->toArray());
        }
    }
}
