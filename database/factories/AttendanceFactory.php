<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        $jenis = $this->faker->randomElement(['masuk', 'pulang']);
        $status = $jenis === 'masuk'
            ? $this->faker->randomElement(['tepat waktu', 'terlambat', 'tidak absen'])
            : $this->faker->randomElement(['akhir shift', 'pulang cepat', 'lembur', 'tidak absen']);

        return [
            'attendance_date' => $this->faker->dateTimeBetween('-2 months', 'now'),
            'attendance_type' => $jenis,
            'status' => $status,
            'description' => $this->faker->optional(0.4)->sentence(3),
            'employee_id' => Employee::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function hadir()
    {
        return $this->state(fn() => ['status' => 'hadir']);
    }
}
