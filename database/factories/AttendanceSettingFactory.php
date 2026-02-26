<?php

namespace Database\Factories;

use App\Models\AttendanceSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceSettingFactory extends Factory
{
    protected $model = AttendanceSetting::class;

    public function definition()
    {
        $absensiMasuk = '08:00';
        $absensiKeluar = '17:00';
        $latitude = '-6.495194520214448';
        $longitude = '106.786166403684';

        return [
            'check_in_setting' => $absensiMasuk,
            'check_out_setting' => $absensiKeluar,
            'overtime_tolerance' => $this->faker->numberBetween(0, 60),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'radius_attendance' => $this->faker->numberBetween(50, 500),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
