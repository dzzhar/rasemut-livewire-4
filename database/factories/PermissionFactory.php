<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition()
    {
        return [
            'permission_date' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'permission_type' => $this->faker->randomElement(['izin', 'sakit', 'lainnya']),
            'description' => $this->faker->optional(0.6)->paragraph(1),
            'employee_id' => Employee::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
