<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Enums\Role;
use App\Enums\Region;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'username' => fake()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('Aqwaqwaqw!123'), 
            'phone' => fake()->numerify('0#########'),
            'city' => fake()->city(),
            'region' => fake()->randomElement(Region::cases()),
            'zipcode' => fake()->numerify('#####'),
            'role' => Role::CLIENT, // default value required before it increased by another role
        ];
    }

    /**
    * Give the user client role
    */
    public function client(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::CLIENT,
        ]);
    }

    /**
    * Give the user client craftsman
    */
    public function craftsman(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::CRAFTSMAN,
        ]);
    }
}
