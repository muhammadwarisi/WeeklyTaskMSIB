<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tasks>
 */
class TasksFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'users_id' => User::factory(),
            'title' => $this->faker->sentence,
            'description'=> $this->faker->paragraph,
            'status'=> $this->faker->randomElement(['On Progress', 'Pending', 'Done']),
        ];
    }
}
