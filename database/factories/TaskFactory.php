<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        $startDate = $this->faker->date();
        $endDate = $this->faker->dateTimeBetween($startDate, '+2 months');

        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(Task::STATUSES),
            'project' => Project::all()->random()->id,
            'user' => User::all()->random()->id,
        ];
    }
}
