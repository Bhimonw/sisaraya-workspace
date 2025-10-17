<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Business>
 */
class BusinessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Business',
            'description' => fake()->paragraph(),
            'created_by' => \App\Models\User::factory(),
            'status' => 'pending',
        ];
    }

    /**
     * Indicate that the business is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_by' => \App\Models\User::factory(),
            'approved_at' => now(),
        ]);
    }

    /**
     * Indicate that the business is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'approved_by' => \App\Models\User::factory(),
            'approved_at' => now(),
            'rejection_reason' => fake()->sentence(),
        ]);
    }
}
