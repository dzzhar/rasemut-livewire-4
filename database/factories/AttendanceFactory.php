<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        $date = Carbon::instance(
            $this->faker->dateTimeBetween('-2 months', 'now')
        )->startOfDay();

        // jam kerja default
        $startWork = (clone $date)->setTime(8, 0);
        $endWork   = (clone $date)->setTime(17, 0);

        // random kondisi
        $type = $this->faker->randomElement([
            'hadir',
            'tidak_hadir',
            'tidak_lengkap'
        ]);

        $checkIn = null;
        $checkOut = null;

        $late = 0;
        $overtime = 0;
        $earlyLeave = 0;

        $description = null;

        if ($type === 'hadir') {
            // check in (bisa telat)
            $checkIn = (clone $startWork)->addMinutes(rand(0, 60));

            // check out (bisa pulang cepat / lembur)
            $checkOut = (clone $endWork)->addMinutes(rand(-60, 60));

            // hitung terlambat
            if ($checkIn > $startWork) {
                $late = $startWork->diffInMinutes($checkIn);
            }

            // lembur
            if ($checkOut > $endWork) {
                $overtime = $endWork->diffInMinutes($checkOut);
            }

            // pulang cepat
            if ($checkOut < $endWork) {
                $earlyLeave = $checkOut->diffInMinutes($endWork);
            }
        }

        if ($type === 'tidak presensi keluar') {
            $checkIn = (clone $startWork)->addMinutes(rand(0, 60));

            if ($checkIn > $startWork) {
                $late = $startWork->diffInMinutes($checkIn);
            }
        }

        // description
        $desc = [];

        if ($late > 0) $desc[] = "Terlambat {$late} menit";
        if ($overtime > 0) $desc[] = "Lembur {$overtime} menit";
        if ($earlyLeave > 0) $desc[] = "Pulang cepat {$earlyLeave} menit";

        if (!empty($desc)) {
            $description = implode(', ', $desc);
        }

        return [
            'attendance_date' => $date->toDateString(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => $type,
            'late_minutes' => $late,
            'overtime_minutes' => $overtime,
            'early_leave_minutes' => $earlyLeave,
            'description' => $description,
            'employee_id' => Employee::factory(),
        ];
    }

    public function present()
    {
        return $this->state(fn() => ['status' => 'present']);
    }

    public function absent()
    {
        return $this->state(fn() => [
            'status' => 'tidak hadir',
            'check_in' => null,
            'check_out' => null,
        ]);
    }

    public function incomplete()
    {
        return $this->state(fn() => [
            'status' => 'tidak presensi keluar',
            'check_out' => null,
        ]);
    }
}
