<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Leave;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveFactory extends Factory
{
    protected $model = Leave::class;

    public function definition()
    {
        $start = $this->faker->dateTimeBetween('-6 months', '+1 month');
        $end = (clone $start)->modify('+' . $this->faker->numberBetween(1, 14) . ' days');

        return [
            'request_date' => $this->faker->dateTimeBetween('-2 months', 'now'),
            'leave_code' => "nferineirgnrig",
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'description' => $this->faker->optional(0.6)->sentence(),
            'status' => $this->faker->randomElement(['pending', 'disetujui', 'ditolak']),
            'employee_id' => Employee::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
