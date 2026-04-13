<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        return [
            'fullname' => $this->faker->name(),
            'is_active' => $this->faker->boolean,
            'leave_remaining' => $this->faker->numberBetween(1, 10),
            'user_id' => User::factory(),
            'position_id' => Position::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
