<?php

namespace Database\Seeders;

use App\Enums\Region;
use App\Enums\Role;
use App\Models\User;
use App\Models\Client;
use App\Models\Craftsman;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // create admin account
        User::factory()->create([ 
            'last_name' => 'admin',
            'first_name' => 'admin',
            'email' => 'admin@admin.io',
            'role' => Role::ADMIN,
            'password' => Hash::make('Admin!123'),
            'phone' => '0000000000',
            'city' => 'Paris',
            'region' => Region::ILE_DE_FRANCE,
            'zipcode' => '00000',
        ]);

        // create 3 clients
        for ($i = 0; $i < 3; $i++){
            $user = User::factory()
                ->client()
                ->state([
                    'email' => 'c' . ($i + 1) . '@c' . ($i + 1) . '.c' . ($i + 1),
                ])
                ->create();

            // Create client with only 3 columns wihtout accessor in client model
            $user->client()->create(
                Client::factory()
                ->make()
                ->only([
                    'street_name', 
                    'street_number', 
                    'complement'
                ])
            );
        }

        // create 40 craftsmen
        $nbCraftsman = 40;
        for ($i = 0; $i < $nbCraftsman; $i++){
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
