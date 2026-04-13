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
            'permission_date' => $this->faker->date(),
            'permission_type' => $this->faker->randomElement(['izin', 'sakit', 'lainnya']),
            'description' => $this->faker->optional(0.6)->sentence(),
            'file_path' => null,
            'status' => 'pending',
            'employee_id' => Employee::factory(),
        ];
    }
}
