<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\CraftsmanJob;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Craftsman>
 */
class CraftsmanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'craftsman_job_id' => CraftsmanJob::inRandomOrder()->first()->id, // get valid id from craftsmanjob
            'price' => fake()->randomFloat(2, 0, 999.99),
            'available' => fake()->numberBetween(0, 1),
            'description' => fake()->sentence(rand(20, 500)),
        ];
    }
}
