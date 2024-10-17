<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
            return [
                'title' => $this->faker->sentence(),
                'description' => $this->faker->paragraph(),
                'type' => $this->faker->randomElement(['Bug', 'Feature', 'Improvement']),
                'status' => $this->faker->randomElement(['Open', 'In_Progress', 'Completed', 'Blocked']),
                'priority' => $this->faker->randomElement(['high', 'medium', 'low']),
                'due_date' => $this->faker->date(),
                'assigned_to' => User::factory(), // Assuming it's assigned to another user
                'created_at' => now(),
                'updated_at' => now(),
            ];
      
    }
}
