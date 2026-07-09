<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Permission;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Position::factory(1)->create();
        AttendanceSetting::factory()->create();

        // generate user
        $admin =  $this->createEmployeeUser('Administrator', 'admin@gmail.com', ['employee', 'admin']);
        $employee = $this->createEmployeeUser('Karyawan', 'karyawan@gmail.com', 'employee');

        // Generate attendance, permit, n leave data 
        $this->generateFullMonthData($admin, '2026-06-01', '2026-06-30');
        $this->generateFullMonthData($employee, '2026-06-01', '2026-06-30');

        $this->generateFullMonthData($admin, '2026-07-01', '2026-07-07');
        $this->generateFullMonthData($employee, '2026-07-01', '2026-07-07');
    }

    private function createEmployeeUser($name, $email, array|string $roles)
    {
        $user = User::factory()->create([
            'email' => $email,
            'roles' => is_array($roles) ? $roles : [$roles],
            'is_active' => true,
            'last_activity' => now(),
            'password' => Hash::make('12345678'),
        ]);

        return Employee::factory()->create([
            'user_id' => $user->id,
            'fullname' => $name,
            'position_id' => Position::inRandomOrder()->first()->id,
        ]);
    }

    private function generateFull($employee, $month)
    {
        $date = now()->setMonth($month)->startOfMonth();
        $daysInMonth = $date->daysInMonth;

        for ($i = 0; $i < $daysInMonth; $i++) {
            if (!$date->isWeekend()) {
                if ($date->day <= 15) {
                    Attendance::factory()->create([
                        'employee_id' => $employee->id,
                        'attendance_date' => $date->toDateString(),
                        'check_in' => $date->copy()->setTime(8, 0, 0),
                        'check_out' => $date->copy()->setTime(17, 0, 0),
                        'status' => 'hadir',
                    ]);
                } elseif ($date->day <= 20) {
                    Permission::factory()->create([
                        'employee_id' => $employee->id,
                        'permission_date' => $date->toDateString(),
                        'permission_type' => 'sakit',
                        'description' => 'Izin keperluan keluarga',
                        'status' => 'izin',
                    ]);
                } else {
                    Leave::factory()->create([
                        'employee_id' => $employee->id,
                        'request_date' => $date->toDateString(),
                        'leave_code' => 'CT',
                        'start_date' => $date->toDateString(),
                        'end_date' => $date->toDateString(),
                        'status' => 'disetujui',
                        'description' => 'Cuti Tahunan',
                    ]);
                }
            }
            $date->addDay();
        }
    }

    private function generateFullMonthData($employee, $startDate, $endDate)
    {
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        $hadir = 0;
        $tidakLengkap = 0;
        $tidakHadir = 0;
        $izin = 0;
        $cuti = 0;

        foreach ($period as $date) {
            if ($date->isWeekend()) continue;

            // BATAS MAKSIMAL
            if ($izin + $cuti + $tidakHadir >= 10) {
                $type = 'attendance';
            } else {
                $rand = rand(1, 100);

                if ($rand <= 70) {
                    $type = 'attendance'; // mayoritas hadir
                } elseif ($rand <= 80) {
                    $type = 'incomplete';
                } elseif ($rand <= 88) {
                    $type = 'absent';
                } elseif ($rand <= 94) {
                    $type = 'permission';
                } else {
                    $type = 'leave';
                }
            }

            switch ($type) {
                case 'attendance':
                    $hadir++;

                    // variasi: normal / telat / lembur
                    $checkIn = $date->copy()->setTime(rand(8, 9), rand(0, 59));
                    $checkOut = $date->copy()->setTime(rand(16, 19), rand(0, 59));

                    Attendance::create([
                        'employee_id' => $employee->id,
                        'attendance_date' => $date->toDateString(),
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'status' => 'hadir',
                    ]);
                    break;

                case 'incomplete':
                    $tidakLengkap++;

                    Attendance::create([
                        'employee_id' => $employee->id,
                        'attendance_date' => $date->toDateString(),
                        'check_in' => $date->copy()->setTime(8, 0),
                        'check_out' => null,
                        'status' => 'tidak_lengkap',
                    ]);
                    break;

                case 'absent':
                    $tidakHadir++;

                    Attendance::create([
                        'employee_id' => $employee->id,
                        'attendance_date' => $date->toDateString(),
                        'status' => 'tidak_hadir',
                    ]);
                    break;

                case 'permission':
                    $izin++;

                    Permission::create([
                        'employee_id' => $employee->id,
                        'permission_date' => $date->toDateString(),
                        'permission_type' => 'pribadi',
                        'description' => 'Keperluan pribadi',
                        'status' => 'izin',
                    ]);
                    break;

                case 'leave':
                    $cuti++;

                    Leave::create([
                        'employee_id' => $employee->id,
                        'request_date' => $date->toDateString(),
                        'leave_code' => 'CT',
                        'start_date' => $date->toDateString(),
                        'end_date' => $date->toDateString(),
                        'status' => 'disetujui',
                        'description' => 'Cuti tahunan',
                    ]);
                    break;
            }
        }
    }
}
