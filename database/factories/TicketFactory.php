<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['todo', 'doing', 'done']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'weight' => fake()->numberBetween(1, 10),
            'due_date' => fake()->dateTimeBetween('now', '+1 month'),
            'creator_id' => \App\Models\User::factory(),
            'project_id' => \App\Models\Project::factory(),
            'context' => 'proyek',
            'type' => 'task',
        ];
    }

    /**
     * Indicate that the ticket is for general context.
     */
    public function general(): static
    {
        return $this->state(fn (array $attributes) => [
            'context' => 'umum',
            'project_id' => null,
        ]);
    }

    /**
     * Indicate that the ticket is claimed by a user.
     */
    public function claimed($userId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'claimed_by' => $userId ?? \App\Models\User::factory(),
            'claimed_at' => now(),
        ]);
    }

    /**
     * Indicate that the ticket is in progress.
     */
    public function inProgress($userId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'doing',
            'claimed_by' => $userId ?? \App\Models\User::factory(),
            'claimed_at' => now(),
            'started_at' => now(),
        ]);
    }

    /**
     * Indicate that the ticket is completed.
     */
    public function completed($userId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'done',
            'claimed_by' => $userId ?? \App\Models\User::factory(),
            'claimed_at' => now()->subDays(5),
            'started_at' => now()->subDays(4),
            'completed_at' => now()->subDays(1),
        ]);
    }

    /**
     * Indicate that the ticket has a specific target role.
     */
    public function forRole(string $role): static
    {
        return $this->state(fn (array $attributes) => [
            'target_role' => $role,
        ]);
    }

    /**
     * Indicate that the ticket has a specific target user.
     */
    public function forUser($userId): static
    {
        return $this->state(fn (array $attributes) => [
            'target_user_id' => $userId,
        ]);
    }
}
