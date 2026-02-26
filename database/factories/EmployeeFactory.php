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
        $nip = $this->faker->unique()->numerify(str_repeat('#', 12));

        return [
            'fullname' => $this->faker->name(),
            'employee_code' => $nip,
            'is_active' => $this->faker->boolean,
            'user_id' => User::factory(),
            'position_id' => Position::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
